<?php

namespace App\Http\Controllers\Tenement;

use Illuminate\Http\Request;
use Kitano;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TenementParkingTariff;
use App\Models\Area;
use DB;
use File;
use Validator;
use Auth;
use App\LaraBase\NumberUtil;

class ParkingDetailController extends Controller {

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

        //check CustomerId is exist or not.
        $existedTenementParkingTariff = TenementParkingTariff::where('id', $id)->count();

        if($existedTenementParkingTariff == '0'){
            return \Response::view('errors.404', array(), 404);
            //return back()->withInput()->withErrors(['existedCustomer' => 'Customer is not exist ! Please confirm with Administrator. Thank you.']);
        }

        $tenementParking = TenementParkingTariff::where('id',$id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('tenement.parkingdetail', [ 'tenementParking'=>$tenementParking[0] ]);
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

        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            'name.required' => 'Biểu phí gửi xe tháng chưa được nhập.',
            'price.required' => 'Đơn giá chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
                    'price' => 'required'
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {
                //check Tenement Id is exist or not.
                $existedTenementParking = TenementParkingTariff::where('id',$request->id)
                    ->where('activation',1)
                    ->where('tenement_id',$id)
                    ->count();

                if($existedTenementParking == 0){
                    return \Response::view('errors.404', array(), 404);
                }  

                $tenementParking = TenementParkingTariff::find($request->id);
                $tenementParking->name = $request->name;

                $tenementParking->price = $utils->number($request->price);
                
                $tenementParking->comment = $request->comment;
                $tenementParking->save();

                DB::commit();
                return back()->with('tenementParking-alert-success','Tenement is updated !');
            } catch (\Exception $e) {
                DB::rollback();

                //something went wrong
                return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
            }
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
        $tenementParking = TenementParkingTariff::where('activation', 1)->get();
        return view('tenement.parkingcreate', compact('tenementParking'));
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
            'name.required' => 'Biểu phí gửi xe tháng chưa được nhập.',
            'price.required' => 'Đơn giá chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
                    'price' => 'required'
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        }

        TenementParkingTariff::create([
            'name'   =>  $request->input('name'),
            'price'   =>  $utils->number($request->input('price')),
            'comment' => $request->input('comment'),
            'tenement_id' => $id,
            'activation' => 1,
            'parking_code'  => $this->proc_getCode($id, 5)
        ]);

        return redirect()->route('TenementParking');
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
            $tenement_parking_id = $request->id;

            $tenementParking = TenementParkingTariff::find($tenement_parking_id);
            $tenementParking->activation = 0;
            $tenementParking->save();

            DB::commit();
            return redirect()->route('TenementParking')->with('tenementWater-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }   
    }

}
