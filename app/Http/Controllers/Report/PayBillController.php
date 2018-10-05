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

class PayBillController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($flat_id, $year_month) {
        //dd(123);
        $tenement_id = Auth::user()->tenement_id;

        $utils = new NumberUtil();
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $tf_paid_hd = DB::table("tf_paybill_hd")
        ->where('activation', '=', 1)
        ->where('flat_id', '=', $flat_id)
        ->where('year_month', '=', $year_month)->get();

        $id = $tf_paid_hd[0]->id;

        $tf_paid_dt = DB::table("tf_paybill_dt as a")
        ->leftjoin('mst_payment_types as b', function($join)
            {
                $join->on('a.payment_type', '=', 'b.id');
            })
        ->select('a.*', 'b.name')
        ->where('a.activation', '=', 1)
        ->where('a.paybill_id', '=', $id)
        ->orderBy('a.id')->get();

        $flat_info = DB::table("tenement_flats")
        ->join('tf_paybill_hd', 'tenement_flats.id', '=', 'tf_paybill_hd.flat_id')
        ->select('tenement_flats.*')
        ->where('tenement_flats.activation', '=', 1)
        ->where('tf_paybill_hd.id', '=', $id)
        ->orderBy('id')->get();


        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $sum = DB::table("tf_paybill_dt")
        ->select(DB::raw('SUM(money) as total_money'))
        ->where('activation', '=', 1)
        ->where('paybill_id', '=', $id)->get();

        //dd($tf_paid_dt);
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
        $mpdf->WriteHTML(View('report.paybillall', [ 
            'flat_info'=> $flat_info[0],
            'tf_paid_hd'=>$tf_paid_hd[0],
            'tf_paid_dt'=>$tf_paid_dt,
            'id'=>$id,
            'total_money'=>$sum[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
            'tenement_info'=> $tenement_info[0]])->render());     
        //$mpdf->WriteHTML('Hello World');       
        $mpdf->debug = true;
        $mpdf->Output($fullPath, 'F');

        return View('report.payment', [ 
            'flat_info'=> $flat_info[0],
            'tf_paid_hd'=>$tf_paid_hd[0],
            'tf_paid_dt'=>$tf_paid_dt,
            'id'=>$id,
            'total_money'=>$sum[0]->total_money,
            'total_money_read'=>str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
            'tenement_info'=> $tenement_info[0]
             ]);

        return View('report.payment', [ 
            'flat_info'=> $flat_info[0],
            'tf_paid_hd'=>$tf_paid_hd[0],
            'tf_paid_dt'=>$tf_paid_dt,
            'id'=>$id,
            'tenement_info'=> $tenement_info[0]
             ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_tenement($year_month,$flat_id) {
        $utils = new NumberUtil();
        $downType = '';
        // $pdf = new LynX39; // or use $pdf = new \PDFMerger; for Laravel
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

        $tenement_id = Auth::user()->tenement_id;
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        // DB::statement("
        //     CALL proc_update_paybill('". $tenement_id . "', '" . $year_month . "','". $flat_id ."','" . Auth::user()->id . "')
        // ");

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $downType = 'F';
        if ($flat_id != 'all' ){
            $downType = 'D';


            $flats = DB::table("tenement_flats")
            ->select('*')
            ->where('activation', '=', 1)
            ->where('tenement_id', '=', $tenement_id)
            ->where('id', '=', $flat_id)
            ->whereIn('id', function($query) use ($year_month)
            {
                $query->select(DB::raw("distinct flat_id from tf_paybill_dt where `year_month` = '". $year_month ."'"));
            })
            ->orderBy('flat_code')->get();
        }
        else {
            $flats = DB::table("tenement_flats")
            ->select('*')
            ->where('activation', '=', 1)
            ->where('tenement_id', '=', $tenement_id)
            ->whereIn('id', function($query) use ($year_month)
            {
                $query->select(DB::raw("distinct flat_id from tf_paybill_dt where `year_month` = '". $year_month ."'"));
            })
            ->orderBy('flat_code')->get();
        };
        $folderTemp = "tmp/" . $tenement_info[0]->id . '/' . $year_month . '/paybill/';
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
        foreach ($flats as $flat) {
            $paybill_lst = array();
            $flat_info = $flat;
            $flat_id = $flat_info->id;

            DB::statement("
                CALL proc_update_paybill('". $tenement_id . "', '" . $year_month . "','". $flat_id ."','" . Auth::user()->id . "')");

            $tf_paid_hd = DB::table("tf_paybill_hd")
            ->where('activation', '=', 1)
            ->where('flat_id', '=', $flat_id)
            ->where('year_month', '=', $year_month)->get();

            $id = $tf_paid_hd[0]->id;

            $tf_paid_dt = DB::table("tf_paybill_dt as a")
            ->leftjoin('mst_payment_types as b', function($join)
                {
                    $join->on('a.payment_type', '=', 'b.id');
                })
            ->leftjoin('tf_paybill_hd as c', function($join)
                {
                    $join->on('c.id', '=', 'a.paybill_id');
                })
            ->select('a.*', 'b.name')
            ->where('a.activation', '=', 1)
            ->where('c.activation', '=', 1)
            ->where('a.paybill_id', '=', $id)
            ->where('a.money', '<>', 0)
            ->orderBy('a.id')->get();

            $sum = DB::table("tf_paybill_dt")
            ->select(DB::raw('SUM(money) as total_money'))
            ->where('activation', '=', 1)
            ->where('paybill_id', '=', $id)->get();

            $row = array(
                "flat_info" => $flat_info,
                "tf_paid_hd" => $tf_paid_hd[0], 
                "tf_paid_dt" => $tf_paid_dt, 
                "total_money" =>$sum[0]->total_money,
                "total_money_read" => str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
                'tenement_info'=> $tenement_info[0]
            );
            array_push($paybill_lst, $row);

            $fileName = $year_month . '_' . $flat_info->flat_code . "_paybill".'.pdf';
        
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request) {
        $year_month = $request->year . $request->month;
        $utils = new NumberUtil();

        $tenement_id = Auth::user()->tenement_id;
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        $paybill_lst = array();

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

        foreach ($flats as $flat) {
            $flat_info = $flat;
            $flat_id = $flat_info->id;

            $tf_paid_hd = DB::table("tf_paybill_hd")
            ->where('activation', '=', 1)
            ->where('flat_id', '=', $flat_id)
            ->where('year_month', '=', $year_month)->get();

            $id = $tf_paid_hd[0]->id;

            $tf_paid_dt = DB::table("tf_paybill_dt as a")
            ->leftjoin('mst_payment_types as b', function($join)
                {
                    $join->on('a.payment_type', '=', 'b.id');
                })
            ->select('a.*', 'b.name')
            ->where('a.activation', '=', 1)
            ->where('a.paybill_id', '=', $id)
            ->orderBy('a.id')->get();

            $sum = DB::table("tf_paybill_dt")
            ->select(DB::raw('SUM(money) as total_money'))
            ->where('activation', '=', 1)
            ->where('paybill_id', '=', $id)->get();

            $row = array(
                "flat_info" => $flat_info,
                "tf_paid_hd" => $tf_paid_hd[0], 
                "tf_paid_dt" => $tf_paid_dt, 
                "total_money" =>$sum[0]->total_money,
                "total_money_read" => str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
                'tenement_info'=> $tenement_info[0]
            );
            array_push($paybill_lst, $row);
        }

        $fileName = "abc".'.pdf';

        $folderTemp = "tmp";
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
    
        $fullPath = "{$path}/{$fileName}";
        $urlFile = url("{$folderTemp}/{$fileName}");
        $mpdf = new mPDF("utf-8", "A4",0,0,0,0,0,0,0);
        $mpdf->WriteHTML(View('report.paybillall', [ 
            'paybill_lst'=> $paybill_lst])->render());          
        //$mpdf->debug = true;
        $mpdf->Output($fullPath, 'F');

        //dd($sum[0]->total_money);
        return null;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_tenement_new($year_month,$flat_id) {
        $utils = new NumberUtil();
        $downType = '';
        // $pdf = new LynX39; // or use $pdf = new \PDFMerger; for Laravel
        $pdf = new LynX39\LaraPdfMerger\PdfManage;

        $tenement_id = Auth::user()->tenement_id;
        //Check period user
        if(Auth::user()->confirmed == 0 || $tenement_id == '') {
            return redirect("/home");
        }

        // DB::statement("
        //     CALL proc_update_paybill('". $tenement_id . "', '" . $year_month . "','". $flat_id ."','" . Auth::user()->id . "')
        // ");

        $tenement_info = DB::table("tenements")
        ->select('*')
        ->where('activation', '=', 1)
        ->where('id', '=', $tenement_id)
        ->orderBy('id')->get();

        $downType = 'F';
        if ($flat_id != 'all' ){
            $downType = 'D';


            $flats = DB::table("tenement_flats")
            ->select('*')
            ->where('activation', '=', 1)
            ->where('tenement_id', '=', $tenement_id)
            ->where('id', '=', $flat_id)
            ->whereIn('id', function($query) use ($year_month)
            {
                $query->select(DB::raw("distinct flat_id from tf_paybill_dt where `year_month` = '". $year_month ."'"));
            })
            ->orderBy('flat_code')->get();
        }
        else {
            $flats = DB::table("tenement_flats")
            ->select('*')
            ->where('activation', '=', 1)
            ->where('tenement_id', '=', $tenement_id)
            ->whereIn('id', function($query) use ($year_month)
            {
                $query->select(DB::raw("distinct flat_id from tf_paybill_dt where `year_month` = '". $year_month ."'"));
            })
            ->orderBy('flat_code')->get();
        };
        $folderTemp = "tmp/" . $tenement_info[0]->id . '/' . $year_month . '/paybill/';
        $path = public_path($folderTemp);
        if (!file_exists( $path )) {
            mkdir($path, 0777, true);
        }
        foreach ($flats as $flat) {
            $paybill_lst = array();
            $flat_info = $flat;
            $flat_id = $flat_info->id;

            DB::statement("
                CALL proc_update_paybill('". $tenement_id . "', '" . $year_month . "','". $flat_id ."','" . Auth::user()->id . "')");

            $tf_paid_hd = DB::table("tf_paybill_hd")
            ->where('activation', '=', 1)
            ->where('flat_id', '=', $flat_id)
            ->where('year_month', '=', $year_month)->get();

            $id = $tf_paid_hd[0]->id;

            $tf_paid_dt = DB::table("tf_paybill_dt as a")
            ->leftjoin('mst_payment_types as b', function($join)
                {
                    $join->on('a.payment_type', '=', 'b.id');
                })
            ->leftjoin('tf_paybill_hd as c', function($join)
                {
                    $join->on('c.id', '=', 'a.paybill_id');
                })
            ->select('a.*', 'b.name')
            ->where('a.activation', '=', 1)
            ->where('c.activation', '=', 1)
            ->where('a.paybill_id', '=', $id)
            ->where('a.money', '<>', 0)
            ->orderBy('a.id')->get();

            $sum = DB::table("tf_paybill_dt")
            ->select(DB::raw('SUM(money) as total_money'))
            ->where('activation', '=', 1)
            ->where('paybill_id', '=', $id)->get();

            $row = array(
                "flat_info" => $flat_info,
                "tf_paid_hd" => $tf_paid_hd[0], 
                "tf_paid_dt" => $tf_paid_dt, 
                "total_money" =>$sum[0]->total_money,
                "total_money_read" => str_replace("Mươi Năm", "Mươi Lăm", $utils->convert_number_to_words($sum[0]->total_money)),
                'tenement_info'=> $tenement_info[0]
            );
            array_push($paybill_lst, $row);

            $fileName = $year_month . '_' . $flat_info->flat_code . "_paybill".'.pdf';
        
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