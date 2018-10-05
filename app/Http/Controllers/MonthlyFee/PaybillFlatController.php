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

class PaybillFlatController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_all($year_month) {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        return View('monthlyfee.paybillall',['year_month'=>$year_month]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tf_paybill_hd = DB::table("tf_paybill_hd")
        ->where('activation', '=', 1)
        ->where('id', '=', $id)
        ->orderBy('id')->get();

        $tf_paybill_dt = DB::table("tf_paybill_dt")
        ->join('mst_payment_types', 'mst_payment_types.id', '=', 'tf_paybill_dt.payment_type')
        ->where('tf_paybill_dt.activation', '=', 1)
        ->where('tf_paybill_dt.paybill_id', '=', $id)
        ->orderBy('year_month', 'desc')
        ->orderBy('mst_payment_types.id', 'asc')
        ->get();

        $flat_info = DB::table("tenement_flats")
        ->join('tf_paybill_hd', 'tenement_flats.id', '=', 'tf_paybill_hd.flat_id')
        ->select('tenement_flats.*')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tf_paybill_hd.id', '=', $id)
        ->orderBy('id')->get();

        return View('monthlyfee.paybilldetailflat', [ 
            'flat_info'=> $flat_info[0],
            'tf_paybill_hd'=>$tf_paybill_hd[0],
            'tf_paybill_dt'=>$tf_paybill_dt,
            'id'=>$id
             ]);
    }


    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($year_month) {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        if ($year_month == 'ALL')
        {
            $year_month = date("Ym");   
        }

        // $PaidflatList = DB::table('tf_paybill_hd')
        //     ->select(['tf_paybill_hd.*'])
        //     ->join("tenement_flats", "tenement_flats.id", "=","tf_paybill_hd.flat_id")
        //     ->join("tenements", "tenement_flats.tenement_id", "=","tenements.id")
        //     ->where('tf_paybill_hd.id', '<>' , 0)
        //     ->where('tf_paybill_hd.year_month', '=' , $year_month)
        //     ->where('tenements.id', '=', Auth::user()->tenement_id)
        //     ->where('tf_paybill_hd.paid_flag', '=', 0)
        //     ->orderBy('tf_paybill_hd.id', 'desc');
    
        // $PaidflatList = DB::table('tf_paybill_hd')
        // ->select(['tf_paybill_hd.*'])
        // ->join("tenement_flats", "tenement_flats.id", "=","tf_paybill_hd.flat_id")
        // ->join("tenements", "tenement_flats.tenement_id", "=","tenements.id")
        // ->where('tf_paybill_hd.id', '<>' , 0)
        // ->where('tf_paybill_hd.year_month', '=' , $year_month)
        // ->where('tenements.id', '=', Auth::user()->tenement_id)
        // ->where('tf_paybill_hd.paid_flag', '=', 0)
        // ->orderBy('tf_paybill_hd.id', 'desc');

        $PaidflatList = DB::table('tf_paybill_hd as a')
            ->join('tf_paybill_dt as b', 'a.id', '=', 'b.paybill_id')
            ->join('tenement_flats as c', 'a.flat_id', '=', 'c.id')
            ->select('a.id','c.address', 'a.bill_no', DB::raw('sum(b.money) as money'),'a.year_month','a.paid_flag')
            ->where('a.activation', '=', 1)
            ->where('b.activation', '=', 1)
            ->where('c.activation', '=', 1)
            ->where('a.paid_flag', '=', 0)
            ->where('c.tenement_id', '=', Auth::user()->tenement_id)
            ->where('a.year_month', '=', $year_month)
            ->groupBy('a.id','a.year_month', 'b.paybill_id','c.address','a.bill_no')
            ->orderBy('a.bill_no');

        //dd($PaidflatList);

        return Datatables::of($PaidflatList)
            ->addColumn('action', function ($PaidflatList) {
                return '<a href="../paybilldetail/'. $PaidflatList->id .'" class="btn btn-xs btn-primary" target="_blank">Thu Phí</a>';                    
            })
            ->make(true);
    }       

    public function save(Request $request)
    {
        $utils = new NumberUtil();
        //Check period user
        $tenement_id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'receive_date' => 'required',
                    'receiver' => 'required',
                    'receive_from' => 'required',
                    'comment' => 'required'
        ]);

        if ($v->fails()) {
            return redirect('monthlyfee/paybilldetail/'.$request->id)->withInput()->withErrors($v);
        }
        
        DB::beginTransaction();
        try {
            if (isset($request->receive_date) && $request->receive_date != ''){
                $date = DateTime::createFromFormat('d/m/Y', $request->receive_date);
                $date = $date->format('Ymd');
            }
            else {
                $date = '';
            }
            DB::statement("
                CALL proc_paid_from_paybill(
                    '" . $tenement_id . "',
                    '" . $request->id . "', 
                    '" . $date . "', 
                    '" . $request->receiver . "', 
                    '" . $request->receive_from . "', 
                    '" . $request->comment . "',
                    '" . Auth::user()->id . "'                    
                )
            ");

            DB::commit();
            return back()->with('tenement-alert-success','Phí thu đã được lưu !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  
    }
}