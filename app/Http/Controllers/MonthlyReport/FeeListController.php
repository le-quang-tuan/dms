<?php

namespace App\Http\Controllers\MonthlyReport;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TenementFlatsImport;
use App\Models\TenementFlat;
use App\Models\Tenement;
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

class FeeListController extends Controller {

    /** Bảng Kê
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('monthlyreport.feelist');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_parking_tariff = DB::table("tenement_parking_tariff")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $elec_tariffs = DB::table("tenement_elec_types a")
        ->leftjoin('tenement_elec_tariff as b', function($join)
        {
            $join->on('a.id', '=', 'b.elec_type_id');
            $join->on('b.activation', '=', '1');
        })
        ->select(['a.*', 'b.name'])
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $water_tariffs = DB::table("tenement_water_types")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $gas_tariffs = DB::table("tenement_gas_types")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $lsChar = range('A', 'Z');
        $lsNum = range('00', '99');

        return View('import.flat', [ 
            'lsChar'=>$lsChar,
            'lsNum'=>$lsNum,
            'gas_tariffs'=>$gas_tariffs,
            'water_tariffs'=>$water_tariffs,
            'elec_tariffs'=>$elec_tariffs,
        ]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $TenementFlatsImport = DB::table('tenement_flats_import')
            ->select(['*'])
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->where('token', '=', Session::token())
            ->orderBy('flat_code', 'asc');

        return Datatables::of($TenementFlatsImport)
                ->addColumn('action', function ($TenementFlatsImport) {
                    return '<a href="flat/'. $TenementFlatsImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                    
                })
                ->addColumn('flat', function ($TenementFlatsImport) {
                    return '<a href="flat/flat/'. $TenementFlatsImport->id . '" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('water', function ($TenementFlatsImport) {
                    return '<a href="flat/water/'. $TenementFlatsImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('flat', function ($TenementFlatsImport) {
                    return '<a href="flat/flat/'. $TenementFlatsImport->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('parking', function ($TenementFlatsImport) {
                    return '<a href="flat/parking/'. $TenementFlatsImport->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->addColumn('service', function ($TenementFlatsImport) {
                    return '<a href="flat/service/'. $TenementFlatsImport->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->make(true);        
    }        

    public function download(Request $request)
    {
        return Excel::create('import_flat' . $request->year . $request->month, function($excel) use ($request) {
                $excel->sheet('data', function($sheet) use ($request)
                {
                    $data = array();

                    $header = array();
                    array_push($header,
                        "TEN_KHU",
                        "KHU_SO",
                        "TANG_SO",
                        "CAN_HO_SO",
                        "BIEU_PHI_DIEN",
                        "BIEU_PHI_NUOC",
                        "BIEU_PHI_GAS",
                        "TEN_CAN_HO",
                        "CHU_HO",
                        "DIEN_THOAI",
                        "DIEN_TICH_CAN_HO",
                        "SO_NHAN_KHAU",
                        "NGAY_NHAN",
                        "DANG_O"
                    );
                    array_push($data, $header);

                    $j = 0;
                    for($i=0;$i<count($request->counter);$i++){
                        $row = array();

                        $blockName = '';
                        $blockNum = '';
                        $floorFrom = '';
                        $floorTo = '';
                        $flatNumFrom = '';
                        $flatNumTo = '';
                        $elecTypeId = '';
                        $waterTypeId = '';
                        $gasTypeId = '';

                        $step = $i+1;    
                        $tempName = 'name'.$step;
                        $tempIndex_from = 'index_from'.$step;
                        $tempPrice = 'price'.$step;

                        $blockName = 'flat_block_name'.$step;
                        $blockNum = 'flat_block_num'.$step;
                        $floorFrom = 'floor_from'.$step;
                        $floorTo = 'floor_to'.$step;
                        $flatNumFrom = 'flat_num_from'.$step;
                        $flatNumTo = 'flat_num_to'.$step;
                        $elecTypeId = 'elec_tariff'.$step;
                        $waterTypeId = 'water_tariff'.$step;
                        $gasTypeId = 'gas_tariff'.$step;

                        $getBlockName = $request->input($blockName);
                        $getBlockNum = $request->input($blockNum); 
                        $getElecTypeId = $request->input($elecTypeId);
                        $getWaterTypeId = $request->input($waterTypeId);
                        $getGasTypeId = $request->input($gasTypeId);

                        if($request->input($floorTo) != ''){
                            for ($m = $request->input($floorFrom); $m <= $request->input($floorTo); $m++){
                                $row = array();
                                $floor = str_pad($m,2,"0",STR_PAD_LEFT);

                                if($request->input($flatNumTo) != ''){
                                    for ($n = $request->input($flatNumFrom); $n <= $request->input($flatNumTo); $n++)
                                    {
                                        $row = array();
                                        $floorNum = str_pad($n,2,"0",STR_PAD_LEFT);

                                        array_push($row,
                                            $getBlockName,
                                            $getBlockNum, 
                                            $floor, 
                                            $floorNum,
                                            $getElecTypeId,
                                            $getWaterTypeId,
                                            $getGasTypeId
                                        );
                                        array_push($data, $row);
                                    }
                                }
                                else {
                                    $row = array();

                                    $floorNum = str_pad($request->input($flatNumFrom),2,"0",STR_PAD_LEFT);
                                    
                                    $row = array();
                                    array_push($row,
                                        $getBlockName,
                                        $getBlockNum, 
                                        $floor, 
                                        $floorNum,
                                        $getElecTypeId,
                                        $getWaterTypeId,
                                        $getGasTypeId
                                    );
                                    array_push($data, $row);
                                }
                            }
                        }
                        else {
                            $floor = str_pad($request->input($flatNumTo),2,"0",STR_PAD_LEFT);
                            if($request->input($flatNumTo) != ''){
                                for ($n = $request->input($flatNumFrom); $n <= $request->input($flatNumTo); $n++)
                                {
                                    $floorNum = str_pad($n,2,"0",STR_PAD_LEFT);

                                    $row = array();
                                    array_push($row,
                                        $getBlockName,
                                        $getBlockNum, 
                                        $floor, 
                                        $floorNum,
                                        $getElecTypeId,
                                        $getWaterTypeId,
                                        $getGasTypeId
                                    );
                                    array_push($data, $row);
                                }
                            }
                            else {
                                $floorNum = str_pad($request->input($flatNumFrom),2,"0",STR_PAD_LEFT);

                                $row = array();
                                array_push($row,
                                    $getBlockName,
                                    $getBlockNum, 
                                    $floor, 
                                    $floorNum,
                                    $getElecTypeId,
                                    $getWaterTypeId,
                                    $getGasTypeId
                                );
                                array_push($data, $row);
                            }

                            array_push($row,
                                $getBlockName,
                                $getBlockNum, 
                                $floor, 
                                $floorNum,
                                $getElecTypeId,
                                $getWaterTypeId,
                                $getGasTypeId
                            );
                            array_push($data, $row);

                        }
                    }
                    $sheet->fromArray($data, null, 'A1', false, false);
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
                    // $receive_date = "";
                    // $date = DateTime::createFromFormat('d/m/Y', $value->ngay_nhan);

                    // dd($value->ngay_nhan);
                    //     //strtotime(str_replace('/', '-',$value->ngay_nhan))));
                    // if ($value->ngay_nhan != "")
                    //     $receive_date = date("Ymd", strtotime(str_replace('/', '-',$value->ngay_nhan)));

                    $address = $value->ten_can_ho;
                    if ($address == "")
                        $address =  $value->ten_khu . 
                                    $value->khu_so . "." .
                                    $value->tang_so . "." .
                                    $value->can_ho_so;

                    $insert[] = [
                        //'flat_id' => $value->id, 
                        'flat_code' =>  $value->ten_khu . 
                                        $value->khu_so . "-" .
                                        $value->tang_so . "-" .
                                        $value->can_ho_so,

                        'block_name' => $value->ten_khu,
                        'block_num' => $value->khu_so,
                        'floor' => $value->tang_so,
                        'flat_num' => $value->can_ho_so,

                        'address' => $address,
                        'name' => $value->chu_ho,
                        'phone' => $value->dien_thoai,
                        'area' => $value->dien_tich_can_ho,
                        'persons' => $value->so_nhan_khau,
                        'comment' => $value->ghi_chu,
                        'receive_date' => $value->ngay_nhan,
                        'exist_flg' => $value->dang_o,

                        'elec_code' => $value->bieu_phi_dien,
                        'water_code' => $value->bieu_phi_nuoc,
                        'gas_code' => $value->bieu_phi_gas,

                        'token' => $request->_token,
                        'tenement_id' => $tenement_id,
                        'created_at' => (new \DateTime()),
                        'updated_at' => (new \DateTime()),
                        'activation' => 1
                        ];
                }
                if(!empty($insert)){
                    $old_data = TenementFlatsImport::where('tenement_id', $tenement_id)
                        ->where('token', Session::token())
                        ->delete();

                    DB::table('tenement_flats_import')->insert($insert);

                    return redirect('import/importFlat');
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
                'flat'
                )
        ");

        return redirect('import/importFlat');
    }
}