<?php

namespace App\Http\Controllers\MonthlyFee;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\TenementFlat;
use DB;
use yajra\Datatables\Datatables;
use Auth;

class FlatController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        // dd(date("YmdH"));
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('monthlyfee.flat');
    }

    public function status() {
        // dd(date("YmdH"));
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        $year_month = '20';
        $monthlyFeeFlat = DB::table('tenement_flats as a')
            // ->leftjoin('tf_payment_all_months as b', function($join) use ($year_month)
            // {
            //     $join->on('a.id', '=', 'b.flat_id');
            //     $join->on('b.year_month', '=', DB::raw($year_month));
            //     $join->on('b.activation', '=' , DB::raw('1'));
            // }, 'left outer')
            ->leftjoin('v_sample as c', function($join) 
            {
                $join->on('a.id', '=', 'c.flat_id');
                $join->on('a.tenement_id', '=', 'c.tenement_id');
            }, 'left outer')
            // ->select(['a.id', 'a.floor_num','a.block_name', 'a.address','a.flat_code', 'a.name','a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area','b.manager_fee','b.elec_fee','b.water_fee','b.gas_fee','b.service_fee','b.parking_fee','b.manager_fee_paid','b.elec_fee_paid','b.water_fee_paid','b.gas_fee_paid','b.service_fee_paid','b.parking_fee_paid', 'c.manager_dept', 'c.elec_dept', 'c.water_dept', 'c.gas_dept', 'c.parking_dept', 'c.service_dept'
            //     ])
            ->select(['a.id', 'a.floor_num','a.block_name', 'a.address','a.flat_code', 'a.name','a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area', 'c.manager_dept', 'c.elec_dept', 'c.water_dept', 'c.gas_dept', 'c.parking_dept', 'c.service_dept'
                ])
            ->where('a.activation', 1)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            // ->orderBy('a.flat_code', 'asc');
            ->orderBy('a.block_name', 'asc')
            ->orderBy('a.floor_num', 'desc')
            ->orderBy(DB::raw("ifnull(a.floor_name,'')"), 'desc')
            ->orderBy('a.flat_num', 'asc')->get();
        // dd($monthlyFeeFlat);
        $flats = array();
        $block = "";
        $floor = "";
        $count = 0;

        foreach ($monthlyFeeFlat as $value) {
            if ($block != $value->block_name){
                $floor = "";
                $block = $value->block_name;
            }

            if ($floor != $value->floor_num){
                $floor = $value->floor_num;
                $count = 0;
            } else {
                $count++;
            }
            $flats[$block . intval($floor) . $count] = $value;            
        }
        //dd($flats);
        return View('monthlyfee.status', [ 'monthlyFeeFlat'=>$monthlyFeeFlat, 'flats'=>$flats]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($year_month) {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        if ($year_month == 'detail')
            $year_month = date("Ym");
        //dd($Tenement[0]);
        return View('monthlyfee.flat', [ 'year_month'=>$year_month]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($year_month) {
        //dd(123);     
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // $monthlyFeeFlat = DB::table('tenement_flats as a')
        //     ->leftjoin('tf_payment_all_months as b', function($join) use ($year_month)
        //     {
        //         $join->on('a.id', '=', 'b.flat_id');
        //         $join->on('b.year_month', '=', DB::raw($year_month));
        //         $join->on('b.activation', '=' , DB::raw('1'));
        //     }, 'left outer')
        //     ->leftjoin('v_payment_dept as c', function($join) 
        //     {
        //         $join->on('a.id', '=', 'c.flat_id');
        //         $join->on('a.tenement_id', '=', 'c.tenement_id');
        //     }, 'left outer')
        //     ->select(['a.id', 'a.address','a.flat_code', 'a.name','a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area','b.manager_fee','b.elec_fee','b.water_fee','b.gas_fee','b.service_fee','b.parking_fee','b.manager_fee_paid','b.elec_fee_paid','b.water_fee_paid','b.gas_fee_paid','b.service_fee_paid','b.parking_fee_paid', 'c.manager_dept', 'c.elec_dept', 'c.water_dept', 'c.gas_dept', 'c.parking_dept', 'c.service_dept','b.manager_fee_skip','b.elec_fee_skip','b.water_fee_skip','b.gas_fee_skip','b.service_fee_skip','b.parking_fee_skip'
        //         ])
        //     ->where('a.activation', 1)
        //     ->where('a.tenement_id', '=', Auth::user()->tenement_id)
        //     ->orderBy('a.flat_code', 'asc');

        $monthlyFeeFlat = DB::table( DB::raw("
            (SELECT a.id, a.address,a.flat_code, a.name,a.phone, a.is_stay, a.persons, a.receive_date, a.area,b.manager_fee,b.elec_fee,b.water_fee,b.gas_fee,b.service_fee,b.parking_fee,b.manager_fee_paid,b.elec_fee_paid,b.water_fee_paid,b.gas_fee_paid,b.service_fee_paid,b.parking_fee_paid, c.manager_dept, c.elec_dept, c.water_dept, c.gas_dept, c.parking_dept, c.service_dept,b.manager_fee_skip,b.elec_fee_skip,b.water_fee_skip,b.gas_fee_skip,b.service_fee_skip,b.parking_fee_skip
            FROM tenement_flats as a, (SELECT * FROM tf_payment_all_months a where a.activation = 1 and a.`year_month` = '". $year_month ."' and a.tenement_id = '". Auth::user()->tenement_id ."') as b, (select a.flat_id AS flat_id,sum(((ifnull(a.manager_fee,0) - ifnull(a.manager_fee_paid,0)) - ifnull(a.manager_fee_skip,0))) AS manager_dept,sum(((ifnull(a.elec_fee,0) - ifnull(a.elec_fee_paid,0)) - ifnull(a.elec_fee_skip,0))) AS elec_dept,sum(((ifnull(a.water_fee,0) - ifnull(a.water_fee_paid,0)) - ifnull(a.water_fee_skip,0))) AS water_dept,sum(((ifnull(a.gas_fee,0) - ifnull(a.gas_fee_paid,0)) - ifnull(a.gas_fee_skip,0))) AS gas_dept,sum(((ifnull(a.parking_fee,0) - ifnull(a.parking_fee_paid,0)) - ifnull(a.parking_fee_skip,0))) AS parking_dept,sum(((ifnull(a.service_fee,0) - ifnull(a.service_fee_paid,0)) - ifnull(a.service_fee_skip,0))) AS service_dept from tf_payment_all_months a where (a.activation = 1) and (a.year_month <= convert(date_format(now(),'%Y%m') using utf8mb4)) and a.tenement_id = '". Auth::user()->tenement_id ."' group by a.flat_id) as c
            WHERE a.id = b.flat_id and a.id = c.flat_id and a.activation = 1 and a.tenement_id = '". Auth::user()->tenement_id ."' order by a.flat_code) A"));
        //dd($monthlyFeeFlat->toSql());

        return Datatables::of($monthlyFeeFlat)
                ->addColumn('action', function ($monthlyFeeFlat) {
                    return '<a style="text-align: center;width: 30%;" href="flat/detail/'. $monthlyFeeFlat->id .'">' . $monthlyFeeFlat->address . '</a>';                    
                })
                ->addColumn('payment', function ($monthlyFeeFlat) use ($year_month){
                    return '<a href="report/paybill/'. $year_month . '/' . $monthlyFeeFlat->id .'" class="btn btn-xs btn-primary" target="_blank"><span class="fa fa-file-pdf-o"></span>Phiếu Thu Phí Tháng</a>';                    
                })
                ->addColumn('paymentnotice', function ($monthlyFeeFlat) use ($year_month){
                    return '<a href="report/paymentnotice/'. $monthlyFeeFlat->id .'/' . $year_month . '" class="btn btn-xs btn-primary" target="_blank"><span class="fa fa-file-pdf-o"></span>Thông Báo Phí Tháng</a>';                    
                })
                ->addColumn('paid', function ($monthlyFeeFlat) use ($year_month){
                    return '<a href="monthlyfee/paymonth/'. $monthlyFeeFlat->id . '" class="btn btn-info" target="_blank"><span class="fa fa-list-alt fa-fw"></span>DS Phiếu Thu</a>';                    
                })
                ->addColumn('paidnew', function ($monthlyFeeFlat) use ($year_month){
                    return '<a href="monthlyfee/paymonth/'. $monthlyFeeFlat->id . '/new' . '" class="btn btn-info" target="_blank"><span class="fa fa-plus"></span>&nbsp;Thu Công Nợ    &nbsp;</a>';
                })
                ->addColumn('deptskip', function ($monthlyFeeFlat) use ($year_month){
                    return '<a href="monthlyfee/deptskip/'. $monthlyFeeFlat->id . '" class="btn btn-danger" target="_blank"><span class="fa fa-list-alt fa-fw"></span>DS Phí Không Thu</a>';                    
                })
                ->addColumn('deptskipnew', function ($monthlyFeeFlat) use ($year_month){
                    return '<a href="monthlyfee/deptskip/'. $monthlyFeeFlat->id . '/new' .'" class="btn btn-danger" target="_blank"><span class="fa fa-plus"></span>Phí Không Thu</a>';                    
                })
                ->addColumn('recalculate', function ($monthlyFeeFlat) use ($year_month){
                    //return '<a href="monthlyfee/recalculate/'. $monthlyFeeFlat->id .'/' . $year_month . '" class="btn btn-xs btn-primary">Tính Lại Phí Tháng</a>';
                    return '<button type="button" 
                        address="'. $monthlyFeeFlat->address .'" 
                        name="'. $monthlyFeeFlat->name .'"  
                        id="'. $monthlyFeeFlat->id .'"
                        class="btn btn-xs btn-primary recalculate">Tính Lại Phí Tháng<br>nếu có thay đổi</button>';                                       
                })
                ->addColumn('elec', function ($monthlyFeeFlat) use ($year_month){
                    //return '<a href="monthlyfee/recalculate/'. $monthlyFeeFlat->id .'/' . $year_month . '" class="btn btn-xs btn-primary">Tính Lại Phí Tháng</a>';
                    return '<button type="button" 
                        id="'. $monthlyFeeFlat->id .'"
                        class="btn btn-xs btn-primary elec">Điện</button>';                                       
                })
                ->addColumn('water', function ($monthlyFeeFlat) use ($year_month){
                    //return '<a href="monthlyfee/recalculate/'. $monthlyFeeFlat->id .'/' . $year_month . '" class="btn btn-xs btn-primary">Tính Lại Phí Tháng</a>';
                    return '<button type="button" 
                        id="'. $monthlyFeeFlat->id .'"
                        class="btn btn-xs btn-primary water">Nước</button>';                                       
                })
                ->addColumn('gas', function ($monthlyFeeFlat) use ($year_month){
                    //return '<a href="monthlyfee/recalculate/'. $monthlyFeeFlat->id .'/' . $year_month . '" class="btn btn-xs btn-primary">Tính Lại Phí Tháng</a>';
                    return '<button type="button" 
                        id="'. $monthlyFeeFlat->id .'"
                        class="btn btn-xs btn-primary gas">Gas</button>';                                       
                })
                ->addColumn('service', function ($monthlyFeeFlat) use ($year_month){
                    //return '<a href="monthlyfee/recalculate/'. $monthlyFeeFlat->id .'/' . $year_month . '" class="btn btn-xs btn-primary">Tính Lại Phí Tháng</a>';
                    return '<button type="button" 
                        id="'. $monthlyFeeFlat->id .'"
                        class="btn btn-xs btn-primary service">Dịch Vụ</button>';                                       
                })
                ->addColumn('vehicle', function ($monthlyFeeFlat) use ($year_month){
                    //return '<a href="monthlyfee/recalculate/'. $monthlyFeeFlat->id .'/' . $year_month . '" class="btn btn-xs btn-primary">Tính Lại Phí Tháng</a>';
                    return '<button type="button" 
                        id="'. $monthlyFeeFlat->id .'"
                        class="btn btn-xs btn-primary vehicle">Xe Tháng</button>';                                       
                })
                ->make(true);        
    }        
}