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

class PaymonthFlatController extends Controller {

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


        $flat = DB::table("tenement_flats")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('tenement_id', '=', $tenement_id)
        ->where('id', '=', $flat_id)
        ->orderBy('flat_code')->get();

        $tenement = Tenement::where('id',$tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('monthlyfee.paymonthflat', [ 
            'flat_id'=>$flat_id,
            'flat_info'=>$flat[0]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new_index($flat_id) {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $mst_payment_types = DB::table("mst_payment_types")
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tf_payment_all_months = DB::table("tf_payment_all_months")
        ->where('activation', '=', 1)
        ->select(
            'year_month',
            DB::raw('ifnull(manager_fee,0) - ifnull(manager_fee_paid,0) - ifnull(manager_fee_skip,0) as mFee'), 
            DB::raw('ifnull(elec_fee,0) - ifnull(elec_fee_paid,0) - ifnull(elec_fee_skip,0) as eFee'),
            DB::raw('ifnull(water_fee,0) - ifnull(water_fee_paid,0)- ifnull(water_fee_skip,0)as wFee'),
            DB::raw('ifnull(gas_fee,0) - ifnull(gas_fee_paid,0)- ifnull(gas_fee_skip,0)as gFee'),
            DB::raw('ifnull(parking_fee,0) - ifnull(parking_fee_paid,0)- ifnull(parking_fee_skip,0)as pFee'),
            DB::raw('ifnull(service_fee,0) - ifnull(service_fee_paid,0)- ifnull(service_fee_skip,0)as sFee'))
        ->where('flat_id', '=', $flat_id)
        ->where('tenement_id', '=', $tenement_id);

        $depts = $tf_payment_all_months->where(function($tf_payment_all_months){
            return $tf_payment_all_months->where(DB::raw('ifnull(manager_fee,0)'), '<>', DB::raw('ifnull(manager_fee_paid,0)'))
                ->orwhere(DB::raw('ifnull(elec_fee,0)'), '<>', DB::raw('ifnull(elec_fee_paid,0)'))
                ->orwhere(DB::raw('ifnull(water_fee,0)'), '<>', DB::raw('ifnull(water_fee_paid,0)'))
                ->orwhere(DB::raw('ifnull(gas_fee,0)'), '<>', DB::raw('ifnull(gas_fee_paid,0)'))
                ->orwhere(DB::raw('ifnull(parking_fee,0)'), '<>', DB::raw('ifnull(parking_fee_paid,0)'))
                ->orwhere(DB::raw('ifnull(service_fee,0)'), '<>', DB::raw('ifnull(service_fee_paid,0)'))
                ->orderBy('id');
        })->get();

        $deptMonthly = array();
        foreach ($depts as $dept){
            if ($dept->mFee > 0)
                array_push($deptMonthly, [$dept->year_month, $dept->mFee,"1"]);
            if ($dept->eFee > 0)
                array_push($deptMonthly, [$dept->year_month, $dept->eFee,"2"]);
            if ($dept->wFee > 0)
                array_push($deptMonthly, [$dept->year_month, $dept->wFee,"3"]);
            if ($dept->gFee > 0)
                array_push($deptMonthly, [$dept->year_month, $dept->gFee,"4"]);
            if ($dept->pFee > 0)
                array_push($deptMonthly, [$dept->year_month, $dept->pFee,"5"]);
            if ($dept->sFee > 0)
                array_push($deptMonthly, [$dept->year_month, $dept->sFee,"6"]);
        }

        $flat = DB::table("tenement_flats")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('tenement_id', '=', $tenement_id)
        ->where('id', '=', $flat_id)
        ->orderBy('flat_code')->get();

        $tenement = Tenement::where('id',$tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('monthlyfee.paidnew', [ 
            'mst_payment_types'=>$mst_payment_types,
            'flat_id'=>$flat_id,
            'flat_info'=>$flat[0],
            'dept'=>$deptMonthly,
            'tenement'=>$tenement[0]
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function status($flat_id) {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $mst_payment_types = DB::table("mst_payment_types")
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tf_payment_all_months = DB::table("tf_payment_all_months")
        ->where('activation', '=', 1)
        ->select(
            'year_month',
            DB::raw('ifnull(manager_fee,0) - ifnull(manager_fee_paid,0) - ifnull(manager_fee_skip,0) as mFeeDept'), 
            DB::raw('ifnull(elec_fee,0) - ifnull(elec_fee_paid,0) - ifnull(elec_fee_skip,0) as eFeeDept'),
            DB::raw('ifnull(water_fee,0) - ifnull(water_fee_paid,0) - ifnull(water_fee_skip,0) as wFeeDept'),
            DB::raw('ifnull(gas_fee,0) - ifnull(gas_fee_paid,0) - ifnull(gas_fee_skip,0) as gFeeDept'),
            DB::raw('ifnull(parking_fee,0) - ifnull(parking_fee_paid,0) - ifnull(parking_fee_skip,0)as pFeeDept'),
            DB::raw('ifnull(service_fee,0) - ifnull(service_fee_paid,0) - ifnull(service_fee_skip,0)as sFeeDept'),
            DB::raw('ifnull(manager_fee,0) as mFee'),
            DB::raw('ifnull(manager_fee_paid,0) as mFeePaid'), 
            DB::raw('ifnull(elec_fee,0) as eFee'),
            DB::raw('ifnull(elec_fee_paid,0) as eFeePaid'),
            DB::raw('ifnull(water_fee,0) as wFee'),
            DB::raw('ifnull(water_fee_paid,0) as wFeePaid'),
            DB::raw('ifnull(gas_fee,0) as gFee'),
            DB::raw('ifnull(gas_fee_paid,0) as gFeePaid'),
            DB::raw('ifnull(parking_fee,0) as pFee'),
            DB::raw('ifnull(parking_fee_paid,0) as pFeePaid'),
            DB::raw('ifnull(service_fee,0) as sFee'),
            DB::raw('ifnull(service_fee_paid,0) as sFeePaid'),
            DB::raw('ifnull(manager_fee_skip,0) as mFeeSkip'),
            DB::raw('ifnull(elec_fee_skip,0) as eFeeSkip'),
            DB::raw('ifnull(water_fee_skip,0) as wFeeSkip'),
            DB::raw('ifnull(gas_fee_skip,0) as gFeeSkip'),
            DB::raw('ifnull(parking_fee_skip,0) as pFeeSkip'),
            DB::raw('ifnull(service_fee_skip,0) as sFeeSkip')
            )
        ->where('flat_id', '=', $flat_id)
        ->where('tenement_id', '=', $tenement_id);

        $depts = $tf_payment_all_months->get();
// dd($depts);
        $deptMonthly = array();
        foreach ($depts as $dept){
            if ($dept->mFeeDept != 0 || $dept->mFee != 0|| $dept->mFeePaid != 0)
                array_push($deptMonthly, [$dept->year_month, $dept->mFeeDept, $dept->mFee, $dept->mFeePaid,"1",$dept->mFeeSkip]);
            if ($dept->eFeeDept != 0 || $dept->eFee != 0 || $dept->eFeePaid != 0)
                array_push($deptMonthly, [$dept->year_month, $dept->eFeeDept, $dept->eFee, $dept->eFeePaid,"2",$dept->eFeeSkip]);
            if ($dept->wFeeDept != 0 || $dept->wFee != 0 || $dept->wFeePaid != 0)
                array_push($deptMonthly, [$dept->year_month, $dept->wFeeDept, $dept->wFee, $dept->wFeePaid,"3",$dept->wFeeSkip]);
            if ($dept->gFeeDept != 0 || $dept->gFee != 0|| $dept->gFeePaid != 0)
                array_push($deptMonthly, [$dept->year_month, $dept->gFeeDept, $dept->gFee, $dept->gFeePaid,"4",$dept->gFeeSkip]);
            if ($dept->pFeeDept != 0 || $dept->pFee != 0 || $dept->pFeePaid != 0)
                array_push($deptMonthly, [$dept->year_month, $dept->pFeeDept, $dept->pFee, $dept->pFeePaid,"5",$dept->pFeeSkip]);
            if ($dept->sFeeDept != 0 || $dept->sFee != 0 || $dept->sFeePaid != 0)
                array_push($deptMonthly, [$dept->year_month, $dept->sFeeDept, $dept->sFee, $dept->sFeePaid,"6",$dept->sFeeSkip]);
        }
        $flat = DB::table("tenement_flats")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('tenement_id', '=', $tenement_id)
        ->where('id', '=', $flat_id)
        ->orderBy('flat_code')->get();

        $tenement = Tenement::where('id',$tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('monthlyfee.statusflat', [ 
            'mst_payment_types'=>$mst_payment_types,
            'flat_id'=>$flat_id,
            'flat_info'=>$flat[0],
            'dept'=>$deptMonthly,
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
            ->where('tf_paid_hd.flat_id', '=' , $flat_id)
            ->where('tenements.id', '=', Auth::user()->tenement_id)
            /////////////////
            ->where(function($query)
            {
                $user = Auth::user();
                if ($user->is('accountant_mem')){
                    $query->where('tf_paid_hd.updated_by', '=',Auth::user()->id);
                }
                else if ($user->is('admin') || 
                         $user->is('manager')  ||
                         $user->is('moderator')  ||
                         $user->is('accountant')
                         ) {
                    $query->where(DB::RAW('1'), '=', DB::RAW('1'));
                }
                else {
                    $query->where(DB::RAW('0'), '=', DB::RAW('1'));                    
                }
            })
            //////////////////
            ->orderBy('tf_paid_hd.id', 'desc');

        return Datatables::of($PaidflatList)
                ->addColumn('action', function ($PaidflatList) {
                    return '<a href="../paiddetail/'. $PaidflatList->id .'" class="btn btn-xs btn-primary" target="_blank">Chi Tiết</a>';                    
                })
                ->addColumn('paidbill', function ($PaidflatList) {
                    return '<a href="../../report/paidbill/'. $PaidflatList->id .'/2" class="btn btn-xs btn-primary" target="_blank">Tải Phiếu Thu</a>';                    
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
            return redirect('monthlyfee/paymonth/'.$request->flat_id)->withInput()->withErrors($v);
        }
        // Tarrif validate
        if( isset($request->counter) ){
            $rules = array(
                'money' => 'required');

            for($i=0;$i<count($request->counter);$i++){
                $tempMoney = '';

                $step = $request->counter[$i];    
                $tempName = 'money'.$step;

                $rules[$tempName] = 'required';
            }
            $v = Validator::make($request->all(), $rules);

            if($v->fails()){
                return redirect('monthlyfee/paymonth/'.$request->flat_id)->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {

            $receive_date = '';

            if ($request->receive_date != ''){
               $receive_date = DateTime::createFromFormat('d/m/Y', $request->receive_date);
               $receive_date = $receive_date->format('Ymd');
            }
            
            $tf_paid_hd_id = TfPaidHd::create([
                'flat_id'   =>  $request->input('flat_id'),
                'receiver'   =>  $request->input('receiver'),
                'receive_date'  =>  $receive_date,
                'receive_from'  =>  $request->input('receive_from'),
                'book_bill'  =>  $request->input('book_bill'),
                'bill_no'  =>  $request->input('bill_no'),
                'year_month'  =>  $request->input('year') . $request->input('month'),
                'money'  =>  $utils->number($request->input('money')),
                'paid_type'  =>  1,
                'activation'  =>  1,
                'paymonth_flg'  =>  1,
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

            return back()->with('tenement-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  
    }
}