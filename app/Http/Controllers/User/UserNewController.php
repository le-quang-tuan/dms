<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenement;
use App\Models\Role;
use DB;
use File;
use Validator;
use Auth;
use Hash;

class UserNewController extends Controller {   

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index() {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $tenement = Tenement::where('id',Auth::user()->tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();
        $company_id = $tenement[0]->company_id;

        if ($user->is('admin')){
            $tenements = Tenement::lists('name', 'id');
            $authorizeList = Role::lists('description', 'id');
        }
        else if ($user->is('manager')) {
            $tenements = Tenement::where('company_id', $company_id)->lists('name', 'id');
            $authorizeList = Role::where('slug', '<>' ,'admin')->lists('description', 'id');
        }
        else if ($user->is('accountant')) {
            $tenements = Tenement::where('company_id', $company_id)->lists('name', 'id');
            $authorizeList = Role::where('slug', '=' ,'accountant_mem')->lists('description', 'id');
        }
        else {
            return redirect("/index");
        }

        return View('user.usernew', [
            'tenements'=>$tenements, 
            'authorizeList'=>$authorizeList]);
    }


	 public function newUser(Request $request) {        
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'username' => 'required|min:5|max:20',
                    'password' => 'required|min:5|max:20',
                    'first_name' => 'required|min:5|max:100',
                    'email' => 'required|email',
                    'authorize' => 'required',
                    'tenement_id' => 'required',
        ]);

        if ($v->fails()) {
            // If validation falis redirect back to login.
            return redirect('user/new'.$request->userId)->withInput()->withErrors($v);
        } 

        $userDetailInfo = Auth::user();

        // if($userDetailInfo->authorize != 1){
        //     return back()->with('user-alert-danger','You do not have permission!');
        // }

        //check UserId is exist or not.
        $existedUser = User::find($request->username);
        if(isset($existedUser)){
            return back()->with('user-alert-danger','User Name is already taken!');
        }

        DB::beginTransaction();
        try {                
            $id = DB::table('users')->insertGetId([
                'username' => $request->username,
                'password' =>  Hash::make($request->password),
                'first_name' => $request->first_name,
                'email' => $request->email,
                'confirmed' => 1,
                'tenement_id' => $request->tenement_id
            ]);

            DB::commit();
            if(isset($id)){
                return back()->with('user-alert-success','New user has been created!');
            }else{
                return back()->with('user-alert-danger','Something went wrong please try again!');
            }
        } catch (\Exception $e) {
            dd($e);
           DB::rollback();
            //something went wrong
           return back()->withInput()->withErrors(['uploadFail' => $e->getMessage()]);//'Passport was not uploaded ! Please confirm with Administrator. Thank you.'
        }
            
    } 


}
