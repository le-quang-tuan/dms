<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Company;
use App\Model\Area;
use DB;
use File;
use Validator;
use Auth;

class CompanyDetailController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        //Check period user
        // if (Auth::user()->authorize == 0) {
        //     return redirect("/home");
        // }
        //check CustomerId is exist or not.
        $existedCompany = Company::where('id', $id)->count();

        if($existedCompany == '0'){
            return \Response::view('errors.404', array(), 404);
            //return back()->withInput()->withErrors(['existedCustomer' => 'Customer is not exist ! Please confirm with Administrator. Thank you.']);
        }

        $company = Company::where('id',$id)->where('activation',1)->orderBy('name', 'asc')->get();
// dd($company);
        // $branch = Area::where('activation',1)->get();        
        return View('company.companydetail', [ 'company'=>$company[0] ]);
    }

    /**
     * upload passport photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        //Check period user
        if (Auth::user()->authorize == 0) {
            return redirect("/home");
        }
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'companyName' => 'required'
        ]);

        if ($v->fails()) {
            // If validation falis redirect back to login.
            return redirect('company/detail/'.$request->companyId)->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {
                //check Company Id is exist or not.
                $existedCompany = Company::where('id',$request->companyId)->where('activation',1)->count();

                if($existedCompany == 0){
                    return \Response::view('errors.404', array(), 404);
                    //return back()->withInput()->withErrors(['existedCompany' => 'Company is not exist ! Please confirm with Administrator. Thank you.']);
                }  

                $company = Company::find($request->companyId);
                $company->name = $request->companyName;
                $company->address = $request->address;
                $company->contactname = $request->contactName;
                $company->tel = $request->tel;
                $company->fax = $request->fax;
                $company->contractrate = $request->contractRate;
                $company->areaid = $request->areaid;
                $company->note = $request->note;

                $company->save();

                DB::commit();
                return back()->with('company-alert-success','Company is updated !');
            } catch (\Exception $e) {
                DB::rollback();

                //something went wrong
                return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
            }
        }    
    }



    /*
    * CREATE COMPANY BY HUNGNGUYEN
    */
    public function create()
    {
        //Check period user
        if (Auth::user()->authorize == 0) {
            return redirect("/home");
        }
        $areas = Area::where('activation', 1)->get();
        return view('company.companycreate', compact('areas'));
    }

    public function store(Request $request)
    {
        //Check period user
        if (Auth::user()->authorize == 0) {
            return redirect("/home");
        }
        $v = Validator::make($request->all(), [
            'contractRate'  =>  'required',
            'companyName'   =>  'required|unique:companyinfo,name'
        ]);
        if($v->fails()){
            return redirect()->route('Company.create')->withInput()->withErrors($v);
        }

        Company::create([
            'name'   =>  $request->input('companyName'),
            'address'  =>  $request->input('address'),
            'contactname'   =>  $request->input('contactName'),
            'tel'   =>  $request->input('tel'),
            'fax'   =>  $request->input('fax'),
            'contractrate'  =>  $request->input('contractRate'),
            'areaid'  =>  $request->input('areaid'),
            'activation'    =>  $request->input('activation'),
            'note'  =>  $request->input('note')
        ]);

        return redirect()->route('Company');
    }
    /*
    * CREATE COMPANY BY HUNGNGUYEN
    */
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request) {
        //Check period user
        if (Auth::user()->authorize == 0) {
            return redirect("/home");
        }
        // dd($request);
        $v = Validator::make($request->all(), [
            'comId' => 'required'                   
        ]);

        if ($v->fails()) {
            // If validation falis redirect back to login.
            return redirect('company/detail/'.$request->comId)->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {                
                //check CustomerId is exist or not.
                $existedCompany = Company::where('id',$request->comId)->where('activation',1)->count();

                if($existedCompany == 0){
                    return \Response::view('errors.404', array(), 404);
                    //return back()->withInput()->withErrors(['existedCompany' => 'Company is not exist ! Please confirm with Administrator. Thank you.']);
                }  
                
                $company = Company::find($request->comId);                
                $company->activation = 0;                
                $company->save();
                DB::commit();
                                    
            // EDIT BY HUNGNGUYEN
                // return View('company.company');
                return redirect()->route('Company');
            // EDIT BY HUNGNGUYEN

            } catch (\Exception $e) {
                DB::rollback();

                //something went wrong
                return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
            }
        }    
    }

}
