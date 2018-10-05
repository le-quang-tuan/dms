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

class ScheduleController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function cal() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }

        $tenement_equipments = DB::table('tenement_equipments as b')
                    ->where('tenement_id', 1)
                    ->orderBy('equipment_group_id', 'equipment_code', 'desc')
                    ->selectRaw("
                        id,
                        name
                    ")->get();

        $mst_maintenance_items = DB::table('mst_maintenance_items as b')
                    ->where('activation', 1)->get();

        return view('tech.schedulecal', compact('tenement_equipments', 'mst_maintenance_items'));
    }

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex($id) {
        $tenement_id = Auth::user()->tenement_id;

        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }

        $tenementEquipment = DB::table('tenement_equipments')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '=' , $id)
            ->where('tenement_id', '=', $tenement_id)
            ->orderBy('id', 'asc')->get();
        
        $tenement_producers = DB::table("tenement_producers")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        $tenement_equipment_groups = DB::table("tenement_equipment_groups")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        return view('tech.schedule', [
            'id'=>$id, 
            'tenementEquipment'=>$tenementEquipment[0],
            'tenement_producers'=>$tenement_producers,
            'tenement_equipment_groups'=>$tenement_equipment_groups]);
        // return view('tech.schedule', compact('id', 'tenementEquipment'));
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($id) { 
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        //dd(123);
        $tenement_equipment_maintenance = DB::table('tenement_equipment_maintenance')
            ->select(['*'])
            ->where('activation', 1)
            ->where('equipment_id', '=' , $id)
            ->orderBy('plan_date', 'desc');

        return Datatables::of($tenement_equipment_maintenance)
            ->addColumn('action', function ($tenement_equipment_maintenance) {
                return '<a href="detail/'.$tenement_equipment_maintenance->id.'" class="btn btn-xs btn-primary">Chọn</a>';
            })
            ->make(true);        
    }

    public function getScheduleDetail($id)
    {
        $tenement_id = Auth::user()->tenement_id;

        $schedules = DB::table('tenement_equipment_maintenance as a')
                ->leftJoin('tenement_equipments as b', 'b.id', '=', 'a.equipment_id')
                ->where('b.tenement_id', '=', $tenement_id)
                ->where('a.activation', '=', 1)
                ->where('b.activation', '=', 1)
                ->where('a.id', $id)
                ->selectRaw('
                    a.id,
                    b.name as name,
                    a.plan_date,
                    a.plan_description,
                    a.plan_company_execute,
                    a.plan_for,
                    a.plan_start_time,
                    a.plan_end_time,
                    report_date, 
                    report_start_time,
                    report_end_time,
                    report_description,
                    report_for,
                    report_company_execute,
                    category1_id,
                    category1_note,
                    category2_id,
                    category2_note,
                    category3_id,
                    category3_note,
                    note
                ')->get();
        
        $mst_maintenance_items = DB::table('mst_maintenance_items as b')
                    ->where('activation', 1)->get();

        return view('tech.scheduledetail', [
            'schedules'=>$schedules[0], 'mst_maintenance_items'=>$mst_maintenance_items]);
    }

    public function getSchedule(Request $request, $user_id)
    {
        $tenement_id = Auth::user()->tenement_id;

        $schedules = DB::table('tenement_equipment_maintenance as a')
                ->leftJoin('tenement_equipments as b', 'b.id', '=', 'a.equipment_id')
                ->where('b.tenement_id', '=', $tenement_id)
                ->where('a.activation', '=', 1)
                ->where('b.activation', '=', 1)
                ->whereBetween('a.plan_date', [$request->start, $request->end])
                ->selectRaw('
                    a.id,
                    b.name as name,
                    a.plan_date,
                    a.plan_start_time,
                    a.plan_end_time,
                    case when report_date = "0000-00-00" then plan_date else report_date end as report_date, 
                    IFNULL(report_start_time, plan_start_time) as report_start_time,
                    IFNULL(report_end_time, plan_end_time) as report_end_time,
                    IFNULL(report_description, plan_description) as  report_description,
                    IFNULL(report_for, plan_for) as report_for,
                    IFNULL(report_company_execute, plan_company_execute) as report_company_execute,
                    category1_id,
                    category1_note,
                    category2_id,
                    category2_note,
                    category3_id,
                    category3_note,
                    note
                ')->get();

        $data = array();


        $data = array();
        foreach ($schedules as $s) {
            $start  = $s->plan_date . 'T' . $s->plan_start_time;
            $end    = $s->plan_date . 'T' . $s->plan_end_time;

            $data[] = array(
                'id'        => $s->id,
                'title'     => ''.$s->name,
                'start'     => $start,
                'end'       => $end,
                'report_date' => $s->report_date,
                'report_start_time' => $s->report_start_time,
                'report_end_time' => $s->report_end_time,
                'report_description' => $s->report_description,
                'report_for' => $s->report_for,
                'report_company_execute' => $s->report_company_execute,
                
                'category1_id'       => $s->category1_id,
                'category1_note'       => $s->category1_note,
                'category2_id'       => $s->category2_id,
                'category2_note'       => $s->category2_note,
                'category3_id'       => $s->category3_id,
                'category3_note'       => $s->category3_note,
                'note'       => $s->note
            );
        }

        return response()->json($data);
    }

    public function getEquipment()
    {
        $tenement_id = Auth::user()->tenement_id;

        $tenement_equipments = DB::table('tenement_equipments as b')
                    ->where('tenement_id', $tenement_id)
                    ->orderBy('equipment_group_id, equipment_code', 'desc')
                    ->selectRaw("
                        name
                    ")->get();

        return response()->json(['success'=>true, 'tenement_equipments'=>$tenement_equipments]);
    }
}