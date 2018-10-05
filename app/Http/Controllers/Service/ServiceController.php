<?php

namespace App\Http\Controllers\Service;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tenement;
use App\Models\User;
use App\Models\TenementFlat;
use App\Models\TfResidentDt;
use App\Models\TfResidentHd;
use DB;
use yajra\Datatables\Datatables;
use Auth;
use Validator;
use DateTime;

class ServiceController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        $tenement_id = Auth::user()->tenement_id;

        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }

        $tenement = Tenement::where('id', $tenement_id)->where('activation',1)->get();
        $userDetailInfo = User::where('id', Auth::user()->id)->get();
        
        return view('flat.flat',  ['tenement'=>$tenement[0], 'userDetailInfo'=>$userDetailInfo[0]]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {        
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd(123);
        $tenementFlat = DB::table('tenement_flats as a')
            ->leftjoin('tenement_elec_types as b', function($join)
            {
                $join->on('a.tenement_id', '=', 'b.tenement_id');
                $join->on('a.elec_type_id', '=', 'b.id');
            })
            ->leftjoin('tenement_water_types as c', function($join)
            {
                $join->on('a.tenement_id', '=', 'c.tenement_id');
                $join->on('a.water_type_id', '=', 'c.id');
            })
            ->leftjoin('tenement_gas_types as d', function($join)
            {
                $join->on('a.tenement_id', '=', 'd.tenement_id');
                $join->on('a.gas_type_id', '=', 'd.id');
            })
            ->select(['a.*','b.elec_type','c.water_type','d.gas_type'])
            ->where('a.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc');
        return Datatables::of($tenementFlat)
                ->addColumn('action', function ($tenementFlat) {
                    return '<a style="text-align: center;" href="flat/detail/'. $tenementFlat->id .'" >' . $tenementFlat->flat_code . '</a>';                    
                })
                ->addColumn('elec', function ($tenementFlat) {
                    return '<a href="flat/elec/'. $tenementFlat->id . '" class="btn btn-xs btn-primary" target="_blank">Chỉ số sử dụng</a>';                    
                })
                ->addColumn('water', function ($tenementFlat) {
                    return '<a href="flat/water/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số sử dụng</a>';                    
                })
                ->addColumn('gas', function ($tenementFlat) {
                    return '<a href="flat/gas/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số sử dụng</a>';                    
                })
                ->addColumn('vehicle', function ($tenementFlat) {
                    return '<a href="flat/vehicle/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Đăng ký</a>';                    
                })
                ->addColumn('service', function ($tenementFlat) {
                    return '<a href="flat/service/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Phát sinh</a>';                    
                })
                ->make(true);        
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tfOwnerAnyData($flat_id) {        
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd(123);
        $tfowner = DB::table('tf_resident_hd')
            ->where('activation', 1)
            ->where('flat_id', '=', $flat_id)
            ->where('stay_type', '=', 0)
            ->orderBy('input_date', 'desc');
        return Datatables::of($tfowner)
                ->addColumn('addRes', function ($tfowner) {
                    return '<button type="button" 
                        id="'. $tfowner->id .'"
                        class="btn btn-xs btn-primary addres">Thêm hộ khẩu</button>';                    
                })
                ->addColumn('destroy', function ($tfowner) {
                    return '<button type="button" 
                        id="'. $tfowner->id .'"
                        flat_id="'. $tfowner->flat_id .'" 
                        class="btn btn-xs btn-primary destroy">Hủy</button>';                    
                })
                ->make(true);        
    }    
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tfRentAnyData($flat_id) {        
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd(123);
        $tfowner = DB::table('tf_resident_hd')
            ->where('activation', 1)
            ->where('flat_id', '=', $flat_id)
            ->where('stay_type', '=', 1)
            ->orderBy('input_date', 'desc');
        return Datatables::of($tfowner)
                ->addColumn('addRes', function ($tfowner) {
                    return '<button type="button" 
                        id="'. $tfowner->id .'"
                        class="btn btn-xs btn-primary addres">Thêm hộ khẩu</button>';                    
                })
                ->addColumn('destroy', function ($tfowner) {
                    return '<button type="button" 
                        id="'. $tfowner->id .'"  
                        flat_id="'. $tfowner->flat_id .'" 
                        class="btn btn-xs btn-primary destroy">Hủy</button>';                      
                })
                ->make(true);        
    } 


    public function resChange(Request $request)
    {
        //Check period user
        $tenement_id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $messages = [
            'content.required' => 'Đợt cập nhật: chưa được nhập.',
            'input_date.required' => 'Ngày cập nhật: chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'content' => 'required',
                    'input_date' => 'required',
        ], $messages);

        if ($v->fails()) {
            return redirect('flat/detail/' . $request->flat_id)->withInput()->withErrors($v);
        }

        if ($request->input_date != ''){
            $date = DateTime::createFromFormat('d/m/Y', $request->input_date);
            $date = $date->format('Ymd');
        }
        else {
            $date = '';
        }

        TfResidentHd::create([
            'content'   =>  $request->content,
            'input_date' => $date,
            'comment' => $request->comment,
            'flat_id' => $request->flat_id,
            'stay_type' => $request->stay_type,
            'activation' => 1,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'updated_at' => date("Y-m-d H:i:s")                
        ]);

        return redirect('flat/detail/'. $request->flat_id);
    }

    public function resAdd(Request $request)
    {
        //Check period user
        $tenement_id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $messages = [
            'res_name.required' => 'Họ tên: chưa được nhập.',
            'res_dob.required' => 'Ngày tháng năm sinh: chưa được nhập.',
            'res_sex.required' => 'Giới tính: chưa được nhập.',
        ];        
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'res_name' => 'required',
                    'res_dob' => 'required',
                    'res_sex' => 'required',
        ], $messages);

        if ($v->fails()) {

            return redirect('flat/detail/' . $request->flat_id)->withInput()->withErrors($v);
        }

        if ($request->res_dob != ''){
            $date = DateTime::createFromFormat('d/m/Y', $request->res_dob);
            $date = $date->format('Ymd');
        }
        else {
            $date = '';
        }

        if ($request->date_issue != ''){
            $date_issue = DateTime::createFromFormat('d/m/Y', $request->date_issue);
            $date_issue = $date_issue->format('Ymd');
        }
        else {
            $date_issue = '';
        }

        TfResidentDt::create([
            'name'   =>  $request->res_name,
            'resident_id'   =>  $request->res_id,
            'dob'   =>  $date,
            'sex'   =>  $request->res_sex,
            'res_type'   =>  $request->res_type,
            'email'   =>  $request->res_email,
            'phone'   =>  $request->res_phone,
            'identity_card'   =>  $request->res_identity_card,
            'date_issue' => $date_issue,
            'issued_by' => $request->issued_by,
            'comment' => $request->res_comment,
            'activation' => 1,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'updated_at' => date("Y-m-d H:i:s")                
        ]);
        //dd($request->res_sex);

        return $request->res_id;
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resData($resident_id) {        
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd(123);
        $resFlat = DB::table('tf_resident_dt as a')
            ->where('a.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.resident_id', '=' , $resident_id)
            ->orderBy('a.id', 'desc');
        return Datatables::of($resFlat)
                ->addColumn('action', function ($resFlat) {
                    return '<a style="text-align: center;" href="flat/detail/'. $resFlat->id .'" >' . $resFlat->id . '</a>';                    
                })                
                ->make(true);        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resDestroy(Request $request) {
        //Check period user
        $id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        DB::beginTransaction();
        try {
            //check Tenement Id is exist or not.
            $tfResidentHd = TfResidentHd::where('id',$request->id)->count();

            if($tfResidentHd == 0){
                return \Response::view('errors.404', array(), 404);
            }  

            $tfResidentHd = TfResidentHd::find($request->id);
            $tfResidentHd->activation = 0;
            $tfResidentHd->updated_by = Auth::user()->id;
            $tfResidentHd->updated_at = date("Y-m-d H:i:s");
            $tfResidentHd->save();

            DB::commit();
            return redirect('flat/detail/'. $request->flat_id);
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }
}