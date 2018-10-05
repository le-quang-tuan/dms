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

    #bbb{
        position: absolute;
/*        width: 85px;*/
        left: 45px;
        margin-top: -520px;
        opacity: 0.2;
        z-index:-1;
        /*margin-left: : 40px;*/
    }

img
{
    position:absolute;
    left:0px;
    top:0px;
    z-index:-1;
}
    #container {
        background: url('http://localhost:8080/dms/public/img/logo_watermark.png');
        /*background: linear-gradient(rgba(255,255,255,.5), rgba(255,255,255,.5)), url("https://i.imgur.com/xnh5x47.jpg");*/
        background-position:center;
        background-repeat: no-repeat;
        opacity: 0.9;
        filter: alpha(opacity=20);
        /*width: 800px;*/
        margin-left: 30px;
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
            $flat_info = $paymentnotice["flat_info"];
            $total_money = $paymentnotice["total_money"];
            $total_money_read = $paymentnotice["total_money_read"];
            $total_money_readEn = $paymentnotice["total_money_readEn"];
            $tenement_info = $paymentnotice["tenement_info"];

            $number = 0;
            $stringNumber = "";
        ?>

        <div class="header">
            <h3 style="font-size: 12px;text-align: left;margin-left: 125px">
                    <br>
                    Ban quản lý tòa nhà {!! $tenement_info->name !!}.<br>
                    Địa chỉ: {!! $tenement_info->address !!}<br>
                    Tel: (028) 3620 7885<br>
                    Hotline: (08) 6844 4387<br>            
            </h3>
        </div>

        <div>
            <div>
                <div class="col-xs-11" style="text-align: center;">
                    <br>
                        THÔNG BÁO NHẮC NHỞ THANH TOÁN PHÍ <br>
                        <i>THE 2nd REMINDER OF INVOICE OUTSTANDING </i>
                </div>
            </div>
        </div>
        
        <div class="row voffset1" id="container">
            <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 10px">
                <u><i>Kính gửi / <i>Respectfully to:</i> </i></u>
            </div>
            <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 50px"><b>Ông/ Bà / <i>Mr/ Ms:</i>            &nbsp; </b>{!! $flat_info->name !!}</div>
            <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 50px"><b>Căn hộ / <i>Apartment No.:</i>   &nbsp;</b> {!! $flat_info->address !!} &nbsp; Chung cư / <i>Apartment Building: Saigonres Plaza</i></div>
            <div class="col-xs-11"></div>    

            <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 10px">
                <br>
                    -   &nbsp; &nbsp; &nbsp; &nbsp; Căn cứ nội quy quản lý Chung cư Saigonres Plaza; <br>
                    -   &nbsp; &nbsp; &nbsp; &nbsp; <i>Basing oneself on the Regulations of  Saigonres Plaza Apartment;</i> <br>
                    -   &nbsp; &nbsp; &nbsp; &nbsp; Căn cứ thông báo thanh toán phí dịch vụ Tháng <?php echo date('m/Y'); ?> đã được gửi đến chủ căn hộ; <br>
                    -   &nbsp; &nbsp; &nbsp; &nbsp; <i>Basing oneself on the monthly fee announcement of <?php echo date('M, Y'); ?>, sent to the apartment owner; </i><br>

            </div>                           
            <div class="col-xs-11">
                <div class="col-xs-12 text-left" style="font-size: 14px">
                    <br>Kính thưa Quý Cư dân/Dear Value Residents, <br><br>

                    Theo quy định việc thanh toán các khoản chi phí dịch vụ của căn hộ sẽ được Quý cư dân thực hiện trước <b>ngày 15 dương lịch</b> hàng tháng, tuy nhiên đến nay Ban Quản Lý vẫn chưa nhận được thanh toán từ Quý cư dân cho các dịch vụ đã sử dụng trong Tháng <?php echo date('m/Y'); ?> với tổng số tiền là: <b>{!! number_format($total_money) !!} vnd</b> 
                    
                    <br>
                    <i>According to the present regulations, the payment for service charges should be made before the <b>15th</b> date monthly. However, up to this time, we have not received your payment for the services used in <?php echo date('M, Y'); ?> yet with the amount:</i> <b>VND {!! number_format($total_money) !!}</b> <br><br>

                    Việc chậm thanh toán phí hàng tháng của Quý Cư dân sẽ gây khó khăn cho công tác quản lý vận hành tòa nhà nói chung và ảnh hưởng đến sinh hoạt riêng của cư dân tòa nhà. Vì vậy chúng tôi rất mong nhận được sự hợp tác và hỗ trợ của Quý cư dân trong việc thanh toán phí đúng thời hạn giúp công tác quản lý toà nhà được tiến hành thuận lợi và việc cung cấp các dịch vụ cho Quý cư dân được duy trì ổn định. 
                    <br>
                    <i>Your late payment has caused some difficulties to the tasks of property management in general and has some adverse effects on the activities of the apartment building. Therefore, we hope to receive your co-operation and support in payment on schedule in order to create favorable conditions for the tasks of property management and stabilize the quality of the management service.</i>  <br><br>

                    <b>Khi nhận được thông báo này mong Quý Cư dân vui lòng thanh toán tại: <br>
                    <i>When receiving this reminder of invoice outstanding, please liquidate your payables in cash at: </i></b><br>
                    -   Khu vực sảnh lễ tân Lô A,B / <i>Reception Desk</i> <br>
                    -   Điện thọai / <i>Telephone No.</i>: (028) 3620 7881-82-85 <br><br>

                    <b>Hoặc chuyển khoản qua ngân hàng/<i>Or please make your payment by transfer to: </i></b><br>
                    -   Số TK/<i>Beneficiary account name</i>: 1687 0407 0119 999 <br>
                    -   Ngân hàng/<i>Bank</i>: Ngân hàng HD Bank   Chi nhánh/<i>Branch</i>: Nguyễn Đình Chiểu <br>
                    -   Tên chủ tài khoản/<i>Beneficiary name</i>: Công ty Cổ Phần Đầu tư Bất Động Sản Hùng Vương <br><br>

                    BQL tòa nhà xin chân thành cảm ơn sự hợp tác và hỗ trợ của Quý cư dân. <br>
                    <i>We would be much obliged for your kind support.</i> <br><br>
                </div>
                <div class="col-xs-12 text-right" style="font-size: 14px">
                    Tp.HCM, <?php echo 'ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y');?>/ <i>HCMC, <?php echo date('M') . ', ' . date('j') . ', ' . date('Y');?></i>
                    <br>
                    BAN QUẢN LÝ TÒA NHÀ/ <i>On behalf of Management Board</i>&nbsp;<br>
                </div>
            </div>
        </div>
    @endforeach
<!--     <div id="bbb">
        {!! HTML::image('img/logo.png', 'alt', array('width' => 700, 'opacity' => 0.2)) !!}
    </div> --> 
    </page>
@endsection

