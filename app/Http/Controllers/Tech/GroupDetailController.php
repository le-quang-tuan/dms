<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use Kitano;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TenementEquipmentGroup;
use App\Models\Area;
use DB;
use File;
use Validator;
use Auth;
use App\LaraBase\NumberUtil;

class GroupDetailController extends Controller {

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

        $tenementEquipmentGroup = TenementEquipmentGroup::where('id',$id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('tech.groupdetail', [ 'tenementEquipmentGroup'=>$tenementEquipmentGroup[0] ]);
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
            'name.required' => 'Tên nhóm chưa được nhập.',
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
                $existedTenementEquipmentGroup = TenementEquipmentGroup::where('id',$request->id)
                    ->where('activation',1)
                    ->where('tenement_id',$id)
                    ->count();

                if($existedTenementEquipmentGroup == 0){
                    return \Response::view('errors.404', array(), 404);
                }  

                $tenementEquipmentGroup = TenementEquipmentGroup::find($request->id);
                $tenementEquipmentGroup->name = $request->name;
                $tenementEquipmentGroup->comment = $request->comment;
                $tenementEquipmentGroup->save();

                DB::commit();
                return back()->with('tenementEquipmentGroup-alert-success','Group is updated !');
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

        return view('tech.groupcreate');
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
            'name.required' => 'Tên nhóm chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        }

        TenementEquipmentGroup::create([
            'name'   =>  $request->input('name'),
            'comment' => $request->input('comment'),
            'tenement_id' => $id,
            'activation' => 1,
            'equipment_group_code' => $this->proc_getCode($id, 7)
        ]);

        return redirect()->route('Group');
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
            $tenement_group_id = $request->id;

            $tenementEquipmentGroup = TenementEquipmentGroup::find($tenement_group_id);
            $tenementEquipmentGroup->activation = 0;
            $tenementEquipmentGroup->save();

            DB::commit();
            return redirect()->route('Group')->with('tenementEquipmentGroup-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }   
    }

}
