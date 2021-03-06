@extends('include.layout')

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
        $("#tenementProducerSubmit").manualSubmit('frmTenementProducer');
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
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Tạo mới Nhà cung cấp - Sản xuất</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementParking-alert-' . $msg))
        <p class="alert alert-{!! $msg !!}">
            {!! Session::get('tenementParking-alert-' . $msg) !!}<br>
            The message will dissable with in <b id="show-time">5</b> seconds                            
        </p>
        @endif
        @endforeach
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

        <form id='frmTenementProducer' action="{!! route('Producer.store') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td>Nhà cung cấp<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! old('name') !!}" id="name" name="name" size="30%"></td>

                        <td>Địa chỉ</td>
                        <td colspan="3"><input type="text" value="{!! old('address') !!}" id="address" name="address" size="80%"></td>
                    </tr>
                    <tr>
                        <td>Liên hệ</td>
                        <td><input type="text" value="{!! old('contact_name') !!}" id="contact_name" name="contact_name" size="30%"></td>

                        <td>Tel</td>
                        <td><input type="text" value="{!! old('tel') !!}" id="tel" name="tel" size="30%"></td>

                        <td>Email</td>
                        <td><input type="text" value="{!! old('email') !!}" id="email" name="email" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Ghi chú</td>
                        <td colspan="5"><input type="text" value="{!! old('comment') !!}" id="comment" name="comment" size="90%"></td>
                    </tr>
                </tbody>
            </table>
        </form>
        <div>
            </br>
            <button id='tenementProducerSubmit' type="button" class="btn btn-primary">Thêm mới</button>    
            <a href="{!! route('Producer') !!} " class="btn btn-info">Trở về màn hình trước</a>

        </div>
    </div>
</div>
@endsection