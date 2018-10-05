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
use App\LaraBase\NumberUtil;

class FeePaidController extends Controller {

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
        return view('monthlyreport.feepaid');
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
                    $tenement_flats = DB::table('tenement_flats')
                    ->where('activation', 1)
                    ->where('tenement_id', '=', Auth::user()->tenement_id)
                    ->get();

                    $payment_all_months = DB::table("tf_payment_all_months as a")
                    ->leftjoin('tenement_flats as b', function($join)
                    {
                        $join->on('a.flat_id', '=', 'b.id');
                        $join->on('b.activation', '=', DB::RAW("1"));
                        $join->on('b.tenement_id', '=', DB::RAW(Auth::user()->tenement_id));
                    })
                    ->select(['a.*'])
                    ->where('year_month', '>=' , $request->year_from . $request->month_from )                    
                    ->where('year_month', '<=' , $request->year_to . $request->month_to )                    
                    ->get();

                    $payment_arr = array();
                    foreach ($payment_all_months as $value) {
                        $payment_arr[$value->flat_id . '#' . $value->year_month] = array(
                            intval($value->manager_fee),
                            intval($value->elec_fee),
                            intval($value->water_fee),
                            intval($value->gas_fee),
                            intval($value->parking_fee),
                            intval($value->service_fee),


                            intval($value->manager_fee_paid),
                            intval($value->elec_fee_paid),
                            intval($value->water_fee_paid),
                            intval($value->gas_fee_paid),
                            intval($value->parking_fee_paid),
                            intval($value->service_fee_paid),

                            intval(
                                intval($value->manager_fee) +
                                intval($value->elec_fee) +
                                intval($value->water_fee) +
                                intval($value->gas_fee) +
                                intval($value->parking_fee) +
                                intval($value->service_fee)
                            ),

                            intval(
                                intval($value->manager_fee_paid) +
                                intval($value->elec_fee_paid) +
                                intval($value->water_fee_paid) +
                                intval($value->gas_fee_paid) +
                                intval($value->parking_fee_paid) +
                                intval($value->service_fee_paid)
                            ),

                            intval(
                                intval($value->manager_fee_skip) +
                                intval($value->elec_fee_skip) +
                                intval($value->water_fee_skip) +
                                intval($value->gas_fee_skip) +
                                intval($value->parking_fee_skip) +
                                intval($value->service_fee_skip)
                            ),

                            intval(
                                (intval($value->manager_fee) +
                                intval($value->elec_fee) +
                                intval($value->water_fee) +
                                intval($value->gas_fee) +
                                intval($value->parking_fee) +
                                intval($value->service_fee))
                                -
                                (
                                (intval($value->manager_fee_paid) +
                                intval($value->elec_fee_paid) +
                                intval($value->water_fee_paid) +
                                intval($value->gas_fee_paid) +
                                intval($value->parking_fee_paid) +
                                intval($value->service_fee_paid))
                                +
                                (
                                intval($value->manager_fee_skip) +
                                intval($value->elec_fee_skip) +
                                intval($value->water_fee_skip) +
                                intval($value->gas_fee_skip) +
                                intval($value->parking_fee_skip) +
                                intval($value->service_fee_skip))
                                )
                            )
                        );
                    }
                    $export = array();
                    $flat_arr = array();
                    $row = 1;
                    $col = 0;
                    $utils = new NumberUtil();

                    $month = strtotime($request->year_from . '-' . $request->month_from . '-01');
                    $end = strtotime($request->year_to . '-' . $request->month_to . '-01');
                    // HEADER Row 1
                    $flat_arr[-1] = array(
                        "Thông tin chủ hộ",
                        "",
                        "",
                        "",
                        "");
                    while($month <= $end)
                    {
                        $flat_id = $value->id . '#' . date('Ym',$month);
                        $flat_arr[-1] = array_merge($flat_arr[-1], array(date('m/Y',$month), "","","","","","", "","","","","","","","", ""));
                        $month = strtotime("+1 month", $month);
                    }
                    array_push($export, $flat_arr[-1]);
                    $col = count($flat_arr[-1]);
                    $letter = $utils->num_to_letters($col);
                    $letter_info = $utils->num_to_letters(5);

                    // Row 2
                    $flat_arr[0] = array(
                        "Căn hộ",
                        "Chủ hộ",
                        "Số điện thoại",
                        "Diện tích",
                        "Số người");
                    $month = strtotime($request->year_from . '-' . $request->month_from . '-01');
                    $end = strtotime($request->year_to . '-' . $request->month_to . '-01');
                    while($month <= $end)
                    {
                        $flat_id = $value->id . '#' . date('Ym',$month);
                        $month = strtotime("+1 month", $month);
                        $flat_arr[0] = array_merge($flat_arr[0], array("Quản lý", "Điện","Nước","Gas","Giữ Xe","Khác","Quản lý", "Điện","Nước","Gas","Giữ Xe","Khác","Tổng Phí","Tổng Đã Thu","Tổng Không Thu", "Còn Nợ"));
                    }

                    array_push($export, $flat_arr[0]);
                    $col = count($flat_arr[0]);
                    $row = 2;
                    $letter = $utils->num_to_letters($col);
                    $sheet->setBorder('A'. $row .':'. $letter . $row, 'thin');
                    // HEADER

                    $row = 3;
                    $col = 0;
                    foreach ($tenement_flats as $value) {
                        $month = strtotime($request->year_from . '-' . $request->month_from . '-01');
                        $end = strtotime($request->year_to . '-' . $request->month_to . '-01');

                        $flat_arr[$value->id] = array(
                            $value->address,
                            $value->name,
                            $value->phone,
                            $value->area,
                            $value->persons);
                        while($month <= $end)
                        {
                            $flat_id = $value->id . '#' . date('Ym',$month);
                            $month = strtotime("+1 month", $month);
                            $flat_arr[$value->id] = array_merge($flat_arr[$value->id], $payment_arr[$flat_id]);
                        }
                        array_push($export, $flat_arr[$value->id]);
                        $col = count($flat_arr[$value->id]);
                        $row++;
                        $letter = $utils->num_to_letters($col);
                    }

                    // Set Format
                    $month = strtotime($request->year_from . '-' . $request->month_from . '-01');
                    $end = strtotime($request->year_to . '-' . $request->month_to . '-01');
                    $col_num = 6;
                    $i = 0;
                    while($month <= $end)
                    {
                        $i++;
                        $sheet->cells($utils->num_to_letters($col_num) . '3:' . $utils->num_to_letters($col_num + 5) . count($export), function($cells) {
                            $cells->setBackground('#ffff00');
                        });
                        $sheet->cells($utils->num_to_letters($col_num + 6) . '3:' . $utils->num_to_letters($col_num + 11) . count($export), function($cells) {
                            $cells->setBackground('#99cc00');
                        });    
                        $sheet->cells($utils->num_to_letters($col_num + 12) . '3:' . $utils->num_to_letters($col_num + 12) . count($export), function($cells) {
                            $cells->setBackground('#ffff00');
                        });
                        $sheet->cells($utils->num_to_letters($col_num + 13) . '3:' . $utils->num_to_letters($col_num + 13) . count($export), function($cells) {
                            $cells->setBackground('#99cc00');
                        });     
                        $sheet->cells($utils->num_to_letters($col_num + 14) . '3:' . $utils->num_to_letters($col_num + 14) . count($export), function($cells) {
                            $cells->setBackground('#6699ff');
                        });     
                        $sheet->cells($utils->num_to_letters($col_num + 15) . '3:' . $utils->num_to_letters($col_num + 15) . count($export), function($cells) {
                            $cells->setBackground('#ff3300');
                        });  

                        $sheet->mergeCells($utils->num_to_letters($col_num) . '1:'. $utils->num_to_letters($col_num + 15) .'1');
                        $sheet->setBorder($utils->num_to_letters($col_num) . '3:' . $utils->num_to_letters($col_num + 15) . count($export));

                        $sheet->cell($utils->num_to_letters($col_num) . '1', function($cell) {
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('A' . count($payment_arr), function($cell) {
                            $cell->setValue('Tổng Tiền');
                        });

                        if ($i%2 == 0){
                            $sheet->cells($utils->num_to_letters($col_num) . '1:'. $utils->num_to_letters($col_num + 15) . '2', function($cells) {
                                $cells->setBackground('#ff9933');
                            });
                        }
                        else {
                            $sheet->cells($utils->num_to_letters($col_num) . '1:'. $utils->num_to_letters($col_num + 15) . '2', function($cells) {
                                $cells->setBackground('#3399ff');
                            });        
                        }
                        $col_num += 16;
                        $month = strtotime("+1 month", $month);

                    }
                    $sheet->cells('A3:E' . count($export), function($cells) {
                        $cells->setBackground('#00cc00');
                    });
                    $sheet->setBorder('A3:E' . count($export));

                    $sheet->mergeCells('A1:E1');

                    $sheet->cells('A1:E2', function($cells) {
                        $cells->setBackground('#ffcc00');
                    });
                    $sheet->cell('A1', function($cell) {
                        $cell->setAlignment('center');
                    });


                    // $letter = $utils->num_to_letters($col);
                    // $paid_arr['SUM'] = array('',
                    //                    '',
                    //                    '',
                    //                    '',
                    //                    '',
                    //                    '',
                    //                    '',
                    //                    '',
                    //                    'Tổng Tiền',
                    //                    $mSumPaid,
                    //                    $eSumPaid,
                    //                    $wSumPaid,
                    //                    $gSumPaid,
                    //                    $pSumPaid,
                    //                    $sSumPaid,
                    //                    $mSumPaid + $eSumPaid + $wSumPaid + $gSumPaid + $pSumPaid + $sSumPaid);
                    // dd($paid_arr);
                    // for ($i = 0; $i <= count($paid_arr); $i++){
                    //     $sheet->setBorder('A'. $i .':P'. $i, 'thin');
                    //     // $sheet->setFontWeight('A'. $i .':P'. $i, 'thin');
                    // }
                    // $sheet->cells('A1:P1', function($cells) {
                    //     $cells->setFontWeight('bold');
                    // });

                    // $sheet->mergeCells('A' . count($payment_arr) . ':I' . count($paid_arr));
                    // $sheet->cell('A' . count($payment_arr), function($cell) {
                    //     $cell->setValue('Tổng Tiền');
                    //     $cell->setAlignment('right');
                    // });
                    // $sheet->setBorder('A1:P1', 'thin');

                    $sheet->fromArray($export, null, 'A1', false, false);
                });

        })->download("xls");
    }
}