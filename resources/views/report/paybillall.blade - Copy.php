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
    }
    .detail td{
        padding-left: 5px;
        padding-right: 5px;
        font-size: 16px;
    }
    .detail th{
        font-size: 12px;
        font-weight: bold;
        text-align: center;
    }

    body {
      background: white; 
      font-size: 10px;
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
</style>
@endsection

@section('content')
<page size="A5" layout="portrait">
    <div id="aaa">
        <img src="http://localhost:8088/dms/public/img/logo.png" alt="Forest" width="50" height="45">
    </div>

    <?php
        $paybillTitle = array(
            "Liên 1: Lưu (Chủ Đầu Tư/Ban Quản Trị)", 
            "Liên 3: Lưu (Kế toán)" . $tenement_info->manager_company, 
            "Liên 2: Giao Khách Hàng"); 
        $num = 1;
    ?>
    @foreach($paybillTitle as $title)

        @foreach($paybill_lst as $paybill)
        <?php
            $tenement_info = $paybill["tenement_info"];
            $flat_info = $paybill["flat_info"];
            $tf_paid_hd = $paybill["tf_paid_hd"];
            $tf_paid_dt = $paybill["tf_paid_dt"];
            $total_money = $paybill["total_money"];
            $total_money_read = $paybill["total_money_read"];
        ?>
            <div  class="header">
                <div class="col-xs-12">
                    <div class="col-xs-5" style="text-align: left;padding-left: 60px">
                            <b>{!! $tenement_info->manager_company !!}</b>
                            <br>
                            <b>Dự Án: &nbsp;</b>Dự Án SaiGonRes Plaza
                            <br>            
                            <b>Mã Phiếu: &nbsp;</b> {!! $tf_paid_hd->paid_code !!}
                    </div>

                    <div class="col-xs-3 text-center">
                            {{ $title }}<br>
                            <h4>Phiếu Thu</h4>
                            <br>
                            Ngày Xuất PT: {!! Date('d') . ' Tháng' . Date('m') . ' Năm' . Date('Y') !!}
                    </div>

                    <div class="col-xs-2 text-right">
                            <b>Quyển Số:&nbsp;</b>00001
                            <!-- {!! $tf_paid_hd->book_bill !!}  -->
                            <br> 
                            <b>Số:&nbsp;</b>{!! $tf_paid_hd->bill_no !!}
                    </div>
                </div>
            </div>

            <div>
                <div>
                    <div class="col-xs-11">
                        <div class="col-xs-12"><b>Họ tên người nộp tiền :&nbsp;</b>{!! $flat_info->name !!}</div>
                        <div class="col-xs-12"><b>Địa chỉ :&nbsp;</b> {!! $flat_info->address !!}</div>
                        <div class="col-xs-12"><b>Lý do nộp tiền :&nbsp;</b> {!! $tf_paid_hd->comment !!}</div>
                        <div class="col-xs-12">
                            Chi Tiết
                        </div>                
                    </div>
                </div>
            </div>

            <div>
                <div class="col-xs-12">
                    <table class="table-bordered detail">
                        <tbody>
                            <tr>
                                <th class="col-xs-1 text-center">STT</th>
                                <th class="col-xs-5 text-center">Nội Dung</th>
                                <th class="col-xs-3 text-center">Số Tiền(VND)</th>
                            </tr>
                            <?php $step = 1; ?>
                            @foreach($tf_paid_dt as $paid)
                                <tr>                    
                                    <td align="center"><span id="snum">{!! $step !!}.</span></td>
                                    <td class="text-center">
                                        {!! 
                                        $paid->name . ' ' . substr($paid->year_month, 4, 2) . '/' . substr($paid->year_month, 0,4) 
                                         !!}
                                    </td>
                                    <td class="text-center">
                                        {!! number_format($paid->money) !!}
                                    </td>
                                </tr>
                                <?php $step++; ?>
                            @endforeach
                        </tbody>
                    </table>
                    <br>
                </div>
            </div>
            <div>
                <div class="col-xs-12">
                    <table class="table detail">
                        <tbody>
                            <tr>
                                <td>Số tiền : {!! number_format($total_money) !!}VND
                                <br>Bằng chữ: {!! $total_money_read !!} Đồng
                                <br>Kèm theo: ............................Chứng từ gốc.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <div class="col-xs-12">
                    <div>
                        <div class="col-xs-12 text-center">
                            <div class="col-xs-2">
                                Trưởng Ban Quản Lý<br>(Ký, họ tên, đóng dấu)
                            </div>
                            <div class="col-xs-2">
                                Kế Toán<br>(Ký, họ tên)
                            </div>
                            <div class="col-xs-2">
                                Người nộp tiền<br>(Ký, họ tên)
                            </div>
                            <div class="col-xs-2">
                                Người lập phiếu<br>(Ký, họ tên)
                            </div>
                            <div>
                                NV Thu Phí<br>(Ký, họ tên)
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="col-xs-12">
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                            <div class="col-xs-12">Đã nhận đủ số tiền (viết bằng chữ): .................................................................................................................................</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php  
                $num++; 
                if ($num < 4)
                    echo "<div style='page-break-after:always;'>&nbsp;</div>";
            ?>
            @endforeach
        @endforeach
</div>
</page>
@endsection

