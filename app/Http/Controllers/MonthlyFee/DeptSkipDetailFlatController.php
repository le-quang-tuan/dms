<?php

namespace App\Http\Controllers\MonthlyFee;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TfDeptSkipDt;
use App\Models\TfDeptSkipHd;
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

class DeptSkipDetailFlatController extends Controller {

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

        $tf_dept_skip_hd = DB::table("tf_dept_skip_hd")
        ->where('activation', '=', 1)
        ->where('id', '=', $id)
        ->orderBy('id')->get();

        $tf_dept_skip_dt = DB::table("tf_dept_skip_dt")
        ->where('activation', '=', 1)
        ->where('dept_skip_id', '=', $id)
        ->orderBy('id')->get();

        $flat_info = DB::table("tenement_flats")
        ->join('tf_dept_skip_hd', 'tenement_flats.id', '=', 'tf_dept_skip_hd.flat_id')
        ->select('tenement_flats.*')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tf_dept_skip_hd.id', '=', $id)
        ->orderBy('id')->get();

        return View('monthlyfee.deptskipdetailflat', [ 
            'flat_info'=> $flat_info[0],
            'tf_dept_skip_hd'=>$tf_dept_skip_hd[0],
            'tf_dept_skip_dt'=>$tf_dept_skip_dt,
            'id'=>$id
             ]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($id) {    
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $PaidDetailflatList = DB::table('tf_dept_skip_dt')
            ->select(['tf_dept_skip_dt.*', 'mst_payment_types.name'])
            ->join("mst_payment_types", "mst_payment_types.id", "=","tf_dept_skip_dt.payment_type")
            ->where('tf_dept_skip_dt.id', '<>' , 0)
            ->where('tf_dept_skip_dt.activation', '=' , 1)
            ->where('tf_dept_skip_dt.dept_skip_id', '=' , $id)
            ->orderBy('tf_dept_skip_dt.id', 'desc');

        return Datatables::of($PaidDetailflatList)
                ->addColumn('action', function ($PaidDetailflatList) {
                    return '<button type="button" class="btn btn-primary btn-details" value="'. $PaidDetailflatList->id .'" >Hủy</button>';
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
            $tf_dept_skip_hd_id = TfDeptSkipHd::create([
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

                TfDeptSkipDt::create([
                    'dept_skip_id'  => $tf_dept_skip_hd_id,
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

    public function destroy($id)
    {
        $tenement_id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        DB::beginTransaction();
        try {
            $TfDeptSkipDt = DB::table("tenement_flats")
            ->join('tf_dept_skip_hd', 'tenement_flats.id', '=', 'tf_dept_skip_hd.flat_id')
            ->join('tf_dept_skip_dt', 'tf_dept_skip_dt.dept_skip_id', '=', 'tf_dept_skip_hd.id')
            ->select('tf_dept_skip_hd.id')
            ->where('tenement_flats.activation', '=', 1)
            ->where('tenement_flats.tenement_id', '=', $tenement_id)
            ->where('tf_dept_skip_dt.id', '=', $id)->count();

            if($TfDeptSkipDt == 0){
                return Response::view('errors.404', array(), 404);
            }  
            $Used = TfDeptSkipDt::find($id);

            $Used->activation = 0;
            $Used->save();

            DB::commit();

            $TfDeptSkipDt = DB::table("tenement_flats")
            ->join('tf_dept_skip_hd', 'tenement_flats.id', '=', 'tf_dept_skip_hd.flat_id')
            ->join('tf_dept_skip_dt', 'tf_dept_skip_dt.dept_skip_id', '=', 'tf_dept_skip_hd.id')
            ->select('tenement_flats.id', 'tf_dept_skip_dt.year_month')
            ->where('tenement_flats.activation', '=', 1)
            ->where('tenement_flats.tenement_id', '=', $tenement_id)
            ->where('tf_dept_skip_dt.id', '=', $id)->get();

            DB::statement("
                CALL proc_payment_cancel_fee('". $tenement_id . "', '" . $TfDeptSkipDt[0]->id . "', '". $TfDeptSkipDt[0]->year_month ."')
            ");
            return ("ok");
        } catch (Exception $e) {
            dd($e);
            DB::rollback();
            return $e;
        }
    }
}