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
    }
    .detail th{
        font-size: 13px;
        font-weight: bold;
        text-align: center;
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
    .container-fluid{
        margin-left: 1px;
        margin-right: 1px;
        overflow: hidden;
    }

    .valign-bottom{
        vertical-align:bottom !important;
    }

    body{
        font-size: 10px;
    }
</style>
@endsection

@section('script')
{!! Html::script('js/jquery.number.min.js') !!}
@endsection

@section('content')
<page size="A5" layout="portrait">
<div class="row voffset">
    <div class="col-xs-12">
        <div class="col-xs-4">
                <b>{!! $tenement_info->manager_company !!}</b>
                <br>
                <b>Dự Án:</b>{!! $tenement_info->name !!}
                <br>            
                <b>Mã Phiếu: </b> {!! $tf_paid_hd->paid_code !!}
        </div>

        <div class="col-xs-3 text-center">
                Liên 1: Lưu (Chủ Đầu Tư/Ban Quản Trị)<br>
                <h4>Phiếu Thu</h4>
                <br>
                Ngày Xuất PT: {!! Date('d') . ' Tháng' . Date('m') . ' Năm' . Date('Y') !!}
        </div>

        <div class="col-xs-3 text-right">
                <b>Quyển Số:</b>{!! $tf_paid_hd->book_bill !!} 
                <br> 
                <b>Số: </b>{!! $tf_paid_hd->bill_no !!}
        </div>
    </div>
</div>

<div class="row voffset1">
    <div class="col-xs-11">
        <div class="col-xs-12"><b>Họ tên người nộp tiền :</b>{!! $tf_paid_hd->receive_from !!}</div>
        <div class="col-xs-12"><b>Địa chỉ :</b> {!! $flat_info->address !!}</div>
        <div class="col-xs-12"><b>Lý do nộp tiền :</b> {!! $tf_paid_hd->comment !!}</div>
        <div class="col-xs-12">
            Chi Tiết
        </div>                
    </div>
</div>

<div>
    <div class="col-xs-12">
        <table class="table table-bordered">
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
                            $paid->name . ' ' . substr($paid->year_month, 0,4) . '/' . substr($paid->year_month, 4, 2)
                             !!}
                        </td>
                        <td class="text-center">
                            {!! $paid->money !!}
                        </td>
                    </tr>
                    <?php $step++; ?>
                @endforeach
            </tbody>
        </table>
    </div>

<div class="row voffset1">
    <div class="col-xs-11">
        <div class="col-xs-12 "><b>Số tiền :</b>{!! $total_money !!}</div>
        <div class="col-xs-12"><b>Bằng chữ:</b>{!! $total_money_read !!}</div>
        <div class="col-xs-12"><b>Kèm theo: ............................Chứng từ gốc.</b></div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 text-center">
        <div class="col-xs-2">
            Trưởng Ban Quản Lý<br>(Ký, họ tên, đóng dấu222)<br>Đã ký
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
<div class="row voffset1">
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
</page>
@endsection

