<?php

namespace App\Http\Controllers\Flat;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tenement;
use App\Models\User;
use App\Models\TenementFlat;
use DB;
use yajra\Datatables\Datatables;
use Auth;

class ResidentController extends Controller {

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

        $tenement = Tenement::where('id', $tenement_id)->where('activation',1)->get();
        $userDetailInfo = User::where('id', Auth::user()->id)->get();
        
        return view('flat.flat',  ['tenement'=>$tenement[0], 'userDetailInfo'=>$userDetailInfo[0]]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData() {        
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd(123);
        $tenementFlat = DB::table('tenement_flats as a')
            ->leftjoin('tenement_elec_types as b', function($join)
            {
                $join->on('a.tenement_id', '=', 'b.tenement_id');
                $join->on('a.elec_type_id', '=', 'b.id');
            })
            ->leftjoin('tenement_water_types as c', function($join)
            {
                $join->on('a.tenement_id', '=', 'c.tenement_id');
                $join->on('a.water_type_id', '=', 'c.id');
            })
            ->leftjoin('tenement_gas_types as d', function($join)
            {
                $join->on('a.tenement_id', '=', 'd.tenement_id');
                $join->on('a.gas_type_id', '=', 'd.id');
            })
            ->select(['a.*','b.elec_type','c.water_type','d.gas_type'])
            ->where('a.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc');
        return Datatables::of($tenementFlat)
                ->addColumn('action', function ($tenementFlat) {
                    return '<a style="text-align: center;" href="flat/detail/'. $tenementFlat->id .'" >' . $tenementFlat->flat_code . '</a>';                    
                })
                ->addColumn('elec', function ($tenementFlat) {
                    return '<a href="flat/elec/'. $tenementFlat->id . '" class="btn btn-xs btn-primary" target="_blank">Chỉ số sử dụng</a>';                    
                })
                ->addColumn('water', function ($tenementFlat) {
                    return '<a href="flat/water/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số sử dụng</a>';                    
                })
                ->addColumn('gas', function ($tenementFlat) {
                    return '<a href="flat/gas/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số sử dụng</a>';                    
                })
                ->addColumn('vehicle', function ($tenementFlat) {
                    return '<a href="flat/vehicle/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Đăng ký</a>';                    
                })
                ->addColumn('service', function ($tenementFlat) {
                    return '<a href="flat/service/'. $tenementFlat->id .'" class="btn btn-xs btn-primary" target="_blank">Phát sinh</a>';                    
                })
                ->make(true);        
    }        
}