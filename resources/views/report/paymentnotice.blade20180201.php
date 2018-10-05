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

        $number = 0;
        $stringNumber = "";
        ?>

        <div class="header">
            <h3 style="font-size: 18px">
            THÔNG BÁO PHÍ - PAYMENT NOTIFICATION (KỲ {!! substr($payment->year_month, 4, 2) . '/' . substr($payment->year_month, 0,4) !!})<br>
            {!! $tenement_info->name !!}<br>
            Ngày/ Date:&nbsp;<?php echo date('d/m/Y');?>
            </h3>
        </div>

        <div>
            <div>
                <div class="col-xs-11">
                    <br>
                    Ban quản lý tòa nhà {!! $tenement_info->name !!} xin trân trọng thông báo về các khoản phí tháng {!! substr($payment->year_month, 4, 2) . '/' . substr($payment->year_month, 0,4) !!}.
                </div>
                <div class="col-xs-11">
                    <i>We are pleased to inform about the fee in <?php echo date('M, Y');?></i>
                </div>
                <div class="col-xs-11"><b>Kính gửi Ông Bà/ To Mr./Ms.:&nbsp; </b>{!! $flat_info->name !!}</div>
                <div class="col-xs-11"><b>Điện thoại/ Phone Number:&nbsp;</b> {!! $flat_info->phone !!}</div>

                <div class="col-xs-6"><b>Căn hộ/ Apartment:&nbsp;</b> {!! $flat_info->address !!}</div>
                <div class="col-xs-5"><b>Diện tích/ Floor Area:&nbsp;</b> {!! $flat_info->area !!}m2</div>
                

                <!-- <div class="col-xs-12"><b>
                    Đơn giá phí quản lý/ Monthly management fee :&nbsp;</b>
                    {!! number_format($tenement_info->manager_fee, 0, ',', '.') !!}đ/m2/tháng
                </div>   -->  
                <div class="col-xs-11"><br></div>            
            </div>
        </div>
        <?php
            $manager_fee = $payment->manager_fee - $paid[0]->manager_fee_paid;
         ?>
        @if ($manager_fee > 0)
        <?php  
            $number += 1;
            $stringNumber .= " + " . $number;
        ?>
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Phí quản lý/ Management fee:&nbsp;</b>
            </div>
        </div>
        <div>
            <div >
                <table class="detail">
                    <tbody>
                        <tr>
                            <th class="col-xs-4 text-center">Kỳ hạn/ Term</th>
                            <th>Đơn Giá/ Unit price (VND/m2)</th>
                            <th class="col-xs-4 text-center">Thành Tiền/ Amount (VND)</th>
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
                                {!! number_format($manager_fee) !!}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
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
                            <th class="col-xs-3 text-center">Chỉ số cũ<br>Old figure</th>
                            <th>{!! $elec[0]->old_index_hd!!}</th>
                            <th>Chỉ số mới<br>New figure</th>
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
                            <th class="text-center">Thành tiền<br>Amount (VND)</th>
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
                        <br>Old figure</th>
                        <th>Chỉ số mới<br>New figure</th>
                        <th>Chỉ số tiêu thụ<br>Electric use (Kwh)</th>
                        <th>Đơn giá<br>Unit price (VND/kwh)</th>
                        <th>Thành tiền<br>Total (VND)</th>
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
                        <td class="text-left" colspan="4">Phí hao hụt/ Loss compensation (5%)</td>
                        <td class="text-right">15250</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Tổng cộng/ Total (VND)</td>
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

        <?php
            $number += 1;
            $stringNumber .= " + " . $number;
            $water_total = 0;
            $water_used_id = '';

            $mount_hd = 0;
            $deduct = 0;

            $date_from = "";
            if ($water[0]->date_from != ''){
                $date_from = DateTime::createFromFormat('Ymd', $water[0]->date_from);
                $date_from = $date_from->format('d/m/Y');
            }

            $date_to = "";
            if ($water[0]->date_to != ''){
                $date_to = DateTime::createFromFormat('Ymd', $water[0]->date_to);
                $date_to = $date_to->format('d/m/Y');
            }
         ?>
        <div>
            <div class="col-xs-11">
                <div class="col-xs-5">
                <b>&nbsp;&nbsp;{!! $number !!}. Tiền nước/ Water charge:&nbsp;</b>
                </div>
                <div class="col-xs-7" style="font-size: 13px;padding-left:  -70px"> ({!! $date_from . "-" . $date_to !!}) 
                @foreach($water as $water_dt)
                    @if ($water_used_id != $water_dt->water_used_id)
                    <?php
                        $mount_hd += $water_dt->new_index_hd - $water_dt->old_index_hd;
                        if (isset($water_dt->used_deduct)){
                            $deduct += $water_dt->used_deduct;
                        }
                        $water_used_id = $water_dt->water_used_id;
                    ?>
                    @endif
                @endforeach
                <?php  
                    $water_used_id = '';
                ?>

                Tiêu thụ/ Consume: {!! $mount_hd !!}m3
                @if ($deduct > 0)
                    Khấu trừ: {!! $deduct  !!}m3
                @endif
                </div>
            </div>

        </div>
        <div>
            <table class="detail" style="margin-left: 50px;width: 95%">
                <tbody>
                    <tr>
                        <th>Chỉ số cũ<br>Old figure</th>
                        <th>Chỉ số mới<br>New figure</th>
                        <th>Tiêu thụ<br>Consume<br>(m3)</th>
                        <th>Đơn giá<br>Unit price (VND/m3)</th>
                        <th>Thành tiền<br>Amount (VND)</th>
                    </tr>
                    
                    @if ($flat_info->persons > 0)
                    <tr>
                        <td colspan="5">
                            Số nhân khẩu được cấp định mức/inhabitants shall be given norms: {!! $flat_info->persons !!}
                        </td>
                    </tr>
                    @endif
                    @foreach($water as $water_dt)

                    @if ($water_used_id != $water_dt->water_used_id)
                    <tr>
                        <td class="text-center">{!! number_format($water_dt->old_index_hd) !!}</td>
                        <td class="text-center">{!! number_format($water_dt->new_index_hd) !!}</td>
                        <td class="text-center">{!! number_format($water_dt->new_index_hd - $water_dt->old_index_hd) !!}</td>
                        <td>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <?php
                        $water_used_id = $water_dt->water_used_id;
                    ?>
                    @endif
                    @if (isset($water_dt->total_hd))    
                        <tr>
                            <td class="text-left" colspan="2">{!! $water_dt->water_tariff_name !!}</td>
                            <td class="text-right">{!! $water_dt->mount !!}</td>
                            <td class="text-right">{!! number_format($water_dt->price) !!}</td>
                            <td class="text-right">{!! number_format($water_dt->total . $water_dt->other_fee01_money_hd . $water_dt->vat_money_hd . $water_dt->other_fee02_money_hd) !!}</td>
                        </tr>
                    @endif

                    <?php 
                        $water_total = $water_dt->total_hd;
                    ?>
                    @endforeach

                    <!-- <tr>
                        <td class="text-left" colspan="4">VAT ({!! number_format($water[0]->vat) !!}%)</td>
                        <td class="text-right">{!! number_format($water[0]->vat_money_hd) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Phí hao hụt/ Loss compensation ({!! number_format($water[0]->other_fee02) !!}%)</td>
                        <td class="text-right">{!! number_format($water[0]->other_fee02_money_hd) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Phí BVMT ({!! number_format($water[0]->other_fee01) !!}%)</td>
                        <td class="text-right">{!! number_format($water[0]->other_fee01_money_hd) !!}</td>
                    </tr> -->
                    <tr>
                        <td class="text-left" colspan="4">Tổng cộng/ Total (VND)</td>
                        <td class="text-right">{!! number_format($water_total) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

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
                            <td class="col-xs-3 text-center">Chỉ số cũ<br>Old figure</td>
                            <td>{!! $gas[0]->old_index_hd!!}</td>
                            <td>Chỉ số mới<br>New figure</td>
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
                            <td class="text-center">Tdành tiền<br>Amount (VND)</td>
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
                        <th>Chỉ số cũ<br>Old figure</th>
                        <th>Chỉ số mới<br>New figure</th>
                        <th>Chỉ số tiêu thụ<br>Gas use (Kg)</th>
                        <th>Đơn giá/ Unit price (VND/kg)</th>
                        <th>Thành tiền<br>Total (VND)</th>
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
                        <td class="text-left" colspan="4">Phí hao hụt /Loss compensation (5%)</td>
                        <td class="text-right">15250</td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="4">Tổng cộng/ Total (VND)</td>
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

        @if (isset($parking[0]->total_count) && $paid[0]->parking_fee_paid < $paid[0]->parking_fee)
        <?php
            $number += 1;
            $stringNumber .= " + " . $number;
        ?>
        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Phí giữ xe/ Parking Service:&nbsp;</b>
                @if ($paid[0]->parking_fee_paid >0)
                    Phí đã trả: {!! number_format($paid[0]->parking_fee_paid) !!}
                @endif
            </div>
        </div>
        <div>
            <div>
                <table class="detail" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th>Loại Xe/ Kind of vehicle</th>
                            <th>Số lượng/ Quantity</th>
                            <th>Đơn giá/ Rate</th>
                            <th>Thành tiền/ Total (VND)</th>
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
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  {!! $number !!}. Phí khác/Other fees:&nbsp;</b>
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
                            <th>Thành tiền/Amount  VND</th>
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
                            <th>Loại Phí/ Other Fees</th>
                            <th>Số Giờ Sử Dụng/ Used Times</th>
                            <th>Đơn Giá/ Unit Price</th>
                            <th>Thành tiền/ Total  VND</th>
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

                                    <!-- @if ($dept_dt->manager_fee_dept > 0 && $first_month)
                                    <br>
                                    <div style="font-size: 10px">Ngày nhận bàn giao + 10 ngày đến hết T1/2017</div>
                                    @endif
                                    <?php $first_month = false; ?> -->

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
        
        <div class="row voffset1">
            <div class="col-xs-11">
                <div class="col-xs-12 "><br><b>Tổng cộng/ Grand Total ({!! substr($stringNumber,2) !!}):&nbsp;</b>{!! number_format($total_money) !!} &nbsp;VND</div>
                <div class="col-xs-12"><b>Bằng chữ:&nbsp;</b>{!! $total_money_read !!}&nbsp;đồng.</div>
                <div class="col-xs-12"><b>By Words:&nbsp;</b>{!! $total_money_readEn !!}&nbsp;dong.</div>
            </div>
        </div>

        <div class="row voffset1">
            <div class="col-xs-11">
                <div class="col-xs-12 text-left" style="font-size: 10px">
                    Trân trọng kính mời Quý Ông/ Bà vui lòng thanh toán tại <b>Văn Phòng BQL</b> hoặc chuyển khoản theo thông tin: <i>Payment can be made by cash at Management Office or bank transfer throught following account:</i><br>
                    - &nbsp;&nbsp;&nbsp;&nbsp;Số TK/Beneficiary account number:&nbsp;<b>{!! $tenement_info->account !!}</b><br>
                    - &nbsp;&nbsp;&nbsp;&nbsp;Ngân hàng/Bank:&nbsp; <b>{!! $tenement_info->bank !!}</b><br>
                    - &nbsp;&nbsp;&nbsp;&nbsp;Chi nhánh:<b>{!! $tenement_info->branch !!}</b><br>
                    - &nbsp;&nbsp;&nbsp;&nbsp;Tên chủ tài khoản/Beneficiary name:&nbsp;<b>{!! $tenement_info->account_name !!}</b><br>
                </div>
            </div>

            <div class="col-xs-11">
                <div class="col-xs-12 text-left" style="font-size: 10px">
                    <b>Thời hạn thanh toán các khoản phí dịch vụ: &nbsp;<u>trong vòng 10 ngày kể từ ngày phát hành thông báo phí.</u></b>/&nbsp;<i>Please <b>pay within 10days from of notification.</b></i><br>
                    Để có đủ kinh phí vận hành Chung cư một cách ổn định và tránh xảy ra bất tiện do các nhà cung cấp tạm ngưng dịch vụ, kính đề nghị Quý cư dân vui lòng thanh toán đầy đủ và đúng thời hạn nêu trên./&nbsp;<i>The residents payment complete and timely manner as above to not paused service, please</i><br>
                    Khi thanh toán bằng chuyển khoản,xin vui lòng <b>ghi rõ mã số căn hộ</b>./&nbsp;<i>When paying by bank transfer,<b>please specify the number of apartments</b></i><br>
                    Ví dụ: Căn khách Nguyễn Văn A số: A-0702. <br>
                    Nội dung thanh toán ghi như sau: Thanh toán phí quản lý, tiền nước, giữ xe căn hộ A-0702 kỳ <?php echo date('m/Y');?>.<br><br>

                    Xin trân trọng cảm ơn và kính chào/ Thank you and best regards!</b><br><br>
                </div>
            </div>
            <div class="col-xs-11">
                <div class="col-xs-7 text-left" style="font-size: 10px; padding-left: 15px">
                    <i><b><u>Mọi chi tiết xin liên hệ</u></b>/&nbsp;Please contact:<br></i>
                    {!! nl2br($tenement_info->contact) !!}
                </div>

                <div class="col-xs-4 text-center" style="font-size: 10px; text-align: center; vertical-align: top">
                    {!! nl2br($tenement_info->managerment) !!}
                </div>
            </div>
        </div>
    @endforeach
    </page>
@endsection

