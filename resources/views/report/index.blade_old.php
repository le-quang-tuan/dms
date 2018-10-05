@extends('includes.invoiceprinthead')

@section('style')
    {!! Html::style('css/card.css') !!}
<style>
    .voffset  { margin-bottom: 2px; }
    .voffset1 { margin-bottom: 5px; }
    .voffset2 { margin-bottom: 10px; }
    .voffset3 { margin-bottom: 15px; }
    .voffset4 { margin-bottom: 30px; }
    .voffset5 { margin-bottom: 40px; }
    .voffset6 { margin-bottom: 60px; }
    .voffset7 { margin-bottom: 80px; }
    .voffset8 { margin-bottom: 100px; }
    .voffset9 { margin-bottom: 150px; }

    .container-fluid{
        margin-left: 5px;
        margin-right: 5px;
        overflow: hidden;
    }

    .valign-bottom{
        vertical-align:bottom !important;
    }

    .body{
    	font-size: 10px;
    }
</style>
@endsection

@section('script')
{!! Html::script('js/jquery.number.min.js') !!}


@endsection

@section('content')
<div class="row voffset1">
    <div class="col-xs-6">
        <div class="col-xs-12"><b>Công Ty Quản Lý Bất Động Sản:</b>{!! $tenement_info->manager_company !!} </div>
        <div class="col-xs-12"><b><td>Dự Án:</b>{!! $tenement_info->name !!}</div>
        <div class="col-xs-12"><b>Mã Phiếu: </b> {!! $tf_paid_hd->paid_code !!}</div>
    </div>
    <div class="col-xs-6" style=" text-align: right;">
        <div class="col-xs-12"><b>Quyển Số:</b>{!! $tf_paid_hd->book_bill !!}</div>
        <div class="col-xs-12"><b>Số: </b>{!! $tf_paid_hd->bill_no !!}</div>
    </div>
</div>

<div class="row voffset1">
    <div class="col-xs-12" style=" text-align: center; margin-top: -47px; position: absolute;margin-left: -150px;">
	    <div class="col-xs-12 text-center">Liên 1: Lưu (Chủ Đầu Tư/Ban Quản Trị)</div>
	    <div class="col-xs-12 text-center"><h3>Phiếu Thu</h3></div>
	    <div class="col-xs-12 text-center">Ngày Xuất PT: {!! Date('d') . ' Tháng' . Date('m') . ' Năm' . Date('Y') !!}</div>
    </div>
</div>

<div class="row voffset1">
    <div class="col-xs-12">
        <div class="col-xs-12"><br><br><b>Họ tên người nộp tiền :</b>{!! $tf_paid_hd->receive_from !!} </div>
        <div class="col-xs-12"><b>Địa chỉ :</b> {!! $flat_info->address !!}</div>
        <div class="col-xs-12"><b>Lý do nộp tiền :</b> {!! $tf_paid_hd->comment !!}</div>
        <div class="col-xs-12"><h3><b>Chi Tiết</b></h3></div>
    </div>
    <div class="col-xs-1 col-xs-offset-1">            
        <div class="col-xs-12">
            
        </div>                
    </div>
</div>

<div class="col-xs-12">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th class="col-xs-1 text-center">STT</th>
                <th class="col-xs-4 text-center">Nội Dung</th>
                <th class="col-xs-2 text-center">Số Tiền(VND)</th>
            </tr>
            	<?php $step = 1; ?>
                <tr>                    
                    <td align="center" rowspan="1"><span id="snum">{!! $step !!}.</span></td>
                    <td class="text-center">
                        111
                    </td>
                    
                    <td class="text-center">
                        123
                    </td>
                </tr>
                <?php $step++; ?>            
        </tbody>
    </table>
</div>

<div class="row voffset1">
    <div class="col-xs-12">
        <div class="col-xs-12 "><b>Số tiền :</b>123</div>
        <div class="col-xs-12"><b>Bằng chữ:</b>TOOOO</div>
        <div class="col-xs-12"><b>Kèm theo: ............................Chứng từ gốc.</b></div>
    </div>
</div>

<div class="row">
    <div class="col-xs-3 col-xs-offset-0">
        <div class="col-xs-12  text-center">Trưởng Ban Quản Lý</div>
        <div class="col-xs-12  text-center">(Ký, họ tên, đóng dấu)</div>
    </div>
    <div class="col-xs-2 col-xs-offset-0">
        <div class="col-xs-12  text-center">Kế Toán</div>
        <div class="col-xs-12  text-center">(Ký, họ tên)</div>
    </div>
    <div class="col-xs-2 col-xs-offset-0">
        <div class="col-xs-12  text-center">Người nộp tiền</div>
        <div class="col-xs-12  text-center">(Ký, họ tên)</div>
    </div>
    <div class="col-xs-3 col-xs-offset-0">
        <div class="col-xs-11  text-center">Người lập phiếu</div>
        <div class="col-xs-11  text-center">(Ký, họ tên)</div>
    </div>
    <div class="col-xs-2 col-xs-offset-0">
        <div class="col-xs-12  text-left">NV Thu Phí</div>
        <div class="col-xs-12  text-left">(Ký, họ tên)</div>
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

    <button class="btn btn-info" id="updatePrintedCounter" name='updatePrintedCounter'>Print</button>
</div>
<script type="text/javascript">
	function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

	var isPrinter = getCookie("isPrinter");
    if (isPrinter != "true") {
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
              //  $(".flash-message").html('');
            }
        }, 1000);
    } else {
        //$(".flash-message").html('');
        setCookie("isPrinter", null);
        var ajax1 = exportPDF();

        $.when(ajax1).done(function() {   

            setTimeout(function(){ // setting the delay for each keypress                
                $.ajax({
                    url: 'index',
                    type: 'POST',
                    dataType : 'json',
                    data: {'id':7, '_token':'{!! csrf_token() !!}'},
                    async: false,
                    success: function (data) {
                        if(data.success == true){
                            window.location.reload(true);
                        }                        
                    }
                });
            }, 3000);            
        });
    }

	function exportPDF(){
        var time = $.now();

        setCookie("methodRequest", null);
         
        return $.ajax({
            url: "{!! url('report/payment/7') !!}",
            type: 'GET',
            dataType : 'json',
            data: {
                '_token':'{!! csrf_token() !!}',
                'id' : 7
            },
            async: false,
            success: function (data) {
                var w = window.open(data.file);
                // w.document.write(data.file);
                if (navigator.appName == 'Microsoft Internet Explorer') window.print();
                else w.print();
            }
        });
    }

	$("#updatePrintedCounter").on('click',function(){   
        $("#invoice_number").val("true"); 
        setCookie("isPrinter", "true");
        $("#frmAddService").submit();         
    });
</script>
@endsection