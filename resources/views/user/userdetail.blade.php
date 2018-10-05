@extends('include.layout')

@section('style')
<style>    
.cImgPassport{
    max-width: 100%;
}

</style>
@endsection

@section('script')

{!! Html::script('js/manual_js/manual_click.js') !!}

<script language='javascript' type='text/javascript'>
    function check(input) {
        if (input.value != document.getElementById('newPassword').value) {
            input.setCustomValidity('New Password and Verify Password do not match.');
        } else {
            // input is valid -- reset the error message
            input.setCustomValidity('');
        }
    }

</script>

@endsection

<?php
    $arrInfo1 = array();    
?>

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thông tin Chung Cư/Khu Căn Hộ và Thiết lập biểu phí</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('user-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('user-alert-' . $msg) }} &nbsp; <div style="display: none;" > <b id="show-time">2</b> </div>
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
            
    <form id='changePasswordForm' action="{!! route('UserDetail.changepassword') !!}" method="POST" role="form" method="post">
        <table class="table table-striped table-bordered table-hover table-condensed">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input id="userId" name='userId' type="hidden" value="{{$userDetailInfo->id}}">
            <tbody>
                <tr>
                    <td class="col-md-3">Tên đăng nhập</td>
                    <td class="col-md-9"><input id="username" name='username' required="required" value="{!! ( isset($userDetailInfo->username) ? $userDetailInfo->username : '') !!}" disabled="disabled"></td>
                </tr>
                 <tr>
                    <td class="col-md-3">Mật Khẩu Hiện Tại</td>
                    <td class="col-md-9"><input id="currentPassword" required="required" name='currentPassword' type='password'></td>
                </tr>
                <tr>
                    <td class="col-md-3">Mật Khẩu Mới</td>
                    <td class="col-md-9"><input id="newPassword" required="required" name='newPassword' type='password'></td>
                </tr>
                <tr>
                    <td class="col-md-3">Xác Nhận Mật Khẩu Mới</td>
                    <td class="col-md-9"><input id="verifyPassword" required="required" name='verifyPassword' type='password' oninput="check(this)" ></td>
                </tr>
                <tr>
                    <td class="col-md-3"></td>
                    <td class="col-md-9"><button type="submit" class="btn btn-primary">Thay Đổi Mật Khẩu</button></td>
                </tr>
            </tbody>
        </table>
    </form> 

    <form id='userDetailForm' action="{!! route('UserDetail.updatedetail') !!}" method="POST" role="form" method="post">
        <table class="table table-striped table-bordered table-hover table-condensed">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input id="userId" name='userId' type="hidden" value="{{$userDetailInfo->id}}">
            <tbody>
                
                <tr>
                    <td class="col-md-3">Họ Tên</td>
                    <td class="col-md-9"> <input id="first_name" name='first_name' required="required" value="{!! ( isset($userDetailInfo->first_name) ? $userDetailInfo->first_name : '') !!}"></td>
                </tr>
                <tr>
                    <td class="col-md-3">Mail</td>
                    <td class="col-md-9"> <input id="email" type="email" name='email' required="required" autocomplete="off" value="{!! ( isset($userDetailInfo->email) ? $userDetailInfo->email : '') !!}"></td>
                </tr>
                <tr>
                    <td class="col-md-3">Quyền</td>
                    <td class="col-md-9">  
                    	<?php
                            echo Form::select('authorize', $authorizeList, isset($role[0]) ? $role[0] : 6, ['id' => 'authorize', 'class' => 'form-control editor', 'required' => 'required']);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="col-md-3">Dự Án Tham Gia</td>
                    <td class="col-md-9">  
                    	<?php 
                            echo Form::select('tenement_id', $tenements, isset($userDetailInfo->tenement_id) ? $userDetailInfo->tenement_id : 1, ['id' => 'tenement_id', 'class' => 'form-control editor', 'required' => 'required']);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="col-md-3"></td>
                    <td class="col-md-9">  
                        <button id='userSubmit' type="submit" class="btn btn-primary">Cập nhật</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>  
@endsection

