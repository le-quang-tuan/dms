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

class PaymentFlatController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($flat_id) {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $mst_payment_types = DB::table("mst_payment_types")
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $flat = DB::table("tenement_flats")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('tenement_id', '=', $tenement_id)
        ->where('id', '=', $flat_id)
        ->orderBy('flat_code')->get();

        return View('monthlyfee.paymentflat', [ 
            'mst_payment_types'=>$mst_payment_types,
            'flat_id'=>$flat_id,
            'flat_info'=>$flat[0]
             ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function load_paid($paid_id) {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $mst_payment_types = DB::table("mst_payment_types")
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $paid = DB::table(DB::raw('tf_paid_hd a, tf_paid_dt b, tenement_flats c'))
            ->select(['a.flat_id', 'a.id','a.book_bill','a.bill_no','a.receive_date', 'a.receive_from','a.receiver','b.comment', 'c.address', 'c.name','c.phone', 'b.year_month', 'b.payment_type', 'b.money'])
            ->where("b.paid_id", "=", DB::raw('a.id'))
            ->where("b.paid_id", "=", $paid_id)
            ->where("c.id", "=", DB::raw('a.flat_id'))
            ->where('c.tenement_id', '=' , $tenement_id)
            ->where('b.activation', '=' , 1)
            ->orderBy('a.id', 'desc')->get(); 

        $flat = DB::table("tenement_flats")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('tenement_id', '=', $tenement_id)
        ->where('id', '=', $paid[0]->flat_id)
        ->orderBy('flat_code')->get();

        $tenement = Tenement::where('id',$tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('monthlyfee.paidpayment', [ 
            'mst_payment_types'=>$mst_payment_types,
            'flat_id'=>$paid[0]->flat_id,
            'flat_info'=>$flat[0],
            'paid'=>$paid,
            'tenement'=>$tenement[0]
            ]);    
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_paid_detail_list() {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $mst_payment_types = DB::table("mst_payment_types")
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        return View('monthlyfee.paiddetaillist', [ 
            'mst_payment_types'=>$mst_payment_types,
             ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_all() {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        return View('monthlyfee.paymentall');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($flat_id) {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        if ('all' != $flat_id){
            $PaidflatList = DB::table('tf_paid_hd')
                ->select(['tf_paid_hd.*'])
                ->join("tenement_flats", "tenement_flats.id", "=","tf_paid_hd.flat_id")
                ->join("tenements", "tenement_flats.tenement_id", "=","tenements.id")
                ->where('tf_paid_hd.id', '<>' , 0)
                ->where('tf_paid_hd.flat_id', '=' , $flat_id)
                ->where('tenements.id', '=', Auth::user()->tenement_id)
                ->orderBy('tf_paid_dt.id', 'desc');
        }
        else {
            $PaidflatList = DB::table('tf_paid_hd')
                ->select(['tf_paid_hd.id','tf_paid_hd.bill_no','tf_paid_hd.receive_date', 'tf_paid_hd.receive_from','tf_paid_hd.receiver','tf_paid_hd.comment', 'tenement_flats.address', 'tenement_flats.name','tenement_flats.phone', DB::raw('SUM(tf_paid_dt.money) as money')])
                ->join("tenement_flats", "tenement_flats.id", "=","tf_paid_hd.flat_id")
                ->join("tf_paid_dt", "tf_paid_dt.paid_id", "=","tf_paid_hd.id")
                ->where('tf_paid_hd.id', '<>' , 0)
                ->where('tenement_flats.tenement_id', '=', Auth::user()->tenement_id)
                ->where('tf_paid_dt.activation', '=', 1)
                ->groupBy('tf_paid_hd.id','tf_paid_hd.bill_no','tf_paid_hd.receive_date', 'tf_paid_hd.receive_from','tf_paid_hd.receiver','tf_paid_hd.comment')
                ->orderBy('tenement_flats.address', 'desc'); 
        }
        return Datatables::of($PaidflatList)
                ->addColumn('action', function ($PaidflatList) {
                    return '<a href="../monthlyfee/paiddetail/'. $PaidflatList->id .'" class="btn btn-xs btn-primary" target="_blank">Chi Tiết</a>';                    
                })
                ->addColumn('paidbill', function ($PaidflatList) {
                    return '<a href="../report/paidbill/'. $PaidflatList->id .'/2" class="btn btn-xs btn-primary" target="_blank">Tải Phiếu Thu</a>';                    
                })
                ->make(true);        
    }       

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyDataDetail(Request $request) {  
        $year_month = $request->input('year_month');
        $flat_id = $request->input('flat_id');

        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $tenement_id = Auth::user()->tenement_id;

        $PaidflatList = DB::table(DB::raw('tf_paid_hd a, tf_paid_dt b, tenement_flats c'))
            ->select(['b.id','a.bill_no','a.receive_date', 'a.receive_from','a.receiver','b.comment', 'c.address', 'c.name','c.phone', 'b.year_month', 'b.money'])
            ->where("b.paid_id", "=",DB::raw('a.id'))
            ->where("c.id", "=",DB::raw('a.flat_id'))
            ->where('c.tenement_id', '=' , $tenement_id)
            ->where('b.activation', '=' , 1)
            ->where(function($query) use ($flat_id,$year_month)
            {
                if (isset($flat_id)){
                    $query->where('a.flat_id', '=' , $flat_id);
                }
                if (isset($year_month)){
                    $query->where('b.year_month', '=' , $year_month);
                }
            })
            ->orderBy('a.id', 'desc');
        
        return Datatables::of($PaidflatList)
                ->addColumn('action', function ($PaidflatList) {
                    return '<button type="button" class="btn btn-primary btn-details" value="'. $PaidflatList->id .'" >Hủy</button>';            
                })
                ->make(true);        
    }

    public function save(Request $request)
    {
        $utils = new NumberUtil();
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'receive_date' => 'required',
                    'receiver' => 'required',
                    'receive_from' => 'required',
                    'book_bill' => 'required',
                    'bill_no' => 'required'
        ]);

        if ($v->fails()) {
            return redirect('monthlyfee/paid/'.$request->flat_id)->withInput()->withErrors($v);
        }
        // Tarrif validate
        if( isset($request->counter) ){
            $rules = array(
                'money' => 'required');

            for($i=0;$i<count($request->counter);$i++){
                $tempMoney = '';

                $step = $i+1;    
                $tempName = 'money'.$step;

                $rules[$tempName] = 'required';
            }
            $v = Validator::make($request->all(), $rules);

            if($v->fails()){
                return redirect('monthlyfee/paid/'.$request->flat_id)->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {
            $tf_paid_hd_id = TfPaidHd::create([
                'flat_id'   =>  $request->input('flat_id'),
                'receiver'   =>  $request->input('receiver'),
                'receive_date'  =>  $request->input('receive_date'),
                'receive_from'  =>  $request->input('receive_from'),
                'book_bill'  =>  $request->input('book_bill'),
                'bill_no'  =>  $request->input('bill_no'),
                'year_month'  =>  $request->input('year') . $request->input('month'),
                'money'  =>  $utils->number($request->input('money')),
                'paid_type'  =>  1,
                'activation'  =>  1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'updated_at' => date("Y-m-d H:i:s")                
            ]) -> id;

            for($i=0;$i<count($request->counter);$i++){
                //dd($t_elec_type_id);

                $tmpPaymentType = '';
                $tmpMoney = '';
                $tmpComment = '';

                $step = $i+1;    
                $tmpPaymentType = 'payment_type'.$step;
                $tmpMoney = 'money'.$step;
                $tmpComment = 'comment'.$step;

                TfPaidDt::create([
                    'paid_id'  => $tf_paid_hd_id,
                    'activation'    => 1,
                    'comment'          => $request->input($tmpComment),
                    'year_month'          => $request->input('year') . $request->input('month'),
                    'payment_type'    => $request->input($tmpPaymentType),
                    'money'         => $utils->number($request->input($tmpMoney)),
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date("Y-m-d H:i:s")                    
                ]);
            }

            DB::commit();
            //update sum
            DB::statement("
                CALL proc_paid_money('". $id . "', '" . $request->input('flat_id') . "',null)
            ");

            return back()->with('tenement-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  
    }

    public function modify(Request $request)
    {
        $utils = new NumberUtil();
        //Check period user
        $id = Auth::user()->tenement_id;

        $paid_id = $request->input('paid_id');
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'receive_date' => 'required',
                    'receiver' => 'required',
                    'receive_from' => 'required',
                    'book_bill' => 'required',
                    'bill_no' => 'required'
        ]);

        if ($v->fails()) {
            return redirect('monthlyfee/paid/'.$request->flat_id)->withInput()->withErrors($v);
        }
        // Tarrif validate
        if( isset($request->counter) ){
            $rules = array(
                'money' => 'required');

            for($i=0;$i<count($request->counter);$i++){
                $tempMoney = '';

                // $step = $i+1;    
                $step = $request->counter[$i];    

                $tempName = 'money'.$step;

                $rules[$tempName] = 'required';
            }
            $v = Validator::make($request->all(), $rules);

            if($v->fails()){
                return redirect('monthlyfee/paidpayment/'.$paid_id)->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {
            $paid_hd = TfPaidHd::find($paid_id);
            $paid_hd->activation = 0;
            $paid_hd->updated_by = Auth::user()->id;
            $paid_hd->updated_at = date("Y-m-d H:i:s");
            $paid_hd->save();

            $paid_dt = TfPaidDt::where('paid_id',$paid_id)
            ->update(['activation' => 0, 'updated_by' => Auth::user()->id, 'updated_at' => date("Y-m-d H:i:s")]);

            $tf_paid_hd_id = TfPaidHd::create([
                'flat_id'   =>  $request->input('flat_id'),
                'receiver'   =>  $request->input('receiver'),
                'receive_date'  =>  $request->input('receive_date'),
                'receive_from'  =>  $request->input('receive_from'),
                'book_bill'  =>  $request->input('book_bill'),
                'bill_no'  =>  $request->input('bill_no'),
                'year_month'  =>  $request->input('year') . $request->input('month'),
                'money'  =>  $utils->number($request->input('money')),
                'paid_type'  =>  1,
                'activation'  =>  1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'updated_at' => date("Y-m-d H:i:s")
            ]) -> id;

            for($i=0;$i<count($request->counter);$i++){
                $tmpPaymentType = '';
                $tmpMoney = '';
                $tmpComment = '';
                $tmpYear = '';
                $tmpMonth = '';

                $step = $request->counter[$i];    
                $tmpPaymentType = 'payment_type'.$step;
                $tmpMoney = 'money'.$step;
                $tmpComment = 'comment'.$step;
                $tmpYear = 'year'.$step;
                $tmpMonth = 'month'.$step;

                TfPaidDt::create([
                    'paid_id'  => $tf_paid_hd_id,
                    'activation'    => 1,
                    'comment'          => $request->input($tmpComment),
                    'year_month'          => $request->input($tmpYear) . $request->input($tmpMonth),
                    'payment_type'    => $request->input($tmpPaymentType),
                    'money'         => $utils->number($request->input($tmpMoney)),
                    'paymonth_flg'  =>  1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
            }

            DB::commit();
            //update sum
            DB::statement("
                CALL proc_paid_money('". $id . "', '" . $request->input('flat_id') . "',null)
            ");

            return redirect('monthlyfee/paidpayment/'. $tf_paid_hd_id)->withInput()->with('tenement-alert-success','Phí thu đã được cập nhật!');

            // return back()->with('tenement-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  
    }
}