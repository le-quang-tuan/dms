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

class DeptSkipFlatController extends Controller {

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

        return View('monthlyfee.deptskipflat', [ 
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

        return View('monthlyfee.deptskipnew', [ 
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

        $DeptSkipflatList = DB::table('tf_dept_skip_hd')
            ->select(['tf_dept_skip_hd.*'])
            ->join("tenement_flats", "tenement_flats.id", "=","tf_dept_skip_hd.flat_id")
            ->join("tenements", "tenement_flats.tenement_id", "=","tenements.id")
            ->where('tf_dept_skip_hd.id', '<>' , 0)
            ->where('tf_dept_skip_hd.flat_id', '=' , $flat_id)
            ->where('tenements.id', '=', Auth::user()->tenement_id)
            ->orderBy('tf_dept_skip_hd.id', 'desc');
        return Datatables::of($DeptSkipflatList)
                ->addColumn('action', function ($DeptSkipflatList) {
                    return '<a href="../deptskipdetail/'. $DeptSkipflatList->id .'" class="btn btn-xs btn-primary" target="_blank">Chi Tiết</a>';                    
                })
                ->addColumn('paidbill', function ($DeptSkipflatList) {
                    return '<a href="../../report/paidbill/'. $DeptSkipflatList->id .'/2" class="btn btn-xs btn-primary" target="_blank">Tải Phiếu Thu</a>';                    
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
                    'skip_date' => 'required',
                    'skip_from' => 'required',
                    'comment' => 'required',
        ]);

        if ($v->fails()) {
            return redirect('monthlyfee/deptskip/'.$request->flat_id)->withInput()->withErrors($v);
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
                return redirect('monthlyfee/deptskip/'.$request->flat_id)->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {

            $skip_date = '';

            if ($request->skip_date != ''){
               $skip_date = DateTime::createFromFormat('d/m/Y', $request->skip_date);
               $skip_date = $skip_date->format('Ymd');
            }
            
            $tf_dept_skip_hd_id = TfDeptSkipHd::create([
                'flat_id'   =>  $request->input('flat_id'),
                'skip_date'  =>  $skip_date,
                'skip_from'  =>  $request->input('skip_from'),
                'money'  =>  $utils->number($request->input('money')),
                'comment'  =>  $request->input('comment'),
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

                TfDeptSkipDt::create([
                    'dept_skip_id'  => $tf_dept_skip_hd_id,
                    'activation'    => 1,
                    'comment'          => $request->input($tmpComment),
                    'year_month'          => $request->input($tmpYear) . $request->input($tmpMonth),
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
                CALL proc_dept_skip_money('". $id . "', '" . $request->input('flat_id') . "',null)
            ");

            return back()->with('tenement-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  
    }
}