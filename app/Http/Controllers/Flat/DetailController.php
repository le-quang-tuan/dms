<?php

namespace App\Http\Controllers\Flat;

use Illuminate\Http\Request;
use Kitano;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TenementFlat;
use App\Models\Area;
use DB;
use File;
use Validator;
use Auth;
use DateTime;

class DetailController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        $tenement_id = Auth::user()->tenement_id;

        // dd(Auth::user()->hasRole('admin'));
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/");
        }

        //check CustomerId is exist or not.
        $existedTenementFlat = TenementFlat::where('id', $id)->count();

        if($existedTenementFlat == '0'){
            return \Response::view('errors.404', array(), 404);
        }

        $tenementFlat = TenementFlat::where('id',$id)
            ->where('activation',1)
            ->where('tenement_id',$tenement_id)
            ->orderBy('name', 'asc')->get();

        $elec_tariffs = DB::table("tenement_elec_types")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $water_tariffs = DB::table("tenement_water_types")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $gas_tariffs = DB::table("tenement_gas_types")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $lsChar = range('A', 'Z');
        $lsNum = range('00', '99');
        $lsBlockSub = range('1', '20');

        return View('flat.flatdetail', [ 
            'tenementFlat'=>$tenementFlat[0],
            'lsChar'=>$lsChar,
            'lsNum'=>$lsNum,
            'lsBlockSub'=>$lsBlockSub,
            'gas_tariffs'=>$gas_tariffs,
            'water_tariffs'=>$water_tariffs,
            'elec_tariffs'=>$elec_tariffs,
             ]);
    }

    /**
     * upload passport photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        //Check period user
        $tenement_id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $messages = [
            'address.required' => 'Căn Hộ: chưa được nhập.',
            'name.required' => 'Chủ Hộ: chưa được nhập.',
            'area.required' => 'Diện Tích: chưa được nhập.',
            //'persons.required' => 'Số Nhân Khẩu: chưa được nhập.',
            //'receive_date.required' => 'Ngày Nhận Căn Hộ: chưa được nhập.',
            //'phone.required' => 'Điện Thoại: chưa được nhập.',
            'flat_num.required' => 'Số: chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
                    'address' => 'required',
                    'area' => 'required',
                    // 'receive_date' => 'required',
                    // 'phone' => 'required',
                    // 'persons' => 'required',
                    'flat_num' => 'required',
        ], $messages);

        if ($v->fails()) {
            // If validation falis redirect back to login.
            return redirect('flat/detail/'.$request->id)->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {  
                //check Tenement Id is exist or not.
                $existedTenementFlat = TenementFlat::where('id',
                    $request->id)
                    ->where('activation', 1)
                    ->where('tenement_id', $tenement_id)
                    ->count();

                if($existedTenementFlat == 0){
                    return \Response::view('errors.404', array(), 404);
                    //return back()->withInput()->withErrors(['existedTenement' => 'Tenement is not exist ! Please confirm with Administrator. Thank you.']);
                }  

                $flat_num = '';
                if (strlen($request->input('flat_num')) > 2){
                    $flat_num = $request->input('flat_num');
                }
                else{
                    $flat_num = str_pad($request->input('flat_num'),2,"0", STR_PAD_LEFT)
                    ;
                }

                $flat_code = $request->input('block_name') . $request->input('block_sub') . '-' . 
                     str_pad($request->input('floor_num'),2,"0", STR_PAD_LEFT) . $request->input('floor_name') . '-' . $flat_num;


                // $flat_code = $request->input('block_name') . $request->input('block_sub') . '-' . 
                //      str_pad($request->input('floor_num'),2,"0", STR_PAD_LEFT) . $request->input('floor_name') . '-' . $request->input('flat_num');

                $tenementFlat = TenementFlat::find($request->id);

                $tenementFlat->name = $request->name;
                $tenementFlat->flat_code = $flat_code;
                $tenementFlat->block_name = $request->input('block_name') . $request->input('block_sub');
                $tenementFlat->floor_num = str_pad($request->floor_num,2,"0", STR_PAD_LEFT);
                $tenementFlat->floor_name = $request->floor_name;
                $tenementFlat->flat_num = $flat_num;
                $tenementFlat->address = $request->address;
                $tenementFlat->phone = $request->phone;
                $tenementFlat->area = $request->area;
                $tenementFlat->persons = $request->persons;
                
                if ($request->receive_date != ''){
                    $date = DateTime::createFromFormat('d/m/Y', $request->receive_date);
                    $date = $date->format('Ymd');
                }
                else {
                    $date = '';
                }   

                $tenementFlat->receive_date = $date;

                $tenementFlat->elec_type_id = $request->elec_type;
                $tenementFlat->gas_type_id = $request->gas_type;
                $tenementFlat->water_type_id = $request->water_type;
                $tenementFlat->next_water_type_id = $request->next_water_type;
                $tenementFlat->next_water_type_year_month = $request->year . $request->month;
                $tenementFlat->comment = $request->comment;
                $tenementFlat->updated_by = Auth::user()->id;
                $tenementFlat->updated_at = date("Y-m-d H:i:s");

                if ($request->rent_from != ''){
                    $date = DateTime::createFromFormat('d/m/Y', $request->rent_from);
                    $date = $date->format('Ymd');
                }
                else {
                    $date = '';
                }
                $tenementFlat->rent_from = $date;

                if ($request->rent_to != ''){
                    $date = DateTime::createFromFormat('d/m/Y', $request->rent_to);
                    $date = $date->format('Ymd');
                }
                else {
                    $date = '';
                }   
                $tenementFlat->rent_to = $date;

                $tenementFlat->rent_status = $request->rent_status;
                $tenementFlat->rent_note = $request->rent_note;
                $tenementFlat->manager_price = $request->manager_price;
                $tenementFlat->manager_fee_recal_flg = $request->manager_fee_recal_flg;

                $tenementFlat->save();

                DB::commit();
                return back()->with('tenementFlat-alert-success','Thông tin căn hộ đã được cập nhật !');
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
        $elec_tariffs = DB::table("tenement_elec_types")
        ->where('tenement_id', '=', $id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $water_tariffs = DB::table("tenement_water_types")
        ->where('tenement_id', '=', $id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $gas_tariffs = DB::table("tenement_gas_types")
        ->where('tenement_id', '=', $id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tenementFlats = TenementFlat::where('activation', 1)->get();
        $lsChar = range('A', 'Z');
        $lsNum = range('00', '99');
        $lsBlockSub = range('1', '20');

        return view('flat.flatcreate') 
            ->with(compact('tenementFlats', 
            'elec_tariffs','water_tariffs','gas_tariffs', 'lsChar', 'lsNum','lsBlockSub')
            );
    }

    public function store(Request $request)
    {
        //Check period user
        $tenement_id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }
        // $flat_code = $request->input('block_name') . '-' . 
        //              str_pad($request->input('floor_num'),2,"0", STR_PAD_LEFT) . $request->input('block_name') . '-' .
        //              str_pad($request->input('flat_num'),3,"0", STR_PAD_LEFT);
        $flat_num = '';

        if (strlen($request->input('flat_num')) > 2){
            $flat_num = $request->input('flat_num');
        }
        else{
            $flat_num = str_pad($request->input('flat_num'),2,"0", STR_PAD_LEFT)
            ;
        }

        $flat_code = $request->input('block_name') . $request->input('block_sub') . '-' . 
             str_pad($request->input('floor_num'),2,"0", STR_PAD_LEFT) . $request->input('floor_name') . '-' . $flat_num;

        $existedFlat = TenementFlat::where('tenement_id', $tenement_id)
            ->where('flat_code', $flat_code)
            ->where('activation',1)
            ->count();

        if($existedFlat == 1){
            return redirect('flat/create')->withInput()->withErrors('Căn hộ này đã được đăng ký trước đây.');
        } 
        $messages = [
            'address.required' => 'Căn Hộ: chưa được nhập.',
            'name.required' => 'Chủ Hộ: chưa được nhập.',
            // 'area.required' => 'Diện Tích: chưa được nhập.',
            // 'persons.required' => 'Số Nhân Khẩu: chưa được nhập.',
            // 'receive_date.required' => 'Ngày Nhận Căn Hộ: chưa được nhập.',
            'phone.required' => 'Điện Thoại: chưa được nhập.',
            'flat_num.required' => 'Số: chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
                    'address' => 'required',
                    'area' => 'required',
                    // 'receive_date' => 'required',
                    // 'phone' => 'required',
                    // 'persons' => 'required',
                    'flat_num' => 'required',
        ], $messages);

        if ($v->fails()) {
            return redirect('flat/create/')->withInput()->withErrors($v);
        }

        if ($request->receive_date != ''){
            $date = DateTime::createFromFormat('d/m/Y', $request->receive_date);
            $date = $date->format('Ymd');
        }
        else {
            $date = '';
        }

        if ($request->rent_from != ''){
            $rent_from = DateTime::createFromFormat('d/m/Y', $request->rent_from);
            $rent_from = $rent_from->format('Ymd');
        }
        else {
            $rent_from = '';
        }       

        if ($request->rent_to != ''){
            $rent_to = DateTime::createFromFormat('d/m/Y', $request->rent_to);
            $rent_to = $rent_to->format('Ymd');
        }
        else {
            $rent_to = '';
        }   
        $tenementFlat->rent_to = $date;

        TenementFlat::create([
            'name'   =>  $request->input('name'),
            'flat_code'   => $flat_code,
            'block_name' => $request->input('block_name') . $request->input('block_sub'),
            'floor_num' => str_pad($request->input('floor_num'),2,"0", STR_PAD_LEFT),
            'floor_name' => $request->input('floor_name'),
            'flat_num' => $flat_num,
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'area' => $request->input('area'),
            'persons' => $request->input('persons'),
            'receive_date' => $date,
            'elec_type_id' => $request->input('elec_type'),
            'gas_type_id' => $request->input('gas_type'),
            'water_type_id' => $request->input('water_type'),
            'comment' => $request->input('comment'),
            'rent_status' => $request->input('rent_status'),
            'rent_note' => $request->input('rent_note'),
            'rent_from' => $rent_from,
            'rent_to' => $rent_to,
            'tenement_id' => $tenement_id,
            'manager_price' => $request->input('manager_price'),
            'activation' => 1,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'updated_at' => date("Y-m-d H:i:s"),
            'manager_fee_recal_flg' => $request->input('manager_fee_recal_flg')
        ]);

        return redirect()->route('TenementFlat');
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
            //check Tenement Id is exist or not.
            $existedTenementFlat = TenementFlat::where('id',$request->tenement_id)->where('activation',1)->count();

            if($existedTenementFlat == 0){
                return \Response::view('errors.404', array(), 404);
                //return back()->withInput()->withErrors(['existedTenement' => 'Tenement is not exist ! Please confirm with Administrator. Thank you.']);
            }  

            $tenement = TenementFlat::find($request->tenement_id);
            $tenement->activation = 0;
            $tenement->updated_by = Auth::user()->id;
            $tenement->updated_at = date("Y-m-d H:i:s");
            $tenement->save();

            DB::commit();
            return redirect("/tenement");;
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }

}
