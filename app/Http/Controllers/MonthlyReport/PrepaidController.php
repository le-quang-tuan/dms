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

class PrepaidController extends Controller {

    /**
     * Displays datatables front end view Trả trước báo cáo
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        // dd(date("YmdH"));
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('monthlyreport.prepaid');
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
        return View('monthlyfee.prepaid', [ 'year_month'=>$year_month]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($year_month) {
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $monthlyReportFlat = DB::table('tenement_flats as a')
            ->join('tf_paid_hd as b', 'a.id', '=', 'b.flat_id')
            ->join('tf_paid_dt as c', 'b.id', '=', 'c.paid_id')
            ->join('mst_payment_types as d', 'd.id', '=', 'c.payment_type')
            ->select(['a.id', 'a.address','a.flat_code', 'a.name','a.phone', 'a.persons', 'b.receive_date', 'a.area','b.receive_from','b.receiver','b.comment','c.money', DB::raw('d.name as payment_name')])
            ->where('a.activation', 1)
            ->where('b.activation', 1)
            ->where('c.activation', 1)
            ->where('c.year_month', $year_month)
            ->where('c.prepaid_flg', 1)
            ->where('d.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc');
        return Datatables::of($monthlyReportFlat)
                ->make(true);        
    }        
}