<?php

namespace App\Http\Controllers\Flat;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TfServiceUsed;
use DB;
use yajra\Datatables\Datatables;
use Validator;
use Auth;
use Redirect;
use DateTime;
use App\LaraBase\NumberUtil;

class ServiceController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('flat.flatservice');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id) {
        $year_month = $request->input('year_month');

        if ($year_month == ''){
            $year_month = date('Ym');
        }
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $TfServiceUsed = TfServiceUsed::where('flat_id',$id)
            ->where('activation',1)
            ->orderBy('id', 'asc')->first();
            // dd($TfServiceUsed);

        //dd($Tenement[0]);
        return View('flat.flatservice', [ 'TfServiceUsed'=>$id, 'year_month'=>$year_month]);
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
        $TfServiceUsed = DB::table('tf_service_used')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            ->where('flat_id', '=', $id)
            ->orderBy('year_month', 'desc');

        // return View('Flat.flatservice', [ 'TfServiceUsed.data'=>$TfServiceUsed]);

        return Datatables::of($TfServiceUsed)
                ->addColumn('action', function ($TfServiceUsed) {
                    return '<button type="button" class="btn btn-primary btn-details" value="'. $TfServiceUsed->id .'" >Hủy</button>';
                })
                ->addColumn('service', function ($TfServiceUsed) {
                    return '<a href="flat/service/'. $TfServiceUsed->id . '" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('water', function ($TfServiceUsed) {
                    return '<a href="flat/water/'. $TfServiceUsed->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('service', function ($TfServiceUsed) {
                    return '<a href="flat/service/'. $TfServiceUsed->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('parking', function ($TfServiceUsed) {
                    return '<a href="flat/parking/'. $TfServiceUsed->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->addColumn('service', function ($TfServiceUsed) {
                    return '<a href="flat/service/'. $TfServiceUsed->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->make(true);        
    }        

    public function store(Request $request)
    {
        //dd($request);
        //Check period user
        $id = Auth::user()->tenement_id;
        
        //Check period user
        if(Auth::user()->confirmed == 0 || $id == '') {
            return redirect("/home");
        }
        $messages = [
            'name.required' => 'Phí khác: chưa nhập dữ liệu',
            'mount.required' => 'Số lượng: chưa nhập dữ liệu',
            'price.required' => 'Số tiền: chưa nhập dữ liệu'
        ];

        $v = Validator::make($request->all(), [
            'year'  =>  'required',
            'month'  =>  'required',
            'name'  =>  'required',
            'mount'  =>  'required',
            'price'  =>  'required'
        ], $messages);

        if($v->fails()){
            return Redirect::to('flat/service/' . $request->input('id'))
                ->withInput()->withErrors($v);
        }
        //Kiểm tra năm tháng tạo có = hiện tại hoặc nhỏ hơn 1 tháng ko
        $prev_month = date('Ym', strtotime(" -1 months"));
        $cur_month = date('Ym');
        $date_from = '';
        $date_to = '';

        if ('' != $request->date_from){
            $date_from = DateTime::createFromFormat('d/m/Y', $request->input('date_from'))->format('Ymd');
        }
        

        if ('' != $request->date_to){
            $date_to   = DateTime::createFromFormat('d/m/Y', $request->input('date_to'))->format('Ymd');
        }

        $year_month = $request->input('year') . $request->input('month');

        if (!($year_month == $prev_month || 
            $year_month == $cur_month))
            return back()
                    ->withInput()
                    ->withErrors(['addFail' => 'Năm/Tháng tạo phải là trước 1 tháng hoặc bằng tháng hiện tại']);
        $utils = new NumberUtil();
        
        DB::beginTransaction();
        try {
            TfServiceUsed::create([
                'name'   => $request->name,
                'year_month'   => $year_month,
                'date_from'   => $date_from,
                'date_to'   => $date_to,
                'mount'   => $utils->number($request->mount),
                'unit'   => $request->unit,
                'price'   => $utils->number($request->price),
                'comment'  =>  $request->comment,
                'flat_id'  =>  $request->id,
                'activation'  => 1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'updated_at' => date("Y-m-d H:i:s")                
            ]);

            DB::commit();
            return Redirect::to('flat/service/' . $request->input('id'));
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
            $TfServiceUsedExits = DB::table("tenement_flats")
            ->join('tf_service_used', 'tenement_flats.id', '=', 'tf_service_used.flat_id')
            ->select('tenement_flats.*')
            ->where('tenement_flats.activation', '=', 1)
            ->where('tenement_flats.tenement_id', '=', $tenement_id)
            ->where('tf_service_used.id', '=', $id)->count();

            if($TfServiceUsedExits == 0){
                return Response::view('errors.404', array(), 404);
            }  

            $Used = TfServiceUsed::find($id);
            $Used->updated_by = Auth::user()->id;
            $Used->updated_at = date("Y-m-d H:i:s"); 
            $Used->activation = 0;
            $Used->save();

            DB::commit();
            return ("ok");
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }
    }
    public function show($id)
    {
        $serviceFlat = DB::table('tenement_flats as a')
            ->join('tf_service_used as b', function($join)
            {
                $join->on('a.id', '=', 'b.flat_id');
                $join->on('b.activation', '=' , DB::raw('1'));
            })
            ->select(['b.id', 'a.address','a.flat_code', DB::raw('a.name owner'),'a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area','b.mount','b.name','b.price','b.unit','b.date_from', 'b.date_to','b.comment'])
            ->where('a.activation', 1)
            ->where('b.id', '=' , $id)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc')->get();

        return json_encode($serviceFlat);
    }

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function allindex() {
        $tenement_id = Auth::user()->tenement_id;
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }

        return View('flat.allflatservice');
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
        $serviceFlat = DB::table('tenement_flats as a')
            ->join('tf_service_used as b', function($join)  use ($year_month)
            {
                $join->on('a.id', '=', 'b.flat_id');
                $join->on('b.year_month', '=', DB::raw($year_month));
                $join->on('b.activation', '=' , DB::raw('1'));
            })
            ->select(
                [DB::raw('a.id flat_id'), 'b.id', 'a.address','a.flat_code', DB::raw('a.name owner'),'a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area','b.date_from','b.name','b.date_to','b.mount','b.price', 'b.total', 'b.unit', 'b.comment', DB::raw( "'" . substr($year_month,4,2) . '/' . substr($year_month,0,4) . "' as 'year_month'")])
            ->where('a.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc');

        return Datatables::of($serviceFlat)
                ->addColumn('action', function ($serviceFlat) {
                    return '<a style="text-align: center;" href="../detail/'. $serviceFlat->flat_id .'">' . $serviceFlat->flat_code .'</a>';                    
                })
                ->addColumn('change', function ($serviceFlat){
                    return '<button type="button" 
                        id="'. $serviceFlat->id .'" 
                        class="btn btn-xs btn-primary">Cập nhật</button>';
                })
                ->make(true);        
    }     

    public function exex_change(Request $request)
    {
        $utils = new NumberUtil();

        $tenement_id = Auth::user()->tenement_id;
        $year_month = $request->bill_year . $request->bill_month;
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }
        DB::beginTransaction();
        try {

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

            $service = TfServiceUsed::find($request->id);

            $service->activation = $request->activation;
            $service->price = $utils->number($request->price);
            $service->name = $request->name;
            $service->unit = $request->unit;
            $service->mount = $utils->number($request->mount);
            $service->date_from = $date_from;
            $service->date_to = $date_to;
            $service->comment = $request->comment;
            $service->updated_by = Auth::user()->id;
            $service->updated_at = date("Y-m-d H:i:s");                

            $service->save();

            DB::commit();
            return redirect('flat/all/service/' . $year_month)->with('tenementService-alert-success','Đã thực hiện Cập nhật thành công !');
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }
}