@extends('includes.report')

@section('style')
<style>
    .container-fluid{
        margin-left: 1px;
        margin-right: 1px;
        overflow: hidden;
    }

    .valign-bottom{
        vertical-align:bottom !important;
    }

    img {
    opacity: 0.2;
    filter: alpha(opacity=20); /* For IE8 and earlier */
    }

    #aaa{
        position: absolute;
        width: 85px;
        left: 25px;
        margin-top: 20px;
    }

    #created{
        position: absolute;
        left: 675px;
        margin-top: 20px;
        font-size: 10px;
    }

    .header{
        margin-top: 20px;
        padding-top: 20px;
        text-align: center;
    }

    .header{
        margin-top: 20px;
        padding-top: 20px;
        text-align: center;
    }

    .detail{
        width: 95%;
        margin-left: 50px;
        border: 1px solid;
    }
    .detail td{
        padding-left: 5px;
        padding-right: 5px;
        border: 1px solid;

    }
    .detail th{
        font-size: 13px;
        font-weight: bold;
        text-align: center;
        border: 1px solid;

    }

    body {
      background: white; 
      font-size: 16px;
    }
    page {
      background: white;
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
      box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
    }
    page[size="A4"] {  
      width: 21cm;
      height: 29.7cm; 
    }
    page[size="A4"][layout="portrait"] {
      width: 29.7cm;
      height: 21cm;  
    }
    page[size="A3"] {
      width: 29.7cm;
      height: 42cm;
    }
    page[size="A3"][layout="portrait"] {
      width: 42cm;
      height: 29.7cm;  
    }
    page[size="A5"] {
      width: 14.8cm;
      height: 21cm;
    }
    page[size="A5"][layout="portrait"] {
      width: 21cm;
      height: 14.8cm;  
    }
    @media print {
      body, page {
        margin: 0;
        box-shadow: 0;
      }
    }
</style>
@endsection

@section('script')
{!! Html::script('js/jquery.number.min.js') !!}


<script>
$(function(){
    $('span.number').number( true, 0 );
});

function commafy(n) {
  var parts = n.toString().split('.');
  parts[0] = parts[0].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
  return parts.join('.');
}

</script>
@endsection

@section('content')
    <page size="A4">
    <div id="aaa">


    {!! HTML::image('img/logo.png', 'alt', array('width' => 170,  'height'=> 100)) !!}

    </div>
    <div id="created">
        <?php  
            echo date('d/m/Y h:i');
        ?>
    </div>
    @foreach($paymentnotic_lst as $paymentnotice)
        <?php
            // $tenement_info = $paybill["tenement_info"];
            // $flat_info = $paybill["flat_info"];
            // $tf_paid_hd = $paybill["tf_paid_hd"];
            // $tf_paid_dt = $paybill["tf_paid_dt"];
            // $total_money = $paybill["total_money"];
            // $total_money_read = $paybill["total_money_read"];
        $flat_info = $paymentnotice["flat_info"];
        $total_money = $paymentnotice["total_money"];
        $total_money_read = $paymentnotice["total_money_read"];
        $total_money_readEn = $paymentnotice["total_money_readEn"];
        $payment = $paymentnotice["payment"];
        $elec = $paymentnotice["elec"];
        $water = $paymentnotice["water"];
        $gas = $paymentnotice["gas"];
        $parking = $paymentnotice["parking"];
        $service = $paymentnotice["service"];
        $dept = $paymentnotice["dept"];
        $paid = $paymentnotice["paid"];
        $tenement_info = $paymentnotice["tenement_info"];

        $number = 1;
        $stringNumber = "1";
        ?>

        <div class="header">
            <h3 style="font-size: 18px">
            PHIẾU THÔNG BÁO THANH TOÁN ({!! substr($payment->year_month, 4, 2) . '/' . substr($payment->year_month, 0,4) !!})<br>
            Dự Án SaiGonRes Plaza<br>
            PAYMENT NOTICE({!! substr($payment->year_month, 4, 2) . '/' . substr($payment->year_month, 0,4) !!})
            </h3>
        </div>

        <div>
            <div>
                <div class="col-xs-11"><b>Kính gửi Ông Bà/ To Mr. Ms:&nbsp; </b>{!! $flat_info->name !!}</div>
                <div class="col-xs-11"><b>Điện thoại/ Phone Number :&nbsp;</b> {!! $flat_info->phone !!}</div>

                <div class="col-xs-6"><b>Căn hộ/ Apartment :&nbsp;</b> {!! $flat_info->address !!}</div>
                <div class="col-xs-5"><b>Diện tích/ Floor Area :&nbsp;</b> {!! $flat_info->area !!}m2</div>
                

                <!-- <div class="col-xs-12"><b>
                    Đơn giá phí quản lý/ Monthly management fee :&nbsp;</b>
                    {!! number_format($tenement_info->manager_fee, 0, ',', '.') !!}đ/m2/tháng
                </div>   -->  
                <div class="col-xs-11"><br></div>            
            </div>
        </div>

        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Phí quản lý/ Management Service:&nbsp;</b>
            </div>
        </div>
        <div>
            <div >
                <table class="detail">
                    <tbody>
                        <tr>
                            <th class="col-xs-5 text-center">Kỳ hạn/ Term</th>
                            <th>Đơn Giá/ Rate</th>
                            <th class="col-xs-4 text-center">Thành Tiền/ Amount(VND)</th>
                        </tr>
                        <tr>                    
                            <td class="text-center">
                                {!! 
                                '01/' . substr($payment->year_month, 4, 2) . '/' . substr($payment->year_month, 0,4) . '~'. date("t/m/Y", strtotime(substr($payment->year_month, 0, 4) . '-' . substr($payment->year_month, 4,2)) . '-01') 
                                !!}
                            </td>
                            <td class="text-center">
                                {!! number_format($tenement_info->manager_fee) !!}
                            </td>
                            <td class="text-center">
                                {!! number_format($payment->manager_fee) !!}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  2. Tiền điện/ Electricity fee:</b>
            </div>
        </div>
        <div>
            <div>
                <table class="detail" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th class="col-xs-3 text-center">Chỉ số cũ<br>Previous figure</th>
                            <th>{!! $elec[0]->old_index_hd!!}</th>
                            <th>Chỉ số mới<br>Current figure</th>
                            <th class="col-xs-1 text-center">{!! $elec[0]->new_index_hd!!}</th>
                            <th>Chỉ số tiêu thụ<br>Electric use (Kwh)</th>
                            <th>{!! $elec[0]->mount_hd !!}</th>
                        </tr>
                        
                        <tr>
                            <th class="text-center">Định mức/ Limits</th>
                            <th class="text-center">Từ/From(Kwh)</th>
                            <th class="text-center">Đến/To(Kwh)</th>
                            <th class="text-center">Đơn giá/Rate</th>
                            <th class="text-center">Phí VAT/VAT Fee</th>
                            <th class="text-center">Thành tiền<br>Amount(VND)</th>
                        </tr>

                        @foreach($elec as $elec_dt)
                        <tr>
                            <th class="text-center">{!! $elec_dt->elec_type_name . '(' . $elec_dt->elec_tariff_name . ')' !!}</th>
                            <th class="text-center">{!! $elec_dt->from_index !!}</th>
                            <th class="text-center">{!! $elec_dt->to_index !!}</th>
                            <th class="text-center">{!! $elec_dt->price !!}</th>
                            <th class="text-center">{!! $elec_dt->vat_money !!}</th>
                            <th class="text-right">{!! $elec_dt->total !!}</th>
                        </tr>
                        @endforeach

                        <tr>
                            <th class="text-left" colspan="5">Phí hao hụt /Loss compensation({!! $elec[0]->other_fee01_hd !!})</th>
                            <th class="text-right">{!! $elec[0]->other_fee01_money_hd !!}</th>
                        </tr>
                        <tr>
                            <th class="text-left" colspan="5">Tổng cộng /Total (VND)</th>
                            <th class="text-right">{!! $elec[0]->total_hd !!}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> -->

        @if (isset($elec[0]->elec_used_id))
        <?php
            $number += 1;
            $stringNumber .= " + " . $number; 
         ?>
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Tiền điện/ Electricity fee:&nbsp;</b>
            </div>
        </div>
        <div>
            <table class="detail" style="margin-left: 50px;width: 95%">
                <tbody>
                    <tr>
                        <th>Chỉ số cũ
                        <br>Previous figure</th>
                        <th>Chỉ số mới<br>Current figure</th>
                        <th>Chỉ số tiêu thụ<br>Electric use (Kwh)</th>
                        <th>Đơn giá<br>Rate</th>
                        <th>Thành tiền<br>Amount(VND)</th>
                    </tr>
                    <tr>
                        <td class="text-center">{!! number_format($water[0]->old_index_hd) !!}</td>
                        <td class="text-center">{!! number_format($water[0]->new_index_hd) !!}</td>
                        <td class="text-center">{!! number_format($water[0]->mount_hd) !!}</td>
                        <td>
                        </td>
                        <td>
                        </td>
                    </tr>

                    @foreach($water as $water_dt)
                    <tr>
                        <td class="text-center" colspan="2">{!! $water_dt->water_type_name . '(' . $water_dt->water_tariff_name . ')' !!}</td>
                        <td class="text-center">{!! $water_dt->mount !!}</td>
                        <td class="text-center">{!! number_format($water_dt->price) !!}</td>
                        <td class="text-right">{!! number_format($water_dt->total) !!}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="text-left" colspan="4">VAT (5%)</td>
                        <td class="text-right">15250</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Phí hao hụt /Loss compensation(5%)</td>
                        <td class="text-right">15250</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Tổng cộng /Total (VND)</td>
                        <td class="text-right">80000</td>
                    </tr>

                    <!-- <tr>
                        <th class="text-left" colspan="4">VAT ({!! $water[0]->vat_hd !!})</th>
                        <th class="text-right">{!! $water[0]->vat_money_hd !!}</th>
                    </tr>
                    <tr>
                        <th class="text-left" colspan="4">Phí hao hụt /Loss compensation({!! $water[0]->other_fee01_hd !!})</th>
                        <th class="text-right">{!! $water[0]->other_fee02_money_hd !!}</th>
                    </tr>
                    <tr>
                        <th class="text-left" colspan="4">Tổng cộng /Total (VND)</th>
                        <th class="text-right">{!! $water[0]->total_hd !!}</th>
                    </tr> -->
                </tbody>
            </table>
        </div>
        @endif

        @if (isset($water[0]->water_used_id) && $water[0]->old_index_hd < $water[0]->new_index_hd)
        <?php
            $number += 1;
            $stringNumber .= " + " . $number; 
         ?>
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Tiền nước/ Water fee:&nbsp;</b>
            </div>
        </div>
        <div>
            <table class="detail" style="margin-left: 50px;width: 95%">
                <tbody>
                    <tr>
                        <th>Chỉ số cũ<br>Previous figure</th>
                        <th>Chỉ số mới<br>Current figure</th>
                        <th>Chỉ số tiêu thụ<br>Volumn (m3)</th>
                        <th>Đơn giá/ Rate</th>
                        <th>Thành tiền<br>Amount(VND)</th>
                    </tr>
                    <tr>
                        <td class="text-center">{!! number_format($water[0]->old_index_hd) !!}</td>
                        <td class="text-center">{!! number_format($water[0]->new_index_hd) !!}</td>
                        <td class="text-center">{!! number_format($water[0]->mount_hd) !!}</td>
                        <td>
                        </td>
                        <td>
                        </td>
                    </tr>

                    @foreach($water as $water_dt)
                    <tr>
                        <td class="text-center" colspan="2">{!! $water_dt->water_type_name !!}</td>
                        <td class="text-center">{!! $water_dt->mount !!}</td>
                        <td class="text-center">{!! number_format($water_dt->price) !!}</td>
                        <td class="text-right">{!! number_format($water_dt->total) !!}</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td class="text-left" colspan="4">VAT ({!! number_format($water[0]->vat) !!}%)</td>
                        <td class="text-right">{!! number_format($water[0]->vat_money_hd) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Phí hao hụt /Loss compensation({!! number_format($water[0]->other_fee02) !!}%)</td>
                        <td class="text-right">{!! number_format($water[0]->other_fee02_money_hd) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Phí BVMT({!! number_format($water[0]->other_fee01) !!}%)</td>
                        <td class="text-right">{!! number_format($water[0]->other_fee01_money_hd) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Tổng cộng /Total (VND)</td>
                        <td class="text-right">{!! number_format($water[0]->total_hd) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  4. Tiền gas/ Gas fee:</b>
            </div>
        </div>
        <div>
            <div>
                <table class="detail" style="margin-left: 50px;widtd: 95%">
                    <tbody>
                        <tr>
                            <td class="col-xs-3 text-center">Chỉ số cũ<br>Previous figure</td>
                            <td>{!! $gas[0]->old_index_hd!!}</td>
                            <td>Chỉ số mới<br>Current figure</td>
                            <td class="col-xs-1 text-center">{!! $gas[0]->new_index_hd!!}</td>
                            <td>Chỉ số tiêu tdụ<br>Electric use (Kwh)</td>
                            <td>{!! $gas[0]->mount_hd !!}</td>
                        </tr>
                        
                        <tr>
                            <td class="text-center">Định mức/ Limits</td>
                            <td class="text-center">Từ/From(Kwh)</td>
                            <td class="text-center">Đến/To(Kwh)</td>
                            <td class="text-center">Đơn giá/Rate</td>
                            <td class="text-center">Phí VAT/VAT Fee</td>
                            <td class="text-center">Tdành tiền<br>Amount(VND)</td>
                        </tr>

                        @foreach($gas as $gas_dt)
                        <tr>
                            <td class="text-center">{!! $gas_dt->gas_type_name . '(' . $gas_dt->gas_tariff_name . ')' !!}</td>
                            <td class="text-center">{!! $gas_dt->from_index !!}</td>
                            <td class="text-center">{!! $gas_dt->to_index !!}</td>
                            <td class="text-center">{!! $gas_dt->price !!}</td>
                            <td class="text-center">{!! $gas_dt->vat_money !!}</td>
                            <td class="text-right">{!! $gas_dt->total !!}</td>
                        </tr>
                        @endforeach

                        <tr>
                            <td class="text-left" colspan="5">Phí hao hụt /Loss compensation({!! $gas[0]->otder_fee01_hd !!})</td>
                            <td class="text-right">{!! $gas[0]->otder_fee01_money_hd !!}</td>
                        </tr>
                        <tr>
                            <td class="text-left" colspan="5">Tổng cộng /Total (VND)</td>
                            <td class="text-right">{!! $gas[0]->total_hd !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> -->
        @if (isset($gas[0]->gas_used_id))
        <?php
            $number += 1;
            $stringNumber .= " + " . $number; 
         ?>
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  4. Tiền gas/ Gas fee:&nbsp;</b>
            </div>
        </div>
        <div>
            <table class="detail" style="margin-left: 50px;width: 95%">
                <tbody>
                    <tr>
                        <th>Chỉ số cũ<br>Previous figure</th>
                        <th>Chỉ số mới<br>Current figure</th>
                        <th>Chỉ số tiêu thụ<br>Gas use (Kg)</th>
                        <th>Đơn giá/ Rate</th>
                        <th>Thành tiền<br>Amount(VND)</th>
                    </tr>
                    <tr>
                        <td class="text-center">{!! $water[0]->old_index_hd!!}15</td>
                        <td class="text-center">{!! $water[0]->new_index_hd!!}24</td>
                        <td class="text-center">{!! $water[0]->mount_hd !!}9</td>
                        <td>
                        </td>
                        <td>
                        </td>
                    </tr>

                    @foreach($water as $water_dt)
                    <tr>
                        <!-- <td class="text-center" colspan="2">{!! $water_dt->water_type_name . '(' . $water_dt->water_tariff_name . ')' !!}</td>
                        <td class="text-center">{!! $water_dt->mount !!}</td>
                        <td class="text-center">{!! $water_dt->price !!}</td>
                        <td class="text-right">{!! $water_dt->total !!}</td> -->

                        <td class="text-center" colspan="2">Định mức 1</td>
                        <td class="text-center">5</td>
                        <td class="text-center">5000</td>
                        <td class="text-right">25000</td>

                    </tr>
                    @endforeach

                    <tr>
                        <td class="text-center" colspan="2">Định mức 2</td>
                        <td class="text-center">4</td>
                        <td class="text-center">9000</td>
                        <td class="text-right">36000</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">VAT (5%)</td>
                        <td class="text-right">15250</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Phí hao hụt /Loss compensation(5%)</td>
                        <td class="text-right">15250</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Tổng cộng /Total (VND)</td>
                        <td class="text-right">80000</td>
                    </tr>

                    <!-- <tr>
                        <td class="text-left" colspan="4">VAT ({!! $water[0]->vat_hd !!})</td>
                        <td class="text-right">{!! $water[0]->vat_money_hd !!}</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Phí hao hụt /Loss compensation({!! $water[0]->otder_fee01_hd !!})</td>
                        <td class="text-right">{!! $water[0]->otder_fee02_money_hd !!}</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Tổng cộng /Total (VND)</td>
                        <td class="text-right">{!! $water[0]->total_hd !!}</td>
                    </tr> -->
                </tbody>
            </table>
        </div>
        @endif


        @if (isset($parking[0]->total_count))
        <?php
            $number += 1;
            $stringNumber .= " + " . $number; 
         ?>
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Phí giữ xe/Parking Service:&nbsp;</b>
            </div>
        </div>
        <div>
            <div>
                <table class="detail" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th>Loại Xe/Kind of vehicle</th>
                            <th>Số lượng/Quantity</th>
                            <th>Đơn giá/Rate</th>
                            <th>Thành tiền/Amount VND</th>
                        </tr>
                        @foreach($parking as $parking_dt)
                            <tr>                    
                                <td class="text-center">
                                    {!! $parking_dt->vehicle_type !!}
                                </td>
                                <td class="text-center">
                                    {!! $parking_dt->total_count !!}
                                </td>
                                <td class="text-center">
                                    {!! number_format($parking_dt->price) !!}
                                </td>
                                <td class="text-center">
                                    {!! number_format($parking_dt->total_money) !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if (isset($service[0]->service_id) && ($service[0]->total_money > 0))
        <?php
            $number += 1;
            $stringNumber .= " + " . $number; 
         ?>
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Phí khác/Other fees:&nbsp; Tiền Điện</b>
            </div>
        </div>
        <!-- <div>
            <div>
                <table class="detail" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th>Diễn giải/Description</th>
                            <th>Số lượng/Quantity</th>
                            <th>Đơn vị</th>
                            <th>Đơn giá/Rate</th>
                            <th>Thành tiền/Amount VND</th>
                        </tr>
                        @foreach($service as $service_dt)
                            <tr>                    
                                <td class="text-center">
                                    {!! $service_dt->service !!}
                                </td>
                                <td class="text-center">
                                    {!! $service_dt->mount !!}
                                </td>
                                <td class="text-center">
                                    {!! $service_dt->unit !!}
                                </td>
                                <td class="text-center">
                                    {!! number_format($service_dt->price) !!}
                                </td>
                                <td class="text-center">
                                    {!! number_format($service_dt->total_money) !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div> -->
        <div>
            <div>
                <table class="detail" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th>Chỉ số /Figure from<br>ngày nhận bàn giao</th>
                            <th>Chỉ số mới/Figure to</th>
                            <th>Tiêu Thụ/Quantity</th>
                            <th>Thành tiền/Amount VND</th>
                        </tr>
                        @foreach($service as $service_dt)
                            <tr>                    
                                <td class="text-center">
                                    {!! $service_dt->service !!}
                                </td>
                                <td class="text-center">
                                    {!! $service_dt->comment !!}
                                </td>
                                <td class="text-center">
                                    {!! $service_dt->mount !!}
                                </td>
                                <td class="text-center">
                                    {!! number_format($service_dt->total_money) !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if (count($dept) > 0)
        <?php
            $number += 1;
            $stringNumber .= " + " . $number; 
         ?>        
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp; {!! $number !!}. Nợ cũ-Truy Thu/Previous debit:&nbsp;</b>
            </div>
        </div>
        <div>
            <div>
                <table class="detail" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th>Tháng<br>Month</th>
                            <th>Phí Quản Lý<br> Manager Fee</th>
                            <th>Phí Nước<br>
                            Water fee</th>
                            <th>Phí Giữ Xe<br>
                            Parking fee</th>
                            <th>Phí Khác<br>
                            Other fee</th>
                        </tr>
                        <?php $first_month = true; ?>
                        @foreach($dept as $dept_dt)
                            <tr>                    
                                <td class="text-center">
                                    {!! 
                                    substr($dept_dt->year_month, 4, 2) . '/' . substr($dept_dt->year_month, 0,4)
                                     !!}
                                </td>
                                <td class="text-center">
                                    {!! number_format($dept_dt->manager_fee_dept) !!}

                                    @if ($dept_dt->manager_fee_dept > 0 && $first_month)
                                    <br>
                                    <div style="font-size: 10px">Ngày nhận bàn giao + 10 ngày đến hết T1/2017</div>
                                    @endif
                                    <?php $first_month = false; ?>

                                </td>
                                <td class="text-center">
                                    {!! number_format($dept_dt->water_fee_dept) !!}
                                </td>
                                <td class="text-center">
                                    {!! number_format($dept_dt->parking_fee_dept) !!}
                                </td>
                                <td class="text-center">
                                    {!! number_format($dept_dt->service_fee_dept) !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        <?php
                $manager_fee_paid = 0;
                $elec_fee_paid = 0;
                $water_fee_paid = 0;
                $gas_fee_paid = 0;
                $parking_fee_paid = 0;
                $service_fee_paid = 0;

            foreach($paid as $paid_dt){
                $manager_fee_paid += $paid_dt->manager_fee_paid;
                $elec_fee_paid += $paid_dt->elec_fee_paid;
                $water_fee_paid += $paid_dt->water_fee_paid;
                $gas_fee_paid += $paid_dt->gas_fee_paid;
                $parking_fee_paid += $paid_dt->parking_fee_paid;
                $service_fee_paid += $paid_dt->service_fee_paid;
            }
        ?>

        @if ($manager_fee_paid > 0 ||
            $elec_fee_paid > 0 ||
            $water_fee_paid > 0 ||
            $gas_fee_paid > 0 ||
            $service_fee_paid > 0 ||
            $parking_fee_paid > 0
        )
        <?php
            $number += 1;
            $stringNumber += "-" . $number; 
         ?>
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Đã Trả/Paid:&nbsp;</b>
            </div>
        </div>
        <div>
            <div>
                <table class="detail" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th>Phí Quản Lý<br>Manager Fee</th>
                            <th>Tiền Điện<br>
                            Electricity fee</th>
                            <th>Phí Nước<br>
                            Water fee</th>
                            <th>Phí Gas<br>
                            Gas fee</th>
                            <th>Phí Giữ Xe<br>
                            Parking fee</th>
                            <th>Phí Khác<br>
                            Other fee</th>
                        </tr>
                        @foreach($paid as $paid_dt)
                            <tr>                    
                                <td class="text-center">
                                    {!! $paid_dt->manager_fee_paid !!}
                                </td>
                                <td class="text-center">
                                    {!! $paid_dt->elec_fee_paid !!}
                                </td>
                                <td class="text-center">
                                    {!! $paid_dt->water_fee_paid !!}
                                </td>
                                <td class="text-center">
                                    {!! $paid_dt->gas_fee_paid !!}
                                </td>
                                <td class="text-center">
                                    {!! $paid_dt->parking_fee_paid !!}
                                </td>
                                <td class="text-center">
                                    {!! $paid_dt->service_fee_paid !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        <div class="row voffset1">
            <div class="col-xs-11">
                <div class="col-xs-12 "><br><b>Tổng cộng /Total ({!! $stringNumber !!}):&nbsp;</b>{!! number_format($total_money) !!} &nbsp;VND</div>
                <div class="col-xs-12"><b>Bằng chữ:&nbsp;</b>{!! $total_money_read !!} &nbsp;Đồng</div>
                <div class="col-xs-12"><b>By Words:&nbsp;</b>{!! $total_money_readEn !!}&nbsp;Dong</div>
            </div>
        </div>

        <div class="row voffset1">
            <div class="col-xs-11">
                <div class="col-xs-12 text-left" style="font-size: 10px">
                    Thời hạn đóng tiền trong vòng 10 ngày kể từ ngày nhận phiếu thông báo thanh toán. Số tiền thông báo trên phiếu này được
                    tính đến hết ngày 31/03/2017. <br>Nếu khách hàng thanh toán sau ngày này, chúng tôi sẽ cập nhật vào phiếu của kỳ sau.<br>

                    (Time limit for payment within 10 days after receiving notice of payment. The amount mentioned in this notice of payment is
                    updated until Mar 31, 2017. <br>So, in case the residents have paid after this time, the payment will be updated to the next
                    notice of payment) <br><br>
                </div>
            </div>
            <div class="col-xs-11">
                <div class="col-xs-7 text-left" style="font-size: 10px; padding-left: 15px">
                    Vui lòng nộp tiền mặt tại văn phòng BQL hoặc chuyển khoản vào tài khoản sau:&nbsp;<br>

                    Please make payment at the management office or transfer money to the account:&nbsp;<br>

                    Số TK/Account:&nbsp;<b>6067 0406 0031342</b><br>

                    Ngân hàng/Bank:&nbsp; <b>Ngân hàng VIB – chi nhánh Bình Thạnh</b><br>
                    Tên chủ tài khoản/Account name:&nbsp;<b>Công ty Cổ phần Đầu tư Bất Động Sản Hùng Vương</b>
                    <br>
                    <b>Khi thanh toán bằng chuyển khoản, vui lòng ghi rõ mã số căn hộ</b>
                </div>

                <div class="col-xs-4 text-center" style="font-size: 10px; text-align: center; vertical-align: top">
                    BAN QUẢN LÝ TÒA NHÀ SAIGONRES PLAZA
                    <br>Trưởng Ban
                    <br>(Đã Ký)
                </div>
            </div>
        </div>
    @endforeach
    </page>
@endsection

