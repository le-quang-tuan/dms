<?php

namespace App\Http\Controllers\Import;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TfElecUsedImport;
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

class ElecController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('flat.flatelec');
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

        return View('import.elec');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $TfElecUsedImport = DB::table('tf_elec_used_import')
            ->select(['*'])
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->where('token', '=', Session::token())
            ->orderBy('flat_code', 'asc');

        return Datatables::of($TfElecUsedImport)
                ->addColumn('action', function ($TfElecUsedImport) {
                    return '<a href="flat/'. $TfElecUsedImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                    
                })
                ->addColumn('elec', function ($TfElecUsedImport) {
                    return '<a href="flat/elec/'. $TfElecUsedImport->id . '" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('water', function ($TfElecUsedImport) {
                    return '<a href="flat/water/'. $TfElecUsedImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('elec', function ($TfElecUsedImport) {
                    return '<a href="flat/elec/'. $TfElecUsedImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('parking', function ($TfElecUsedImport) {
                    return '<a href="flat/parking/'. $TfElecUsedImport->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->addColumn('service', function ($TfElecUsedImport) {
                    return '<a href="flat/service/'. $TfElecUsedImport->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->make(true);        
    }        

    public function download(Request $request)
    {
        return Excel::create('import_elec' . $request->year . $request->month, function($excel) use ($request) {
                $excel->sheet('mySheet', function($sheet) use ($request)
                {
                    $date_from = "";
                    $date_to = "";

                    if ($request->date_from != ""){
                        $date = DateTime::createFromFormat('d/m/Y', $request->date_from);
                        $date_from = $date->format('Ymd');
                    }
                    
                    if ($request->date_to != ""){
                        $date = DateTime::createFromFormat('d/m/Y', $request->date_to);
                        $date_to = $date->format('Ymd');
                    }

                    $data = TenementFlat::where('tenement_id',Auth::user()->tenement_id)
                    ->where('activation',1)
                    ->select("id as Id", "flat_code as MA_CAN_HO", "name as CHU_HO", "address as SO_CAN_HO", 
                        DB::raw("'". $request->year ."' as NAM"), 
                        DB::raw("'". $request->month ."' as THANG"),
                        DB::raw("'". $date_from ."' as TU_NGAY"),
                        DB::raw("'". $date_to ."' as DEN_NGAY"),
                        DB::raw("'". $request->comment ."' as GHI_CHU"),
                        DB::raw("'' as CHI_SO_TRUOC"),
                        DB::raw("'' as CHI_SO_MOI") )
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
                    // $date_from = "";
                    // $date_to = "";

                    // if ($value->tu_ngay != "")
                    //     $date_from = date("Ymd", strtotime(str_replace('/', '-',$value->tu_ngay)));
                    
                    // if ($value->den_ngay != "")
                    //     $date_to = date("Ymd", strtotime(str_replace('/', '-',$value->den_ngay)));

                    $insert[] = [
                        'flat_id' => $value->id, 
                        'flat_code' => $value->ma_can_ho,
                        'year_month' => $value->nam . $value->thang,
                        'date_from' => $value->tu_ngay,
                        'date_to' => $value->den_ngay,
                        'comment' => $value->ghi_chu,
                        'old_index' => $value->chi_so_truoc,
                        'new_index' => $value->chi_so_moi,
                        'tenement_id' => $tenement_id,
                        'token' => $request->_token,
                        'created_at' => (new DateTime()),
                        'updated_at' => (new DateTime()),
                        'activation' => 1
                        ];
                }
                // dd($insert);
                if(!empty($insert)){

                    $old_data = TfElecUsedImport::where('tenement_id', $tenement_id)
                        ->where('token', Session::token())
                        ->delete();

                    DB::table('tf_elec_used_import')->insert($insert);

                    return redirect('import/importElec');
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
                'elec'
                )
        ");

        return redirect('import/importElec');
    }
}