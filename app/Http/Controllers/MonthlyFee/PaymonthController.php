<?php

namespace App\Http\Controllers\MonthlyFee;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TfPaidDt;
use App\Models\TfPaidHd;
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
use App\LaraBase\NumberUtil;

class PaymonthController extends Controller {

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

        return View('monthlyfee.exepaymonth');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $tenement_id = Auth::user()->tenement_id;

        $payment_exe_his = DB::table('tf_payment_his')
            ->select(['*'])
            ->where('tenement_id', '=' , $tenement_id)
            ->orderBy('id', 'desc');

        return Datatables::of($payment_exe_his)
                ->addColumn('paymentnotice', function ($payment_exe_his) {
                    return '<a href="../report/paybill/'. $payment_exe_his->year_month .'" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                    
                })
                ->addColumn('paybill', function ($payment_exe_his   ) {
                    return '<a href="../report/paymentnoticefiles/'. $payment_exe_his->year_month .'" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                       
                })

                ->make(true);        
    }        

    public function exex_store(Request $request)
    {
        //Check period user
        $id = Auth::user()->tenement_id;
        $year_month = $request->year . $request->month;
        
        $date = date('Ym');
        // if ($year_month != $date){
        //     return back()->withInput()->withErrors(['updateFail' => "Chỉ thực hiện kết sổ cho tháng hiện tại"]);
        // }

        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        DB::beginTransaction();
        try {
            DB::statement("
                CALL proc_payment_month(". $id . ", " . $year_month . ", null, " . Auth::user()->id .")
            ");
            DB::commit();

            return redirect('monthlyfee/exepaymonth')->with('paymentExe-alert-success','Đã thực hiện kết sổ thành công !');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }

    public function exex_recalculate(Request $request)
    {
        $id = Auth::user()->tenement_id;
        $year_month = $request->recal_year . $request->recal_month;
        $flat_id = $request->flat_id;

        // dd($id);
        //return response()->json(array('year_month'=> $year_month), 200);

        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        // DB::beginTransaction();

        try {
            $cur_month = date('Ym');

            $timestamp = strtotime($request->recal_year . '-' . $request->recal_month . '-01'.'-0 month');
            $exe_month = date('Ym', $timestamp);
            $i = 0;

            if ($cur_month < $exe_month){
                $cur_month = $exe_month;
            }
            // dd($exe_month . $cur_month);
            while ($exe_month <= $cur_month){
                DB::statement("
                    CALL proc_payment_month_flat(". $id . ", " . $exe_month . ", ". $flat_id .", " . Auth::user()->id .")
                ");
                $i++;

                $timestamp = strtotime($request->recal_year . '-' . $request->recal_month . '-01'.'+' . $i . ' month');
                $exe_month = date('Ym', $timestamp);                
            }

            // DB::commit();

            //$a = new PaymentNoticeController();

            // return redirect('monthlyfee')->with('tenementElec-alert-success','Đã thực hiện kết sổ thành công !');
            return response()->json(array('msg'=> 'OK'), 200);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }
}