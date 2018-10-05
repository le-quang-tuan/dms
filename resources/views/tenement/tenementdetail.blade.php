@extends('include.layout')

@section('style')
<style>    
.cImgPassport{
    max-width: 40%;
}

</style>
@endsection

@section('script')
{!! Html::script('js/ckeditor/ckeditor.js') !!}

{!! Html::script('js/manual_js/manual_click.js') !!}
<script>
    $(function(){        
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);
        @role('admin|manager|moderator')
            $("#tenementSubmit").click(function(){
                bootbox.confirm("Thông tin Dự Án sẽ được cập nhật?", function(result) {
                    if(result){
                        $("#frmtenement").submit();
                    }
                });
            }); 

            $("#tenementDelete").click(function(){
                bootbox.confirm("Thông tin Dự Án sẽ được xóa?", function(result) {
                    if(result){
                        $("#frmCom").submit();
                    }
                });
            });
        @endrole

        $("#manager_fee").ForceNumericOnly("##,###,###,###", "-"); 
        $("#loss_avg").ForceNumericOnly("##,###,###,###", "-"); 
        $("#loss_avg_elec").ForceNumericOnly("##,###,###,###", "-"); 
        $("#loss_avg_gas").ForceNumericOnly("##,###,###,###", "-"); 
    });
    
</script>

@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thông Tin Khu Căn Hộ</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenement-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('tenement-alert-' . $msg) }} &nbsp; <div style="display: none;" > <b id="show-time">2</b> </div>
        </p>
        @endif
        @endforeach
    </div> 
    <!-- end .flash-message -->
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id='frmtenement' action="{!! route('TenementDetail.update') !!}" method="POST" role="form" method="post">
        <table class="table table-striped table-bordered table-hover table-condensed">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
            <input id="tenement_id" name='tenement_id' type="hidden" value="{!! $tenement->id !!}">
            <tbody>
                <tr>
                    <td class="col-md-4">Tên Dự Án<em style='color:red'>(*)</em> </td>
                    <td class="col-md-2"><input type="text" value="{!! $tenement->name !!}" id="name" name="name" size="40%"></td>

                    <td class="col-md-4">Mã Dự Án<em style='color:red'>(*)</em> </td>
                    <td class="col-md-2"><input type="text" value="{!! $tenement->tenement_code !!}" id="tenement_code" name="tenement_code" size="40%"></td>

                </tr>
                <tr>
                    <td>Địa chỉ<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenement->address !!}" id="address" name="address" size="40%"></td>

                    <td>Phí quản lý căn hộ<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenement->manager_fee !!}" id="manager_fee" name="manager_fee" size="40%"></td>
                </tr>
                <tr>
                    <td>Hao hụt chia sẻ(Nước)<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenement->loss_avg !!}" id="loss_avg" name="loss_avg" size="40%"></td>
                    <td> Hao hụt chia sẻ(Điện)<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenement->loss_avg_elec !!}" id="loss_avg_elec" name="loss_avg_elec" size="40%"></td>
                </tr>
                <tr>
                    <td> Hao hụt chia sẻ(Gas)<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenement->loss_avg_gas !!}" id="loss_avg_gas" name="loss_avg_gas" size="40%"></td>
                    <td> Đơn vị Gas<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenement->gas_unit !!}" id="gas_unit" name="gas_unit" size="40%"></td>
                </tr>
                <tr>
                    <td> Cách tính Phí gửi xe<em style='color:red'>(*)</em></td>
                    <td>
                        <input type="radio" <?php echo ($tenement->parkingfee_calculate_type == 0)?'checked':'' ?> id="txt_parkingfee_calculate_type0" value="0" name="parkingfee_calculate_type"> <label for="txt_parkingfee_calculate_type0">Theo tháng/Nửa tháng</label>
                        <br>

                        <input type="radio" <?php echo ($tenement->parkingfee_calculate_type == 1)?'checked':'' ?> id="txt_parkingfee_calculate_type1" value="1" name="parkingfee_calculate_type"> <label for="txt_parkingfee_calculate_type1">Theo ngày xác định trong tháng</label>                              
                    </td>
                    <td> Cách tính Phí quản lý<em style='color:red'>(*)</em></td>
                    <td>
                        <input type="radio" <?php echo ($tenement->managerfee_calculate_type == 0)?'checked':'' ?> id="txt_managerfee_calculate_type0" value="0" name="managerfee_calculate_type"> <label for="txt_managerfee_calculate_type0">Theo lịch</label> 
                        <br>
                        <input type="radio" <?php echo ($tenement->managerfee_calculate_type == 1)?'checked':'' ?> id="txt_managerfee_calculate_type1" value="1" name="managerfee_calculate_type"> <label for="txt_managerfee_calculate_type1">Theo số ngày</label>                              
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                    <p class="pagetitle">Thông Tin Phiếu Thông Báo Phí Tháng</p>
                    </td>
                </tr>
                <tr>
                    <td>Tên ngân hàng</td>
                    <td><input type="text" value="{!! $tenement->bank !!}" id="bank" name="bank" size="40%"></td>
                    <td>Tài khoản/ Chủ tài khoản</td>
                    <td><input type="text" value="{!! $tenement->account !!}" id="account" name="account" size="15%">
                    &nbsp;<input type="text" value="{!! $tenement->account_name !!}" id="account_name" name="account_name" size="15%"></td>
                </tr>
                <tr>
                    <td>Chi nhánh</td>
                    <td colspan="3"><input type="text" value="{!! $tenement->branch !!}" id="branch" name="branch" size="40%"></td>
                </tr>
                <tr>
                    <td>Liên Hệ</td>
                    <td>
                        <textarea id="contact" name="contact" rows="2" cols="38%">{!! $tenement->contact !!}</textarea>
                    </td>
                    <td>Trưởng Ban</td>
                    <td>
                        <textarea id="managerment" name="managerment" rows="2" cols="38%">{!! $tenement->managerment !!}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>Ghi chú</td>
                    <td colspan="3">
                        <textarea id="comment" name="comment" rows="2" cols="100%">{!! $tenement->comment !!}</textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                    <p class="pagetitle">Thông Tin Phiếu Thu</p>
                    </td>
                </tr>
                <tr>
                    <td>Công ty quản lý</td>
                    <td colspan="3"><input type="text" value="{!! $tenement->manager_company !!}" id="manager_company" name="manager_company" size="90%"></td>
                </tr>
                <tr>
                    <td>Liên 1</td>
                    <td>
                        <input type="text" id="caption1" name="caption1" size="40%" value="{!! $tenement->caption1 !!}">
                    </td>
                    <td>Liên 2</td>
                    <td>
                        <input type="text" id="caption2" name="caption2" size="40%" value="{!! $tenement->caption2 !!}">
                    </td>
                </tr>
                <tr>
                    <td>Liên 3</td>
                    <td colspan="3">
                        <input type="text" id="caption3" name="caption3" size="40%"  value="{!! $tenement->caption3 !!}">
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    @role('admin|manager|moderator')
        <div>
            </br>
            <button id='tenementSubmit' type="button" class="btn btn-primary">Lưu</button>            
            <button id='tenementDelete' type="button" class="btn btn-danger">Xóa</button> &nbsp;
            <a href="{!! route('Tenement') !!} " class="btn btn-info">Trở về màn hình trước</a>
            <form id='frmCom' action="{!! route('TenementDetail.destroy') !!}" method="POST" role="form" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input id="tenement_id" name='tenement_id' type="hidden" value="{!! $tenement->id !!}">                        
            </form>
        </div>
    @endrole
</div>
@endsection

