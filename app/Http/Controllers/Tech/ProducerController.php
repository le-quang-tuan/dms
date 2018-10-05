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

class ProducerController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('tech.producer');
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
        $tenement_producer = DB::table('tenement_producers')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('id', 'asc');

        return Datatables::of($tenement_producer)
                ->addColumn('action', function ($tenement_producer) {
                    return '<a href="producer/'.$tenement_producer->id.'" class="btn btn-xs btn-primary">Chọn</a>';                    
                })
                ->make(true);        
    }        
}