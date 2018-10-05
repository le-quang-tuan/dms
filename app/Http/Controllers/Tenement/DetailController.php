<?php

namespace App\Http\Controllers\Tenement;

use Illuminate\Http\Request;
use Kitano;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tenement;
// use App\Models\Permission;
//use App\Models\Role;
use App\Models\User;
use App\Models\Area;
use DB;
use File;
use Validator;
use Auth;
use App\LaraBase\NumberUtil;
use Kodeine\Acl\Models\Eloquent\Permission;
use Kodeine\Acl\Models\Eloquent\Role;

class DetailController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //Laragrowl::message('Your stuff has been stored', 'success');

// Tạo Role
// $roleAdmin = new Role();
// $roleAdmin->name = 'Administrator';
// $roleAdmin->slug = 'administrator';
// $roleAdmin->description = 'manage administration privileges';
// $roleAdmin->save();

// $roleModerator = new Role();
// $roleModerator->name = 'Moderator';
// $roleModerator->slug = 'moderator';
// $roleModerator->description = 'manage moderator privileges';
// $roleModerator->save();

// assign role cho User
// $user = User::find(5);
// $user->assignRole(5);
// $user->assignRole(6);

// Get tất cả roles của Users
// $user = User::find(5);
// dd($user->getRoles());

// $permission = new Permission();
// $permPost = $permission->create([ 
//     'name'        => 'post',
//     'slug'        => [          // pass an array of permissions.
//         'create'     => true,
//         'view'       => true,
//         'update'     => true,
//         'delete'     => true,
//     ],
//     'description' => 'manage post permissions'
// ]);
// $permUser->description = 'manage user permissions';
// $permUser->save();

// $permUser = Permission::first(); // administrator
// //dd($permUser);
// $roleAdmin = Role::first(); // administrator
// // permission as an object
// $roleAdmin->assignPermission($permUser);

// dd($roleAdmin->getPermissions());

// $admin = Role::find(5); // administrator
// $admin->assignPermission($permPost);


//dd($admin->can('a.post|delete.post'));

// $user = User::find(5);

// create crud permissions
// create.user, view.user, update.user, delete.user
// returns false if alias exists.
// $user->addPermission('post');        

// dd($user->is('administrator'));
// dd($user->isModerator());
// dd($user->can('create.post'));
// dd($user->getPermissions());
// $permission = new Permission();
// $permPost = $permission->create([ 
//     'name'        => 'post',
//     'slug'        => [          // pass an array of permissions.
//         'create'     => true,
//         'view'       => true,
//         'update'     => true,
//         'delete'     => true,
//     ],
//     'description' => 'manage post permissions'
// ]);

        $id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/index");
        }

        //check CustomerId is exist or not.
        $existedTenement = Tenement::where('id', $id)->count();

        if($existedTenement == '0'){
            return \Response::view('errors.404', array(), 404);
            //return back()->withInput()->withErrors(['existedCustomer' => 'Customer is not exist ! Please confirm with Administrator. Thank you.']);
        }

        $tenement = Tenement::where('id',$id)->where('activation',1)->orderBy('name', 'asc')->get();
// dd($tenement);
        // $branch = Area::where('activation',1)->get();        
        return View('tenement.tenementdetail', [ 'tenement'=>$tenement[0] ]);
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
        $utils = new NumberUtil();
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            'name.required' => 'Tên Dự Án/Khu Căn Hộ chưa được nhập.',
            'tenement_code.required' => 'Mã Dự Án chưa được nhập.',
            'address.required' => 'Địa chỉ chưa được nhập.',
            'manager_fee.required' => 'Phí Quản Lý chưa được nhập.',
            'loss_avg.required' => 'Hao hụt chia sẻ(Nước) chưa được nhập.',
            'loss_avg_elec.required' => 'Hao hụt chia sẻ(Điện) chưa được nhập.',
            'loss_avg_gas.required' => 'Hao hụt chia sẻ(Gas) chưa được nhập.',
        ];

        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
                    'tenement_code' => 'required',
                    'address' => 'required',
                    'manager_fee' => 'required',
                    'loss_avg' => 'required',
                    'loss_avg_elec' => 'required',
                    'loss_avg_gas' => 'required',
        ], $messages);

        if ($v->fails()) {
            // If validation falis redirect back to login.
            return redirect('tenement/detail')->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {
                //check Tenement Id is exist or not.
                $existedTenement = Tenement::where('id',$request->tenement_id)->where('activation',1)->count();

                if($existedTenement == 0){
                    return \Response::view('errors.404', array(), 404);
                    //return back()->withInput()->withErrors(['existedTenement' => 'Tenement is not exist ! Please confirm with Administrator. Thank you.']);
                }  

                $tenement = Tenement::find($request->tenement_id);
                $tenement->name = $request->name;

                $tenement->name = $request->name;
                $tenement->address = $request->address;
                $tenement->manager_fee = $utils->number($request->manager_fee);
                $tenement->loss_avg = $utils->number($request->loss_avg);
                $tenement->bank = $request->bank;
                $tenement->account = $request->account;
                $tenement->account_name = $request->account_name;
                $tenement->branch = $request->branch;
                // $tenement->office = $request->office;
                // $tenement->office_address = $request->office_address;
                // $tenement->office_phone = $request->office_phone;
                $tenement->parkingfee_calculate_type = $request->parkingfee_calculate_type;
                $tenement->loss_avg_elec = $utils->number($request->loss_avg_elec);
                $tenement->managerfee_calculate_type = $request->managerfee_calculate_type;
                $tenement->loss_avg_gas = $utils->number($request->loss_avg_gas);
                $tenement->contact = $request->contact;
                $tenement->caption1 = $request->caption1;
                $tenement->caption2 = $request->caption2;
                $tenement->caption3 = $request->caption3;
                $tenement->manager_company = $request->manager_company;
                $tenement->managerment = $request->managerment;
                $tenement->gas_unit = $request->gas_unit;
                $tenement->comment = $request->comment;
                $tenement->updated_by = Auth::user()->id;

                $tenement->save();

                DB::commit();
                return back()->with('tenement-alert-success','Thông tin đã được cập nhật!');
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
        $tenements = Tenement::where('activation', 1)->get();
        return view('tenement.tenementcreate', compact('tenements'));
    }

    public function store(Request $request)
    {
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        // Applying validation rules.
        $v = Validator::make($request->all(), [
                    'name' => 'required',
                    'tenement_code' => 'required',
                    'address' => 'required',
                    'manager_fee' => 'required',
                    'loss_avg' => 'required'
        ]);

        if ($v->fails()) {
            // If validation falis redirect back to login.
            return redirect('tenement/create/')->withInput()->withErrors($v);
        }

        Tenement::create([
            'name'   =>  $request->input('name'),
            'tenement_code'   =>  $request->input('tenement_code'),
            'address' => $request->input('address'),
            'manager_fee' => $request->input('manager_fee'),
            'loss_avg' => $request->input('loss_avg'),
            'bank' => $request->input('bank'),
            'account' => $request->input('account'),
            'account_name' => $request->input('account_name'),
            'branch' => $request->input('branch'),
            // 'office' => $request->input('office'),
            // 'office_address' => $request->input('office_address'),
            // 'office_phone' => $request->input('office_phone'),
            'parkingfee_calculate_type' => $request->input('parkingfee_calculate_type'),
            'loss_avg_elec' => $request->input('loss_avg_elec'),
            'managerfee_calculate_type' => $request->input('managerfee_calculate_type'),
            'contact' => $request>input('contact'),
            'caption1' => $request>input('caption1'),
            'caption2' => $request>input('caption2'),
            'caption3' => $request>input('caption3'),
            'manager_company' => $request>input('manager_company'),
            'managerment' => $request>input('managerment'),
            'loss_avg_gas' => $request->input('loss_avg_gas'),
            'gas_unit' => $request->input('gas_unit'),
            'comment' => $request->input('comment')
        ]);

        return redirect()->route('Tenement');
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
            $existedTenement = Tenement::where('id',$request->tenement_id)->where('activation',1)->count();

            if($existedTenement == 0){
                return \Response::view('errors.404', array(), 404);
                //return back()->withInput()->withErrors(['existedTenement' => 'Tenement is not exist ! Please confirm with Administrator. Thank you.']);
            }  

            $tenement = Tenement::find($request->tenement_id);
            $tenement->activation = 0;

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
