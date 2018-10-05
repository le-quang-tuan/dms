<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use Kitano;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TenementDailyActivity;
use App\Models\Area;
use DB;
use File;
use Validator;
use Auth;
use App\LaraBase\NumberUtil;
use DateTime;

class DailyActivityController extends Controller {

    /**
     * Display a listing of the esource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $mst_daily_activity_types = DB::table("mst_daily_activity_types")
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        return View('tech.dailyactivity', [ 
            'mst_daily_activity_types'=>$mst_daily_activity_types]);
    }

    /**
     * upload passport photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request) {
        //Check period user
        $id = Auth::user()->tenement_id;
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        if ($request->id != '' || $request->id != null)
        {
            $this->update($request);
        }
        else
        {
            $this->create($request);
        }

        return back()->with('tenementDailyActiVity-alert-success','Equipment is updated !');
        // $mst_daily_activity_types = DB::table('mst_daily_activity_types as b')
        //             ->where('activation', 1)->get();

        // return view('tech.dailyactivitycal', compact('mst_daily_activity_types'));
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
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            'daily_date.required' => 'Ngày thực hiện chưa được nhập.',
        ];
        // Applying validation rules.
        $v = Validator::make($request->all(), [
            'daily_date' => 'required',
        ], $messages);

        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        } else {
            DB::beginTransaction();
            try {
                //check Tenement Id is exist or not.
                $existedTenementDailyactiVity = TenementDailyActivity::where('id', $request->id)
                    ->where('activation',1)
                    ->count();

                if($existedTenementDailyactiVity == 0){
                    return \Response::view('errors.404', array(), 404);
                }  

                $tenementDailyactiVity = TenementDailyActivity::find($request->id);
                
                $date = DateTime::createFromFormat('d/m/Y', $request->daily_date);

                $tenementDailyactiVity->name = $request->name;                
                $tenementDailyactiVity->daily_date = $date;                
                $tenementDailyactiVity->start_time = $request->start_time;
                $tenementDailyactiVity->end_time = $request->end_time;

                $tenementDailyactiVity->charge_for = $request->charge_for;
                $tenementDailyactiVity->description = $request->description;
                $tenementDailyactiVity->company_execute = $request->company_execute;
                $tenementDailyactiVity->note = $request->note;

                $tenementDailyactiVity->category1_id = $request->category1;
                $tenementDailyactiVity->category1_note = $request->note1;

                $tenementDailyactiVity->category2_id = $request->category2;
                $tenementDailyactiVity->category2_note = $request->note2;

                $tenementDailyactiVity->category3_id = $request->category3;
                $tenementDailyactiVity->category3_note = $request->note3;

                $tenementDailyactiVity->save();

                DB::commit();
                return back()->with('tenementDailyActiVity-alert-success','Equipment is updated !');
            } catch (\Exception $e) {
                DB::rollback();

                //something went wrong
                return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
            }
        } 
    }

    public function create(Request $request)
    {
        $id = Auth::user()->tenement_id;
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        
        $rules = [
            'name' => 'required',
            'daily_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'required'
        ];

        $tempName = 'name';
        $tempDaily_date = 'daily_date';
        $tempStart_time = 'start_time';
        $tempEnd_time = 'end_time';
        $tempDescription = 'description';

        $messages = [
            $tempName . '.required' => 'Nhật ký kỹ thuật chưa được nhập.',
            $tempDaily_date . '.required' => 'Ngày thực hiện chưa được nhập.',
            $tempStart_time . '.required' => 'Thời gian bắt đầu chưa được nhập.',
            $tempEnd_time . '.required' => 'Thời gian kết thúc chưa được nhập.',
            $tempDescription . '.required' => 'Hạng mục chưa được nhập.'
        ];

        $v = Validator::make($request->all(), $rules, $messages);

        if($v->fails()){
            return back()->withInput()->withErrors($v);
        }
        try {
            DB::beginTransaction();

            $tempName = 'name';
            $tempDaily_date = 'daily_date';
            $tempStart_time = 'start_time';
            $tempEnd_time = 'end_time';
            $tempDescription = 'description';
            $tempCompany_execute = 'company_execute';
            $tempCharge_for = 'charge_for';
            $tempNote = 'note';
            $tempCategory1 = 'category1';
            $tempCategory1_note = 'note1';
            $tempCategory2 = 'category2';
            $tempCategory2_note = 'note2';
            $tempCategory3 = 'category3';
            $tempCategory3_note = 'note3';

            $date = DateTime::createFromFormat('d/m/Y', $request->input($tempDaily_date));

            TenementDailyActivity::create([
                'name'    => $request->input($tempName),
                'daily_date'    => $date->format('Y-m-d'),
                'start_time' => $request->input($tempStart_time),
                'end_time'   => $request->input($tempEnd_time),
                'description' => $request->input($tempDescription),
                'company_execute'   => $request->input($tempCompany_execute),
                'charge_for'   => $request->input($tempCharge_for),
                'note'   => $request->input($tempNote),
                'tenement_id' => $id,
                'activation' => 1,
                'category1_id'   => $request->input($tempCategory1),
                'category1_note'   => $request->input($tempCategory1_note),

                'category2_id'   => $request->input($tempCategory2),
                'category2_note'   => $request->input($tempCategory2_note),

                'category3_id'   => $request->input($tempCategory3),
                'category3_note'   => $request->input($tempCategory3_note)
            ]);
            DB::commit();

            // $mst_daily_activity_types = DB::table("mst_daily_activity_types")
            // ->where('activation', '=', 1)
            // ->orderBy('id')->get();

            // return View('tech.dailyactivity', [ 
            //     'mst_daily_activity_types'=>$mst_daily_activity_types]);
        } catch (Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  
    }

    public function store(Request $request)
    {
        $id = Auth::user()->tenement_id;
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        
        $request->session()->put('counter', count($request->counter));

        if( isset($request->counter) ){
            $rules = array(
            'name1' => 'required',
            'daily_date1' => 'required',
            'start_time1' => 'required',
            'end_time1' => 'required',
            'description1' => 'required');

            $items = array();

            for($i=0;$i<count($request->counter);$i++){
                $tempName = '';
                $tempDaily_date = '';
                $tempStart_time = '';
                $tempEnd_time = '';
                $tempDescription = '';

                $step = $i+1;
                $tempName = 'name' . $step;
                $tempDaily_date = 'daily_date' . $step;
                $tempStart_time = 'start_time' . $step;
                $tempEnd_time = 'end_time' . $step;
                $tempDescription = 'description' . $step;

                $messages[$tempName . '.required']  = 'Nhật ký kỹ thuật (' . $step . ') chưa được nhập.';
                $messages[$tempDaily_date . '.required']  = 'Ngày thực hiện (' . $step . ') chưa được nhập.';
                $messages[$tempStart_time . '.required']  = 'Thời gian bắt đầu (' . $step . ') chưa được nhập.';
                $messages[$tempEnd_time . '.required']  = 'Thời gian kết thúc (' . $step . ') chưa được nhập.';
                $messages[$tempDescription . '.required']  = 'Hạng mục (' . $step . ') chưa được nhập.';

                $rules[$tempName] = 'required';
                $rules[$tempDaily_date] = 'required';
                $rules[$tempStart_time] = 'required';
                $rules[$tempEnd_time] = 'required';
                $rules[$tempDescription] = 'required';
            }
            $v = Validator::make($request->all(), $rules, $messages);

            if($v->fails()){
                return back()->withInput()->withErrors($v);
            }
        }

        DB::beginTransaction();
        try {
            for($i=0;$i<count($request->counter);$i++){
                $tempName = '';
                $tempDaily_date = '';
                $tempStart_time = '';
                $tempEnd_time = '';
                $tempDescription = '';
                $tempCharge_for = '';
                $tempCompany_execute = '';
                $tempNote = '';
                $tempCategory1_id = '';
                $tempCategory1_note = '';
                $tempCategory2_id = '';
                $tempCategory2_note = '';
                $tempCategory3_id = '';
                $tempCategory3_note = '';

                $step = $i+1;    
                $tempName = 'name' . $step;
                $tempDaily_date = 'daily_date' . $step;
                $tempStart_time = 'start_time' . $step;
                $tempEnd_time = 'end_time' . $step;
                $tempDescription = 'description' . $step;
                $tempCompany_execute = 'company_execute' . $step;
                $tempCharge_for = 'charge_for' . $step;
                $tempNote = 'note' . $step;
                $tempCategory1_id = 'category1_id' . $step;
                $tempCategory1_note = 'category1_note' . $step;
                $tempCategory2_id = 'category2_id' . $step;
                $tempCategory2_note = 'category2_note' . $step;
                $tempCategory3_id = 'category3_id' . $step;
                $tempCategory3_note = 'category3_note' . $step;

                $date = DateTime::createFromFormat('d/m/Y', $request->input($tempDaily_date));

                TenementDailyActivity::create([
                    'name'    => $request->input($tempName),
                    'daily_date'    => $date->format('Y-m-d'),
                    'start_time' => $request->input($tempStart_time),
                    'end_time'   => $request->input($tempEnd_time),
                    'description' => $request->input($tempDescription),
                    'company_execute'   => $request->input($tempCompany_execute),
                    'charge_for'   => $request->input($tempCharge_for),
                    'note'   => $request->input($tempNote),
                    'tenement_id' => $id,
                    'activation' => 1,
                    'category1_id'   => $request->input($tempCategory1_id),
                    'category1_note'   => $request->input($tempCategory1_note),

                    'category2_id'   => $request->input($tempCategory2_id),
                    'category2_note'   => $request->input($tempCategory2_note),

                    'category3_id'   => $request->input($tempCategory3_id),
                    'category3_note'   => $request->input($tempCategory2_note)
                ]);
            }
            DB::commit();
            $request->session()->forget('counter');

            return redirect()->route('DailyActivity');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  

        return redirect()->route('DailyActivity');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request) {
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        DB::beginTransaction();
        try {
            //check Tenement Id is exist or not.
            $existedTenementDailyActivity = TenementDailyActivity::where('id', $request->id)
                ->where('activation',1)
                ->count();

            if($existedTenementDailyActivity == 0){
                return \Response::view('errors.404', array(), 404);
            }  

            $tenementDailyActivity = TenementDailyActivity::find($request->id);
            
            $tenementDailyActivity->activation = 0;                

            $tenementDailyActivity->save();

            DB::commit();
            return redirect("tech/schedule/" . $TenementDailyActivity->equipment_id);
            //return redirect()->route('ScheduleCal');
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }
    }

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function cal() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }

        $mst_daily_activity_types = DB::table('mst_daily_activity_types as b')
                    ->where('activation', 1)->get();

        return view('tech.dailyactivitycal', compact('mst_daily_activity_types'));
    }

    public function getDailyActivity(Request $request)
    {
        $tenement_id = Auth::user()->tenement_id;
        
        $schedules = DB::table('tenement_daily_activity')
                ->where('tenement_id', '=', $tenement_id)
                ->where('activation', '=', 1)
                ->whereBetween('daily_date', [$request->start, $request->end])
                ->selectRaw('
                    id,
                    name,
                    daily_date,
                    start_time,
                    end_time,
                    description,
                    charge_for,
                    company_execute,
                    category1_id,
                    category1_note,
                    category2_id,
                    category2_note,
                    category3_id,
                    category3_note,
                    note
                ')->get();
        $data = array();

        foreach ($schedules as $s) {
            $start  = $s->daily_date . 'T' . $s->start_time;
            $end    = $s->daily_date . 'T' . $s->end_time;

            $data[] = array(
                'id'        => $s->id,
                'title'     => ''. $s->name,
                'start'     => $start,
                'end'       => $end,
                'daily_date' => $s->daily_date,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'description' => $s->description,
                'charge_for' => $s->charge_for,
                'company_execute' => $s->company_execute,
                
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
}
