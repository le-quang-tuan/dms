<?php

namespace App\Http\Controllers\Tenement;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TenementWaterType;
use App\Models\TenementWaterTariff;
use DB;
use File;
use Validator;
use Auth;
use Illuminate\Support\Facades\View;
use App\LaraBase\NumberUtil;

class WaterDetailController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenementWater = TenementWaterType::where('id',$id)
                ->where('activation',1)
                ->where('tenement_id',$tenement_id)
                ->get();

        $lsTarrif = TenementWaterTariff::where('water_type_id',$id)
                ->where('activation',1)
                ->get();

        return View('tenement.waterdetail', [ 'tenementWater'=>$tenementWater[0], 'lsTarrif'=> $lsTarrif ]);
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
        //dd($request);
        $utils = new NumberUtil();

        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            'water_type.required' => 'Biểu phí điện sử dụng chưa được nhập.',
        ];

        $v = Validator::make($request->all(), [
            'water_type'  =>  'required'
        ], $messages);

        if($v->fails()){
            return redirect('/tenement/water/' . $request->tenement_water_id)->withInput()->withErrors($v);        
            //return redirect()->route('TenementWater.update')->withInput()->withErrors($v);
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
                return redirect('/tenement/water/' . $request->tenement_water_id)->withInput()->withErrors($v);
                //return redirect()->route('TenementWater.update')->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {
            $tenement_water_id = $request->tenement_water_id;

            $tenementWater = TenementWaterType::find($tenement_water_id);
            $tenementWater->water_type = $request->water_type;
            $tenementWater->comment = $request->comment;
            $tenementWater->save();

            TenementWaterTariff::where('water_type_id', $tenement_water_id)->update(['activation' => 0]);

            for($i = 0; $i < count($request->counter); $i++){
                $tmpName = '';
                $tmpIndex_from = '';
                $tmpPrice = '';

                $tmpOther_fee01 = '';
                $tmpOther_fee02 = '';
                $tmpVat = '';

                $tmpOther_fee01_price = '';
                $tmpOther_fee02_price = '';
                $tmpVat_price = '';

                
                $deleteid = '';

                $step = $i + 1;    
                $tmpName = 'name'.$step;
                $tmpIndex_from = 'index_from'.$step;
                $tmpPrice = 'price'.$step;

                $tmpOther_fee01 = 'other_fee01'.$step;
                $tmpOther_fee02 = 'other_fee02'.$step;
                $tmpVat = 'vat'.$step;

                $tmpOther_fee01_price = 'other_fee01_price'.$step;
                $tmpOther_fee02_price = 'other_fee02_price'.$step;
                $tmpVat_price = 'vat_price'.$step;
                
                $deleteid = 'deleteid'.$step;
                if (!isset($request->$deleteid)){
                    TenementWaterTariff::create([
                        'water_type_id'  => $tenement_water_id,
                        'activation'    => 1,
                        'name'          => $request->input($tmpName),
                        'index_from'    => $utils->number($request->input($tmpIndex_from)),
                        'price'         => $utils->number($request->input($tmpPrice)),
                        'other_fee01'   => $utils->number($request->input($tmpOther_fee01)),
                        'other_fee02'   => $utils->number($request->input($tmpOther_fee02)),
                        'vat'           => $utils->number($request->input($tmpVat)),

                        'other_fee01_price'   => $utils->number($request->input($tmpOther_fee01_price)),
                        'other_fee02_price'   => $utils->number($request->input($tmpOther_fee02_price)),
                        'vat_price'           => $utils->number($request->input($tmpVat_price))
                    ]);
                }
            }
            DB::commit();
            return back()->with('tenementWater-alert-success','Biểu phí đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }

    public function create()
    {
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $tenement_water_types = TenementWaterType::where('activation', 1)->get();
        return view('tenement.watercreate', compact('tenement_water_types'));
    }

    public function store(Request $request)
    {
        $utils = new NumberUtil();

        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            'name.required' => 'Biểu phí nước sử dụng chưa được nhập.',
        ];

        $v = Validator::make($request->all(), [
            'name'  =>  'required'
        ], $messages);

        if($v->fails()){
            return back()->withInput()->withErrors($v);
            //return redirect()->route('TenementWater.create')->withInput()->withErrors($v);
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
                    return redirect()->route('TenementWater.create')->withInput()->withErrors('Không được nhập các biểu phí trùng');
                }
                else {
                    array_push($items, $request->input($tempIndex_from));
                }
            }
            $v = Validator::make($request->all(), $rules, $messages);

            if($v->fails()){
                return back()->withInput()->withErrors($v);
                //return redirect()->route('TenementWater.create')->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {
            $t_water_type_id = TenementWaterType::create([
                'water_type'   =>  $request->input('name'),
                'tenement_id'   =>  $id,
                'comment'  =>  $request->input('comment'),
                'activation'  =>  1,
                'water_code'  => $this->proc_getCode($id, 3)
            ]) -> id;

            for($i=0;$i<count($request->counter);$i++){
                //dd($t_water_type_id);

                $tmpName = '';
                $tmpIndex_from = '';
                $tmpPrice = '';

                $tmpOther_fee01 = '';
                $tmpOther_fee02 = '';
                $tmpVat = '';

                $tmpOther_fee01_price = '';
                $tmpOther_fee02_price = '';
                $tmpVat_price = '';

                $step = $i+1;    
                $tmpName = 'name'.$step;
                $tmpIndex_from = 'index_from'.$step;
                $tmpPrice = 'price'.$step;

                $tmpOther_fee01 = 'other_fee01'.$step;
                $tmpOther_fee02 = 'other_fee02'.$step;
                $tmpVat = 'vat'.$step;

                $tmpOther_fee01_price = 'other_fee01_price'.$step;
                $tmpOther_fee02_price = 'other_fee02_price'.$step;
                $tmpVat_price = 'vat_price'.$step;

                TenementWaterTariff::create([
                    'water_type_id'  => $t_water_type_id,
                    'activation'    => 1,
                    'name'          => $request->input($tmpName),
                    'index_from'    => $utils->number($request->input($tmpIndex_from)),
                    'price'         => $utils->number($request->input($tmpPrice)),
                    'other_fee01'   => $utils->number($request->input($tmpOther_fee01)),
                    'other_fee02'   => $utils->number($request->input($tmpOther_fee02)),
                    'vat' => $utils->number($request->input($tmpVat)),
                    'other_fee01_price'   => $utils->number($request->input($tmpOther_fee01_price)),
                    'other_fee02_price'   => $utils->number($request->input($tmpOther_fee02_price)),
                    'vat_price' => $utils->number($request->input($tmpVat_price))
                ]);
            }
            DB::commit();
            $request->session()->forget('counter');
            
            return redirect()->route('TenementWater');
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
            $tenement_water_id = $request->tenement_water_id;

            $tenementWater = TenementWaterType::find($tenement_water_id);
            $tenementWater->activation = 0;
            $tenementWater->save();

            DB::commit();
            return redirect()->route('TenementWater')->with('tenementWater-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }

}
