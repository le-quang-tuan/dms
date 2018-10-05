<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use yajra\Datatables\Datatables;
use App\Http\Controllers\SspController;
use Auth;
use App\Models\User;
use App\Models\Tenement;

class UserController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        if ($user->is('admin|manager|accountant'))
            return view('user.user');
        return redirect("/index");
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {
       // return Datatables::of(User::select('*'))->make(true);
        /*
        $users= User::all('id', 
                        'fullname',
                        'email', 
                        'username', 
                        'authorize', 
                        'tenementsid', 
                        'activation')
        ;*/
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        // dd($user->getRoles());

        $tenement = Tenement::where('id',Auth::user()->tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();
        $company_id = $tenement[0]->company_id;

        $users =  DB::table('users')
            ->leftjoin('tenements as b', function($join)
            {
                $join->on('b.id', '=', 'users.tenement_id');
                $join->on('b.activation', '=' , DB::raw('1'));
            }, 'left outer')
            ->leftjoin('company as c', function($join)
            {
                $join->on('b.id', '=', 'b.company_id');
                $join->on('b.activation', '=' , DB::raw('1'));
            }, 'left outer')
            ->leftjoin('roles as d', function($join)
            {
                $join->on('d.id', '=', 'users.role_id');
            }, 'left outer')
            ->select('users.id as id', 
                    'users.first_name as fullname',
                    'users.email as email', 
                    'users.username as username', 
                    'd.name AS authorize',
                    'b.name as tenement', 
                    'users.activation as activation')
            ->where(function($query) use ($user,$company_id)
            {
                if ($user->is('admin')){
                    $query->where('users.activation', '=','1');
                }
                else if ($user->is('manager')) {
                    $query->where('c.id', '=', $company_id);
                }
            });
        
        return Datatables::of($users)
                ->addColumn('action', function ($users) {
                    return '<a href="user/detail/'.$users->id.'" class="btn btn-xs btn-primary" target="_blank">Choose</a>';                    
                })
                ->make(true); 

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
