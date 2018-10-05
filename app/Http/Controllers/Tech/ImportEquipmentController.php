<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TenementEquipmentImport;
use App\Models\TenementProducer;
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

class ImportEquipmentController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('tech.equipmentimport');
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

        return View('tech.equipmentimport');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $TenementEquipmentsImport = DB::table('tenement_equipments_import')
            ->select(['*'])
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->where('token', '=', Session::token())
            ->orderBy('equipment_group_id', 'producer_id', 'name', 'asc');

        return Datatables::of($TenementEquipmentsImport)
                ->make(true);        
    }        

    public function download(Request $request)
    {
        return Excel::create('import_equipment' . $request->year . $request->month, function($excel) use ($request) {
                $excel->sheet('mySheet', function($sheet) use ($request)
                {
                    $data = TenementProducer::where('tenement_id',Auth::user()->tenement_id)
                    ->where('activation',1)
                    ->select("producer_code as MA_NHA_CUNG_CAP", "name as NHA_CUNG_CAP", 
                        DB::raw("'' as MA_NHOM"), 
                        DB::raw("'' as THIET_BI"),
                        DB::raw("'' as NHAN"),
                        DB::raw("'' as MODEL"),
                        DB::raw("'' as THONG_SO"),
                        DB::raw("'' as KHU_VUC"),
                        DB::raw("'". $request->comment ."' as GHI_CHU"))
                    ->orderBy('producer_code', 'asc')
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
                        'group_code' => $value->ma_nhom, 
                        'equipment_type_id' => 1,
                        'producer_code' => $value->ma_nha_cung_cap,
                        'producer' => $value->nha_cung_cap,
                        'equipment_code' => "",
                        'name' => $value->thiet_bi,
                        'label' => $value->nhan,
                        'model' => $value->model,
                        'specification' => $value->thong_so,
                        'area' => $value->khu_vuc,
                        'tenement_id' => $tenement_id,
                        'comment' => $value->ghi_chu,
                        'token' => $request->_token,
                        'created_at' => (new \DateTime()),
                        'updated_at' => (new \DateTime()),
                        'activation' => 1
                        ];
                }
                // dd($insert);
                if(!empty($insert)){

                    $old_data = TenementEquipmentImport::where('tenement_id', $tenement_id)
                        ->where('token', Session::token())
                        ->delete();

                    DB::table('tenement_equipments_import')->insert($insert);

                    DB::statement("
                        CALL proc_import_equipments(
                            '" . $tenement_id ."' ,
                            '" . Session::token() . "'
                            )
                    ");

                    return redirect('tech/importequipment');
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
                'equipment'
                )
        ");

        return redirect('tech/importequipment');
    }
}