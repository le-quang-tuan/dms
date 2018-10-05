@extends('includes.layout1')

@section('style')
<style>    
.cImgPassport{
    max-width: 100%;
}

</style>
@endsection

@section('script')
{!! Html::script('js/ckeditor/ckeditor.js') !!}

{!! Html::script('js/manual_js/manual_click.js') !!}
<script>
    // Replace the <textarea id="address"> with a CKEditor
    // instance, using default configuration.
    // auto dissable success message.
    var settimmer = 0;
    $(function(){
        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        
        
        /* The plugin will submit form and scroll to top*/
        $("#tenementSubmit").manualSubmit('frmTenement');

        /* The plugin will submit form and scroll to top*/
        $("#tenementDelete").manualSubmit('frmTen');
                
        $("#tenementRefresh").manualRefresh('frmTenement');
    });
    
</script>

@endsection

@section('content')

<?php 
/*
    =======================================
        CREATE BY HUNGNGUYEN
    =======================================
*/
?>


<div class="container-fluid time-table-no-margin">
    <div class="row">        
        <div class="col-md-12">
        <p class="pagetitle">Tạo mới Dự Án Khu Căn Hộ</p>
            <!-- begin .flash-message -->
            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('tenement-alert-' . $msg))
                <p class="alert alert-{!! $msg !!}">
                    {!! Session::get('tenement-alert-' . $msg) !!}<br>
                    The message will dissable with in <b id="show-time">5</b> seconds                            
                </p>
                @endif
                @endforeach
            </div> 
            <!-- end .flash-message -->
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id='frmTenement' action="{!! route('Tenement.store') !!}" method="POST" role="form" method="post">
                {!! csrf_field() !!}
                <table class="table table-striped table-bordered table-hover table-condensed">
                    <tbody>
                        <tr>
                            <td class="col-md-2">Tên Dự Án<em style='color:red'>(*)</em> </td>
                            <td class="col-md-4"><input type="text" value="{!! old('name') !!}" id="name" name="name" size="50%"></td>

                            <td class="col-md-2">Mã Dự Án<em style='color:red'>(*)</em> </td>
                            <td class="col-md-4"><input type="text" value="{!! old('tenement_code') !!}" id="tenement_code" name="tenement_code" size="10%"> chỉ cấp 1 lần không được thay đổi</td>
                        </tr>
                        <tr>
                            <td>Địa chỉ<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('address') !!}" id="address" name="address" size="50%"></td>

                            <td>Phí quản lý căn hộ<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('manager_fee') !!}" id="manager_fee" name="manager_fee" size="50%"></td>
                        </tr>
                        <tr>
                            <td>Hao hụt chia sẻ(Nước)<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('loss_avg') !!}" id="loss_avg" name="loss_avg" size="50%"></td>
                            <td> Hao hụt chia sẻ(Điện)<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('loss_avg_elec') !!}" id="loss_avg_elec" name="loss_avg_elec" size="50%"></td>
                        </tr>
                        <tr>
                            <td> Hao hụt chia sẻ(Gas)<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('loss_avg_gas') !!}" id="loss_avg_gas" name="loss_avg_gas" size="50%"></td>
                            <td> Đơn vị Gas<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('gas_unit') !!}" id="gas_unit" name="gas_unit" size="50%"></td>
                        </tr>
                        <tr>
                            <td> Cách tính Phí gửi xe<em style='color:red'>(*)</em></td>
                            <td>
                                <input type="radio" id="txt_parkingfee_calculate_type0" value="0" name="parkingfee_calculate_type"> <label for="txt_parkingfee_calculate_type0">Theo tháng/Nửa tháng</label>
                                <br>

                                <input type="radio" id="txt_parkingfee_calculate_type1" value="1" name="parkingfee_calculate_type"> <label for="txt_parkingfee_calculate_type1">Theo ngày xác định trong tháng</label>                              
                            </td>
                            <td> Cách tính Phí quản lý<em style='color:red'>(*)</em></td>
                            <td>
                                <input type="radio" id="txt_managerfee_calculate_type0" value="0" name="managerfee_calculate_type"> <label for="txt_managerfee_calculate_type0">Theo lịch</label> 
                                <br>
                                <input type="radio" id="txt_managerfee_calculate_type1" value="1" name="managerfee_calculate_type"> <label for="txt_managerfee_calculate_type1">Theo số ngày</label>                              
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                            <p class="pagetitle"> Thông Tin Tài Khoản Thu Phí và Báo Cáo</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Tên ngân hàng</td>
                            <td><input type="text" value="{!! old('bank') !!}" id="bank" name="bank" size="50%"></td>
                            <td>Tài khoản/Chủ tài khoản</td>
                            <td><input type="text" value="{!! old('account') !!}" id="account" name="account" size="20%">
                            &nbsp;<input type="text" value="{!! old('account_name') !!}" id="account_name" name="account_name" size="20%"></td>
                        </tr>
                        <tr>
                            <td>Thanh toán tại Văn phòng </td>
                            <td><input type="text" value="{!! old('office') !!}" id="office" name="office" size="50%"></td>

                            <td>Địa chỉ/Điện Thoại</td>
                            <td><input type="text" value="{!! old('office_address') !!}" id="office_address" name="office_address" size="20%">
                            &nbsp;<input type="text" value="{!! old('office_phone') !!}" id="office_phone" name="office_phone" size="20%"></td>
                        </tr>
                        <tr>
                            <td>Thiết lập Phiếu Thu </td>
                            <td>
                                <textarea name="note" id="paid_bill_text" name="paid_bill_text" rows="3" cols="50%">
                                    {!! old('paid_bill_text') !!}
                                </textarea>
                            </td>

                            <td>Ghi chú</td>
                            <td>
                                <textarea name="note" id="comment" name="comment" rows="3" cols="50%">
                                    {!!old('comment') !!}
                                </textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Activation</td>
                            <td colspan="3">
                                <input type="radio" name="activation" id="inputActivation_yes" value="1" checked="checked"> <label for="inputActivation_yes">Yes</label>
                                    &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="activation" id="inputActivation_no" value="0"> <label for="inputActivation_no">No</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>    
        
        <div class="col-md-12 col-md-offset-2">
            </br>
            <button id='tenementSubmit' type="button" class="btn btn-primary">Create</button>            
            <button id='tenementRefresh' type="button" class="btn btn-default">Refresh</button>
            <a href="{!! route('Tenement') !!}">Back To List</a>
        </div>
    </div>
</div>
@endsection