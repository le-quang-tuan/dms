<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\Tenement;
use DB;
use yajra\Datatables\Datatables;
use Auth;

class GroupController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('tech.group');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() { 
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        //dd(123);
        $tenement_group = DB::table('tenement_equipment_groups')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('id', 'asc');

        return Datatables::of($tenement_group)
                ->addColumn('action', function ($tenement_group) {
                    return '<a href="group/'.$tenement_group->id.'" class="btn btn-xs btn-primary">Chọn</a>';                    
                })
                ->make(true);        
    }        
}