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

class PaymentDetailFlatController extends Controller {

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

        $tf_paid_hd = DB::table("tf_paid_hd")
        ->where('activation', '=', 1)
        ->where('id', '=', $id)
        ->orderBy('id')->get();

        $tf_paid_dt = DB::table("tf_paid_dt")
        ->where('activation', '=', 1)
        ->where('paid_id', '=', $id)
        ->orderBy('id')->get();

        $flat_info = DB::table("tenement_flats")
        ->join('tf_paid_hd', 'tenement_flats.id', '=', 'tf_paid_hd.flat_id')
        ->select('tenement_flats.*')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tf_paid_hd.id', '=', $id)
        ->orderBy('id')->get();

        return View('monthlyfee.paymentdetailflat', [ 
            'flat_info'=> $flat_info[0],
            'tf_paid_hd'=>$tf_paid_hd[0],
            'tf_paid_dt'=>$tf_paid_dt,
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

        $PaidDetailflatList = DB::table('tf_paid_dt')
            ->select(['tf_paid_dt.*', 'mst_payment_types.name'])
            ->join("mst_payment_types", "mst_payment_types.id", "=","tf_paid_dt.payment_type")
            ->where('tf_paid_dt.id', '<>' , 0)
            ->where('tf_paid_dt.activation', '=' , 1)
            ->where('tf_paid_dt.paid_id', '=' , $id)
            ->orderBy('tf_paid_dt.id', 'desc');

        return Datatables::of($PaidDetailflatList)
                ->addColumn('action', function ($PaidDetailflatList) {
                    return '<button type="button" class="btn btn-primary btn-details" value="'. $PaidDetailflatList->id .'" >Hủy</button>';
                })
                ->make(true);        
    }        

    public function store(Request $request)
    {
        $tenement_id = Auth::user()->tenement_id;
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();

            $data = Excel::load($path, function($reader) {

            })->get();

            if(!empty($data) && $data->count()){

                foreach ($data as $key => $value) {

                    $insert[] = [
                        //'flat_id' => $value->id, 
                        'flat_code' =>  $value->ten_khu . 
                                        $value->khu_so . "-" .
                                        $value->tang_so . "-" .
                                        $value->can_ho_so,

                        'block_name' => $value->ten_khu,
                        'block_num' => $value->khu_so,
                        'floor' => $value->tang_so,
                        'flat_num' => $value->can_ho_so,

                        'name' => $value->chu_ho,
                        'phone' => $value->dien_thoai,
                        'area' => $value->dien_tich_can_ho,
                        'persons' => $value->so_nhan_khau,
                        'comment' => $value->ghi_chu,
                        'receive_date' => $value->ngay_nhan,
                        'exist_flg' => $value->dang_o,

                        'elec_type_id' => $value->bieu_phi_dien,
                        'water_type_id' => $value->bieu_phi_nuoc,
                        'gas_type_id' => $value->bieu_phi_gas,

                        'token' => $request->_token,
                        'tenement_id' => $tenement_id,
                        'created_at' => (new \DateTime()),
                        'updated_at' => (new \DateTime()),
                        'activation' => 1
                        ];
                }
                // dd($insert);
                if(!empty($insert)){

                    $old_data = PaidflatList::where('tenement_id', $tenement_id)
                        ->where('token', Session::token())
                        ->delete();

                    DB::table('tenement_flats_import')->insert($insert);

                    return redirect('import/importFlat');
                }
            }
        }
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

    public function destroy($id)
    {
        $tenement_id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        DB::beginTransaction();
        try {
            $TfPaidDt = DB::table("tenement_flats")
            ->join('tf_paid_hd', 'tenement_flats.id', '=', 'tf_paid_hd.flat_id')
            ->join('tf_paid_dt', 'tf_paid_dt.paid_id', '=', 'tf_paid_hd.id')
            ->select('tf_paid_hd.id')
            ->where('tenement_flats.activation', '=', 1)
            ->where('tenement_flats.tenement_id', '=', $tenement_id)
            ->where('tf_paid_dt.id', '=', $id)->count();

            if($TfPaidDt == 0){
                return Response::view('errors.404', array(), 404);
            }  
            $Used = TfPaidDt::find($id);

            $Used->activation = 0;
            $Used->save();

            DB::commit();

            $TfPaidDt = DB::table("tenement_flats")
            ->join('tf_paid_hd', 'tenement_flats.id', '=', 'tf_paid_hd.flat_id')
            ->join('tf_paid_dt', 'tf_paid_dt.paid_id', '=', 'tf_paid_hd.id')
            ->select('tenement_flats.id', 'tf_paid_dt.year_month')
            ->where('tenement_flats.activation', '=', 1)
            ->where('tenement_flats.tenement_id', '=', $tenement_id)
            ->where('tf_paid_dt.id', '=', $id)->get();

            DB::statement("
                CALL proc_payment_cancel_fee('". $tenement_id . "', '" . $TfPaidDt[0]->id . "', '". $TfPaidDt[0]->year_month ."')
            ");
            return ("ok");
        } catch (Exception $e) {
            dd($e);
            DB::rollback();
            return $e;
        }
    }
}