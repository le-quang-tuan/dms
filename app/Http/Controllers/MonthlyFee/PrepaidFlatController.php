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

class PrepaidFlatController extends Controller {

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

        $tenement = Tenement::where('id',$tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('monthlyfee.prepaidflat', [ 
            'mst_payment_types'=>$mst_payment_types,
            'flat_id'=>$flat_id,
            'flat_info'=>$flat[0],
            'tenement'=>$tenement[0]
             ]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($flat_id) {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $PaidflatList = DB::table('tf_paid_hd')
            ->select(['tf_paid_hd.*'])
            ->join("tenement_flats", "tenement_flats.id", "=","tf_paid_hd.flat_id")
            ->join("tenements", "tenement_flats.tenement_id", "=","tenements.id")
            ->where('tf_paid_hd.id', '<>' , 0)
            ->where('tf_paid_hd.prepaid_flg', '=' , 1)
            ->where('tf_paid_hd.flat_id', '=' , $flat_id)
            ->where('tenements.id', '=', Auth::user()->tenement_id)
            ->orderBy('tf_paid_hd.id', 'desc');

        return Datatables::of($PaidflatList)
                ->addColumn('action', function ($PaidflatList) {
                    return '<a href="../paiddetail/'. $PaidflatList->id .'" class="btn btn-xs btn-primary" target="_blank">Chi Tiết</a>';                    
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
            return redirect('monthlyfee/prepaid/'.$request->flat_id)->withInput()->withErrors($v);
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
                return redirect('monthlyfee/prepaid/'.$request->flat_id)->withInput()->withErrors($v);
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
                'prepaid_flg'  => 1, 
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'updated_at' => date("Y-m-d H:i:s")  
            ]) -> id;

            for($i=0;$i<count($request->counter);$i++){
                //dd($t_elec_type_id);

                $tmpPaymentType = '';
                $tmpMoney = '';
                $tmpComment = '';
                $tmpYear = '';
                $tmpMonth = '';

                $step = $i+1;    
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
                    'prepaid_flg'  =>  1,
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
}