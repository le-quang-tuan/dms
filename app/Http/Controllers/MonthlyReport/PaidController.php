<?php

namespace App\Http\Controllers\MonthlyReport;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\TenementFlat;
use DB;
use yajra\Datatables\Datatables;
use Auth;
use Excel;
use DateTime;

class PaidController extends Controller {

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        // dd(date("YmdH"));
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        return view('monthlyreport.paid');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData(Request $request) {
        //dd(123);     
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        if ($request->date_from != ''){
            $date_from = DateTime::createFromFormat('d/m/Y', $request->date_from);
            $date_from = $date_from->format('Ymd');
        }
        else {
            $date_from = date('Ym01');
        }

        if ($request->date_to != ''){
            $date_to = DateTime::createFromFormat('d/m/Y', $request->date_to);
            $date_to = $date_to->format('Ymd');
        }
        else {
            $date_to = date('Ymd');
        }

        $monthlyReportFlat = DB::table('tenement_flats as a')
                    ->join('tf_paid_hd as b', 'a.id', '=', 'b.flat_id')
                    ->join('tf_paid_dt as c', 'b.id', '=', 'c.paid_id')
                    ->join('mst_payment_types as d', 'd.id', '=', 'c.payment_type')
                    ->select(['a.address', 'a.name', 'a.phone', 'a.area', 'a.persons', 'a.receive_date', 'b.id', 'b.flat_id', 'b.bill_no', 'b.receive_from', 'b.receiver','b.comment','c.money', 'c.year_month', 'c.payment_type', DB::raw('d.name as payment_name'), DB::raw('b.receive_date as paid_receive_date')])
                    ->where('a.activation', 1)
                    ->where('b.activation', 1)
                    ->where('c.activation', 1)
                    ->where('d.activation', 1)
                    ->where('a.id', '<>' , 0)
                    ->where('b.receive_date', '>=' , $date_from)
                    ->where('b.receive_date', '<=' , $date_to)
                    ->where('a.tenement_id', '=', Auth::user()->tenement_id)
                    ->orderBy('a.flat_code', 'asc');

        return Datatables::of($monthlyReportFlat)
                ->make(true);        
    }       
    public function download(Request $request)
    {
        return Excel::create('paid_report' . $request->date_from . $request->date_to, function($excel) use ($request) {
                $excel->sheet('data', function($sheet) use ($request)
                {
                    if ($request->date_from != ''){
                        $date_from = DateTime::createFromFormat('d/m/Y', $request->date_from);
                        $date_from = $date_from->format('Ymd');
                    }
                    else {
                        $date_from = date('Ymd');
                    }

                    if ($request->date_to != ''){
                        $date_to = DateTime::createFromFormat('d/m/Y', $request->date_to);
                        $date_to = $date_to->format('Ymd');
                    }
                    else {
                        $date_to = date('Ymd');
                    }                    
                    $data = array();

                    $header = array();
                    array_push($header,
                        "Căn hộ",
                        "Chủ hộ",
                        "Số Điện Thoại",
                        "Diện tích",
                        "Số người định mức nước",
                        "Ngày bàn giao căn hộ",
                        "Số Phiếu Thu",
                        "Ngày thu",
                        "Phí Quản lý",
                        "Phí Sử Dụng Điện",
                        "Phí Sử Dụng Nước",
                        "Phí Sử Dụng Gas",
                        "Phí Gửi Xe Tháng",
                        "Phí Khác",
                        "Tháng sử dụng",
                        "Ghi Chú"
                    );
                    array_push($data, $header);
                    $flat_arr = array();
                    $paid_arr = array();

                    array_push($paid_arr, array("Căn hộ",
                        "Chủ Hộ",
                        "Số Điện Thoại",
                        "Diện Tích",
                        "Số Người Định Mức Nước",
                        "Ngày Bàn Giao Căn Hộ",
                        "Số Phiếu Thu",
                        "Ngày Thu",
                        "Phí Tháng",
                        "Phí Quản lý",
                        "Phí Sử Dụng Điện",
                        "Phí Sử Dụng Nước",
                        "Phí Sử Dụng Gas",
                        "Phí Gửi Xe Tháng",
                        "Phí Khác",
                        "Ghi Chú"));

                    $tenement_flats = DB::table('tenement_flats')
                    ->where('activation', 1)
                    ->where('tenement_id', '=', Auth::user()->tenement_id)
                    ->get();
                    foreach ($tenement_flats as $value) {
                        $flat_arr[$value->id] = $value;
                    }

                    $paid = DB::table('tenement_flats as a')
                    ->join('tf_paid_hd as b', 'a.id', '=', 'b.flat_id')
                    ->join('tf_paid_dt as c', 'b.id', '=', 'c.paid_id')
                    ->join('mst_payment_types as d', 'd.id', '=', 'c.payment_type')
                    ->select(['b.id', 'b.flat_id', 'b.bill_no', 'b.receive_date', 'b.receive_from', 'b.receiver','b.comment','c.money', 'c.year_month', 'c.payment_type', DB::raw('d.name as payment_name')])
                    ->where('a.activation', 1)
                    ->where('b.activation', 1)
                    ->where('c.activation', 1)
                    ->where('d.activation', 1)
                    ->where('a.id', '<>' , 0)
                    ->where('b.receive_date', '>=' , $date_from)
                    ->where('b.receive_date', '<=' , $date_to)
                    ->where('a.tenement_id', '=', Auth::user()->tenement_id)
                    ->orderBy('a.flat_code', 'asc')
                    ->get();

                    $mSumPaid = 0;
                    $eSumPaid = 0;
                    $wSumPaid = 0;
                    $gSumPaid = 0;
                    $pSumPaid = 0;
                    $sSumPaid = 0;

                    foreach ($paid as $value) {
                        if ($value->payment_type == 1){
                            $mSumPaid += $value->money;
                        } else if ($value->payment_type == 2) {
                            $eSumPaid += $value->money;
                        } else if ($value->payment_type == 3) {
                            $wSumPaid += $value->money;
                        } else if ($value->payment_type == 4) {
                            $gSumPaid += $value->money;
                        } else if ($value->payment_type == 5) {
                            $pSumPaid += $value->money;
                        } else if ($value->payment_type == 6) {
                            $sSumPaid += $value->money;
                        }

                        if (isset($paid_arr[$value->id . '#' . $value->year_month])) {

                            $tmp = $paid_arr[$value->id . '#' . $value->year_month];

                            $mPaid = $tmp[9];
                            $ePaid = $tmp[10];
                            $wPaid = $tmp[11];
                            $gPaid = $tmp[13];
                            $pPaid = $tmp[14];
                            $sPaid = $tmp[14];

                            if ($value->payment_type == 1){
                                $mPaid += $value->money;
                            } else if ($value->payment_type == 2) {
                                $ePaid += $value->money;
                            } else if ($value->payment_type == 3) {
                                $wPaid += $value->money;
                            } else if ($value->payment_type == 4) {
                                $gPaid += $value->money;
                            } else if ($value->payment_type == 5) {
                                $pPaid += $value->money;
                            } else if ($value->payment_type == 6) {
                                $sPaid += $value->money;
                            }
                            $receive_date = "";
                            if ($value->receive_date != ""){
                                $receive_date = $value->receive_date;
                                $receive_date = substr($receive_date, -2) . '/' .  substr($receive_date, -4, 2) . '/' . substr($receive_date, 0, 4);
                            }

                            $flat = $flat_arr[$value->flat_id];

                            $flat_receive_date = "";
                            if ($flat->receive_date != ""){
                                $flat_receive_date = $flat->receive_date;
                                $flat_receive_date = substr($flat_receive_date, -2) . '/' .  substr($flat_receive_date, -4, 2) . '/' . substr($flat_receive_date, 0, 4);
                            }

                            $paid_arr[$value->id . '#' . $value->year_month] = array($flat->address,
                                       $flat->name,
                                       $flat->phone,
                                       $flat->area,
                                       $flat->persons,
                                       $flat_receive_date,
                                       $value->bill_no,
                                       $receive_date,
                                       substr($value->year_month,-2,2) . '/' . substr($value->year_month,0,4),
                                       intval($mPaid),
                                       intval($ePaid),
                                       intval($wPaid),
                                       intval($gPaid),
                                       intval($pPaid),
                                       intval($sPaid),
                                       $value->comment);
                        } 
                        else {
                            $mPaid = 0;
                            $ePaid = 0;
                            $wPaid = 0;
                            $gPaid = 0;
                            $pPaid = 0;
                            $sPaid = 0;

                            $flat = $flat_arr[$value->flat_id];
                            $flat_receive_date = "";
                            if ($flat->receive_date != ""){
                                $flat_receive_date = $flat->receive_date;
                                $flat_receive_date = substr($flat_receive_date, -2) . '/' .  substr($flat_receive_date, -4, 2) . '/' . substr($flat_receive_date, 0, 4);
                            }

                            if ($value->payment_type == 1){
                                $mPaid = $value->money;
                            } else if ($value->payment_type == 2) {
                                $ePaid = $value->money;
                            } else if ($value->payment_type == 3) {
                                $wPaid = $value->money;
                            } else if ($value->payment_type == 4) {
                                $gPaid = $value->money;
                            } else if ($value->payment_type == 5) {
                                $pPaid = $value->money;
                            } else if ($value->payment_type == 6) {
                                $sPaid = $value->money;
                            }

                            $receive_date = "";
                            if ($value->receive_date != ""){
                                $receive_date = $value->receive_date;
                                $receive_date = substr($receive_date, -2) . '/' .  substr($receive_date, -4, 2) . '/' . substr($receive_date, 0, 4);
                            }
                            $paid_arr[$value->id . '#' . $value->year_month] = array($flat->address,
                                       $flat->name,
                                       $flat->phone,
                                       $flat->area,
                                       $flat->persons,
                                       $flat_receive_date,
                                       $value->bill_no,
                                       $receive_date,
                                       substr($value->year_month,-2,2) . '/' . substr($value->year_month,0,4),
                                       intval($mPaid),
                                       intval($ePaid),
                                       intval($wPaid),
                                       intval($gPaid),
                                       intval($pPaid),
                                       intval($sPaid),
                                       $value->comment);
                        }
                    }
                    $paid_arr['SUM'] = array('',
                                       '',
                                       '',
                                       '',
                                       '',
                                       '',
                                       '',
                                       '',
                                       'Tổng Tiền',
                                       $mSumPaid,
                                       $eSumPaid,
                                       $wSumPaid,
                                       $gSumPaid,
                                       $pSumPaid,
                                       $sSumPaid,
                                       $mSumPaid + $eSumPaid + $wSumPaid + $gSumPaid + $pSumPaid + $sSumPaid);
                    // dd($paid_arr);
                    for ($i = 0; $i <= count($paid_arr); $i++){
                        $sheet->setBorder('A'. $i .':P'. $i, 'thin');
                        // $sheet->setFontWeight('A'. $i .':P'. $i, 'thin');
                    }
                    $sheet->cells('A1:P1', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A' . count($paid_arr) . ':I' . count($paid_arr));
                    $sheet->cell('A' . count($paid_arr), function($cell) {
                        $cell->setValue('Tổng Tiền');
                        $cell->setAlignment('right');
                    });
                    // $sheet->setBorder('A1:P1', 'thin');

                    $sheet->fromArray($paid_arr, null, 'A1', false, false);
                });

        })->download("xls");
    } 
}