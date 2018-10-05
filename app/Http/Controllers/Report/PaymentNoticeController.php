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
use Response;

class PaymentNoticeController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
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
        ->join('tf_paid_hd', 'tenement_flats.id', '=', 'tf_paid_hd.flat_id')
        ->select('tenement_flats.*')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tf_paid_hd.id', '=', $id)
        ->orderBy('id')->get();

        $payment = DB::table("tf_payment_all_months")
        ->where('activation', '=', 1)
        ->where('id', '=', $id)->get();

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
        ->orderBy('b.id','desc')->get();

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

        $fileName = "sample".'.pdf';

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
    
        $fullPath = "{$path}/{$fileName}";
        $urlFile = url("{$folderTemp}/{$fileName}");
        $mpdf = new mPDF("utf-8", "A4",0,0,0,0,0,0,0);
// dd($utils->convert_number_to_wordsEn($total[0]->total_money));
        //$mpdf = new mPDF('utf-8', 'A4-L');
        $mpdf->WriteHTML(View('report.paymentnotice', [ 
            'flat_info'=> $flat_info[0],
            'id'=>$id,
            'total_money'=>$total[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
            'total_money_readEn'=>$utils->convert_number_to_wordsEn($total[0]->total_money),
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
        //$pdf = new LynX39;
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

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
        // ->where('receive_date', '<>', null)
        // ->where('id', '>', 505)
        ->where('tenement_id', '=', $tenement_id)
        ->orderBy('flat_code')->get();

        $folderTemp = "tmp/" . $tenement_info[0]->id . '/' . $year_month . '/paymentnotice/';
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
            ->where('year_month', '<=', $payment[0]->year_month)
            ->where(DB::raw('
                (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
                '<>',
                DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
            ->where('flat_id', '=', $flat_id) 
            ->orderBy('year_month')->get();

            if ($total[0]->total_money <= 0)
                continue;

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

            // $water = DB::table("tf_payment_water_hd as a")
            // ->leftjoin('tf_payment_water_dt as b', function($join)
            //     {
            //         $join->on('a.id', '=', 'b.water_hd_id');
            //     })
            // ->select('a.*', 'b.*')
            // ->where('a.activation', '=', 1)
            // ->where('a.year_month', '=', $payment[0]->year_month)
            // ->where('a.flat_id', '=', $flat_id)
            // ->orderBy('b.row_no', 'desc')->get();

            $water = DB::table("tf_payment_water_hd as a")
            ->leftjoin('tf_payment_water_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.water_hd_id');
                }, 'left outer')
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('a.water_used_id', 'asc')
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
            ->orderBy('a.id')->get();

            $parking = DB::table("tf_payment_parking_hd as a")
            ->leftjoin('tf_payment_parking_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.parking_hd_id');
                })
            ->select(DB::raw('count(*) as total_count '), 
                    DB::raw('b.vehicle_type as vehicle_type'),
                    DB::raw('b.price as price'),
                    DB::raw('sum(b.total_money) as total_money'))
            ->where('a.activation', '=', 1)
            ->where('b.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->groupBy('a.id', 'b.vehicle_type', 'b.price', 'a.year_month', 'a.flat_id')
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
                DB::raw('ifnull(parking_fee_paid,0) as parking_fee_paid'),
                DB::raw('ifnull(manager_fee,0) as manager_fee'), 
                DB::raw('ifnull(elec_fee,0) as elec_fee'), 
                DB::raw('ifnull(water_fee,0) as water_fee'), 
                DB::raw('ifnull(gas_fee,0) as gas_fee'), 
                DB::raw('ifnull(service_fee,0) as service_fee'), 
                DB::raw('ifnull(parking_fee,0) as parking_fee'))
            ->where('flat_id', '=', $flat_id)        
            ->orderBy('year_month')->get();

            $row = array(
                'flat_info'=> $flat_info,
                'id'=>$flat_id,
                'total_money'=>$total[0]->total_money,
                'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
                'total_money_readEn'=>ucwords(str_replace(",", " ", $utils->convert_number_to_wordsEn($total[0]->total_money))), 
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
            $mpdf = new mPDF("utf-8", "A4-P",0,0,0,0,0,0,0);
            $mpdf->useSubstitutions = false;
            $mpdf->simpleTables = true; 
            $mpdf->WriteHTML(View('report.paymentnotice', [ 
                'paymentnotic_lst'=> $paymentnotic_lst])->render());          
            $mpdf->debug = false;
            $mpdf->Output($fullPath, 'F');
            //break;
            //$pdf->addPDF($fullPath, 'all','P');
        }
        // $pdf->merge('file', $path . $year_month . '_' . $tenement_info[0]->tenement_code . '_paymentnotice.pdf', 'P');

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
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

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

        $folderTemp = "tmp/" . $tenement_info[0]->id . '/' . $year_month . '/paymentnotice/';
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
            ->where('year_month', '<=', $payment[0]->year_month)
            ->where(DB::raw('
                (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0))'), 
                '<>',
                DB::raw('(ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))'))
            ->where('flat_id', '=', $flat_id) 
            ->orderBy('year_month')->get();

            // if ($total[0]->total_money <= 0){
            //     continue;
            // }

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
                }, 'left outer')
            ->select('a.*', 'b.*')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->orderBy('a.water_used_id', 'asc')
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
            ->select(DB::raw('count(*) as total_count '), 
                    DB::raw('b.vehicle_type as vehicle_type'),
                    DB::raw('b.price as price'),
                    DB::raw('sum(b.total_money) as total_money'))
            ->where('a.activation', '=', 1)
            ->where('b.activation', '=', 1)
            ->where('a.year_month', '=', $payment[0]->year_month)
            ->where('a.flat_id', '=', $flat_id)
            ->groupBy('a.id', 'b.vehicle_type', 'b.price', 'a.year_month', 'a.flat_id')
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
// dd($service);
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

            $dept_dic = array();
            foreach ($dept as $dept_dt) {
                $dept_dic[$dept_dt->year_month] = clone $dept_dt;
                $dept_dic[$dept_dt->year_month]->water_fee_dept = 0;

                if ($dept_dt->water_fee_dept != 0){
                    $water_year_month = date('Ym',(strtotime('-1 day',strtotime($dept_dt->year_month . "01"))));

                    if (array_key_exists($water_year_month, $dept_dic)){
                        $tmp = $dept_dic[$water_year_month];
                        $tmp->water_fee_dept = $dept_dt->water_fee_dept;
                    } else {
                        $tmp = clone $dept_dt;

                        $tmp->year_month = $water_year_month;
                        $tmp->manager_fee_dept = 0;
                        $tmp->elec_fee_dept = 0;
                        $tmp->water_fee_dept = $dept_dt->water_fee_dept;                        
                        $tmp->gas_fee_dept = 0;
                        $tmp->service_fee_dept = 0;
                        $tmp->parking_fee_dept = 0;

                        $dept_dic[$water_year_month] = $tmp;
                    }
                }
            }
            //dd($dept_dic);

            $dept_year_month = array();
            foreach ($dept_dic as $dept_item) {
                if (!(  $dept_item->manager_fee_dept == 0 &&  
                        $dept_item->elec_fee_dept == 0 &&
                        $dept_item->water_fee_dept == 0 &&                   
                        $dept_item->gas_fee_dept == 0 &&
                        $dept_item->service_fee_dept == 0 &&
                        $dept_item->parking_fee_dept == 0)){
                    $dept_year_month[$dept_item->year_month] = clone $dept_item;  
                }
            }
            // dd($abc);

            // $dept_tmp = array();
            // // array_push($header,
            // foreach ($dept as $dept_dt) {
            //     $tmp = clone $dept_dt;
            //     if ($dept_dt->water_fee_dept > 0){
            //         $water_year_month = date('Ym',(strtotime('-1 day',strtotime($dept_dt->year_month . "01"))));

            //         $tmp->water_fee_dept = 0;
            //         //dd(gettype($tmp));
            //         array_push($dept_tmp,$tmp);

            //         $tmp = clone $dept_dt;
            //         $tmp->year_month = $water_year_month;
            //         //dd(gettype($tmp));
            //         array_push($dept_tmp,$tmp);
            //     }
            // }
            // dd($dept_tmp);

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
                DB::raw('ifnull(parking_fee_paid,0) as parking_fee_paid'),
                DB::raw('ifnull(manager_fee,0) as manager_fee'), 
                DB::raw('ifnull(elec_fee,0) as elec_fee'), 
                DB::raw('ifnull(water_fee,0) as water_fee'), 
                DB::raw('ifnull(gas_fee,0) as gas_fee'), 
                DB::raw('ifnull(service_fee,0) as service_fee'), 
                DB::raw('ifnull(parking_fee,0) as parking_fee'))
            ->where('flat_id', '=', $flat_id)        
            ->orderBy('year_month')->get();

            // dd($paid);
            $row = array(
                'flat_info'=> $flat_info,
                'id'=>$flat_id,
                'total_money'=>$total[0]->total_money,
                'total_money_read'=>ucfirst(strtolower(str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)))),
                'total_money_readEn'=>ucfirst(str_replace(",", " ", $utils->convert_number_to_wordsEn($total[0]->total_money))),                
                'payment'=>$payment[0],
                'elec'=>$elec,
                'water'=>$water,
                'gas'=>$gas,
                'parking'=>$parking,
                'service'=>$service,
                // 'dept'=>$dept,
                'dept'=>$dept_year_month,
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

            // $mpdf->Output($fullPath, 'F');
            $mpdf->Output($fileName, 'D');

            // $pdf->addPDF($fullPath, 'all','P');
        }
        // $pdf->merge('file', $path . $year_month . '_' . $tenement_info[0]->tenement_code . '_paymentnotice.pdf', 'P');

        return View('report.paymentnotice', [ 
                'paymentnotic_lst'=> $paymentnotic_lst]);
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dept_notice($time) {
        $tenement_id = Auth::user()->tenement_id;
        //$pdf = new LynX39;
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

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

        $folderTemp = "tmp/" . $tenement_info[0]->id . '/dept_notice/' . $time . '/';
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }

        $paymentnotic_lst = array();

        foreach ($flats as $flat) {
            $paymentnotic_lst = array();
            $flat_info = $flat;
            $flat_id = $flat_info->id;

            $total = DB::table("tf_payment_all_months")
            ->select(
                DB::raw('flat_id,
                            sum((ifnull(manager_fee, 0) +
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
            ->where('flat_id', '=', $flat_id)->get();

            if ($total[0]->total_money <= 0)
                continue;

            $row = array(
                'flat_info'=> $flat_info,
                'id'=>$flat_id,
                'total_money'=>$total[0]->total_money,
                'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
                'total_money_readEn'=>ucwords(str_replace(",", " ", $utils->convert_number_to_wordsEn($total[0]->total_money))), 
                'tenement_info'=> $tenement_info[0]
            );
            array_push($paymentnotic_lst, $row);

            $fileName = $flat_info->flat_code . '_deptnotice.pdf';
            $fullPath = "{$path}/{$fileName}";
            $urlFile = url("{$folderTemp}/{$fileName}");
            $mpdf = new mPDF("utf-8", "A4-P",0,0,0,0,0,0,0);
            $mpdf->useSubstitutions = false;
            $mpdf->simpleTables = true; 
            $mpdf->WriteHTML(View('report.deptnotice_' . $time , [ 
                'paymentnotic_lst'=> $paymentnotic_lst])->render());          
            $mpdf->debug = false;
            $mpdf->Output($fullPath, 'F');
        }

        return View('report.deptnotice_' . $time, [ 
                'paymentnotic_lst'=> $paymentnotic_lst]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dept_notice_files($time) {
        //Laragrowl::message('Your stuff has been stored', 'success');
        $tenement_id = Auth::user()->tenement_id;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_id = Auth::user()->tenement_id;
        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $fullPath = "{$path}/{$tenement_id}/dept_notice/{$time}";
        if (!file_exists( $fullPath )) {
            mkdir($fullPath, 0777, true);
        }

        $filelistTmp = array_diff(scandir($fullPath), array('.', '..'));

        $data = array();
        $rows = array();
        $i = 1;

        foreach($filelistTmp as $file){
            list($flat_code, $type) = explode('_', $file);
            $rows = array();
            array_push($rows, $i, $flat_code, $time . '/' . $file);
            array_push($data, $rows);
            $i++;
        }
        return View('report.dept_notice_files',[ 
            'time'=> $time,
            'tenement_id'=> $tenement_id,
            'json'=> json_encode($data),
        ]);
    }

        /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function download_deptnotice($time, $file_name) {
        $tenement_id = Auth::user()->tenement_id;

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $file = "{$path}/{$tenement_id}/dept_notice/{$time}/{$file_name}";

        return Response::download($file);
    }
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview_deptnotice($time, $file_name) {
        $tenement_id = Auth::user()->tenement_id;

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $file = "{$path}/{$tenement_id}/dept_notice/{$time}/{$file_name}";

        return Response::make(file_get_contents($file), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$file_name.'"'        
            ]); 
    } 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dept_notices_merge($time) {
        $tenement_id = Auth::user()->tenement_id;
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement = Tenement::where('id',$tenement_id)->where('activation',1)->orderBy('name', 'asc')->get();

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        $fullPath = "{$path}/{$tenement_id}/dept_notice/$time";
        if (!file_exists( $fullPath )) {
            mkdir($fullPath, 0777, true);
        }

        $filelistTmp = array_diff(scandir($fullPath), array('.', '..'));

        $data = array();
        $rows = array();
        $i = 1;

        // dd($filelistTmp);

        foreach($filelistTmp as $file){
            list($flat_code, $type) = explode('_', $file);
            $rows = array();
            array_push($rows, $i, $flat_code, $time . '/' . $file);
            array_push($data, $rows);
            $i++;

            $pdf->addPDF($fullPath . '/' . $file, 'all','P');
        }

        // dd($pdf);
        $pdf->merge('file', $fullPath . '/' . $tenement[0]->tenement_code .  '_deptnotice.pdf', 'P');

        return View('report.dept_notice_files',[ 
            'time'=> $time,
            'tenement_id'=> $tenement_id,
            'json'=> json_encode($data),
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_tenement_new($year_month) {
        $tenement_id = Auth::user()->tenement_id;
        //$pdf = new LynX39;
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

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
        $paymentInfo = DB::table(DB::raw("
            (select 
                flat.id, flat.name, flat.phone, flat.area, flat.address, flat.persons, a.`year_month`, flat.flat_code,
                a.manager_fee, a.manager_fee_paid, 
                a.elec_fee, a.elec_fee_paid, 
                a.water_fee, a.water_fee_paid, 
                a.gas_fee, a.gas_fee_paid,
                a.parking_fee, a.parking_fee_paid,
                a.service_fee, a.service_fee_paid, c.total_money
            from tenement_flats flat left join tf_payment_all_months a on flat.id = a.flat_id  left join
                (Select flat_id, sum((   ifnull(manager_fee, 0) +
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
                                             ifnull(parking_fee_skip, 0))) as total_money 
                from tf_payment_all_months
                where activation = 1 and `year_month` <= '". $year_month ."' and
                (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0)) <> (ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0))
                group by flat_id) as c on a.flat_id = c.flat_id
            where a.`year_month` = '". $year_month ."' and flat.tenement_id = ". $tenement_id .") A"))->get();
        $flatArr = array();
        foreach ($paymentInfo as $flat) {
            $info = array();
            $total_money = $flat->total_money;
            $flat->total_money_read = str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total_money));
            $flat->total_money_readEn = ucwords(str_replace(",", " ", $utils->convert_number_to_wordsEn($total_money))); 

            array_push($info, $flat, array(), array(), array(), array(), array(), array());
            $flatArr[$flat->id] = $info;
        }

        $folderTemp = "tmp/" . $tenement_info[0]->id . '/' . $year_month . '/paymentnotice/';
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }

        $paymentnotic_lst = array();

        $elecInfo = DB::table("tf_payment_elec_hd as a")
            ->leftjoin('tf_payment_elec_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.elec_hd_id');
                })
            ->select('a.flat_id','a.id','a.date_from', 'a.date_to', 'a.new_index_hd', 'a.old_index_hd','a.tenement_id', 'a.elec_used_id', 'b.price', 'b.total', 'a.other_fee01_money_hd', 'a.vat_money_hd', 'a.other_fee02_money_hd', 'a.total_hd', 'b.from_index', 'b.to_index')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $year_month)
            ->where('a.tenement_id', '=', $tenement_id)
            ->orderBy('a.flat_id')->get();
        foreach ($elecInfo as $elec) {
            array_push($flatArr[$elec->flat_id][1], $elec);
        }

        $waterInfo = DB::table("tf_payment_water_hd as a")
            ->leftjoin('tf_payment_water_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.water_hd_id');
                }, 'left outer')
            ->select('a.flat_id','a.id','a.date_from', 'a.date_to', 'a.new_index_hd', 'a.old_index_hd', 'a.used_deduct','a.tenement_id', 'a.water_used_id', 'b.price', 'b.total', 'a.other_fee01_money_hd', 'a.vat_hd', 'a.vat_money_hd', 'a.other_fee02_money_hd', 'a.total_hd', 'b.from_index', 'b.to_index', 'b.water_tariff_name', 'b.mount')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $year_month)
            ->where('a.tenement_id', '=', $tenement_id)
            ->orderBy('a.water_used_id', 'asc')
            ->orderBy('b.row_no', 'desc')->get();
        foreach ($waterInfo as $water) {
            array_push($flatArr[$water->flat_id][2], $water);
        }

        $gasInfo = DB::table("tf_payment_gas_hd as a")
            ->leftjoin('tf_payment_gas_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.gas_hd_id');
                })
            ->select('a.flat_id','a.id','a.date_from', 'a.date_to', 'a.new_index_hd', 'a.old_index_hd','a.tenement_id', 'a.gas_used_id', 'b.price', 'b.total', 'a.other_fee01_money_hd', 'a.vat_money_hd', 'a.other_fee02_money_hd', 'a.total_hd', 'b.from_index', 'b.to_index')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $year_month)
            ->where('a.tenement_id', '=', $tenement_id)
            ->orderBy('a.id')->get();
        foreach ($gasInfo as $gas) {
            array_push($flatArr[$gas->flat_id][3], $gas);
        }

        $parkingInfo = DB::table("tf_payment_parking_hd as a")
            ->leftjoin('tf_payment_parking_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.parking_hd_id');
                })
            ->select(DB::raw('count(*) as total_count '), 
                    DB::raw('b.vehicle_type as vehicle_type'),
                    DB::raw('b.price as price'),
                    DB::raw('a.flat_id'),
                    DB::raw('sum(b.total_money) as total_money'))
            ->where('a.activation', '=', 1)
            ->where('b.activation', '=', 1)
            ->where('a.year_month', '=', $year_month)
            ->where('a.tenement_id', '=', $tenement_id)
            ->groupBy('a.id', 'b.vehicle_type', 'b.price', 'a.year_month', 'a.flat_id')
            ->orderBy('a.id')->get();
        foreach ($parkingInfo as $parking) {
            // array_push($info, $flat->id, $flat, array(), array(), array(), array(), array(), array());            
            array_push($flatArr[$parking->flat_id][4], $parking);
        }

        $serviceInfo = DB::table("tf_payment_service_hd as a")
            ->leftjoin('tf_payment_service_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.service_hd_id');
                })
            ->select('a.flat_id','a.id','a.tenement_id','a.service_date_from','a.service_date_to', 'b.price', 'b.mount', 'b.service', 'a.vat_money', 'a.other_fee01_money', 'a.other_fee02_money', 'a.total_money')
            ->where('a.activation', '=', 1)
            ->where('a.year_month', '=', $year_month)
            ->where('a.tenement_id', '=', $tenement_id)
            ->orderBy('a.id')->get();
        foreach ($serviceInfo as $service) {
            array_push($flatArr[$service->flat_id][5], $service);
        }

        $deptInfo = DB::table(DB::raw("
            (
            Select
                    flat_id, `year_month`,
                    ifnull(manager_fee,0) - ifnull(manager_fee_paid,0) - ifnull(manager_fee_skip,0) as manager_fee_dept,
                    ifnull(elec_fee,0) - ifnull(elec_fee_paid,0) - ifnull(elec_fee_skip,0) as elec_fee_dept,
                    ifnull(water_fee,0) - ifnull(water_fee_paid,0)- ifnull(water_fee_skip,0) as water_fee_dept,
                    ifnull(gas_fee,0) - ifnull(gas_fee_paid,0)- ifnull(gas_fee_skip,0) as gas_fee_dept,
                    ifnull(service_fee,0) - ifnull(service_fee_paid,0)- ifnull(service_fee_skip,0) as service_fee_dept,
                    ifnull(parking_fee,0) - ifnull(parking_fee_paid,0)- ifnull(parking_fee_skip,0) as parking_fee_dept
            from    tf_payment_all_months
            where activation = 1 and 
                    `year_month` < '". $year_month ."' and tenement_id = '". $tenement_id ."' and
                    (ifnull(manager_fee,0) + ifnull(elec_fee,0) + ifnull(water_fee,0) + ifnull(gas_fee,0) + ifnull(service_fee ,0) + ifnull(parking_fee,0)) > 
                   (
                        ifnull(manager_fee_paid,0) + ifnull(elec_fee_paid,0) + ifnull(water_fee_paid,0) + ifnull(gas_fee_paid,0) + ifnull(service_fee_paid,0) + ifnull(parking_fee_paid,0) +
                        ifnull(manager_fee_skip,0) + ifnull(elec_fee_skip,0) + ifnull(water_fee_skip,0) + ifnull(gas_fee_skip,0) + ifnull(service_fee_skip,0) + ifnull(parking_fee_skip,0))
            group by flat_id, `year_month`
            order by flat_id, `year_month`) as A"))->get();
        foreach ($deptInfo as $dept) {
            array_push($flatArr[$dept->flat_id][6], $dept);
        }

        // $row = array(
        //     'total_money'=>$total[0]->total_money,
        //     'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($total[0]->total_money)),
        //     'total_money_readEn'=>ucwords(str_replace(",", " ", $utils->convert_number_to_wordsEn($total[0]->total_money))), 
        //     'payment'=>$payment[0],
        //     'elec'=>$elec,
        //     'water'=>$water,
        //     'gas'=>$gas,
        //     'parking'=>$parking,
        //     'service'=>$service,
        //     'dept'=>$dept,
        //     'paid'=>$paid,
        //     'tenement_info'=> $tenement_info[0]
        // );
        // array_push($paymentnotic_lst, $row);
        foreach ($flatArr as $flat_info) {
            $fileName = $year_month . '_' . $flat_info[0]->flat_code . "_paymentnotice".'.pdf';
            // $fileName = $year_month . '_' . $tenement_id . "_paymentnotice".'.pdf';
            $fullPath = "{$path}/{$fileName}";
            $urlFile = url("{$folderTemp}/{$fileName}");
            $mpdf = new mPDF("utf-8", "A4-P",0,0,0,0,0,0,0);
            $mpdf->useSubstitutions = false;
            $mpdf->simpleTables = true; 
            $mpdf->WriteHTML(View('report.paymentnotice_new', [ 
                'paymentnotice'=> $flat_info, 'tenement_info' => $tenement_info[0]])->render());          
            $mpdf->debug = false;
            $mpdf->Output($fullPath, 'F');
        }
// dd($temp);
// dd($flatArr["1"][0]->year_month);
        // return View('report.paymentnotice_new', [ 
        //         'paymentnotic_lst'=> $flatArr, 'tenement_info' => $tenement_info[0]]);
    }
}