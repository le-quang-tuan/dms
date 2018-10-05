@extends('include.layout')

@section('style')
<style>    
.cImgPassport{
    max-width: 50%;
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
        //CKEDITOR.replace( 'comment' );    

        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        
        @role('admin|manager|moderator')
            $("#tenementParkingSubmit").click(function(){
                bootbox.confirm("Thông tin Biểu Phí sẽ được cập nhật?", function(result) {
                    if(result){
                        $("#frmtenementParking").submit();
                    }
                });
            }); 

            $("#tenementParkingDelete").click(function(){
                bootbox.confirm("Thông tin Biểu Phí sẽ được xóa?", function(result) {
                    if(result){
                        $("#frmCom").submit();
                    }
                });
            });
        @endrole
        $("#tenementParkingRefresh").manualRefresh('frmtenementParking');

        $("#price").ForceNumericOnly("##,###,###,###", "-");
        
    });
    
</script>

@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Tạo mới biểu phí xe tháng</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementParking-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('tenementParking-alert-' . $msg) }}<br>
            The message will dissable with in <b id="show-time">2</b> seconds                            
        </p>
        @endif
        @endforeach
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

        <form id='frmtenementParking' action="{!! route('TenementParking.update') !!}" method="POST" role="form" method="post">
            <table class="table table-striped table-bordered table-hover table-condensed">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
                <input id="id" name='id' type="hidden" value="{!! $tenementParking->id !!}">
                <tbody>
                    <tr>
                        <td width="110px">Loại xe<em style='color:red'>(*)</em> </td>
                        <td width="110px"><input type="text" value="{!! $tenementParking->name !!}" id="name" name="name" size="30%"></td>

                        <td>Tiền tháng<em style='color:red'>(*)</em> </td>
                        <td width="110px"><input type="text" value="{!! $tenementParking->price !!}" id="price" name="price" size="10%"></td>

                        <td>Ghi chú<em style='color:red'>(*)</em></td>
                        <td width="110px"><input type="text" value="{!! $tenementParking->comment !!}" id="comment" name="comment" size="50%"></td>
                    <tr>
                </tbody>
            </table>
        </form>
        @role('admin|manager|moderator')
            <div>
                </br>
                <button id='tenementParkingSubmit' type="button" class="btn btn-primary">Lưu</button>            
                <button id='tenementParkingDelete' type="button" class="btn btn-danger">Xóa</button> &nbsp;
                <a href="{!! route('TenementParking') !!} " class="btn btn-info">Trở về màn hình trước</a>
                <form id='frmCom' action="{!! route('TenementParking.destroy') !!}" method="POST" role="form" method="post">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input id="id" name='id' type="hidden" value="{!! $tenementParking->id !!}">                        
                </form>
            </div>
        @endrole
    </div>
</div>
@endsection

