<?php

namespace App\Http\Controllers\MonthlyReport;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use App\User;
use App\Model\TenementFlat;
use App\Models\User;
use DB;
use yajra\Datatables\Datatables;
use Auth;
use Excel;
use DateTime;
use App\LaraBase\NumberUtil;

class VehicleController extends Controller {

    /**
     * Displays datatables front end view Trả trước báo cáo
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() {
        // dd(date("YmdH"));
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }
        $parking_tariffs = DB::table('tenement_parking_tariff')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('id', 'asc')->get();

        //dd($Tenement[0]);
        return View('monthlyreport.vehicle', [ 
                    'parking_tariffs' => $parking_tariffs]);

        //return view('monthlyreport.vehicle');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if(Auth::user()->confirmed == 0 || Auth::user()->tenement_id == '') {
            return redirect("/home");
        }

        $users = User::where('tenement_id', Auth::user()->tenement_id)
        ->where('activation', '=', '1')
        ->where('confirmed', '=', '1')
        ->get();

        // $users->push('');

        //dd($Tenement[0]);
        return View('monthlyreport.vehicle_fee', [ 
                    'users' => $users]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($year_month) {
        ini_set('memory_limit','256M'); //
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $select = ['a.id', 'a.address','a.flat_code', 'a.name','a.phone', 'a.persons', DB::raw('DATE_FORMAT(begin_contract_date, "%d/%m/%Y") as begin_contract_date'), 'a.area',DB::raw('DATE_FORMAT(end_contract_date, "%d/%m/%Y") as end_contract_date'),DB::raw('b.name as owner'), 'b.label', 'b.maker', 'b.color',DB::raw('b.comment as v_comment'),'b.vehicle_type_id','b.number_plate'];

        $parking_tariffs = DB::table('tenement_parking_tariff')
            ->select(['*'])
            ->where('activation', 1)
            ->where('id', '<>' , 0)
            ->where('tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('id', 'asc')->get();

        $i = count($select);
        foreach ($parking_tariffs as $parking_tariff){
            $select[$i++] = DB::raw('Case When vehicle_type_id = '. $parking_tariff->id .' then number_plate else "" end  as "Vehicle' . $parking_tariff->id . '"');
        }

        $monthlyVehicleFlats = DB::table('tenement_flats as a')
            ->join('tf_payment_parking_dt as b', 'a.id', '=', 'b.flat_id')
            ->join('tf_payment_parking_hd as c', 'b.parking_hd_id', '=', 'c.id')
            ->select($select)
            ->where('a.activation', 1)
            ->where('b.activation', 1)
            ->where('c.activation', 1)
            ->where('b.year_month', $year_month)
            ->where('a.id', '<>' , 0)
            ->where('a.tenement_id', '=', Auth::user()->tenement_id)
            ->orderBy('a.id', 'asc');
        $monthlyVehicle[] = null;

        foreach ($monthlyVehicleFlats->get() as $monthlyVehicleFlat){
            $type = 'Vehicle' . $monthlyVehicleFlat->vehicle_type_id;
            
            if ($monthlyVehicleFlat->number_plate != null){
                $monthlyVehicleFlat->$type = $monthlyVehicleFlat->number_plate;
            }
            array_push($monthlyVehicle, $monthlyVehicleFlat);

        }
        return Datatables::of($monthlyVehicleFlats)
                ->make(true);        
    }

    public function download(Request $request)
    {
        return Excel::create('vehicle_report' . $request->date_from . $request->date_to, function($excel) use ($request) {
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

                // $year_month_lst = DB::table("tf_payment_parking_dt as a")
                // ->leftjoin('tenement_flats as b', function($join)
                // {
                //     $join->on('a.flat_id', '=', 'b.id');
                //     $join->on('b.activation', '=', DB::RAW("1"));
                //     $join->on('b.tenement_id', '=', DB::RAW(Auth::user()->tenement_id));
                // })
                // ->select(DB::RAW("Distinct a.year_month year_month_lst"))
                // ->where('year_month', '>=' , $request->year_from . $request->month_from)
                // ->where('year_month', '<=' , $request->year_to . $request->month_to)
                // ->where('a.activation', '=' , DB::RAW("1"))
                // ->where('b.activation', '=' , DB::RAW("1"))
                // ->get();

                $year_month_lst = DB::table("tf_paid_hd as a")
                ->leftjoin('tf_paid_dt as b', function($join)
                {
                    $join->on('a.id', '=', 'b.paid_id');
                })
                ->leftjoin('tenement_flats as c', function($join)
                {
                    $join->on('a.flat_id', '=', 'c.id');
                    $join->on('c.tenement_id', '=', DB::RAW(Auth::user()->tenement_id));
                })
                ->select(DB::RAW("Distinct a.year_month year_month_lst"))
                ->where('a.receive_date', '>=' , $date_from)
                ->where('a.receive_date', '<=' , $date_to)
                ->where('a.activation', '=' , DB::RAW("1"))
                ->where('b.activation', '=' , DB::RAW("1"))
                ->where('c.activation', '=' , DB::RAW("1"))
                ->where(function($query) use ($request)
                {
                    if ('' != $request->user){
                        $query->where('a.updated_by', '=', $request->user);
                    }
                })
                ->get();

                $tenement_parking_tariff = DB::table('tenement_parking_tariff')
                ->where('activation', 1)
                ->where('tenement_id', '=', DB::RAW(Auth::user()->tenement_id))
                ->get();

                foreach ($year_month_lst as $value) {
                    $excel->sheet($value->year_month_lst, function($sheet) use ($request, $value, $tenement_parking_tariff, $date_from, $date_to)
                    {
                        $flat_arr = array();
                        $vehicle_arr = array();
                        $tenement_flats = DB::table("tenement_flats as a")
                        ->leftjoin('tf_payment_all_months as b', function($join) use ($value)
                        {
                            $join->on('a.id', '=', 'b.flat_id');
                            $join->on('a.activation', '=', DB::RAW("1"));
                            $join->on('b.activation', '=', DB::RAW("1"));
                            $join->on('b.year_month', '=', DB::RAW($value->year_month_lst));
                        }, 'left outer')
                        ->select(DB::RAW('b.flat_id, a.address, a.name,
                            ifnull(manager_fee,0), ifnull(manager_fee_paid,0), ifnull(manager_fee_skip,0), 
                            ifnull(manager_fee,0) - ifnull(manager_fee_paid,0) - ifnull(manager_fee_skip,0) as manager_fee_dept,
                            -- ifnull(elec_fee,0), ifnull(elec_fee_paid,0), ifnull(elec_fee_skip,0),
                            -- ifnull(elec_fee,0) - ifnull(elec_fee_paid,0) - ifnull(elec_fee_skip,0) as elec_fee_dept, 
                            ifnull(water_fee,0), ifnull(water_fee_paid,0), ifnull(water_fee_skip,0),
                            ifnull(water_fee,0) - ifnull(water_fee_paid,0)- ifnull(water_fee_skip,0) as water_fee_dept,
                            -- ifnull(gas_fee,0),ifnull(gas_fee_paid,0),ifnull(gas_fee_skip,0),
                            -- ifnull(gas_fee,0) - ifnull(gas_fee_paid,0)- ifnull(gas_fee_skip,0) as gas_fee_dept, 
                            ifnull(service_fee,0), ifnull(service_fee_paid,0), ifnull(service_fee_skip,0),
                            ifnull(service_fee,0) - ifnull(service_fee_paid,0)- ifnull(service_fee_skip,0) as service_fee_dept, 
                            ifnull(parking_fee,0), ifnull(parking_fee_paid,0), ifnull(parking_fee_skip,0),
                            ifnull(parking_fee,0) - ifnull(parking_fee_paid,0)- ifnull(parking_fee_skip,0) as parking_fee_dept'
                        ))
                        ->get();

                        $paid_arr = array();
                        foreach ($tenement_flats as $flat) {
                            $paid_arr[$flat->flat_id] = array();
                            $paid_arr[$flat->flat_id]['1'] = 0;
                            // $paid_arr[$flat->flat_id]['2'] = 0;
                            $paid_arr[$flat->flat_id]['3'] = 0;
                            // $paid_arr[$flat->flat_id]['4'] = 0;
                            $paid_arr[$flat->flat_id]['5'] = 0;
                            $paid_arr[$flat->flat_id]['6'] = 0;

                            foreach ($tenement_parking_tariff as $tariff) {
                                $vehicle_arr[$flat->flat_id][$tariff->id] = 0;
                            }                            
                        }


                        $tf_payment_parking_dt = DB::table('tf_payment_parking_dt as a')
                            ->join('tenement_flats as b', 'a.flat_id', '=', 'b.id')
                            ->select('a.flat_id','a.vehicle_type_id', DB::raw('sum(a.total_money) money'))
                            ->where('a.activation', '=', 1)
                            ->where('b.activation', '=', 1)
                            ->where('b.tenement_id', '=', Auth::user()->tenement_id)
                            ->where('a.year_month', '=', $value->year_month_lst)
                            ->groupBy('a.flat_id','a.vehicle_type_id')
                            ->orderBy('a.flat_id')
                            ->get();

                        foreach ($tf_payment_parking_dt as $parking) {
                            $vehicle_arr[$parking->flat_id][$parking->vehicle_type_id] = $parking->money;
                        }

                        // Danh sách số tiền đã trả
                        $paid_lst = DB::table("tf_paid_hd as a")
                        ->leftjoin('tf_paid_dt as b', function($join)
                        {
                            $join->on('a.id', '=', 'b.paid_id');
                        })
                        ->leftjoin('tenement_flats as c', function($join)
                        {
                            $join->on('a.flat_id', '=', 'c.id');
                            $join->on('c.tenement_id', '=', DB::RAW(Auth::user()->tenement_id));
                        })
                        ->select(DB::RAW("a.flat_id, sum(b.money) money, payment_type"))
                        ->where('a.receive_date', '>=' , $date_from)
                        ->where('a.receive_date', '<=' , $date_to)
                        ->where('a.activation', '=' , DB::RAW("1"))
                        ->where('b.activation', '=' , DB::RAW("1"))
                        ->where('c.activation', '=' , DB::RAW("1"))
                        ->where('b.year_month', '=' , $value->year_month_lst)
                        ->where(function($query) use ($request)
                        {
                            if ('' != $request->user){
                                $query->where('a.updated_by', '=', $request->user);
                            }
                        })
                        ->groupBy('a.flat_id', 'b.payment_type')
                        ->get();
                        
                        foreach ($paid_lst as $paid){
                            $paid_arr[$paid->flat_id][$paid->payment_type] = $paid->money;
                        }

                        $flat_arr[-1] = array(
                        "Căn hộ",
                        "Chủ hộ",

                        "PQL",
                        "Đã Trả",
                        "Không Thu",
                        "Công Nợ",

                        "Nước",
                        "Đã Trả",
                        "Không Thu",
                        "Công Nợ",

                        "Dịch vụ",
                        "Đã Trả",
                        "Không Thu",
                        "Công Nợ",

                        "Xe Tháng",
                        "Đã Trả",
                        "Không Thu",
                        "Công Nợ",

                        );
                        foreach ($tenement_parking_tariff as $tariff) {
                            $flat_arr[-1] = array_merge($flat_arr[-1], (array)$tariff->name);
                        }

                        $flat_arr[-1] = array_merge($flat_arr[-1], array('PQL','Nước','Xe','Phí Khác'));


                        foreach ($tenement_flats as $flat) {
                            $flat_arr[$flat->flat_id] = array();
                            if (!isset($vehicle_arr[$flat->flat_id])){
                                foreach ($tenement_parking_tariff as $tariff) {
                                    $vehicle_arr[$flat->flat_id][$tariff->id] = 0;
                                }
                            }
                            $flat_arr[$flat->flat_id] = array_merge(
                                array_slice((array)$flat, 1) , 
                                $vehicle_arr[$flat->flat_id],
                                $paid_arr[$flat->flat_id]
                                );
                        }

                        // Set Format
                        $row = count($flat_arr);
                        $col_num = 1;
                        $utils = new NumberUtil();

                        $i = 0;
                        // while($i < $row)
                        // {
                        //     $i++;
                            $sheet->cells('A1:' . $utils->num_to_letters(count($flat_arr[-1])) . '1', function($cells) {
                                $cells->setBackground('#F5D2D3');
                            });

                            $sheet->cells('A2:B' . count($flat_arr), function($cells) {
                                $cells->setBackground('#ffff00');
                            });

                            $sheet->cells('C2:F' . count($flat_arr), function($cells) {
                                $cells->setBackground('#99cc00');
                            });    
                            $sheet->cells('G2:J' . count($flat_arr), function($cells) {
                                $cells->setBackground('#ffff00');
                            });
                            $sheet->cells('K2:N' . count($flat_arr), function($cells) {
                                $cells->setBackground('#F48024');
                            });     
                            $sheet->cells('O2:R' . count($flat_arr), function($cells) {
                                $cells->setBackground('#6699ff');
                            });     

                            $sheet->cells('S2:' . $utils->num_to_letters(18 + count($tenement_parking_tariff)) . count($flat_arr), function($cells) {
                                $cells->setBackground('#FFFCE2');
                            });  

                            // dd(count($tenement_parking_tariff));
                            $sheet->cells($utils->num_to_letters(19 + count($tenement_parking_tariff)) . '2:' . $utils->num_to_letters(20 + count($tenement_parking_tariff)) . count($flat_arr), function($cells) {
                                $cells->setBackground('#99cc00');
                            });  

                            $sheet->cells($utils->num_to_letters(20 + count($tenement_parking_tariff)) . '2:' . $utils->num_to_letters(21 + count($tenement_parking_tariff)) . count($flat_arr), function($cells) {
                                $cells->setBackground('#ffff00');
                            });  

                            $sheet->cells($utils->num_to_letters(21 + count($tenement_parking_tariff)) . '2:' . $utils->num_to_letters(22 + count($tenement_parking_tariff)) . count($flat_arr), function($cells) {
                                $cells->setBackground('#F48024');
                            });  

                            $sheet->cells($utils->num_to_letters(22 + count($tenement_parking_tariff)) . '2:' . $utils->num_to_letters(23 + count($tenement_parking_tariff) - 1) . count($flat_arr), function($cells) {
                                $cells->setBackground('#6699ff');
                            });  

                            // // $sheet->cell('A' . count($payment_arr), function($cell) {
                            // //     $cell->setValue('Tổng Tiền');
                            // // });

                            // if ($i%2 == 0){
                            //     $sheet->cells($utils->num_to_letters($col_num) . '1:'. $utils->num_to_letters($col_num + 15) . '2', function($cells) {
                            //         $cells->setBackground('#ff9933');
                            //     });
                            // }
                            // else {
                            //     $sheet->cells($utils->num_to_letters($col_num) . '1:'. $utils->num_to_letters($col_num + 15) . '2', function($cells) {
                            //         $cells->setBackground('#3399ff');
                            //     });        
                            // }
                            // $col_num += 16;
                            // $month = strtotime("+1 month", $month);

                        // }
                        // $sheet->cells('A3:B' . count($flat_arr), function($cells) {
                        //     $cells->setBackground('#00cc00');
                        // });
                        // $sheet->setBorder('A3:E' . count($flat_arr));

                        // // $sheet->mergeCells('A1:E1');

                        // $sheet->cells('A1:E2', function($cells) {
                        //     $cells->setBackground('#ffcc00');
                        // });
                        // $sheet->cell('A1', function($cell) {
                        //     $cell->setAlignment('center');
                        // });                        
                        $sheet->fromArray($flat_arr, null, 'A1', false, false);
                    });
                }
        })->download("xls");
    }

}