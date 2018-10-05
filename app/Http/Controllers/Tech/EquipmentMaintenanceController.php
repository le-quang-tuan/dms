<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use Kitano;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TenementEquipment;
use App\Models\TenementEquipmentMaintenance;
use App\Models\Area;
use DB;
use File;
use Validator;
use Auth;
use App\LaraBase\NumberUtil;
use DateTime;

class EquipmentMaintenanceController extends Controller {

    /**
     * Display a listing of the esource.
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

        $mst_maintenance_items = DB::table("mst_maintenance_items")
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tenement_producers = DB::table("tenement_producers")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tenement_equipment_groups = DB::table("tenement_equipment_groups")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tenementEquipment = TenementEquipment::where('id',$id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('tech.equipmentmaintenance', [ 
            'tenementEquipment'=>$tenementEquipment[0],
            'mst_maintenance_items'=>$mst_maintenance_items,
            'tenement_producers'=>$tenement_producers,
            'tenement_equipment_groups'=>$tenement_equipment_groups,]);
    }

    /**
     * upload passport photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request) {
        dd($request);
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            'report_date.required' => 'Ngày báo cáo chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
            'report_date' => 'required',
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {
                //check Tenement Id is exist or not.
                $existedTenementEquipmentMaintenance = TenementEquipmentMaintenance::where('id', $request->id)
                    ->where('activation',1)
                    ->count();

                if($existedTenementEquipmentMaintenance == 0){
                    return \Response::view('errors.404', array(), 404);
                }  

                $equipmentMaintenance = TenementEquipmentMaintenance::find($request->id);
                
                // dd($equipmentMaintenance);

                // $equipmentMaintenance->name = $request->name;
                $date = DateTime::createFromFormat('d/m/Y', $request->report_date);

                $equipmentMaintenance->report_date = $date;                
                $equipmentMaintenance->report_start_time = $request->report_start_time;
                $equipmentMaintenance->report_end_time = $request->report_end_time;

                $equipmentMaintenance->report_for = $request->report_for;
                $equipmentMaintenance->report_description = $request->report_description;
                $equipmentMaintenance->report_company_execute = $request->report_company_execute;
                $equipmentMaintenance->note = $request->note;

                $equipmentMaintenance->category1_id = $request->category1;
                $equipmentMaintenance->category1_note = $request->note1;

                $equipmentMaintenance->category2_id = $request->category2;
                $equipmentMaintenance->category2_note = $request->note2;

                $equipmentMaintenance->category3_id = $request->category3;
                $equipmentMaintenance->category3_note = $request->note3;

                // dd($equipmentMaintenance);

                $equipmentMaintenance->save();

                DB::commit();
                return back()->with('tenementEquipment-alert-success','Equipment is updated !');
            } catch (\Exception $e) {
                DB::rollback();

                //something went wrong
                return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
            }
        } 
    }
    /**
     * upload passport photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        // //Check period user
        // $id = Auth::user()->tenement_id;
        
        // //Check period user
        // if(Auth::user()->confirmed == 0 || $id == '') {
        //     return redirect("/home");
        // }

        // $messages = [
        //     'name.required' => 'Tên chưa được nhập.',
        // ];
        // // Applying validation rules.
        // $v = Validator::make($request->all(), [
        //     'name' => 'required',
        // ], $messages);

        // if ($v->fails()) {
        //     return back()->withInput()->withErrors($v);
        // } else {
        //     DB::beginTransaction();
        //     try {
        //         //check Tenement Id is exist or not.
        //         $existedTenementEquipment = TenementEquipment::where('id',$request->id)
        //             ->where('activation',1)
        //             ->where('tenement_id',$id)
        //             ->count();

        //         if($existedTenementEquipment == 0){
        //             return \Response::view('errors.404', array(), 404);
        //         }  

        //         $tenementEquipment = TenementEquipment::find($request->id);
        //         $tenementEquipment->name = $request->name;
        //         $tenementEquipment->equipment_group_id = $request->equipment_group_id;
        //         $tenementEquipment->producer_id = $request->producer_id;
        //         $tenementEquipment->name = $request->name;
        //         $tenementEquipment->label = $request->label;
        //         $tenementEquipment->model = $request->model;
        //         $tenementEquipment->specification = $request->specification;
        //         $tenementEquipment->area = $request->area;
                
        //         $tenementEquipment->comment = $request->comment;
        //         $tenementEquipment->save();

        //         DB::commit();
        //         return back()->with('tenementEquipment-alert-success','Equipment is updated !');
        //     } catch (\Exception $e) {
        //         DB::rollback();

        //         //something went wrong
        //         return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        //     }
        // }
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            'plan_date.required' => 'Ngày kế hoạch chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
            'plan_date' => 'required',
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {
                //check Tenement Id is exist or not.
                $existedTenementEquipmentMaintenance = TenementEquipmentMaintenance::where('id', $request->id)
                    ->where('activation',1)
                    ->count();

                if($existedTenementEquipmentMaintenance == 0){
                    return \Response::view('errors.404', array(), 404);
                }  

                $equipmentMaintenance = TenementEquipmentMaintenance::find($request->id);
                
                // dd($equipmentMaintenance);

                // $equipmentMaintenance->name = $request->name;
                $plan_date = DateTime::createFromFormat('d/m/Y', $request->plan_date);
                $report_date = DateTime::createFromFormat('d/m/Y', $request->report_date);

                $equipmentMaintenance->plan_date = $plan_date;                
                $equipmentMaintenance->plan_start_time = $request->plan_start_time;
                $equipmentMaintenance->plan_end_time = $request->plan_end_time;

                $equipmentMaintenance->plan_for = $request->plan_for;
                $equipmentMaintenance->plan_description = $request->plan_description;
                $equipmentMaintenance->plan_company_execute = $request->plan_company_execute;

                $equipmentMaintenance->report_date = $report_date;                
                $equipmentMaintenance->report_start_time = $request->report_start_time;
                $equipmentMaintenance->report_end_time = $request->report_end_time;

                $equipmentMaintenance->report_for = $request->report_for;
                $equipmentMaintenance->report_description = $request->report_description;
                $equipmentMaintenance->report_company_execute = $request->report_company_execute;
                
                $equipmentMaintenance->note = $request->note;

                $equipmentMaintenance->category1_id = $request->category1;
                $equipmentMaintenance->category1_note = $request->note1;

                $equipmentMaintenance->category2_id = $request->category2;
                $equipmentMaintenance->category2_note = $request->note2;

                $equipmentMaintenance->category3_id = $request->category3;
                $equipmentMaintenance->category3_note = $request->note3;

                $equipmentMaintenance->save();

                DB::commit();
                return back()->with('tenementEquipment-alert-success','Equipment is updated !');
            } catch (\Exception $e) {
                DB::rollback();

                //something went wrong
                return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
            }
        } 
    }

    public function store(Request $request)
    {
        $id = Auth::user()->tenement_id;
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        $messages = [
            'id.required' => 'Chưa chọn Thiết bị từ danh sách để lên kế hoạch.',
        ];

        $v = Validator::make($request->all(), [
            'id'  =>  'required'
        ], $messages);
        
        if($v->fails()){
            return back()->withInput()->withErrors($v);
        }
        $request->session()->put('counter', count($request->counter));

        if( isset($request->counter) ){
            $rules = array(
            'plan_date1' => 'required',
            'plan_start_time1' => 'required',
            'plan_end_time1' => 'required',
            'plan_description1' => 'required');

            $items = array();

            for($i=0;$i<count($request->counter);$i++){
                $tempPlan_date = '';
                $tempPlan_start_time = '';
                $tempPlan_end_time = '';
                $tempPlan_description = '';

                $step = $i+1;
                $tempPlan_date = 'plan_date' . $step;
                $tempPlan_start_time = 'plan_start_time' . $step;
                $tempPlan_end_time = 'plan_end_time' . $step;
                $tempPlan_description = 'plan_description' . $step;

                $messages[$tempPlan_date . '.required']  = 'Ngày kế hoạch (' . $step . ') chưa được nhập.';
                $messages[$tempPlan_start_time . '.required']  = 'Thời gian bắt đầu (' . $step . ') chưa được nhập.';
                $messages[$tempPlan_end_time . '.required']  = 'Thời gian kết thúc (' . $step . ') chưa được nhập.';
                $messages[$tempPlan_description . '.required']  = 'Hạng mục (' . $step . ') chưa được nhập.';

                $rules[$tempPlan_date] = 'required';
                $rules[$tempPlan_start_time] = 'required';
                $rules[$tempPlan_end_time] = 'required';
                $rules[$tempPlan_description] = 'required';
            }
            $v = Validator::make($request->all(), $rules, $messages);

            if($v->fails()){
                return back()->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {
            for($i=0;$i<count($request->counter);$i++){
                $tempPlan_date = '';
                $tempPlan_start_time = '';
                $tempPlan_end_time = '';
                $tempPlan_description = '';

                $step = $i+1;    
                $tempPlan_date = 'plan_date' . $step;
                $tempPlan_start_time = 'plan_start_time' . $step;
                $tempPlan_end_time = 'plan_end_time' . $step;
                $tempPlan_description = 'plan_description' . $step;
                $tempPlan_company_execute = 'plan_company_execute' . $step;
                $tempPlan_for = 'plan_for' . $step;

                $date = DateTime::createFromFormat('d/m/Y', $request->input($tempPlan_date));

                TenementEquipmentMaintenance::create([
                    'equipment_id'  => $request->input('id'),
                    'plan_date'    => $date->format('Y-m-d'),
                    'plan_start_time' => $request->input($tempPlan_start_time),
                    'plan_end_time'   => $request->input($tempPlan_end_time),
                    'plan_description' => $request->input($tempPlan_description),
                    'plan_company_execute'   => $request->input($tempPlan_company_execute),
                    'plan_for'   => $request->input($tempPlan_for)
                ]);
            }
            DB::commit();
            $request->session()->forget('counter');

            return redirect()->route('Equipment');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  

        return redirect()->route('Equipment');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request) {
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        DB::beginTransaction();
        try {
            //check Tenement Id is exist or not.
            $existedTenementEquipmentMaintenance = TenementEquipmentMaintenance::where('id', $request->id)
                ->where('activation',1)
                ->count();

            if($existedTenementEquipmentMaintenance == 0){
                return \Response::view('errors.404', array(), 404);
            }  

            $equipmentMaintenance = TenementEquipmentMaintenance::find($request->id);
            
            $equipmentMaintenance->activation = 0;                

            $equipmentMaintenance->save();

            DB::commit();
            return redirect("tech/schedule/" . $equipmentMaintenance->equipment_id);
            //return redirect()->route('ScheduleCal');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }
    }
}
