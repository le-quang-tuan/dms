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
                    Địa chỉ:<br>
                    Tel:<br>
                    Hotline:<br>            
            </h3>
        </div>

        <div>
            <div>
                <div class="col-xs-11" style="text-align: center;">
                    <br>
                        THÔNG BÁO NHẮC NHỞ THANH TOÁN PHÍ <br>
                        THE 1st REMINDER OF INVOICE OUTSTANDING 
                </div>
                <!-- <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 10px">
                    <u><i>Kính gửi / Respectfully to: </i></u>
                </div>
                <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 50px"><b>Ông/ Bà / Mr/ Ms:            &nbsp; </b>{!! $flat_info->name !!}</div>
                <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 50px"><b>Căn hộ / Apartment No.:   &nbsp;</b> {!! $flat_info->address !!} &nbsp; Chung cư / Apartment Building: Saigonres Plaza </div>
                <div class="col-xs-11"></div>   -->          
            </div>
        </div>
        
        <!-- <div>
            <div>
                <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 10px">
                    <br>
                        -   &nbsp; &nbsp; &nbsp; &nbsp; Căn cứ nội quy quản lý Chung cư Saigonres Plaza; <br>
                        -   &nbsp; &nbsp; &nbsp; &nbsp; Basing oneself on the Regulations of  Saigonres Plaza Apartment Building; <br>
                        -   &nbsp; &nbsp; &nbsp; &nbsp; Căn cứ thông báo thanh toán phí dịch vụ Tháng 06/2017 đã được gửi đến chủ căn hộ; <br>
                        -   &nbsp; &nbsp; &nbsp; &nbsp; Basing oneself on the monthly fee announcement of June, 2017, sent to the apartment owner; <br>

                </div>       
            </div>
        </div> -->

        <div class="row voffset1">
                <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 10px">
                    <u><i>Kính gửi / Respectfully to: </i></u>
                </div>
                <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 50px"><b>Ông/ Bà / Mr/ Ms:            &nbsp; </b>{!! $flat_info->name !!}</div>
                <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 50px"><b>Căn hộ / Apartment No.:   &nbsp;</b> {!! $flat_info->address !!} &nbsp; Chung cư / Apartment Building: Saigonres Plaza </div>
                <div class="col-xs-11"></div>    

                <div class="col-xs-11" style="text-align: left;font-size: 14px;margin-left: 10px">
                    <br>
                        -   &nbsp; &nbsp; &nbsp; &nbsp; Căn cứ nội quy quản lý Chung cư Saigonres Plaza; <br>
                        -   &nbsp; &nbsp; &nbsp; &nbsp; Basing oneself on the Regulations of  Saigonres Plaza Apartment Building; <br>
                        -   &nbsp; &nbsp; &nbsp; &nbsp; Căn cứ thông báo thanh toán phí dịch vụ Tháng 06/2017 đã được gửi đến chủ căn hộ; <br>
                        -   &nbsp; &nbsp; &nbsp; &nbsp; Basing oneself on the monthly fee announcement of June, 2017, sent to the apartment owner; <br>

                </div>                           
<!--             <div class="col-xs-11">
                <div class="col-xs-12 text-left" style="font-size: 12px">
                    Trân trọng kính mời Quý Ông/ Bà vui lòng thanh toán tại <b>Văn Phòng BQL</b> hoặc chuyển khoản theo thông tin: <i>Payment can be made by cash at Management Office or bank transfer throught following account:</i><br>
                    - &nbsp;&nbsp;&nbsp;&nbsp;Số TK/Beneficiary account number:&nbsp;<b>{!! $tenement_info->account !!}</b><br>
                    - &nbsp;&nbsp;&nbsp;&nbsp;Ngân hàng/Bank:&nbsp; <b>{!! $tenement_info->bank !!}</b><br>
                    - &nbsp;&nbsp;&nbsp;&nbsp;Chi nhánh:<b>{!! $tenement_info->branch !!}</b><br>
                    - &nbsp;&nbsp;&nbsp;&nbsp;Tên chủ tài khoản/Beneficiary name:&nbsp;<b>{!! $tenement_info->account_name !!}</b><br>
                </div>
            </div> -->

            <div class="col-xs-11">
                <div class="col-xs-12 text-left" style="font-size: 14px">
                    <br>Kính thưa Quý Cư dân/Dear Value Residents, <br><br>

                    Theo quy định việc thanh toán các khoản chi phí dịch vụ của căn hộ sẽ được Quý cư dân thực hiện trước <b>ngày 15 dương lịch</b> hàng tháng, tuy nhiên đến nay Ban Quản Lý vẫn chưa nhận được thanh toán từ Quý cư dân cho các dịch vụ đã sử dụng trong Tháng 06/2017 với tổng số tiền là: <b>793.494 vnd</b> 
                    
                    <br>
                    According to the present regulations, the payment for service charges should be made before the <b>15th</b> date monthly. However, up to this time, we have not received your payment for the services used in June, 2017 yet with the amount: <b>VND 793.494</b> <br><br>

                    Việc chậm thanh toán phí hàng tháng của Quý Cư dân sẽ gây khó khăn cho công tác quản lý vận hành tòa nhà nói chung và ảnh hưởng đến sinh hoạt chung của cư dân tòa nhà. Vì vậy chúng tôi rất mong nhận được sự hợp tác và hỗ trợ của Quý cư dân trong việc thanh toán phí đúng thời hạn giúp công tác quản lý toà nhà được tiến hành thuận lợi và việc cung cấp các dịch vụ cho Quý cư dân được duy trì ổn định. 
                    <br>
                    Your late payment has caused some difficulties to the tasks of property management in general and has some adverse effects on the activities of the apartment building. Therefore, we hope to receive your co-operation and support in payment on schedule in order to create favorable conditions for the tasks of property management and stabilize the quality of the management service.  <br><br>

                    <b>Khi nhận được thông báo này mong Quý Cư dân vui lòng thanh toán tại: <br>
                    When receiving this reminder of invoice outstanding, please liquidate your payables in cash at: </b><br>
                    -   Khu vực sảnh lễ tân Lô A,B / Reception Area Block A,B <br>
                    -   Điện thọai / Telephone No.: (028) 3620 7881-82-85 <br><br>

                    <b>Hoặc chuyển khoản qua ngân hàng/Or please make your payment by transfer to: </b><br>
                    -   Số TK/Beneficiary account name: 1687 0407 0119 999 <br>
                    -   Ngân hàng/Bank: Ngân hàng HD Bank   Chi nhánh/Branch: Nguyễn Đình Chiểu <br>
                    -   Tên chủ tài khoản/Beneficiary name: Công ty Cổ Phần Đầu tư Bất Động Sản Hùng Vương <br><br>

                    BQL tòa nhà xin chân thành cảm ơn sự hợp tác và hỗ trợ của Quý cư dân. <br>
                    We would be much obliged for your kind support. <br><br>
                         
                    Tp.HCM, ngày 23 tháng 06 năm 2017/HCMC, June, 23rd2017 <br>
                    BAN QUẢN LÝ TÒA NHÀ/On behalf of Management Board <br>
                </div>
            </div>
        </div>
    @endforeach
    <div id="bbb">
        {!! HTML::image('img/logo.png', 'alt', array('width' => 700, 'opacity' => 0.2)) !!}
    </div> 
    </page>
@endsection

