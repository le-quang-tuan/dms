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
        
        $("#tenementProducerSubmit").click(function(){
            bootbox.confirm("Thông tin Nhà Cung Cấp sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmtenementProducer").submit();
                }
            });
        }); 

        $("#tenementProducerDelete").click(function(){
            bootbox.confirm("Thông tin Nhà Cung Cấp sẽ được xóa?", function(result) {
                if(result){
                    $("#frmCom").submit();
                }
            });
        });

        $("#tenementProducerRefresh").manualRefresh('frmtenementProducer');

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
        <h2>Cập nhật thông tin nhà Cung cấp - Sản xuất</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementProducer-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('tenementProducer-alert-' . $msg) }}<br>
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
    </div>
    <div>
        <form id='frmtenementProducer' action="{!! route('Producer.update') !!}" method="POST" role="form" method="post">
            <table class="table table-striped table-bordered table-hover table-condensed">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
                <input id="id" name='id' type="hidden" value="{!! $tenementProducer->id !!}">
                <tbody>
                    <tr>
                        <td>Nhà cung cấp<em style='color:red'>(*)</em> </td>
                        <td><input type="text" value="{!! $tenementProducer->name !!}" id="name" name="name" size="30%"></td>

                        <td>Địa chỉ</td>
                        <td colspan="4"><input type="text" value="{!! $tenementProducer->address !!}" id="name" name="address" size="80%"></td>
                    </tr>
                    <tr>
                        <td>Liên hệ</td>
                        <td><input type="text" value="{!! $tenementProducer->contact_name !!}" id="contact_name" name="contact_name" size="30%"></td>
                        
                        <td>Tel</td>
                        <td><input type="text" value="{!! $tenementProducer->tel !!}" id="tel" name="tel" size="30%"></td>

                        <td>Email</td>
                        <td><input type="text" value="{!! $tenementProducer->email !!}" id="email" name="email" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Ghi chú<em style='color:red'>(*)</em></td>
                        <td colspan="5"><input type="text" value="{!! $tenementProducer->comment !!}" id="comment" name="comment" size="50%"></td>
                    <tr>
                </tbody>
            </table>
        </form>
        <div>
            <br>
            <button id='tenementProducerSubmit' type="button" class="btn btn-primary">Lưu</button>            
            <button id='tenementProducerDelete' type="button" class="btn btn-danger">Xóa</button> &nbsp;
            <a href="{!! route('Producer') !!} " class="btn btn-info">Trở về màn hình trước</a>
            <form id='frmCom' action="{!! route('Producer.destroy') !!}" method="POST" role="form" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input id="id" name='id' type="hidden" value="{!! $tenementProducer->id !!}">                        
            </form>
        </div>
    </div>
</div>
@endsection

