<?php

namespace App\Http\Controllers\Tenement;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tenement;
use App\Models\User;
use DB;
use yajra\Datatables\Datatables;
use Auth;

class WaterController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        $tenement_id = Auth::user()->tenement_id;

        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }

        return view('tenement.water');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {        
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $tenement_water_types = DB::table('tenement_water_types')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('id', 'asc');

        return Datatables::of($tenement_water_types)
                ->addColumn('action', function ($tenement_water_types) {
                    return '<a href="water/'.$tenement_water_types->id.'" class="btn btn-xs btn-primary">Chọn</a>';                    
                })            
                ->make(true);        
    }        
}