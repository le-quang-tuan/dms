<?php

namespace App\Http\Controllers\Flat;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TfVehicle;
use DB;
use yajra\Datatables\Datatables;
use Validator;
use Auth;
use Redirect;
use DateTime;

class VehicleController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('flat.flatvehicle');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $TfVehicle = TfVehicle::where('flat_id',$id)
            ->where('activation',1)
            ->orderBy('id', 'asc')->first();
            // dd($TfVehicle);

        $parking_tariffs = DB::table("tenement_parking_tariff")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        //dd($Tenement[0]);
        return View('flat.flatvehicle', [ 'TfVehicle'=>$id, 'parking_tariffs'=> $parking_tariffs]);
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
        $TfVehicle = DB::table('tf_vehicle')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            // ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->where('flat_id', '=', $id)
            ->orderBy('id', 'desc');

        // return View('Flat.flatparking', [ 'TfVehicle.data'=>$TfVehicle]);

        return Datatables::of($TfVehicle)
                ->addColumn('action', function ($TfVehicle) {
                    return '<button type="button" class="btn btn-primary btn-details" value="'. $TfVehicle->id .'" >Hủy</button>';
                })
                ->addColumn('parking', function ($TfVehicle) {
                    return '<a href="flat/parking/'. $TfVehicle->id . '" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('water', function ($TfVehicle) {
                    return '<a href="flat/water/'. $TfVehicle->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('parking', function ($TfVehicle) {
                    return '<a href="flat/parking/'. $TfVehicle->id .'" class="btn btn-xs btn-primary" target="_blank">Chỉ số tháng</a>';                    
                })
                ->addColumn('parking', function ($TfVehicle) {
                    return '<a href="flat/parking/'. $TfVehicle->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
                })
                ->addColumn('service', function ($TfVehicle) {
                    return '<a href="flat/service/'. $TfVehicle->id .'" class="btn btn-xs btn-primary" target="_blank">Xe tháng</a>';                    
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
            'number_plate.required' => 'Số Xe: chưa nhập dữ liệu',
            // 'name.required' => 'Chủ sở hữu: chưa nhập dữ liệu',
            'vehicle_type_id.required' => 'Loại phí: chưa nhập dữ liệu',
            'begin_contract_date.required' => 'Hợp đồng từ ngày: chưa nhập dữ liệu'
        ];

        $v = Validator::make($request->all(), [
            'number_plate'  =>  'required',
            // 'name'  =>  'required',
            'vehicle_type_id'  =>  'required',
            'begin_contract_date'  =>  'required'
        ], $messages);

        if($v->fails()){
            return Redirect::to('flat/vehicle/' . $request->input('id'))
                ->withInput()->withErrors($v);
        }
        $date_from = '';
        $date_to = '';

        if ('' != $request->begin_contract_date){
            $date_from = DateTime::createFromFormat('d/m/Y', $request->input('begin_contract_date'))->format('Ymd');
        }
        

        if ('' != $request->end_contract_date){
            $date_to   = DateTime::createFromFormat('d/m/Y', $request->input('end_contract_date'))->format('Ymd');
        }

        //Kiểm tra từ ngày < đến ngày
        if ($date_from != '' && $date_to != '' && $date_from > $date_to){
            return back()->withInput()->withErrors(['addFail' => 'Từ ngày phải <= đến ngày']);            
        }

        DB::beginTransaction();
        try {
            TfVehicle::create([
                'number_plate'  =>  $request->number_plate,
                'name'  =>  $request->name,
                'label'  =>  $request->label,
                'maker'  =>  $request->maker,
                'color'  =>  $request->color,
                'vehicle_type_id'  =>  $request->vehicle_type_id,
                'begin_contract_date'  =>  $date_from,
                'end_contract_date'  =>  $date_to,
                'comment'  =>  $request->comment,
                'flat_id'  =>  $request->id,
                'activation'  => 1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::commit();
            return Redirect::to('flat/vehicle/' . $request->input('id'));
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
            $TfVehicleUsedExits = DB::table("tenement_flats")
            ->join('tf_vehicle', 'tenement_flats.id', '=', 'tf_vehicle.flat_id')
            ->select('tenement_flats.*')
            ->where('tenement_flats.activation', '=', 1)
            ->where('tenement_flats.tenement_id', '=', $tenement_id)
            ->where('tf_vehicle.id', '=', $id)->count();

            if($TfVehicleUsedExits == 0){
                return Response::view('errors.404', array(), 404);
            }  

            $Used = TfVehicle::find($id);

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

    public function show($id)
    {
        $vehicleFlat = DB::table('tenement_flats as a')
            ->join('tf_vehicle as b', function($join)
            {
                $join->on('a.id', '=', 'b.flat_id');
                $join->on('b.activation', '=' , DB::raw('1'));
            })
            ->select(['b.id', 'a.address','a.flat_code', DB::raw('a.name owner'),'a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area','b.number_plate','b.name','b.label','b.maker','b.color', 'b.vehicle_type_id', 'b.begin_contract_date', 'b.end_contract_date', 'b.driver', 'b.comment'])
            ->where('a.activation', 1)
            ->where('b.id', '=' , $id)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc')->get();

        return json_encode($vehicleFlat);
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

        $parking_tariffs = DB::table("tenement_parking_tariff")
        ->where('tenement_id', '=', $tenement_id)
        ->where('activation', '=', 1)
        ->orderBy('id')->get();

        //dd($Tenement[0]);
        return View('flat.allflatvehicle', [ 'parking_tariffs'=> $parking_tariffs]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allFlatData() {
        //dd(123);     
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        // dd(123);
        $vehicleFlat = DB::table('tenement_flats as a')
            ->join('tf_vehicle as b', function($join)
            {
                $join->on('a.id', '=', 'b.flat_id');
                $join->on('b.activation', '=' , DB::raw('1'));
            })
            ->join('tenement_parking_tariff as c', function($join)
            {
                $join->on('c.id', '=', 'b.vehicle_type_id');
                $join->on('b.activation', '=' , DB::raw('1'));
            }, 'left outer')
            ->select([DB::raw('a.id flat_id'), DB::raw('c.name vehicle_type'), 'b.id', 'a.address','a.flat_code', DB::raw('a.name owner'),'a.phone', 'a.is_stay', 'a.persons', 'a.receive_date', 'a.area','b.number_plate','b.name','b.label','b.maker','b.color', 'b.vehicle_type_id', 'b.begin_contract_date', 'b.end_contract_date', 'b.driver', 'b.comment'])
            ->where('a.activation', 1)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.flat_code', 'asc');

        return Datatables::of($vehicleFlat)
                ->addColumn('action', function ($vehicleFlat) {
                    return '<a style="text-align: center;" href="../detail/'. $vehicleFlat->flat_id .'">' . $vehicleFlat->flat_code .'</a>';                    
                })
                ->addColumn('change', function ($vehicleFlat){
                    return '<button type="button" 
                        id="'. $vehicleFlat->id .'" 
                        class="btn btn-xs btn-primary">Cập nhật</button>';
                })
                ->make(true);        
    }     

    public function exex_change(Request $request)
    {
        $tenement_id = Auth::user()->tenement_id;
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }
        DB::beginTransaction();
        try {

            $begin_contract_date = '';
            $end_contract_date = '';

            if ($request->begin_contract_date != ''){
               $begin_contract_date = DateTime::createFromFormat('d/m/Y', $request->begin_contract_date);
               $begin_contract_date = $begin_contract_date->format('Ymd');
            }

            if ($request->end_contract_date != ''){
               $end_contract_date = DateTime::createFromFormat('d/m/Y', $request->end_contract_date);
               $end_contract_date = $end_contract_date->format('Ymd');
            }

            $vehicle = TfVehicle::find($request->id);

            $vehicle->activation = $request->activation;
            $vehicle->number_plate = $request->number_plate;
            $vehicle->name = $request->name;
            $vehicle->label = $request->label;
            $vehicle->maker = $request->maker;
            $vehicle->color = $request->color;
            $vehicle->vehicle_type_id = $request->vehicle_type_id;
            $vehicle->begin_contract_date = $begin_contract_date;
            $vehicle->end_contract_date = $end_contract_date;
            $vehicle->comment = $request->comment;
            $vehicle->updated_by = Auth::user()->id;
            $vehicle->updated_at = date("Y-m-d H:i:s");    
            $vehicle->save();

            DB::commit();
            return redirect('flat/all/vehicle')->with('tenementVehicle-alert-success','Đã thực hiện Cập nhật thành công !');
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return back()->withInput()->withErrors(['updateFail' => $e->getMessage()]);
        }  
    }
}