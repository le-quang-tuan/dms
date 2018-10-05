<?php

namespace App\Http\Controllers\MonthlyFee;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tenement;
use App\Models\User;
use App\Models\TenementFlat;
use DB;
use yajra\Datatables\Datatables;
use Auth;

class FlatPaidController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        $tenement_id = Auth::user()->tenement_id;

        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }

        $tenement = Tenement::where('id', $tenement_id)->where('activation',1)->get();
        $userDetailInfo = User::where('id', Auth::user()->id)->get();
        
        return view('monthlyfee.flatpaid');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {        
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd(123);
        $tenementFlat = DB::table('tenement_flats as a')
            ->select(['a.*'])
            ->where('a.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc');
        return Datatables::of($tenementFlat)
                ->addColumn('action', function ($tenementFlat) {
                    return '<a style="text-align: center;" href="flat/detail/'. $tenementFlat->id .'" >' . $tenementFlat->flat_code . '</a>';                    
                })
                ->addColumn('paidlst', function ($tenementFlat) {
                    return '<a href="../monthlyfee/paid/'. $tenementFlat->id . '" class="btn btn-xs btn-primary" target="_blank">Danh Sách & Thêm Mới</a>';                    
                })
                ->addColumn('prepaid', function ($tenementFlat) {
                    return '<a href="../monthlyfee/prepaid/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Danh Sách & Thêm Mới</a>';                    
                })
                ->addColumn('payment', function ($tenementFlat) {
                    return '<a href="../monthlyfee/paymonth/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Thu Tổng Nợ</a>';                    
                })
                ->addColumn('otherpayment', function ($tenementFlat) {
                    return '<a href="../monthlyfee/paymonth/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Thu Tổng Nợ</a>';                    
                })
                ->make(true);        
    }        
}