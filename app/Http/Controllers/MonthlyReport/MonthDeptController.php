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

class MonthDeptController extends Controller {

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
        return view('monthlyreport.flat');
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

        $monthlyReportFlat = DB::table('tenement_flats as a')
            ->leftjoin('tf_payment_all_months as b', function($join) use ($year_month)
            {
                $join->on('a.id', '=', 'b.flat_id');
                $join->on('b.year_month', '=', DB::raw($year_month));
            })

            ->leftjoin('tf_payment_elec_hd as c', function($join) use ($year_month)
            {
                $join->on('a.id', '=', 'c.flat_id');
                $join->on('c.year_month', '=', DB::raw($year_month));
                $join->on('c.activation', '=', DB::raw('1'));                
            }, 'left outer')
            ->leftjoin('tf_payment_water_hd as d', function($join) use ($year_month)
            {
                $join->on('a.id', '=', 'd.flat_id');
                $join->on('d.year_month', '=', DB::raw($year_month));
                $join->on('d.activation', '=', DB::raw('1'));                
            }, 'left outer')
            ->leftjoin('tf_payment_gas_hd as e', function($join) use ($year_month)
            {
                $join->on('a.id', '=', 'e.flat_id');
                $join->on('e.year_month', '=', DB::raw($year_month));
                $join->on('e.activation', '=', DB::raw('1'));                

            }, 'left outer')
            ->select(['a.id', 'a.address','a.flat_code', 'a.name','a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area','b.manager_fee','b.elec_fee','b.water_fee','b.gas_fee','b.service_fee','b.parking_fee','b.manager_fee_paid','b.elec_fee_paid','b.water_fee_paid','b.gas_fee_paid','b.service_fee_paid','b.parking_fee_paid','b.year_month',

                'c.elec_type_name', DB::raw('c.old_index_hd as elec_old_index_hd'), DB::raw('c.new_index_hd as elec_new_index_hd'), DB::raw('c.mount_hd as elec_mount_hd'),
                'd.water_type_name', DB::raw('d.old_index_hd as water_old_index_hd'), DB::raw('d.new_index_hd as water_new_index_hd'), DB::raw('d.mount_hd as water_mount_hd'),
                'e.gas_type_name', DB::raw('e.old_index_hd as gas_old_index_hd'), DB::raw('e.new_index_hd as gas_new_index_hd'), DB::raw('e.mount_hd as gas_mount_hd') 
                ])
            ->where('a.activation', 1)
            ->where('b.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc');
        //dd($monthlyReportFlat->toSql());   
        return Datatables::of($monthlyReportFlat)
                ->make(true);        
    }        
}