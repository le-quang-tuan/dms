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

class EquipmentController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('tech.equipment');
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
        $tenement_equipments = DB::table('tenement_equipments')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('id', 'asc');

        return Datatables::of($tenement_equipments)
                ->addColumn('action', function ($tenement_equipments) {
                    return '<a href="equipment/'.$tenement_equipments->id.'" class="btn btn-xs btn-primary">Chọn</a>';                    
                })
                ->addColumn('maintenance', function ($tenement_equipments) {
                    return '<a href="equipmainte/'.$tenement_equipments->id.'" class="btn btn-xs btn-primary">Lên Kế Hoạch</a>';                    
                })
                ->make(true);        
    }        
}