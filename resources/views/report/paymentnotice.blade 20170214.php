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

    body{
        font-size: 12px;
        width: 755px;
    }

    img {
    opacity: 0.2;
    filter: alpha(opacity=20); /* For IE8 and earlier */
    }

    #aaa{
        position: absolute;
/*        top: -90px;
        right: 750px;*/
        width: 85px;
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
</style>
@endsection

@section('script')
{!! Html::script('js/jquery.number.min.js') !!}

<script>
</script>
@endsection

@section('content')
<div id="aaa">
    <img src="http://localhost:8088/dms/public/img/logo.png" alt="Forest" width="170" height="100">
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
        $payment = $paymentnotice["payment"];
        $elec = $paymentnotice["elec"];
        $water = $paymentnotice["water"];
        $gas = $paymentnotice["gas"];
        $parking = $paymentnotice["parking"];
        $service = $paymentnotice["service"];
        $dept = $paymentnotice["dept"];
        $paid = $paymentnotice["paid"];
        $tenement_info = $paymentnotice["tenement_info"];
        ?>

        <div class="header">
            <h3 style="font-size: 18px">
            PHIẾU THÔNG BÁO THANH TOÁN ({!! substr($payment->year_month, 0,4) . '/' . substr($payment->year_month, 4, 2)
            !!})<br>
            {!! $tenement_info->name !!}<br>
            PAYMENT NOTICE({!! substr($payment->year_month, 0,4) . '/' . substr($payment->year_month, 4, 2) !!})
            </h3>
        </div>

        <div>
            <div class="col-xs-12">
                <div class="col-xs-6"><b>Kính gửi Ông Bà/ To Mr. Ms: </b>{!! $flat_info->name !!}</div>
                <div class="col-xs-4"><b>Căn hộ/ Apartment :</b> {!! $flat_info->address !!}</div>
                <div class="col-xs-6"><b>Diện tích/ Floor Area :</b> {!! $flat_info->area !!}m2</div>
                <div class="col-xs-4"><b>Điện thoại/ Phone Number :</b> {!! $flat_info->phone !!}</div>

                <div class="col-xs-12"><b>
                    Đơn giá phí quản lý/ Monthly management fee :</b>{!! $tenement_info->manager_fee !!}đ/m2/tháng
                </div>                
            </div>
        </div>


        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  1. Phí quản lý/ Management Service:</b>
            </div>
        </div>
        <div>
            <div >
                <table class="table-bordered" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th class="col-xs-5 text-center">Kỳ hạn/ Term</th>
                            <th class="col-xs-2 text-center">Đơn Giá/ Rate</th>
                            <th class="col-xs-3 text-center">Thành Tiền/Amount(VND)</th>
                        </tr>
                        <tr>                    
                            <td class="text-center">
                                {!! 
                                '01/' . substr($payment->year_month, 4, 2) . '/' . substr($payment->year_month, 0,4) . '~'. date("t/m/Y", strtotime(substr($payment->year_month, 0, 4) . '-' . substr($payment->year_month, 4,2)) . '-01') 
                                !!}
                            </td>
                            <td class="text-center">
                                {!! $tenement_info->manager_fee !!}
                            </td>
                            <td class="text-center">
                                {!! $payment->manager_fee !!}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  2. Tiền điện/ Electricity fee:</b>
            </div>
        </div>
        <div>
            <div>
                <table class="table-bordered" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th class="col-xs-3 text-center">Chỉ số cũ/<br>Previous figure</th>
                            <th class="col-xs-2 text-center">{!! $elec[0]->old_index_hd!!}</th>
                            <th class="col-xs-2 text-center">Chỉ số mới/<br>Current figure</th>
                            <th class="col-xs-1 text-center">{!! $elec[0]->new_index_hd!!}</th>
                            <th class="col-xs-2 text-center">Chỉ số tiêu thụ/<br>Electric use (Kwh)</th>
                            <th class="col-xs-2 text-center">{!! $elec[0]->mount_hd !!}</th>
                        </tr>
                        
                        <tr>
                            <th class="text-center">Định mức/ Limits</th>
                            <th class="text-center">Từ/From(Kwh)</th>
                            <th class="text-center">Đến/To(Kwh)</th>
                            <th class="text-center">Đơn giá/Rate</th>
                            <th class="text-center">Phí VAT/VAT Fee</th>
                            <th class="text-center">Thành tiền/<br>Amount(VND)</th>
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
        </div>


        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  3. Tiền nước/ Water fee:</b>
            </div>
        </div>
        <div>
            <table class="table-bordered" style="margin-left: 50px;width: 95%">
                <tbody>
                    <tr>
                        <th class="col-xs-3 text-center">Chỉ số cũ/<br>Previous figure</th>
                        <th class="col-xs-2 text-center">Chỉ số mới/<br>Current figure</th>
                        <th class="col-xs-2 text-center">Chỉ số tiêu thụ/<br>Volumn (m3)</th>
                        <th class="text-center">Đơn giá/ Rate</th>
                        <th class="text-center">Thành tiền/<br>Amount(VND)</th>

                    </tr>
                    <tr>
                    
                        <th class="col-xs-2 text-center">{!! $water[0]->old_index_hd!!}15</th>
                        <th class="col-xs-1 text-center">{!! $water[0]->new_index_hd!!}24</th>
                        <th class="col-xs-2 text-center">{!! $water[0]->mount_hd !!}9</th>
                        <th class="col-xs-2 text-center">
                        </th>
                        <th class="col-xs-2 text-center">
                        </th>
                    </tr>

                    @foreach($water as $water_dt)
                    <tr>
                        <!-- <th class="text-center" colspan="2">{!! $water_dt->water_type_name . '(' . $water_dt->water_tariff_name . ')' !!}</th>
                        <th class="text-center">{!! $water_dt->mount !!}</th>
                        <th class="text-center">{!! $water_dt->price !!}</th>
                        <th class="text-right">{!! $water_dt->total !!}</th> -->

                        <th class="text-center" colspan="2">Định mức 1</th>
                        <th class="text-center">5</th>
                        <th class="text-center">5000</th>
                        <th class="text-right">25000</th>

                    </tr>
                    @endforeach

                    <tr>
                        <th class="text-center" colspan="2">Định mức 2</th>
                        <th class="text-center">4</th>
                        <th class="text-center">9000</th>
                        <th class="text-right">36000</th>
                    </tr>
                    <tr>
                        <th class="text-left" colspan="4">VAT (5%)</th>
                        <th class="text-right">15250</th>
                    </tr>
                    <tr>
                        <th class="text-left" colspan="4">Phí hao hụt /Loss compensation(5%)</th>
                        <th class="text-right">15250</th>
                    </tr>
                    <tr>
                        <th class="text-left" colspan="4">Tổng cộng /Total (VND)</th>
                        <th class="text-right">80000</th>
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

        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  4. Tiền gas/ Gas fee:</b>
            </div>
        </div>
        <div>
            <div>
                <table class="table-bordered" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th class="col-xs-3 text-center">Chỉ số cũ/<br>Previous figure</th>
                            <th class="col-xs-2 text-center">{!! $gas[0]->old_index_hd!!}</th>
                            <th class="col-xs-2 text-center">Chỉ số mới/<br>Current figure</th>
                            <th class="col-xs-1 text-center">{!! $gas[0]->new_index_hd!!}</th>
                            <th class="col-xs-2 text-center">Chỉ số tiêu thụ/<br>Electric use (Kwh)</th>
                            <th class="col-xs-2 text-center">{!! $gas[0]->mount_hd !!}</th>
                        </tr>
                        
                        <tr>
                            <th class="text-center">Định mức/ Limits</th>
                            <th class="text-center">Từ/From(Kwh)</th>
                            <th class="text-center">Đến/To(Kwh)</th>
                            <th class="text-center">Đơn giá/Rate</th>
                            <th class="text-center">Phí VAT/VAT Fee</th>
                            <th class="text-center">Thành tiền/<br>Amount(VND)</th>
                        </tr>

                        @foreach($gas as $gas_dt)
                        <tr>
                            <th class="text-center">{!! $gas_dt->gas_type_name . '(' . $gas_dt->gas_tariff_name . ')' !!}</th>
                            <th class="text-center">{!! $gas_dt->from_index !!}</th>
                            <th class="text-center">{!! $gas_dt->to_index !!}</th>
                            <th class="text-center">{!! $gas_dt->price !!}</th>
                            <th class="text-center">{!! $gas_dt->vat_money !!}</th>
                            <th class="text-right">{!! $gas_dt->total !!}</th>
                        </tr>
                        @endforeach

                        <tr>
                            <th class="text-left" colspan="5">Phí hao hụt /Loss compensation({!! $gas[0]->other_fee01_hd !!})</th>
                            <th class="text-right">{!! $gas[0]->other_fee01_money_hd !!}</th>
                        </tr>
                        <tr>
                            <th class="text-left" colspan="5">Tổng cộng /Total (VND)</th>
                            <th class="text-right">{!! $gas[0]->total_hd !!}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  5.Phí giữ xe/Parking Service:</b>
            </div>
        </div>
        <div>
            <div>
                <table class="table-bordered" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th class="col-xs-5 text-center">Loại Xe/Kind of vehicle</th>
                            <th class="col-xs-2 text-center">Số lượng/Quantity</th>
                            <th class="col-xs-2 text-center">Đơn giá/Rate</th>
                            <th class="col-xs-2 text-center">Thành tiền/Amount VND</th>
                        </tr>
                        @foreach($parking as $parking_dt)
                            <tr>                    
                                <td class="text-center">
                                    {!! $parking_dt->parking_name !!}
                                </td>
                                <td class="text-center">
                                    {!! $parking_dt->total_count !!}
                                </td>
                                <td class="text-center">
                                    {!! $parking_dt->price !!}
                                </td>
                                <td class="text-center">
                                    {!! $parking_dt->total_money !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  6.Phí khác/Other fees:</b>
            </div>
        </div>
        <div>
            <div>
                <table class="table-bordered" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th class="col-xs-4 text-center">Diễn giải/Description</th>
                            <th class="col-xs-2 text-center">Số lượng/Quantity</th>
                            <th class="col-xs-2 text-center">Đơn vị</th>
                            <th class="col-xs-2 text-center">Đơn giá/Rate</th>
                            <th class="col-xs-2 text-center">Thành tiền/Amount VND</th>
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
                                    {!! $service_dt->price !!}
                                </td>
                                <td class="text-center">
                                    {!! $service_dt->total_money !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  7.Nợ cũ/Previous debit:</b>
            </div>
        </div>
        <div>
            <div>
                <table class="table-bordered" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th class="col-xs-1 text-center">Tháng/ Month</th>
                            <th class="col-xs-2 text-center">Phí Quản Lý/<br> Manager Fee</th>
                            
                            <th class="col-xs-2 text-center">Tiền Điện/<br>
                            Electricity fee</th>
                            <th class="col-xs-2 text-center">Phí Nước/<br>
                            Water fee</th>
                            <th class="col-xs-1 text-center">Phí Gas/<br>
                            Gas fee</th>
                            <th class="col-xs-2 text-center">Phí Giữ Xe/<br>
                            Parking fee</th>
                            <th class="col-xs-2 text-center">Phí Khác/<br>
                            Other fee</th>
                        </tr>
                        @foreach($dept as $dept_dt)
                            <tr>                    
                                <td class="text-center">
                                    {!! 
                                    substr($dept_dt->year_month, 0,4) . '/' . substr($dept_dt->year_month, 4, 2)
                                     !!}
                                </td>
                                <td class="text-center">
                                    {!! $dept_dt->manager_fee_dept !!}
                                </td>
                                <td class="text-center">
                                    {!! $dept_dt->elec_fee_dept !!}
                                </td>
                                <td class="text-center">
                                    {!! $dept_dt->water_fee_dept !!}
                                </td>
                                <td class="text-center">
                                    {!! $dept_dt->gas_fee_dept !!}
                                </td>
                                <td class="text-center">
                                    {!! $dept_dt->parking_fee_dept !!}
                                </td>
                                <td class="text-center">
                                    {!! $dept_dt->service_fee_dept !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="col-xs-11">
                <b>&nbsp;&nbsp;&nbsp;&nbsp;  8.Đã Trả/Paid:</b>
            </div>
        </div>
        <div>
            <div>
                <table class="table-bordered" style="margin-left: 50px;width: 95%">
                    <tbody>
                        <tr>
                            <th class="col-xs-2 text-center">Phí Quản Lý/ Manager Fee</th>
                            
                            <th class="col-xs-2 text-center">Tiền Điện/
                            Electricity fee</th>
                            <th class="col-xs-2 text-center">Phí Nước/
                            Water fee</th>
                            <th class="col-xs-2 text-center">Phí Gas/
                            Gas fee</th>
                            <th class="col-xs-2 text-center">Phí Giữ Xe/
                            Parking fee</th>
                            <th class="col-xs-2 text-center">Phí Khác/
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

        <div class="row voffset1">
            <div class="col-xs-11">
                <div class="col-xs-12 "><b>Tổng cộng /Total (1+2+3+4+5+6+7-8)VND:</b>{!! $total_money !!}</div>
                <div class="col-xs-12"><b>Bằng chữ:</b>{!! $total_money_read !!}</div>
                <div class="col-xs-12"><b>By Words:</b>{!! $total_money_read !!}</div>
            </div>
        </div>

        <div>
            <div class="col-xs-12 text-left">
                Thời hạn đóng tiền trong vòng 10 ngày kể từ ngày nhận phiếu thông báo thanh toán. Số tiền thông báo trên phiếu này được
                tính đến hết ngày 31/10/2016. Nếu khách hàng thanh toán sau ngày này, chúng tôi sẽ cập nhật vào phiếu của kỳ sau.<br>

                (Time limit for payment within 10 days after receiving notice of payment. The amount mentioned in this notice of payment is
                updated until Oct 31, 2016. So, in case the residents have paid after this time, the payment will be updated to the next
                notice of payment) <br>

                Vui lòng nộp tiền mặt tại văn phòng BQL hoặc chuyển khoản vào tài khoản sau:<br>

                Please make payment at the management office or transfer money to the account:<br>

                Số TK/Account:2208 1485 1026 812 (Nộp phí QL, nước)/ 2208 1485 1029 485 (Nộp phí xe)<br>

                Ngân hàng/Bank:Ngân hàng Eximbank - Chi nhánh TP. Hồ Chí Minh<br>
                Tên chủ tài khoản/Account name:Công ty TNHH XD Thành Trường Lộc
            </div>
        </div>
    @endforeach
@endsection

