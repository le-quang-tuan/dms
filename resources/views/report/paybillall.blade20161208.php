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
        font-size: 10px;
        width: 755px;
    }
</style>
@endsection

@section('content')

    @foreach($paybill_lst as $paybill)
    <?php
        $tenement_info = $paybill["tenement_info"];
        $flat_info = $paybill["flat_info"];
        $tf_paid_hd = $paybill["tf_paid_hd"];
        $tf_paid_dt = $paybill["tf_paid_dt"];
        $total_money = $paybill["total_money"];
        $total_money_read = $paybill["total_money_read"];
    ?>
<div class="row voffset">
    <div class="col-xs-12">
        <div class="col-xs-4">
                <b>Công Ty Quản Lý Bất Động Sản:</b>{!! $tenement_info->manager_company !!}
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
<div class="row voffset1">
    <div class="col-xs-12">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
        <div class="col-xs-12">Đã nhận đủ số tiền (viết bằng chữ): .................................................................................................................................</div>
        <div class="col-xs-12">+ Tỷ giá ngoại tệ (vàng, bạc, đá quý): .............................................................................................................................</div>
        <div class="col-xs-12">+ Số tiền quy đổi: ..........................................................................................................................................................</div>
    </div>
</div>
@endforeach
    
@endsection

