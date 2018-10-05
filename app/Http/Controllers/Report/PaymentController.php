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

class PaymentController extends Controller {

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

        $tf_paid_hd = DB::table("tf_paid_hd")
        ->where('activation', '=', 1)
        ->where('id', '=', $id)
        ->orderBy('id')->get();

        $tf_paid_dt = DB::table("tf_paid_dt as a")
        ->leftjoin('mst_payment_types as b', function($join)
            {
                $join->on('a.payment_type', '=', 'b.id');
            })
        ->select('a.*', 'b.name')
        ->where('a.activation', '=', 1)
        ->where('a.paid_id', '=', $id)
        ->orderBy('a.id')->get();

        $flat_info = DB::table("tenement_flats")
        ->join('tf_paid_hd', 'tenement_flats.id', '=', 'tf_paid_hd.flat_id')
        ->select('tenement_flats.*')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tf_paid_hd.id', '=', $id)
        ->orderBy('id')->get();

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $sum = DB::table("tf_paid_dt")
        ->select(DB::raw('SUM(money) as total_money'))
        ->where('activation', '=', 1)
        ->where('paid_id', '=', $id)->get();

        $fileName = "sample".'.pdf';

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
    
        $fullPath = "{$path}/{$fileName}";
        $urlFile = url("{$folderTemp}/{$fileName}");
        $mpdf = new mPDF("utf-8", "A4",0,0,0,0,0,0,0);

        $mpdf->WriteHTML(View('report.paybillall', [ 
            'flat_info'=> $flat_info[0],
            'tf_paid_hd'=>$tf_paid_hd[0],
            'tf_paid_dt'=>$tf_paid_dt,
            'id'=>$id,
            'total_money'=>$sum[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
            'tenement_info'=> $tenement_info[0]])->render());     
        $mpdf->debug = true;
        $mpdf->Output($fullPath, 'F');

        return View('report.paybillall', [ 
            'flat_info'=> $flat_info[0],
            'tf_paid_hd'=>$tf_paid_hd[0],
            'tf_paid_dt'=>$tf_paid_dt,
            'id'=>$id,
            'total_money'=>$sum[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
            'tenement_info'=> $tenement_info[0]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function report($id) {
        //dd(123);
        $tenement_id = Auth::user()->tenement_id;

        $utils = new NumberUtil();
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tf_paid_hd = DB::table("tf_paid_hd")
        ->where('activation', '=', 1)
        ->where('id', '=', $id)
        ->orderBy('id')->get();

        $tf_paid_dt = DB::table("tf_paid_dt as a")
        ->leftjoin('mst_payment_types as b', function($join)
            {
                $join->on('a.payment_type', '=', 'b.id');
            })
        ->select('a.*', 'b.name')
        ->where('a.activation', '=', 1)
        ->where('a.paid_id', '=', $id)
        ->orderBy('a.id')->get();

        $flat_info = DB::table("tenement_flats")
        ->join('tf_paid_hd', 'tenement_flats.id', '=', 'tf_paid_hd.flat_id')
        ->select('tenement_flats.*')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tf_paid_hd.id', '=', $id)
        ->orderBy('id')->get();

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $sum = DB::table("tf_paid_dt")
        ->select(DB::raw('SUM(money) as total_money'))
        ->where('activation', '=', 1)
        ->where('paid_id', '=', $id)->get();

        $fileName = "sample".'.pdf';

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
    
        $fullPath = "{$path}/{$fileName}";
        $urlFile = url("{$folderTemp}/{$fileName}");
        $mpdf = new mPDF("utf-8", "A4",0,0,0,0,0,0,0);

        $mpdf->WriteHTML(View('report.paymentnotice', [ 
            'flat_info'=> $flat_info[0],
            'tf_paid_hd'=>$tf_paid_hd[0],
            'tf_paid_dt'=>$tf_paid_dt,
            'id'=>$id,
            'total_money'=>$sum[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
            'tenement_info'=> $tenement_info[0]])->render());     
        $mpdf->debug = true;
        $mpdf->Output($fullPath, 'F');

        return View('report.paymentnotice', [ 
            'flat_info'=> $flat_info[0],
            'tf_paid_hd'=>$tf_paid_hd[0],
            'tf_paid_dt'=>$tf_paid_dt,
            'id'=>$id,
            'total_money'=>$sum[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
            'tenement_info'=> $tenement_info[0]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paid_report($id, $print_type) {
        $utils = new NumberUtil();
        $downType = '';
        // $pdf = new LynX39; // or use $pdf = new \PDFMerger; for Laravel
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

        $tenement_id = Auth::user()->tenement_id;
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $downType = 'D';

        $flats = DB::table("tenement_flats as a")
        ->leftjoin('tf_paid_hd as b', function($join)
        {
            $join->on('a.id', '=', 'b.flat_id');
        })
        ->select('a.*')
        ->where('a.activation', '=', 1)
        ->where('b.activation', '=', 1)
        ->where('b.id', '=', $id)
        ->where('tenement_id', '=', $tenement_id)->get();
        
        $folderTemp = "tmp/" . $tenement_info[0]->id . '/' . $flats[0]->id . '/paid/';
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
        foreach ($flats as $flat) {
            $paybill_lst = array();
            $flat_info = $flat;
            $flat_id = $flat_info->id;
            $tf_paid_hd = DB::table("tf_paid_hd")
            ->where('activation', '=', 1)
            ->where('id', '=', $id)->get();

            $id = $tf_paid_hd[0]->id;

            $tf_paid_dt = DB::table("tf_paid_dt as a")
            ->leftjoin('mst_payment_types as b', function($join)
                {
                    $join->on('a.payment_type', '=', 'b.id');
                })
            ->leftjoin('tf_paid_hd as c', function($join)
                {
                    $join->on('c.id', '=', 'a.paid_id');
                })
            ->select('a.*', 'b.name')
            ->where('a.activation', '=', 1)
            ->where('c.activation', '=', 1)
            ->where('a.paid_id', '=', $id)
            ->orderBy('a.id')->get();

            $sum = DB::table("tf_paid_dt")
            ->select(DB::raw('SUM(money) as total_money'))
            ->where('activation', '=', 1)
            ->where('paid_id', '=', $id)->get();

            $row = array(
                "flat_info" => $flat_info,
                "tf_paid_hd" => $tf_paid_hd[0], 
                "tf_paid_dt" => $tf_paid_dt, 
                "total_money" =>$sum[0]->total_money,
                "total_money_read" => str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
                'tenement_info'=> $tenement_info[0]
            );
            array_push($paybill_lst, $row);

            $fileName = $flat_info->flat_code . "_paidbill".'.pdf';
        
            $fullPath = "{$path}/{$fileName}";
            $urlFile = url("{$folderTemp}/{$fileName}");
            //dd($fullPath);
            $mpdf = new mPDF("utf-8", "A4",0,0,0,0,0,0,0);
            $mpdf->useSubstitutions = false;
            $mpdf->simpleTables = true; 
            $mpdf->WriteHTML(View('report.paybillall', [ 
                'paybill_lst'=> $paybill_lst])->render());          
            $mpdf->debug = false;
            if ($downType == 'D' ){
                $mpdf->Output($fileName, $downType);
            }
            else{
                $mpdf->Output($fullPath, $downType);
            }
        }

        return View('report.paybillall', [ 
            'paybill_lst'=> $paybill_lst]);
    }
}