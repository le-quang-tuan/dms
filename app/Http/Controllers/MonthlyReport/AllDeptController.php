<?php

namespace App\Http\Controllers\MonthlyReport;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\TenementFlat;
use DB;
use yajra\Datatables\Datatables;
use Auth;

class AllDeptController extends Controller {

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
        return view('monthlyreport.alldept');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData(Request $request) {
        $to_month = '';
        if (isset($request->to_month)){
            $to_month = " and a.year_month <= '" . $request->to_month . "'" ;
        }

        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // $monthlyReportFlatGeneral = DB::table(DB::raw("(
        //     Select tenement_flats.*, tenement_elec_types.elec_type, tenement_water_types.water_type, tenement_gas_types.gas_type, A.*, B.elec_mount, C.water_mount, D.gas_mount
        //     From
        //     (Select 
        //     a.flat_id, 
        //     sum(a.manager_fee) manager_fee, sum(a.elec_fee) elec_fee, sum(a.water_fee) water_fee, sum(a.gas_fee) gas_fee, sum(a.parking_fee) parking_fee, sum(a.service_fee) service_fee,
        //     sum(ifnull(a.manager_fee,0) + ifnull(a.elec_fee,0) + ifnull(a.water_fee,0) + ifnull(a.gas_fee,0) + ifnull(a.parking_fee,0) + ifnull(a.service_fee,0)) as monthfee,
        //     sum(a.manager_fee_paid + a.elec_fee_paid + a.water_fee_paid + a.gas_fee_paid + a.parking_fee_paid + a.service_fee_paid) as paidfee,
        //     sum(ifnull(a.manager_fee,0) + ifnull(a.elec_fee,0) + ifnull(a.water_fee,0) + ifnull(a.gas_fee,0) + ifnull(a.parking_fee,0) + ifnull(a.service_fee,0)) -
        //     sum(ifnull(a.manager_fee_paid,0) + ifnull(a.elec_fee_paid,0) + ifnull(a.water_fee_paid,0) + ifnull(a.gas_fee_paid,0) + ifnull(a.parking_fee_paid,0) + ifnull(a.service_fee_paid,0)) as deptfee
        //     from tf_payment_all_months a
        //     where a.activation = 1
        //     and a.year_month <= '201702'
        //     and a.tenement_id = 1
        //     group by a.flat_id
        //     order by a.flat_id) as A 
        //     left join
        //     tenement_flats
        //     on A.flat_id = tenement_flats.id and tenement_flats.tenement_id = 1
        //     left join
        //     tenement_elec_types
        //     on tenement_flats.elec_type_id = tenement_elec_types.id
        //     left join
        //     tenement_gas_types
        //     on tenement_flats.water_type_id = tenement_gas_types.id
        //     left join
        //     tenement_water_types
        //     on tenement_flats.gas_type_id = tenement_water_types.id
             
        //     left join
        //     (Select 
        //     a.flat_id,
        //     sum(a.mount_hd) elec_mount
        //     from tf_payment_elec_hd a
        //     where a.activation = 1
        //     and a.tenement_id = 1
        //     group by a.flat_id
        //     order by a.flat_id) as B

        //     on A.flat_id = B.flat_id

        //     left join
        //     (Select 
        //     a.flat_id,
        //     sum(a.mount_hd) water_mount
        //     from tf_payment_water_hd a
        //     where a.activation = 1
        //     and a.tenement_id = 1
        //     group by a.flat_id
        //     order by a.flat_id) as C
        //     on A.flat_id = C.flat_id
        //     left join
        //     (Select 
        //     a.flat_id,
        //     sum(a.mount_hd) gas_mount
        //     from tf_payment_gas_hd a
        //     where a.activation = 1
        //     and a.tenement_id = 1
        //     group by a.flat_id
        //     order by a.flat_id) as D
        //     on A.flat_id = D.flat_id) as B"));
            $monthlyReportFlatGeneral = DB::table(DB::raw("(
            Select tenement_flats.*, '' as elec_type, '' as water_type, '' as gas_type, A.*, '' as elec_mount, '' as water_mount, '' as gas_mount
            From
            (Select 
            a.flat_id, 
            sum(a.manager_fee) manager_fee, 
            sum(a.elec_fee) elec_fee, 
            sum(a.water_fee) water_fee, 
            sum(a.gas_fee) gas_fee, 
            sum(a.parking_fee) parking_fee, 
            sum(a.service_fee) service_fee,
            sum(ifnull(a.manager_fee_skip, 0) + 
                ifnull(a.elec_fee_skip, 0) + 
                ifnull(a.water_fee_skip, 0) +
                ifnull(a.gas_fee_skip, 0) + 
                ifnull(a.parking_fee_skip, 0) + 
                ifnull(a.service_fee_skip, 0)) fee_skip,

            sum(ifnull(a.manager_fee,0) + ifnull(a.elec_fee,0) + ifnull(a.water_fee,0) + ifnull(a.gas_fee,0) + ifnull(a.parking_fee,0) + ifnull(a.service_fee,0)) 
            as monthfee,
            sum(ifnull(a.manager_fee_paid, 0) + ifnull(a.elec_fee_paid, 0) + ifnull(a.water_fee_paid, 0) + ifnull(a.gas_fee_paid, 0) + ifnull(a.parking_fee_paid, 0) + ifnull(a.service_fee_paid, 0)) as paidfee,
            sum(ifnull(a.manager_fee,0) + ifnull(a.elec_fee,0) + ifnull(a.water_fee,0) + ifnull(a.gas_fee,0) + ifnull(a.parking_fee,0) + ifnull(a.service_fee,0)) -
            (
            sum(ifnull(a.manager_fee_paid,0) + ifnull(a.elec_fee_paid,0) + ifnull(a.water_fee_paid,0) + ifnull(a.gas_fee_paid,0) + ifnull(a.parking_fee_paid,0) + ifnull(a.service_fee_paid,0))
            +
                sum(ifnull(a.manager_fee_skip, 0) + 
                ifnull(a.elec_fee_skip, 0) + 
                ifnull(a.water_fee_skip, 0) +
                ifnull(a.gas_fee_skip, 0) + 
                ifnull(a.parking_fee_skip, 0) + 
                ifnull(a.service_fee_skip, 0))
            ) as deptfee

            from tf_payment_all_months a
            where a.activation = 1
            " . $to_month . "
            and a.tenement_id = 1
            group by a.flat_id
            order by a.flat_id) as A 
            left join
            tenement_flats
            on A.flat_id = tenement_flats.id and tenement_flats.tenement_id = 1
             
            left join
            (Select 
            a.flat_id,
            sum(a.mount_hd) elec_mount
            from tf_payment_elec_hd a
            where a.activation = 1
            and a.tenement_id = 1
            group by a.flat_id
            order by a.flat_id) as B

            on A.flat_id = B.flat_id

            left join
            (Select 
            a.flat_id,
            sum(a.mount_hd) water_mount
            from tf_payment_water_hd a
            where a.activation = 1
            and a.tenement_id = 1
            group by a.flat_id
            order by a.flat_id) as C
            on A.flat_id = C.flat_id
            left join
            (Select 
            a.flat_id,
            sum(a.mount_hd) gas_mount
            from tf_payment_gas_hd a
            where a.activation = 1
            and a.tenement_id = 1
            group by a.flat_id
            order by a.flat_id) as D
            on A.flat_id = D.flat_id) as B"));
        //dd($monthlyReportFlatGeneral);
        return Datatables::of($monthlyReportFlatGeneral)
                ->make(true); 
    }        
}