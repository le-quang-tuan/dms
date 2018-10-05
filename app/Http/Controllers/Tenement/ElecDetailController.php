<?php

namespace App\Http\Controllers\Tenement;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tenement;
use App\Models\User;
use App\Models\TenementElecType;
use App\Models\TenementElecTariff;
use DB;
use File;
use Validator;
use Auth;
use App\LaraBase\NumberUtil;

class ElecDetailController extends Controller {

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

        $tenementElec = TenementElecType::where('id',$id)
                ->where('activation',1)
                ->where('tenement_id',$tenement_id)
                ->get();

        $lsTarrif = TenementElecTariff::where('elec_type_id',$id)
                ->where('activation',1)
                ->get();

        return View('tenement.elecdetail', [ 'tenementElec'=>$tenementElec[0], 'lsTarrif'=> $lsTarrif]);
    }

    private function proc_getCode($tenement_id, $type) {
        $number = DB::select(DB::raw("
            CALL proc_getCode($tenement_id,  $type)
        "));
        //dd($number[0]->oNumber);
        if (count($number) ==0)
            return 1;
        return $number[0]->code;
    }

    /**
     * upload passport photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $utils = new NumberUtil();

        $id = Auth::user()->tenement_id;
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        $messages = [
            'elec_type.required' => 'Biểu phí điện sử dụng chưa được nhập.',
        ];

        $v = Validator::make($request->all(), [
            'elec_type'  =>  'required'
        ], $messages);

        if($v->fails()){
            return redirect('/tenement/elec/' . $request->tenement_elec_id)->withInput()->withErrors($v);        
        }

        // Tarrif validate
        if( isset($request->counter) ){
            $rules = array(
            'name1' => 'required',
            'index_from1' => 'required',
            'price1' => 'required');

            for($i=0;$i<count($request->counter);$i++){
                $tempName = '';
                $tempIndex_from = '';
                $tempPrice = '';

                $step = $i+1;    
                $tempName = 'name'.$step;
                $tempIndex_from = 'index_from'.$step;
                $tempPrice = 'price'.$step;

                $messages[$tempName . '.required'] = 'Định mức (' . $step . ') chưa được nhập.';
                $messages[$tempIndex_from . '.required'] = 'Chỉ số tiêu thụ từ (' . $step . ') chưa được nhập.';
                $messages[$tempPrice . '.required'] = 'Đơn giá (' . $step . ') chưa được nhập.';

                $rules[$tempName] = 'required';
                if($request->input($tempName) != ''){
                    $rules[$tempIndex_from] = 'required';
                    $rules[$tempPrice] = 'required';
                }
            }
            $v = Validator::make($request->all(), $rules, $messages);

            if($v->fails()){
                return redirect('/tenement/elec/' . $request->tenement_elec_id)->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {
            $tenement_elec_id = $request->tenement_elec_id;

            $tenementElec = TenementElecType::find($tenement_elec_id);
            $tenementElec->elec_type = $request->elec_type;
            $tenementElec->comment = $request->comment;
            $tenementElec->save();

            TenementElecTariff::where('elec_type_id', $tenement_elec_id)->update(['activation' => 0]);

            //dd(count($request->counter));
            for($i = 0; $i < count($request->counter); $i++){
                //dd($t_elec_type_id);

                $tmpName = '';
                $tmpIndex_from = '';
                $tmpPrice = '';
                $tmpOther_fee01 = '';
                $tmpOther_fee02 = '';
                $tmpVat = '';
                $deleteid = '';

                $step = $i + 1;    
                $tmpName = 'name'.$step;
                $tmpIndex_from = 'index_from'.$step;
                $tmpPrice = 'price'.$step;
                $tmpOther_fee01 = 'other_fee01'.$step;
                $tmpOther_fee02 = 'other_fee02'.$step;
                $tmpVat = 'vat'.$step;
                $deleteid = 'deleteid'.$step;

                if (!isset($request->$deleteid)){
                    TenementElecTariff::create([
                        'elec_type_id'  => $tenement_elec_id,
                        'activation'    => 1,
                        'name'          => $request->input($tmpName),
                        'index_from'    => $utils->number($request->input($tmpIndex_from)),
                        'price'         => $utils->number($request->input($tmpPrice)),
                        'other_fee01'   => $utils->number($request->input($tmpOther_fee01)),
                        'other_fee02'   => $utils->number($request->input($tmpOther_fee02)),
                        'vat'           => $utils->number($request->input($tmpVat))
                    ]);
                }
            }
            DB::commit();
            return back()->with('tenementElec-alert-success','Biểu phí đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }

    public function create()
    {
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $tenement_elec_types = TenementElecType::where('activation', 1)->get();

        $tenement = Tenement::where('id', $tenement_id)->where('activation',1)->get()[0];
        $userDetailInfo = User::where('id', Auth::user()->id)->get()[0];

        return view('tenement.eleccreate', compact('tenement_elec_types','tenement', 'userDetailInfo' ));
    }

    public function store(Request $request)
    {
        $utils = new NumberUtil();
        $id = Auth::user()->tenement_id;
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        $messages = [
            'name.required' => 'Biểu phí điện sử dụng chưa được nhập.',
        ];

        $v = Validator::make($request->all(), [
            'name'  =>  'required'
        ], $messages);
        
        if($v->fails()){
            return back()->withInput()->withErrors($v);
            //return redirect()->route('TenementElec.create')->withInput()->withErrors($v);
        }
        $request->session()->put('counter', count($request->counter));

        // Tarrif validate
        if( isset($request->counter) ){
            $rules = array(
            'name1' => 'required',
            'index_from1' => 'required',
            'price1' => 'required');

            $items = array();

            for($i=0;$i<count($request->counter);$i++){
                $tempName = '';
                $tempIndex_from = '';
                $tempPrice = '';

                $step = $i+1;    
                $tempName = 'name'.$step;
                $tempIndex_from = 'index_from'.$step;
                $tempPrice = 'price'.$step;

                $messages[$tempName . '.required'] = 'Định mức (' . $step . ') chưa được nhập.';
                $messages[$tempIndex_from . '.required'] = 'Chỉ số tiêu thụ từ (' . $step . ') chưa được nhập.';
                $messages[$tempPrice . '.required'] = 'Đơn giá (' . $step . ') chưa được nhập.';

                $rules[$tempName] = 'required';
                if($request->input($tempName) != ''){
                    $rules[$tempIndex_from] = 'required';
                    $rules[$tempPrice] = 'required';
                }

                if (in_array($request->input($tempIndex_from),$items))
                {
                    return redirect()->route('TenementElec.create')->withInput()->withErrors('Không được nhập các biểu phí trùng');
                }
                else {
                    array_push($items, $request->input($tempIndex_from));
                }

            }
            $v = Validator::make($request->all(), $rules, $messages);

            if($v->fails()){
                return back()->withInput()->withErrors($v);
                //return redirect()->route('TenementElec.create')->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {
            $t_elec_type_id = TenementElecType::create([
                'elec_type'   =>  $request->input('name'),
                'tenement_id'   =>  $id,
                'comment'  =>  $request->input('comment'),
                'activation'  =>  1,
                'elec_code'  => $this->proc_getCode($id, 2)
            ]) -> id;

            for($i=0;$i<count($request->counter);$i++){
                $tmpName = '';
                $tmpIndex_from = '';
                $tmpPrice = '';
                $tmpOther_fee01 = '';
                $tmpOther_fee02 = '';
                $tmpVat = '';

                $step = $i+1;    
                $tmpName = 'name'.$step;
                $tmpIndex_from = 'index_from'.$step;
                $tmpPrice = 'price'.$step;
                $tmpOther_fee01 = 'other_fee01'.$step;
                $tmpOther_fee02 = 'other_fee02'.$step;
                $tmpVat = 'vat'.$step;

                TenementElecTariff::create([
                    'elec_type_id'  => $t_elec_type_id,
                    'activation'    => 1,
                    'name'          => $request->input($tmpName),
                    'index_from'    => $utils->number($request->input($tmpIndex_from)),
                    'price'         => $utils->number($request->input($tmpPrice)),
                    'other_fee01'   => $utils->number($request->input($tmpOther_fee01)),
                    'other_fee02'   => $utils->number($request->input($tmpOther_fee02)),
                    'vat'           => $utils->number($request->input($tmpVat))
                ]);
            }
            DB::commit();
            $request->session()->forget('counter');

            return redirect()->route('TenementElec');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request) {
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        DB::beginTransaction();
        try {
            $tenement_elec_id = $request->tenement_elec_id;

            $tenementElec = TenementElecType::find($tenement_elec_id);
            $tenementElec->activation = 0;
            $tenementElec->save();

            DB::commit();
            return redirect()->route('TenementElec')->with('tenementElec-alert-success','Định mức đã được xóa !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }

}
