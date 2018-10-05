<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\User;
use App\Models\Role;
use App\Models\Occupation;
use DB;
use Validator;
use Hash;

class UsersController extends Controller
{
    public function index()
    {
        $occupationList = Occupation::all('id', 'name')->toArray();
    	$roleList = Role::all('id', 'name')->toArray();

        array_unshift($occupationList, ['id'=>0, 'name'=>'']);

    	return view('front.f05', compact('occupationList', 'roleList'));
    }

    public function filter(Request $request)
    {
    	$params = $request->all();

    	// DB::enableQueryLog();

    	$query = DB::table('users as u')
                ->leftJoin('occupations as o', 'o.id', '=', 'u.occupation_id')
                ->leftJoin('role_user as ru', 'ru.user_id', '=', 'u.id')
    			->leftJoin('roles as r', 'r.id', '=', 'ru.role_id');

    	if ($params['id'] != '') {
    		$query->where('u.id', '=', $params['id']);
    	}
    	if ($params['email'] != '') {
    		$query->where('u.email', '=', $params['email']);
    	}
    	if ($params['name'] != '') {
    		$query->where('u.name', 'like', '%'.$params['name'].'%');
    	}
    	if ($params['nick_name'] != '') {
    		$query->where('u.nick_name', 'like', '%'.$params['nick_name'].'%');
    	}
    	if ($params['occupation_id'] != 0) {
    		$query->where('u.occupation_id', '=', $params['occupation_id']);
    	}
    	if ($params['inactive'] != true) {
    		$query->where('u.activated', '=', '1');
    	}

    	$query->selectRaw('
    		u.id,
    		u.name,
            u.nick_name,
    		u.email,
            u.occupation_id as occupation_id,
            o.name as occupation_name,
            r.id as role_id,
    		r.name as role_name,
    		u.activated
    	');
    	$dataSet = $query->get();
    	// dd(DB::getQueryLog());

    	return response()->json(['success'=>true, 'dataSet'=>$dataSet]);
    }

    public function create(Request $request)
    {
        $out = array('success'=>false, 'message'=>null, 'errors'=>array());
        $data = $request->all();

        // CHECK VALIDATE
        if (empty($data['id'])) {
            $out['errors']['params.id'] = trans('validation.required', ['attribute'=>'ID']);
        }
        if (!empty($data['id'])) {
            if (!is_numeric($data['id'])) {
                $out['errors']['params.id'] = trans('validation.numeric', ['attribute'=>'ID']);
            }
            
            if (strlen($data['id']) > 4) {
                $out['errors']['params.id'] = trans('validation.max.string', ['attribute'=>'ID', 'max'=>'4']);
            }

            $data['id'] = sprintf('%04d', $data['id']);
            $user = User::find($data['id']);
            if (isset($user)) {
                $out['errors']['params.id'] = trans('validation.unique', ['attribute'=>'ID']);
            }
        }
        if (empty($data['email'])) {
            $out['errors']['params.email'] = trans('validation.required', ['attribute'=>'Email']);
        }
        if (!empty($data['email'])) {
            $checkEmailExists = User::where('email', $data['email'])->first();
            if (isset($checkEmailExists)) {
                $out['errors']['params.email'] = trans('validation.unique', ['attribute'=>'Email']);
            }
        }
        if (empty($data['occupation_id'])) {
            $out['errors']['params.occupation_id'] = trans('validation.required', ['attribute'=>'支店コード']);
        }
        if (empty($data['password'])) {
            $out['errors']['params.password'] = trans('validation.required', ['attribute'=>'パスワード']);
        }
        if (!empty($data['password'])) {
            if ($data['password'] != $data['password_confirmation']) {
                $out['errors']['params.password_confirmation'] = trans('validation.same', ['attribute'=>'パスワード', 'other'=>'パスワード確認']);
            }
        }
        if (empty($data['role_id'])) {
            $out['errors']['params.role_id'] = trans('validation.required', ['attribute'=>'権限']);
        }
        if (count($out['errors']) > 0) {
            return response()->json($out, 200);
        }
        // END CHECK VALID

    	try {
    		DB::beginTransaction();

            $role_id = $data['role_id'];
            $data['activated'] = !$data['inactive'];
	    	$data['activation_code'] = str_random(32);
	    	$data['password'] = Hash::make($data['id']);
            unset($data['password_confirmation']);
            unset($data['inactive']);
	    	unset($data['role_id']);

	    	$user = User::create($data);
            switch ($role_id) {
                case 1:
                    $user->assignAdminRole();
                    break;
                case 2:
                    $user->assignManagerRole();
                    break;
                case 3:
                    $user->assignMemberRole();
                    break;
                default:
                    $user->assignMemberRole();
                    break;
            }

	    	DB::commit();

	    	return response()->json(['success'=>true, 'message'=>trans('messages.create_success')]);
    	} catch (Exception $e) {
    		DB::rollBack();
    	}
    	
    	return response()->json(['success'=>false, 'message'=>trans('messages.has_error')]);
    }

    public function update(Request $request, $id)
    {
        $out = array('success'=>false, 'message'=>null, 'errors'=>array());
    	$data = $request->all();
    	$user = User::find($id);

        // CHECK VALIDATE
        if (empty($data['id'])) {
            $out['errors']['params.id'] = trans('validation.required', ['attribute'=>'担当者ID']);
        }
        if (!empty($data['id'])) {
            if (!is_numeric($data['id'])) {
                $out['errors']['params.id'] = trans('validation.numeric', ['attribute'=>'担当者ID']);
            }
            if (strlen($data['id']) > 4) {
                $out['errors']['params.id'] = trans('validation.max.string', ['attribute'=>'担当者ID', 'max'=>'4']);
            }
        }
        if (empty($data['email'])) {
            $out['errors']['params.email'] = trans('validation.required', ['attribute'=>'メールアドレス']);
        }
        if (!empty($data['email'])) {
            $checkEmailExists = User::where('email', $data['email'])->first();
            if (isset($checkEmailExists) && $checkEmailExists->id != $data['id']) {
                $out['errors']['params.email'] = trans('validation.unique', ['attribute'=>'メールアドレス']);
            }
        }
        if (empty($data['occupation_id']) || $data['occupation_id'] == 0) {
            $out['errors']['params.occupation_id'] = trans('validation.required', ['attribute'=>'支店']);
        }
        if ($data['is_change_pass'] == 'true') {
            if (empty($data['password'])) {
                $out['errors']['params.password'] = trans('validation.required', ['attribute'=>'パスワード']);
            }
            if (!empty($data['password'])) {
                if ($data['password'] != $data['password_confirmation']) {
                    $out['errors']['params.password_confirmation'] = trans('validation.same', ['attribute'=>'パスワード', 'other'=>'パスワード確認']);
                }
            }
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        if (empty($data['role_id'])) {
            $out['errors']['params.role_id'] = trans('validation.required', ['attribute'=>'権限']);
        }
        if (count($out['errors']) > 0) {
            return response()->json($out, 200);
        }
        // END CHECK VALIDATE

        try {
            DB::beginTransaction();
            $role_id = $data['role_id'];
            $data['activated'] = !$data['inactive'];
            unset($data['inactive']);
        	unset($data['is_change_pass']);
            unset($data['password_confirmation']);
        	unset($data['role_id']);
        	$user->update($data);
            $user->revokeRole();
            switch ($role_id) {
                case 1:
                    $user->assignAdminRole();
                    break;
                case 2:
                    $user->assignManagerRole();
                    break;
                case 3:
                    $user->assignMemberRole();
                    break;
                default:
                    $user->assignMemberRole();
                    break;
            }
            DB::commit();

            // UPDATE ROW DATATABLE
            $user = $user->toArray();
            $occupation = Occupation::find($user['occupation_id']);
            $role = Role::find($role_id);
            $user['occupation_name'] = $occupation->name;
            $user['role_name'] = $role->name;
            
            return response()->json(['success'=>true, 'message'=>trans('messages.update_success'), 'user'=>$user]);
        } catch (Exception $e) {
            DB::rollBack();
        }
        return response()->json(['success'=>false, 'message'=>trans('messages.has_error')]);
    }

    public function destroy($id)
    {
    	User::destroy($id);
    	return response()->json(['success'=>true, 'message'=>trans('messages.delete_success')]);
    }

    public function reloadUserList(Request $request)
    {
        $input = $request->all();
        // DB::enableQueryLog();
        $query = DB::table('users');

        if (!$input['inactive']) {
            $query->where('activated', 1);
        }
        if (!empty($input['occupation_id']) && $input['occupation_id'] != '0') {
            $query->where('occupation_id', $input['occupation_id']);
        }

        $userList = $query->selectRaw('id, name')->get();
        // dd(DB::getQueryLog());
        return response()->json(['success'=>true, 'user_list'=>$userList]);
    }
}
