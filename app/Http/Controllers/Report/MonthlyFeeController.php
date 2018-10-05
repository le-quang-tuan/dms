<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\TfPaidDt;
use App\Models\TfPaidHd;
use App\Models\Tenement;
use App\Models\Item;
use DB;
use yajra\Datatables\Datatables;
use Validator;
use Auth;
use Redirect;
use DateTime;
use Input;
use Excel;
use Session;
use mPDF;
use PDF;
use App\LaraBase\NumberUtil;
use LynX39;

// Bảng Kê Tất Cả các Loại Phí
class MonthlyFeeController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($year_month) {
        //dd(123);
        $tenement_id = Auth::user()->tenement_id;

        $utils = new NumberUtil();
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $payment = DB::table("tenement_flats")
        ->join('tf_payment_all_months', 'tenement_flats.id', '=', 'tf_payment_all_months.flat_id')
        ->join('tenements', 'tenements.id', '=', 'tenement_flats.tenement_id')
        ->select('tenement_flats.*', 'tf_payment_all_months.manager_fee', 'tf_payment_all_months.elec_fee', 'tf_payment_all_months.water_fee', 'tf_payment_all_months.gas_fee', 'tf_payment_all_months.parking_fee', 'tf_payment_all_months.service_fee')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tf_payment_all_months.year_month', '=', $year_month)->get();

        if (!isset($payment[0])){
            return "/home";
        }

        $elec = DB::table("tf_payment_elec_hd as a")
        ->leftjoin('tf_payment_elec_dt as b', function($join)
            {
                $join->on('a.id', '=', 'b.elec_hd_id');
            })
        ->select('a.*', 'b.*')
        ->where('a.activation', '=', 1)
        ->where('a.year_month', '=', $payment[0]->year_month)
        ->orderBy('a.id')->get();

        $water = DB::table("tf_payment_water_hd as a")
        ->leftjoin('tf_payment_water_dt as b', function($join)
            {
                $join->on('a.id', '=', 'b.water_hd_id');
            })
        ->select('a.*', 'b.*')
        ->where('a.activation', '=', 1)
        ->where('a.year_month', '=', $payment[0]->year_month)
        ->orderBy('a.id')->get();

        $gas = DB::table("tf_payment_gas_hd as a")
        ->leftjoin('tf_payment_gas_dt as b', function($join)
            {
                $join->on('a.id', '=', 'b.gas_hd_id');
            })
        ->select('a.*', 'b.*')
        ->where('a.activation', '=', 1)
        ->where('a.year_month', '=', $payment[0]->year_month)
        ->orderBy('a.id')->get();

        $parking = DB::table("tf_payment_parking")
        ->select('parking_name',DB::raw('count(*) as total_count'),'price',DB::raw('SUM(total_money) as total_money'))
        ->groupBy('parking_id','parking_name','days_parking','price')
        ->where('activation', '=', 1)
        ->where('year_month', '=', $payment[0]->year_month)
        ->orderBy('id')->get();

        $service = DB::table("tf_payment_service")
        ->select('service','mount', 'unit','price','total_money')
        ->where('activation', '=', 1)
        ->where('year_month', '=', $payment[0]->year_month)
        ->orderBy('id')->get();

        $dept = DB::table("tf_payment_all_months")
        ->select(
            'year_month',
            DB::raw('ifnull(manager_fee,0) - ifnull(manager_fee_paid,0) as manager_fee_dept'), 
            DB::raw('ifnull(elec_fee,0) - ifnull(elec_fee_paid,0) as elec_fee_dept'), 
            DB::raw('ifnull(water_fee,0) - ifnull(water_fee_paid,0) as water_fee_dept'), 
            DB::raw('ifnull(gas_fee,0) - ifnull(gas_fee_paid,0) as gas_fee_dept'), 
            DB::raw('ifnull(service_fee,0) - ifnull(service_fee_paid,0) as service_fee_dept'), 
            DB::raw('ifnull(parking_fee,0) - ifnull(parking_fee_paid,0) as parking_fee_dept'))
        ->where('activation', '=', 1)
        ->where('year_month', '<', $payment[0]->year_month)
        ->where(DB::raw('
            (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
            '<>',
            DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
        ->orderBy('year_month')->get();

        $paid = DB::table("tf_payment_all_months")
        ->where('activation', '=', 1)
        ->where('year_month', '=', $payment[0]->year_month)
        ->select(
            'year_month',
            DB::raw('ifnull(manager_fee_paid,0) as manager_fee_paid'), 
            DB::raw('ifnull(elec_fee_paid,0) as elec_fee_paid'), 
            DB::raw('ifnull(water_fee_paid,0) as water_fee_paid'), 
            DB::raw('ifnull(gas_fee_paid,0) as gas_fee_paid'), 
            DB::raw('ifnull(service_fee_paid,0) as service_fee_paid'), 
            DB::raw('ifnull(parking_fee_paid,0) as parking_fee_paid'))
        ->orderBy('year_month')->get();
        
        $total = DB::table("tf_payment_all_months")
        ->select(
            DB::raw('sum((   ifnull(manager_fee, 0) +
                         ifnull(elec_fee, 0) +
                         ifnull(water_fee, 0) +
                         ifnull(gas_fee, 0) +
                         ifnull(service_fee, 0) +
                         ifnull(parking_fee, 0)) -

                        (ifnull(manager_fee_paid, 0) +
                         ifnull(elec_fee_paid, 0) +
                         ifnull(water_fee_paid, 0) +
                         ifnull(gas_fee_paid, 0) +
                         ifnull(service_fee_paid, 0) +
                         ifnull(parking_fee_paid, 0))) as total_money'))
        ->where('activation', '=', 1)
        ->where('year_month', '<', $payment[0]->year_month)
        ->where(DB::raw('
            (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
            '<>',
            DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
        ->orderBy('year_month')->get();

        //dd($total);
        $fileName = "sample".'.pdf';

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
    
        $fullPath = "{$path}/{$fileName}";
        $urlFile = url("{$folderTemp}/{$fileName}");
        $mpdf = new mPDF("utf-8", "A4",0,0,0,0,0,0,0);

        //$mpdf = new mPDF('utf-8', 'A4-L');
        $mpdf->WriteHTML(View('report.paymentnotice', [ 
            'flat_info'=> $flat_info[0],
            'id'=>$id,
            'total_money'=>$total[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
            'payment'=>$payment[0],
            'elec'=>$elec,
            'water'=>$water,
            'gas'=>$gas,
            'parking'=>$parking,
            'service'=>$service,
            'dept'=>$dept,
            'paid'=>$paid,
            'tenement_info'=> $tenement_info[0]])->render());           
        $mpdf->debug = true;
        $mpdf->Output($fullPath, 'F');

        //dd($payment[0]);
        return View('report.paymentnotice', [ 
            'flat_info'=> $flat_info[0],
            'id'=>$id,
            'total_money'=>$total[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
            'payment'=>$payment[0],
            'elec'=>$elec,
            'water'=>$water,
            'gas'=>$gas,
            'parking'=>$parking,
            'service'=>$service,
            'dept'=>$dept,
            'paid'=>$paid,
            'tenement_info'=> $tenement_info[0]
             ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexpdf_old($flat_id, $year_month) {
        //dd(123);
        $tenement_id = Auth::user()->tenement_id;

        $utils = new NumberUtil();
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $flat_info = DB::table("tenement_flats")
        ->select('tenement_flats.*')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tenement_flats.id', '=', $flat_id)
        ->orderBy('id')->get();

        $payment = DB::table("tf_payment_all_months")
        ->where('activation', '=', 1)
        ->where('year_month', '=', $year_month)
        ->where('flat_id', '=', $flat_id)->get();

        if (!isset($payment[0])){
            return "/home";
        }

        $elec = DB::table("tf_payment_elec_hd as a")
        ->leftjoin('tf_payment_elec_dt as b', function($join)
            {
                $join->on('a.id', '=', 'b.elec_hd_id');
            })
        ->select('a.*', 'b.*')
        ->where('a.activation', '=', 1)
        ->where('a.year_month', '=', $payment[0]->year_month)
        ->where('a.flat_id', '=', $flat_id)        
        ->orderBy('a.id')->get();

        $water = DB::table("tf_payment_water_hd as a")
        ->leftjoin('tf_payment_water_dt as b', function($join)
            {
                $join->on('a.id', '=', 'b.water_hd_id');
            })
        ->select('a.*', 'b.*')
        ->where('a.activation', '=', 1)
        ->where('a.year_month', '=', $payment[0]->year_month)
        ->where('a.flat_id', '=', $flat_id)
        ->orderBy('a.id')->get();

        $gas = DB::table("tf_payment_gas_hd as a")
        ->leftjoin('tf_payment_gas_dt as b', function($join)
            {
                $join->on('a.id', '=', 'b.gas_hd_id');
            })
        ->select('a.*', 'b.*')
        ->where('a.activation', '=', 1)
        ->where('a.year_month', '=', $payment[0]->year_month)
        ->where('a.flat_id', '=', $flat_id)
        ->orderBy('a.id')->get();

        $parking = DB::table("tf_payment_parking_hd as a")
        ->leftjoin('tf_payment_parking_dt as b', function($join)
            {
                $join->on('a.id', '=', 'b.parking_hd_id');
            })
        ->select('a.*', 'b.*')
        ->where('a.activation', '=', 1)
        ->where('a.year_month', '=', $payment[0]->year_month)
        ->where('a.flat_id', '=', $flat_id)
        ->orderBy('a.id')->get();

        $service = DB::table("tf_payment_service_hd as a")
        ->leftjoin('tf_payment_service_dt as b', function($join)
            {
                $join->on('a.id', '=', 'b.service_hd_id');
            })
        ->select('a.*', 'b.*')
        ->where('a.activation', '=', 1)
        ->where('a.year_month', '=', $payment[0]->year_month)
        ->where('a.flat_id', '=', $flat_id)
        ->orderBy('a.id')->get();

        //dd($service_fee);

        // $parking = DB::table("tf_payment_parking_hd")
        // ->select('parking_name',DB::raw('count(*) as total_count'),'price',DB::raw('SUM(total_money) as total_money'))
        // ->groupBy('parking_id','parking_name','days_parking','price')
        // ->where('activation', '=', 1)
        // ->where('year_month', '=', $payment[0]->year_month)
        // ->orderBy('id')->get();

        // $service = DB::table("tf_payment_service_hd")
        // ->select('service','mount', 'unit','price','total_money')
        // ->where('activation', '=', 1)
        // ->where('year_month', '=', $payment[0]->year_month)
        // ->orderBy('id')->get();

        $dept = DB::table("tf_payment_all_months")
        ->select(
            'year_month',
            DB::raw('ifnull(manager_fee,0) - ifnull(manager_fee_paid,0) as manager_fee_dept'), 
            DB::raw('ifnull(elec_fee,0) - ifnull(elec_fee_paid,0) as elec_fee_dept'), 
            DB::raw('ifnull(water_fee,0) - ifnull(water_fee_paid,0) as water_fee_dept'), 
            DB::raw('ifnull(gas_fee,0) - ifnull(gas_fee_paid,0) as gas_fee_dept'), 
            DB::raw('ifnull(service_fee,0) - ifnull(service_fee_paid,0) as service_fee_dept'), 
            DB::raw('ifnull(parking_fee,0) - ifnull(parking_fee_paid,0) as parking_fee_dept'))
        ->where('activation', '=', 1)
        ->where('year_month', '<', $payment[0]->year_month)
        ->where(DB::raw('
            (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
            '<>',
            DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
        ->where('flat_id', '=', $flat_id)        
        ->orderBy('year_month')->get();

        $paid = DB::table("tf_payment_all_months")
        ->where('activation', '=', 1)
        ->where('year_month', '=', $payment[0]->year_month)
        ->select(
            'year_month',
            DB::raw('ifnull(manager_fee_paid,0) as manager_fee_paid'), 
            DB::raw('ifnull(elec_fee_paid,0) as elec_fee_paid'), 
            DB::raw('ifnull(water_fee_paid,0) as water_fee_paid'), 
            DB::raw('ifnull(gas_fee_paid,0) as gas_fee_paid'), 
            DB::raw('ifnull(service_fee_paid,0) as service_fee_paid'), 
            DB::raw('ifnull(parking_fee_paid,0) as parking_fee_paid'))
        ->where('flat_id', '=', $flat_id)        
        ->orderBy('year_month')->get();
        
        $total = DB::table("tf_payment_all_months")
        ->select(
            DB::raw('sum((   ifnull(manager_fee, 0) +
                         ifnull(elec_fee, 0) +
                         ifnull(water_fee, 0) +
                         ifnull(gas_fee, 0) +
                         ifnull(service_fee, 0) +
                         ifnull(parking_fee, 0)) -

                        (ifnull(manager_fee_paid, 0) +
                         ifnull(elec_fee_paid, 0) +
                         ifnull(water_fee_paid, 0) +
                         ifnull(gas_fee_paid, 0) +
                         ifnull(service_fee_paid, 0) +
                         ifnull(parking_fee_paid, 0))) as total_money'))
        ->where('activation', '=', 1)
        ->where('year_month', '<', $payment[0]->year_month)
        ->where(DB::raw('
            (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
            '<>',
            DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
        ->where('flat_id', '=', $flat_id) 
        ->orderBy('year_month')->get();

        $fileName = "sample".'.pdf';

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
    
        $fullPath = "{$path}/{$fileName}";
        $urlFile = url("{$folderTemp}/{$fileName}");
        $mpdf = new mPDF("utf-8", "A4",0,0,0,0,0,0,0);

        //$mpdf = new mPDF('utf-8', 'A4-L');
        $mpdf->WriteHTML(View('report.paymentnotice', [ 
            'flat_info'=> $flat_info[0],
            'id'=>$flat_id,
            'total_money'=>$total[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
            'payment'=>$payment[0],
            'elec'=>$elec,
            'water'=>$water,
            'gas'=>$gas,
            'parking'=>$parking,
            'service'=>$service,
            'dept'=>$dept,
            'paid'=>$paid,
            'tenement_info'=> $tenement_info[0]])->render());           
        $mpdf->debug = true;
        $mpdf->Output($fullPath, 'F');

        //dd($payment[0]);
        return View('report.paymentnotice', [ 
            'flat_info'=> $flat_info[0],
            'id'=>$flat_id,
            'total_money'=>$total[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
            'payment'=>$payment[0],
            'elec'=>$elec,
            'water'=>$water,
            'gas'=>$gas,
            'parking'=>$parking,
            'service'=>$service,
            'dept'=>$dept,
            'paid'=>$paid,
            'tenement_info'=> $tenement_info[0]
             ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_tenement($year_month) {
        $tenement_id = Auth::user()->tenement_id;
        $pdf = new LynX39;

        $utils = new NumberUtil();
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $flats = DB::table("tenement_flats")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('tenement_id', '=', $tenement_id)
        ->orderBy('flat_code')->get();

        $folderTemp = "tmp/" . $tenement_info[0]->id . '/paymentnotice/';
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }

        $paymentnotic_lst = array();

        foreach ($flats as $flat) {
            $paymentnotic_lst = array();
            $flat_info = $flat;
            $flat_id = $flat_info->id;

            $payment = DB::table("tf_payment_all_months")
            ->where('activation', '=', 1)
            ->where('year_month', '=', $year_month)
            ->where('flat_id', '=', $flat_id)->get();

            if (!isset($payment[0])){
                return "/home";
            }

            $elec = DB::table("tf_payment_elec_hd as a")
            ->leftjoin('tf_payment_elec_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.elec_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)        
            ->orderBy('a.id')->get();

            $water = DB::table("tf_payment_water_hd as a")
            ->leftjoin('tf_payment_water_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.water_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('a.id')->get();

            $gas = DB::table("tf_payment_gas_hd as a")
            ->leftjoin('tf_payment_gas_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.gas_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('a.id')->get();

            $parking = DB::table("tf_payment_parking_hd as a")
            ->leftjoin('tf_payment_parking_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.parking_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('a.id')->get();

            $service = DB::table("tf_payment_service_hd as a")
            ->leftjoin('tf_payment_service_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.service_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('a.id')->get();

            $dept = DB::table("tf_payment_all_months")
            ->select(
                'year_month',
                DB::raw('ifnull(manager_fee,0) - ifnull(manager_fee_paid,0) - ifnull(manager_fee_skip,0) as manager_fee_dept'), 
                DB::raw('ifnull(elec_fee,0) - ifnull(elec_fee_paid,0) - ifnull(elec_fee_skip,0) as elec_fee_dept'), 
                DB::raw('ifnull(water_fee,0) - ifnull(water_fee_paid,0)- ifnull(water_fee_skip,0) as water_fee_dept'), 
                DB::raw('ifnull(gas_fee,0) - ifnull(gas_fee_paid,0)- ifnull(gas_fee_skip,0) as gas_fee_dept'), 
                DB::raw('ifnull(service_fee,0) - ifnull(service_fee_paid,0)- ifnull(service_fee_skip,0) as service_fee_dept'), 
                DB::raw('ifnull(parking_fee,0) - ifnull(parking_fee_paid,0)- ifnull(parking_fee_skip,0) as parking_fee_dept'))
            ->where('activation', '=', 1)
            ->where('year_month', '<', $payment[0]->year_month)
            ->where(DB::raw('
                (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
                '<>',
                DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
            ->where('flat_id', '=', $flat_id)        
            ->orderBy('year_month')->get();

            $paid = DB::table("tf_payment_all_months")
            ->where('activation', '=', 1)
            ->where('year_month', '=', $payment[0]->year_month)
            ->select(
                'year_month',
                DB::raw('ifnull(manager_fee_paid,0) as manager_fee_paid'), 
                DB::raw('ifnull(elec_fee_paid,0) as elec_fee_paid'), 
                DB::raw('ifnull(water_fee_paid,0) as water_fee_paid'), 
                DB::raw('ifnull(gas_fee_paid,0) as gas_fee_paid'), 
                DB::raw('ifnull(service_fee_paid,0) as service_fee_paid'), 
                DB::raw('ifnull(parking_fee_paid,0) as parking_fee_paid'))
            ->where('flat_id', '=', $flat_id)        
            ->orderBy('year_month')->get();
            
            $total = DB::table("tf_payment_all_months")
            ->select(
                DB::raw('sum((   ifnull(manager_fee, 0) +
                             ifnull(elec_fee, 0) +
                             ifnull(water_fee, 0) +
                             ifnull(gas_fee, 0) +
                             ifnull(service_fee, 0) +
                             ifnull(parking_fee, 0)) -

                            (ifnull(manager_fee_paid, 0) +
                             ifnull(elec_fee_paid, 0) +
                             ifnull(water_fee_paid, 0) +
                             ifnull(gas_fee_paid, 0) +
                             ifnull(service_fee_paid, 0) +
                             ifnull(parking_fee_paid, 0)) - 

                             (ifnull(manager_fee_skip, 0) +
                             ifnull(elec_fee_skip, 0) +
                             ifnull(water_fee_skip, 0) +
                             ifnull(gas_fee_skip, 0) +
                             ifnull(service_fee_skip, 0) +
                             ifnull(parking_fee_skip, 0))) as total_money'))
            ->where('activation', '=', 1)
            ->where('year_month', '<', $payment[0]->year_month)
            ->where(DB::raw('
                (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
                '<>',
                DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
            ->where('flat_id', '=', $flat_id) 
            ->orderBy('year_month')->get();

            $row = array(
                'flat_info'=> $flat_info,
                'id'=>$flat_id,
                'total_money'=>$total[0]->total_money,
                'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
                'payment'=>$payment[0],
                'elec'=>$elec,
                'water'=>$water,
                'gas'=>$gas,
                'parking'=>$parking,
                'service'=>$service,
                'dept'=>$dept,
                'paid'=>$paid,
                'tenement_info'=> $tenement_info[0]
            );
            array_push($paymentnotic_lst, $row);

            $fileName = $year_month . '_' . $flat_info->flat_code . "_paymentnotice".'.pdf';
        
            $fullPath = "{$path}/{$fileName}";
            $urlFile = url("{$folderTemp}/{$fileName}");
            //dd($fullPath);
            $mpdf = new mPDF("utf-8", "A4-P",0,0,0,0,0,0,0);
            $mpdf->useSubstitutions = false;
            $mpdf->simpleTables = true; 
            $mpdf->WriteHTML(View('report.paymentnotice', [ 
                'paymentnotic_lst'=> $paymentnotic_lst])->render());          
            $mpdf->debug = false;
            $mpdf->Output($fullPath, 'F');

            $pdf->addPDF($fullPath, 'all','P');
        }
        $pdf->merge('file', $path . $year_month . '_' . $tenement_info[0]->tenement_code . '_paymentnotice.pdf', 'P');

        return View('report.paymentnotice', [ 
                'paymentnotic_lst'=> $paymentnotic_lst]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexpdf($flat_id, $year_month) {
        $tenement_id = Auth::user()->tenement_id;
        $pdf = new LynX39;

        $utils = new NumberUtil();
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $flats = DB::table("tenement_flats")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('tenement_id', '=', $tenement_id)
        ->where('id', '=', $flat_id)
        ->orderBy('flat_code')->get();

        $folderTemp = "tmp/" . $tenement_info[0]->id . '/paymentnotice/';
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }

        $paymentnotic_lst = array();

        foreach ($flats as $flat) {
            $paymentnotic_lst = array();
            $flat_info = $flat;
            $flat_id = $flat_info->id;

            $payment = DB::table("tf_payment_all_months")
            ->where('activation', '=', 1)
            ->where('year_month', '=', $year_month)
            ->where('flat_id', '=', $flat_id)->get();

            if (!isset($payment[0])){
                return "/home";
            }

            $elec = DB::table("tf_payment_elec_hd as a")
            ->leftjoin('tf_payment_elec_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.elec_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)        
            ->orderBy('b.row_no', 'desc')->get();

            $water = DB::table("tf_payment_water_hd as a")
            ->leftjoin('tf_payment_water_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.water_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('b.row_no', 'desc')->get();

            $gas = DB::table("tf_payment_gas_hd as a")
            ->leftjoin('tf_payment_gas_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.gas_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('b.row_no', 'desc')->get();

            $parking = DB::table("tf_payment_parking_hd as a")
            ->leftjoin('tf_payment_parking_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.parking_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('a.id')->get();

            $service = DB::table("tf_payment_service_hd as a")
            ->leftjoin('tf_payment_service_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.service_hd_id');
                })
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('a.id')->get();

            $dept = DB::table("tf_payment_all_months")
            ->select(
                'year_month',
                DB::raw('ifnull(manager_fee,0) - ifnull(manager_fee_paid,0) - ifnull(manager_fee_skip,0) as manager_fee_dept'), 
                DB::raw('ifnull(elec_fee,0) - ifnull(elec_fee_paid,0) - ifnull(elec_fee_skip,0) as elec_fee_dept'), 
                DB::raw('ifnull(water_fee,0) - ifnull(water_fee_paid,0)- ifnull(water_fee_skip,0) as water_fee_dept'), 
                DB::raw('ifnull(gas_fee,0) - ifnull(gas_fee_paid,0)- ifnull(gas_fee_skip,0) as gas_fee_dept'), 
                DB::raw('ifnull(service_fee,0) - ifnull(service_fee_paid,0)- ifnull(service_fee_skip,0) as service_fee_dept'), 
                DB::raw('ifnull(parking_fee,0) - ifnull(parking_fee_paid,0)- ifnull(parking_fee_skip,0) as parking_fee_dept'))
            ->where('activation', '=', 1)
            ->where('year_month', '<', $payment[0]->year_month)
            ->where(DB::raw('
                (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
                '<>',
                DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
            ->where('flat_id', '=', $flat_id)        
            ->orderBy('year_month')->get();

            $paid = DB::table("tf_payment_all_months")
            ->where('activation', '=', 1)
            ->where('year_month', '=', $payment[0]->year_month)
            ->select(
                'year_month',
                DB::raw('ifnull(manager_fee_paid,0) as manager_fee_paid'), 
                DB::raw('ifnull(elec_fee_paid,0) as elec_fee_paid'), 
                DB::raw('ifnull(water_fee_paid,0) as water_fee_paid'), 
                DB::raw('ifnull(gas_fee_paid,0) as gas_fee_paid'), 
                DB::raw('ifnull(service_fee_paid,0) as service_fee_paid'), 
                DB::raw('ifnull(parking_fee_paid,0) as parking_fee_paid'))
            ->where('flat_id', '=', $flat_id)        
            ->orderBy('year_month')->get();
            
            $total = DB::table("tf_payment_all_months")
            ->select(
                DB::raw('sum((   ifnull(manager_fee, 0) +
                             ifnull(elec_fee, 0) +
                             ifnull(water_fee, 0) +
                             ifnull(gas_fee, 0) +
                             ifnull(service_fee, 0) +
                             ifnull(parking_fee, 0)) -

                            (ifnull(manager_fee_paid, 0) +
                             ifnull(elec_fee_paid, 0) +
                             ifnull(water_fee_paid, 0) +
                             ifnull(gas_fee_paid, 0) +
                             ifnull(service_fee_paid, 0) +
                             ifnull(parking_fee_paid, 0)) -

                             (ifnull(manager_fee_skip, 0) +
                             ifnull(elec_fee_paid_skip, 0) +
                             ifnull(water_fee_paid_skip, 0) +
                             ifnull(gas_fee_paid_skip, 0) +
                             ifnull(service_fee_paid_skip, 0) +
                             ifnull(parking_fee_paid_skip, 0))) as total_money'))
            ->where('activation', '=', 1)
            ->where('year_month', '<', $payment[0]->year_month)
            ->where(DB::raw('
                (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
                '<>',
                DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
            ->where('flat_id', '=', $flat_id) 
            ->orderBy('year_month')->get();

            $row = array(
                'flat_info'=> $flat_info,
                'id'=>$flat_id,
                'total_money'=>$total[0]->total_money,
                'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
                'payment'=>$payment[0],
                'elec'=>$elec,
                'water'=>$water,
                'gas'=>$gas,
                'parking'=>$parking,
                'service'=>$service,
                'dept'=>$dept,
                'paid'=>$paid,
                'tenement_info'=> $tenement_info[0]
            );
            array_push($paymentnotic_lst, $row);

            $fileName = $year_month . '_' . $flat_info->flat_code . "_paymentnotice".'.pdf';
        
            $fullPath = "{$path}/{$fileName}";
            $urlFile = url("{$folderTemp}/{$fileName}");
            //dd($fullPath);
            $mpdf = new mPDF("utf-8", "A4-P",0,0,0,0,0,0,0);
            $mpdf->useSubstitutions = false;
            $mpdf->simpleTables = true; 
            $mpdf->WriteHTML(View('report.paymentnotice', [ 
                'paymentnotic_lst'=> $paymentnotic_lst])->render());          
            $mpdf->debug = false;
            $mpdf->Output($fullPath, 'F');

            $pdf->addPDF($fullPath, 'all','P');
        }
        $pdf->merge('file', $path . $year_month . '_' . $tenement_info[0]->tenement_code . '_paymentnotice.pdf', 'P');

        return View('report.paymentnotice', [ 
                'paymentnotic_lst'=> $paymentnotic_lst]);
    }
}