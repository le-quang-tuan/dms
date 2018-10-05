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

class TenementController extends Controller {
    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        $tenement_id = Auth::user()->tenement_id;

        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/auth/login");
        }
        
        return view('tenement.tenement');
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
        $tenements = DB::table('tenements')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            ->where('id', '=', Auth::user()->tenement_id)
            ->orderBy('tenement_code', 'asc');

        return Datatables::of($tenements)
                ->addColumn('action', function ($tenements) {
                    return '<a href="tenement/detail" class="btn btn-xs btn-primary">Chọn</a>';                    
                })
                ->addColumn('elec', function ($tenements) {
                    return '<a href="tenement/elec" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                    
                })
                ->addColumn('water', function ($tenements) {
                    return '<a href="tenement/water" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                    
                })
                ->addColumn('gas', function ($tenements) {
                    return '<a href="tenement/gas" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                    
                })
                ->addColumn('parking', function ($tenements) {
                    return '<a href="tenement/parking" class="btn btn-xs btn-primary" target="_blank">Chọn</a>';                    
                })
                ->make(true);        
    }        
}