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

class UserDetailController extends Controller {   

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        $tenement_id = Auth::user()->tenement_id;

        $existedUser = User::where('id', $id)->count();
        if($existedUser == '0'){
            return \Response::view('errors.404', array(), 404);
        }

        $userDetailInfo = User::where('id', $id)->get();

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
            $slug = Auth::user()->getRoles();

            $tenements = Tenement::where('company_id', $company_id)->where('id', $tenement_id)->lists('name', 'id');
            $authorizeList = Role::where('slug', '=' ,$slug)->lists('description', 'id');
        }

        $User = User::find($id);

        return View('user.userdetail', ['userDetailInfo'=>$userDetailInfo[0], 
            'tenements'=>$tenements,
            'role' => array_keys($User->getRoles()),
            'authorizeList'=>$authorizeList]);
    }

	
	
    /**
     * update user information
     *
     * @param  \Illuminate\Http\Request  $request : requirement and complaint
     * @return \Illuminate\Http\Response
     */
    public function changepassword(Request $request) {

        $v = Validator::make($request->all(), [
                    'userId' => 'required',
                    'currentPassword' => 'required' ,
                    'newPassword' => 'required|min:5' ,
                    'verifyPassword' => 'required' ,
        ]);

        if ($v->fails()) {
            // If validation falis redirect back to login.
            return redirect('user/detail/'.$request->userId)->withInput()->withErrors($v);
        }

        if($request->newPassword != $request->verifyPassword){
            return back()->with('user-alert-danger','New Password and Verify Password do not match.');
        }

        //check UserId is exist or not.
        if (Auth::attempt(['id' => $request->userId, 'password' => $request->currentPassword]) == false) {
            return back()->with('user-alert-danger','Invalid Current Password!');
        }

        DB::beginTransaction();
        try{            
            $User = User::find($request->userId);
            $User->password = Hash::make($request->newPassword);
            $User->save();
            DB::commit();

            return back()->with('user-alert-success','User password has been changed !');
        } catch (\Exception $e) {
           DB::rollback();
            //something went wrong
           return back()->withInput()->with(['user-alert-danger' => $e->getMessage()]);//'Passport was not uploaded ! Please confirm with Administrator. Thank you.'
        }
    }

    /**
     * upload passport photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Request $request : file and userId
     * @return \Illuminate\Http\Response
     */
    public function updatedetail(Request $request) {        
        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'userId' => 'required',
                    'first_name' => 'required|min:5|max:100',
                    'email' => 'required|email'
        ]);

        if ($v->fails()) {
            // If validation falis redirect back to login.
            return redirect('user/detail/'.$request->userId)->withInput()->withErrors($v);
        } 

        DB::beginTransaction();
        try {                
             $existedUser = User::find($request->userId);
            if(!isset($existedUser)){
                return \Response::view('errors.404', array(), 404);
            }
            $User = User::find($request->userId);
            $User->first_name = $request->first_name;
            $User->email = $request->email;
            $User->role_id = $request->authorize;
            $User->save();

            $User->revokeAllRoles();
            $User->assignRole($request->authorize);

           DB::commit();

           return back()->with('user-alert-success','User information has been updated !');
        } catch (\Exception $e) {
           DB::rollback();
            //something went wrong
           return back()->withInput()->withErrors(['uploadFail' => $e->getMessage()]);//'Passport was not uploaded ! Please confirm with Administrator. Thank you.'
        }
            
    } 


}
