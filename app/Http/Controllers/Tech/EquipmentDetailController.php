<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use Kitano;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TenementEquipment;
use App\Models\Area;
use DB;
use File;
use Validator;
use Auth;
use App\LaraBase\NumberUtil;

class EquipmentDetailController extends Controller {

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

        $tenement_producers = DB::table("tenement_producers")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tenement_equipment_groups = DB::table("tenement_equipment_groups")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tenementEquipment = TenementEquipment::where('id',$id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('tech.equipmentdetail', [ 
            'tenementEquipment'=>$tenementEquipment[0],
            'tenement_producers'=>$tenement_producers,
            'tenement_equipment_groups'=>$tenement_equipment_groups, ]);
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
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            'name.required' => 'Tên chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
            'name' => 'required',
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {
                //check Tenement Id is exist or not.
                $existedTenementEquipment = TenementEquipment::where('id',$request->id)
                    ->where('activation',1)
                    ->where('tenement_id',$id)
                    ->count();

                if($existedTenementEquipment == 0){
                    return \Response::view('errors.404', array(), 404);
                }  

                $tenementEquipment = TenementEquipment::find($request->id);
                $tenementEquipment->name = $request->name;
                $tenementEquipment->equipment_group_id = $request->equipment_group_id;
                $tenementEquipment->producer_id = $request->producer_id;
                $tenementEquipment->name = $request->name;
                $tenementEquipment->label = $request->label;
                $tenementEquipment->model = $request->model;
                $tenementEquipment->specification = $request->specification;
                $tenementEquipment->area = $request->area;
                
                $tenementEquipment->comment = $request->comment;
                $tenementEquipment->save();

                DB::commit();
                return back()->with('tenementEquipment-alert-success','Equipment is updated !');
            } catch (\Exception $e) {
                DB::rollback();

                //something went wrong
                return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
            }
        }    
    }

    public function create()
    {
        $id = Auth::user()->tenement_id;
        
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $tenement_producers = DB::table("tenement_producers")
        ->where('tenement_id', '=', $id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tenement_equipment_groups = DB::table("tenement_equipment_groups")
        ->where('tenement_id', '=', $id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        return view('tech.equipmentcreate')->with(compact('tenement_producers', 'tenement_equipment_groups'));
    }

    public function store(Request $request)
    {
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        $messages = [
            'name.required' => 'Tên chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        }

        TenementEquipment::create([
            'name'   =>  $request->input('name'),
            'equipment_group_id'   =>  $request->input('equipment_group_id'),
            'equipment_type_id' => 1,
            'producer_id' => $request->input('producer_id'),
            'producer' => $request->input('producer'),
            'label' => $request->input('label'),
            'model' => $request->input('model'),
            'specification' => $request->input('specification'),
            'area' => $request->input('area'),
            'comment' => $request->input('comment'),
            'tenement_id' => $id,
            'activation' => 1,
            'equipment_code'  => $this->proc_getCode($id, 8)
        ]);

        return redirect()->route('Equipment');
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
            $tenement_equipment_id = $request->id;

            $tenementEquipment = TenementEquipment::find($tenement_equipment_id);
            $tenementEquipment->activation = 0;
            $tenementEquipment->save();

            DB::commit();
            return redirect()->route('Equipment')->with('tenementEquipment-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }   
    }

}
