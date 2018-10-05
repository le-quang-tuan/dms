<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TfvehicleImport;
use App\Models\TenementFlat;
use App\Models\Item;
use DB;
use yajra\Datatables\Datatables;
use Validator;
use Auth;
use Redirect;
use DateTime;
use Input;
use Excel;
use Session;
class ImportVehicleController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('flat.flatvehicle');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        return View('Import.vehicle');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd("aaa");

        // dd(123);
        $TfvehicleImport = DB::table('tf_vehicle_import')
            ->select(['*'])
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->where('token', '=', Session::token())
            ->orderBy('flat_code', 'asc');

        return Datatables::of($TfvehicleImport)
                ->addColumn('action', function ($TfvehicleImport) {
                    return '<a href="flat/'. $TfvehicleImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                    
                })
                ->addColumn('vehicle', function ($TfvehicleImport) {
                    return '<a href="flat/vehicle/'. $TfvehicleImport->id . '" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('water', function ($TfvehicleImport) {
                    return '<a href="flat/water/'. $TfvehicleImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('vehicle', function ($TfvehicleImport) {
                    return '<a href="flat/vehicle/'. $TfvehicleImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('parking', function ($TfvehicleImport) {
                    return '<a href="flat/parking/'. $TfvehicleImport->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->addColumn('service', function ($TfvehicleImport) {
                    return '<a href="flat/service/'. $TfvehicleImport->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->make(true);        
    }        

    public function download(Request $request)
    {
        return Excel::create('import_vehicle' . $request->year . $request->month, function($excel) use ($request) {
                $excel->sheet('data', function($sheet) use ($request)
                {
                    //dd($request->year );
                    $data = TenementFlat::where('tenement_id',Auth::user()->tenement_id)
                    ->where('activation',1)
                    ->select("id as Id", "flat_code as MA_CAN_HO", "name as CHU_HO", "address as SO_CAN_HO", 
                        DB::raw("'' as BIEN_SO"),
                        DB::raw("'' as CHU_XE"),
                        DB::raw("'' as HIEU_XE"),
                        DB::raw("'' as HANG_XE"),
                        DB::raw("'' as MAU_XE"),
                        DB::raw("'' as GIU_XE_TU_NGAY"),
                        DB::raw("'' as GIU_XE_DEN_NGAY"),
                        DB::raw("'' as LOAI_PHI_XE"),
                        DB::raw("'' as GHI_CHU"))
                    ->orderBy('flat_code', 'asc')
                    ->get()->toArray();
                    $sheet->fromArray($data);
                });

        })->download("xls");
    }

    public function store(Request $request)
    {
        $tenement_id = Auth::user()->tenement_id;
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();

            $data = Excel::load($path, function($reader) {

            })->get();

            if(!empty($data) && $data->count()){

                foreach ($data as $key => $value) {

                    $insert[] = [
                        'flat_id' => $value->id, 
                        'flat_code' => $value->ma_can_ho,
                        'begin_contract_date' => $value->giu_xe_tu_ngay,
                        'end_contract_date' => $value->giu_xe_den_ngay,
                        'comment' => $value->ghi_chu,
                        'name' => $value->chu_xe,
                        'number_plat' => $value->bien_so,
                        'label' => $value->hieu_xe,
                        'maker' => $value->hang_xe,
                        'color' => $value->mau_xe,
                        'vehicle_type_id' => $value->loai_phi_xe,
                        'token' => $request->_token,
                        'tenement_id' => $tenement_id,
                        'created_at' => (new \DateTime()),
                        'updated_at' => (new \DateTime()),
                        'activation' => 1
                        ];
                }
                // dd($insert);
                if(!empty($insert)){

                    $old_data = TfvehicleImport::where('tenement_id', $tenement_id)
                        ->where('token', Session::token())
                        ->delete();

                    DB::table('tf_vehicle_import')->insert($insert);

                    return redirect('import/importVehicle');
                }
            }
        }
    }

    public function save(Request $request)
    {
        $tenement_id = Auth::user()->tenement_id;

        DB::statement("
            CALL proc_getImportData(
                '" . $request->_token . "', 
                '" . $tenement_id ."' ,
                'vehicle'
                )
        ");

        return redirect('import/importVehicle');
    }
}