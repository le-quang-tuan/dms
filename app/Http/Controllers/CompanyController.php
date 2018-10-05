<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\Company;
use DB;
use yajra\Datatables\Datatables;
use Auth;

class CompanyController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        if(Auth::user()->confirmed == 0) {
            return redirect("/home");
        }
        
        return view('company.company');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {        
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $companies = DB::table('companyinfo')
            ->join('areas', 'areas.id', '=', 'companyinfo.areaid')
            ->select([  'companyinfo.id as id', 
                        'companyinfo.name as name', 
                        'companyinfo.contactname as contactname',
                        'companyinfo.address as address',
                        'companyinfo.tel as tel',
                        DB::raw("CASE companyinfo.contractrate WHEN 1 THEN 'yes' ELSE 'no' END AS 'contractrate'"),
                        'companyinfo.note as note', 
                        'areas.name as areaname'
            ])
            ->where('companyinfo.activation', 1)
            ->where('companyinfo.id', '<>' , 0)
            ->orderBy('companyinfo.name', 'desc');

        return Datatables::of($companies)
                ->addColumn('action', function ($companies) {
                    return '<a href="company/detail/'.$companies->id.'" class="btn btn-xs btn-primary" target="_blank">Choose</a>';                    
                })
                ->addColumn('elec', function ($companies) {
                    return '<a href="company/detail/'.$companies->id.'" class="btn btn-xs btn-primary" target="_blank">Choose</a>';                    
                })
                ->addColumn('water', function ($companies) {
                    return '<a href="company/detail/'.$companies->id.'" class="btn btn-xs btn-primary" target="_blank">Choose</a>';                    
                })
                ->addColumn('gas', function ($companies) {
                    return '<a href="company/detail/'.$companies->id.'" class="btn btn-xs btn-primary" target="_blank">Choose</a>';                    
                })
                ->addColumn('parking', function ($companies) {
                    return '<a href="company/detail/'.$companies->id.'" class="btn btn-xs btn-primary" target="_blank">Choose</a>';                    
                })
                ->make(true);        
    }        
}