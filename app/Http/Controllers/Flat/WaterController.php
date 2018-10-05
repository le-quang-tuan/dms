<?php

namespace App\Http\Controllers\Flat;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TfWaterUsed;
use DB;
use yajra\Datatables\Datatables;
use Validator;
use Auth;
use Redirect;
use DateTime;
use App\LaraBase\NumberUtil;

class WaterController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('flat.flatwater');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($id) {        
        // dd("123 ");
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd("aaa");

        // dd(123);
        $TfWaterUsed = DB::table('tf_water_used')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            // ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->where('flat_id', '=', $id)
            ->orderBy('year_month', 'desc');

        // return View('Flat.flatwater', [ 'TfWaterUsed.data'=>$TfWaterUsed]);

        return Datatables::of($TfWaterUsed)
                ->addColumn('action', function ($TfWaterUsed) {
                    return '<button type="button" class="btn btn-primary btn-details" value="'. $TfWaterUsed->id .'" >Hủy</button>';
                })
                ->addColumn('water', function ($TfWaterUsed) {
                    return '<a href="flat/water/'. $TfWaterUsed->id . '" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('water', function ($TfWaterUsed) {
                    return '<a href="flat/water/'. $TfWaterUsed->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('gas', function ($TfWaterUsed) {
                    return '<a href="flat/gas/'. $TfWaterUsed->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('parking', function ($TfWaterUsed) {
                    return '<a href="flat/parking/'. $TfWaterUsed->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->addColumn('service', function ($TfWaterUsed) {
                    return '<a href="flat/service/'. $TfWaterUsed->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->make(true);        
    }  

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$id) {
        $year_month = $request->input('year_month');

        if ($year_month == ''){
            $year_month = date('Ym');
        }

        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $TfWaterUsed = TfWaterUsed::where('flat_id',$id)
            ->where('activation',1)
            ->orderBy('year_month', 'asc')->first();
            // dd($TfWaterUsed);

        //dd($Tenement[0]);
        return View('flat.flatwater', [ 'TfWaterUsed'=>$id, 'year_month'=>$year_month]);
    }

    public function store(Request $request)
    {
        $id = Auth::user()->tenement_id;
        
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }

        $messages = [
            // 'old_index.required' => 'Chỉ số cũ: chưa nhập dữ liệu',
            'new_index.required' => 'Chỉ số mới: chưa nhập dữ liệu'
        ];

        $v = Validator::make($request->all(), [
            'year'  =>  'required',
            'month'  =>  'required',
            // 'old_index'  =>  'required',
            'new_index'  =>  'required'
        ], $messages);

        if($v->fails()){
            return Redirect::to('flat/water/' . $request->input('id'))
                ->withInput()->withErrors($v);
        }
        //Kiểm tra năm tháng tạo có = hiện tại hoặc nhỏ hơn 1 tháng ko
        $prev_month = date('Ym', strtotime(" -1 months"));
        $cur_month = date('Ym');

        $year_month = $request->input('year') . $request->input('month');

        $date_from = '';
        $date_to = '';

        if ('' != $request->date_from){
            $date_from = DateTime::createFromFormat('d/m/Y', $request->input('date_from'))->format('Ymd');
        }
        

        if ('' != $request->date_to){
            $date_to   = DateTime::createFromFormat('d/m/Y', $request->input('date_to'))->format('Ymd');
        }

        //Kiểm tra từ ngày < đến ngày
        if ($date_from != '' && $date_to != '' && $date_from > $date_to){
            return back()->withInput()->withErrors(['addFail' => 'Từ ngày phải <= đến ngày']);            
        }

        // if (!($year_month == $prev_month || 
        //     $year_month == $cur_month))
        //     return back()
        //             ->withInput()
        //             ->withErrors(['addFail' => 'Năm/Tháng tạo phải là trước 1 tháng hoặc bằng tháng hiện tại']);

        //Kiểm tra chỉ số từ < chỉ số mới
        $old_index = $request->old_index;
        $new_index = $request->new_index;

        // $prev_month = date_create($request->input('year') . '-' . $request->input('month') . '-01');
        // $prev_month = strtotime("-1 months", date($prev_month));

        $timestamp = strtotime($request->input('year') . '-' . $request->input('month') . '-01'.'-1 month');
        $prev_month = date('Ym', $timestamp);
        if ($old_index == ''){
            $pre_month_water = TfWaterUsed::where('year_month', $prev_month)
                ->where('activation',1)
                ->where('flat_id',$request->id)
                ->get();
            if (isset($pre_month_water)){
                $old_index = $pre_month_water[0]->new_index;
            }
        }

        if ($old_index > $new_index)
            return back()->withInput()->withErrors(['addFail' => 'Chỉ số từ phải <= Chỉ số mới']);
        
        //Kiểm tra đã tồn tại Năm tháng tạo hay chưa, nếu tồn tại thì update
        $existedYM = TfWaterUsed::where('year_month', $year_month)
                        ->where('activation',1)
                        ->where('flat_id',$request->id)
                        ->count();

        // if ($existedYM == 1)
        //     return back()->withInput()->withErrors(['addFail' => 'Tháng này đã được tạo trước đây']);
        $utils = new NumberUtil();
        
        DB::beginTransaction();
        try {
            TfWaterUsed::create([
                'year_month'   => $year_month,
                'date_from'   => $date_from,
                'date_to'   => $date_to,
                'old_index'   => $utils->number($old_index),
                'new_index'   => $utils->number($new_index),
                'comment'  =>  $request->comment,
                'flat_id'  =>  $request->id,
                'activation'  => 1,
                'used_deduct'  => $request->deduct,
                'prev_year_month_price'  => $request->prev_price_year . $request->prev_price_month,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::commit();
            return Redirect::to('flat/water/' . $request->input('id'));
        } catch (\Exception $e) {
            DB::rollback();

            //something went wrong
            return back()->withInput()->withErrors(['InsertFail' => $e->getMessage()]);
        }  
    }
    public function destroy($id)
    {
        $tenement_id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        
        DB::beginTransaction();
        try {
            $TfWaterUsedExits = DB::table("tenement_flats")
            ->join('tf_water_used', 'tenement_flats.id', '=', 'tf_water_used.flat_id')
            ->select('tenement_flats.*')
            ->where('tenement_flats.activation', '=', 1)
            ->where('tenement_flats.tenement_id', '=', $tenement_id)
            ->where('tf_water_used.id', '=', $id)->count();

            if($TfWaterUsedExits == 0){
                return Response::view('errors.404', array(), 404);
            }  

            $Used = TfWaterUsed::find($id);

            $Used->activation = 0;
            $Used->updated_by = Auth::user()->id;
            $Used->updated_at = date("Y-m-d H:i:s");               
            $Used->save();

            DB::commit();
            return ("ok");
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }
    }
    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getAllIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('flat.allflatwater');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allindex($year_month) {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        if ($year_month == 'detail')
            $year_month = date("Ym");
        //dd($Tenement[0]);
        return View('flat.allflatwater', [ 'year_month'=>$year_month]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allFlatData($year_month) {
        //dd(123);     
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd(123);
        $monthlyFeeFlat = DB::table('tenement_flats as a')
            ->leftjoin('tf_water_used as b', function($join) use ($year_month)
            {
                $join->on('a.id', '=', 'b.flat_id');
                $join->on('b.year_month', '=', DB::raw($year_month));
                $join->on('b.activation', '=' , DB::raw('1'));
            }, 'left outer')
            ->select(['a.id', 'a.address','a.flat_code', 'a.name','a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area','b.date_from','b.date_to','b.old_index','b.new_index','b.comment', DB::raw( "'" . substr($year_month,4,2) . '/' . substr($year_month,0,4) . "' as 'year_month'")
                ])
            ->where('a.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc');

        return Datatables::of($monthlyFeeFlat)
                ->addColumn('action', function ($monthlyFeeFlat) {
                    return '<a style="text-align: center;" href="flat/detail/'. $monthlyFeeFlat->id .'">' . $monthlyFeeFlat->flat_code .'</a>';                    
                })
                ->addColumn('change', function ($monthlyFeeFlat) use ($year_month){
                    return '<button type="button" 
                        flat_id="'. $monthlyFeeFlat->id .'" 
                        year_month="'. $year_month .'" 
                        old_index="'. $monthlyFeeFlat->old_index .'" 
                        new_index="'. $monthlyFeeFlat->new_index .'" 
                        date_from="'. $monthlyFeeFlat->date_from .'" 
                        date_to="'. $monthlyFeeFlat->date_to .'" 
                        comment="'. $monthlyFeeFlat->comment .'" 
                        address="'. $monthlyFeeFlat->address .'" 
                        name="'. $monthlyFeeFlat->name .'"  
                        class="btn btn-xs btn-primary">Cập nhật</button>';
                })
                ->make(true);        
    }     

    public function exex_change(Request $request)
    {
        //Check period user
        $tenement_id = Auth::user()->tenement_id;
        $year_month = $request->bill_year . $request->bill_month;
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }
        DB::beginTransaction();
        try {
            $utils = new NumberUtil();
            $TfWaterUsedExits = DB::table("tenement_flats")
                ->join('tf_water_used', 'tenement_flats.id', '=', 'tf_water_used.flat_id')
                ->select('tf_water_used.*')
                ->where('tenement_flats.activation', '=', 1)
                ->where('tf_water_used.activation', '=', 1)
                ->where('tenement_flats.tenement_id', '=', $tenement_id)
                ->where('tf_water_used.flat_id', '=', $request->flat_id)
                ->where('tf_water_used.year_month', '=', $year_month)->get();

            if (count($TfWaterUsedExits) > 0){
                $Used = TfWaterUsed::find($TfWaterUsedExits[0]->id);
                $Used->activation = 0;
                $Used->updated_by = Auth::user()->id;
                $Used->updated_at = date("Y-m-d H:i:s");                  
                $Used->save();
            }

            $date_from = '';
            $date_to = '';

            if ($request->date_from != ''){
               $date_from = DateTime::createFromFormat('d/m/Y', $request->date_from);
               $date_from = $date_from->format('Ymd');
            }

            if ($request->date_to != ''){
               $date_to = DateTime::createFromFormat('d/m/Y', $request->date_to);
               $date_to = $date_to->format('Ymd');

            }

            $timestamp = strtotime($request->bill_year . '-' . $request->bill_month . '-01'.'-1 month');
            $prev_month = date('Ym', $timestamp);

            $old_index = $request->old_index;
            if ($request->old_index == ''){
                $old_index = '0';
                $pre_month_water = TfWaterUsed::where('year_month', $prev_month)
                    ->where('activation',1)
                    ->where('flat_id',$request->flat_id)
                    ->get();
                if (isset($pre_month_water)){
                    $old_index = $pre_month_water[0]->new_index;
                }
            }
            TfWaterUsed::create([
                'year_month'   => $year_month,
                'date_from'   => $date_from,
                'date_to'   => $date_to,
                'old_index'   => $utils->number($old_index),
                'new_index'   => $utils->number($request->new_index),
                'comment'  =>  $request->comment,
                'used_deduct'  => $request->deduct,
                'prev_year_month_price'  => $request->prev_price_year . $request->prev_price_month,                
                'flat_id'  =>  $request->flat_id,
                'activation'  => 1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'updated_at' => date("Y-m-d H:i:s") 
            ]);

            DB::commit();
            return redirect('flat/all/water/' . $year_month)->with('tenementElec-alert-success','Đã thực hiện Cập nhật chỉ số thành công !');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }
}