<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use Kitano;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TenementProducer;
use App\Models\Area;
use DB;
use File;
use Validator;
use Auth;
use App\LaraBase\NumberUtil;

class ProducerDetailController extends Controller {

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

        $tenementProducer = TenementProducer::where('id',$id)->where('activation',1)->orderBy('name', 'asc')->get();

        return View('tech.producerdetail', [ 'tenementProducer'=>$tenementProducer[0] ]);
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
            'name.required' => 'Tên nhà cung cấp chưa được nhập.',
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
                $existedTenementProducer = TenementProducer::where('id',$request->id)
                    ->where('activation',1)
                    ->where('tenement_id',$id)
                    ->count();

                if($existedTenementProducer == 0){
                    return \Response::view('errors.404', array(), 404);
                }  

                $tenementProducer = TenementProducer::find($request->id);
                $tenementProducer->name = $request->name;
                $tenementProducer->address = $request->address;
                $tenementProducer->contact_name = $request->contact_name;
                $tenementProducer->tel = $request->tel;
                $tenementProducer->email = $request->email;
                
                $tenementProducer->comment = $request->comment;
                $tenementProducer->save();

                DB::commit();
                return back()->with('tenementProducer-alert-success','Producer is updated !');
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

        return view('tech.producercreate');
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
            'name.required' => 'Tên nhà cung cấp chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        }

        TenementProducer::create([
            'name'   =>  $request->input('name'),
            'address'   =>  $request->input('address'),
            'contact_name' => $request->input('contact_name'),
            'tel' => $request->input('tel'),
            'email' => $request->input('email'),
            'comment' => $request->input('comment'),
            'tenement_id' => $id,
            'activation' => 1,
            'producer_code'  => $this->proc_getCode($id, 6)
        ]);

        return redirect()->route('Producer');
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
            $tenement_producer_id = $request->id;

            $tenementProducer = TenementProducer::find($tenement_producer_id);
            $tenementProducer->activation = 0;
            $tenementProducer->save();

            DB::commit();
            return redirect()->route('Producer')->with('tenementProducer-alert-success','Tenement is updated !');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }   
    }

}
