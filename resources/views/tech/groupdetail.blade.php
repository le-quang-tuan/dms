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
        
        $("#tenementEquipmentGroupSubmit").click(function(){
            bootbox.confirm("Thông tin Nhóm Máy Móc - Thiết Bị - Vật Tư sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmtenementEquipmentGroup").submit();
                }
            });
        }); 

        $("#tenementEquipmentGroupDelete").click(function(){
            bootbox.confirm("Thông tin Nhóm Máy Móc - Thiết Bị - Vật Tư sẽ được xóa?", function(result) {
                if(result){
                    $("#frmCom").submit();
                }
            });
        });

        $("#tenementEquipmentGroupRefresh").manualRefresh('frmtenementEquipmentGroup');

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
        <h2>Cập nhật thông tin Nhóm Máy Móc - Thiết Bị - Vật Tư</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementEquipmentGroup-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('tenementEquipmentGroup-alert-' . $msg) }}<br>
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
        <form id='frmtenementEquipmentGroup' action="{!! route('Group.update') !!}" method="POST" role="form" method="post">
            <table class="table table-striped table-bordered table-hover table-condensed">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
                <input id="id" name='id' type="hidden" value="{!! $tenementEquipmentGroup->id !!}">
                <tbody>
                    <tr>
                        <td>Tên Nhóm<em style='color:red'>(*)</em> </td>
                        <td><input type="text" value="{!! $tenementEquipmentGroup->name !!}" id="name" name="name" size="30%"></td>

                        <td>Ghi chú<em style='color:red'>(*)</em></td>
                        <td colspan="5"><input type="text" value="{!! $tenementEquipmentGroup->comment !!}" id="comment" name="comment" size="50%"></td>
                    <tr>
                </tbody>
            </table>
        </form>
        <div>
            <br>
            <button id='tenementEquipmentGroupSubmit' type="button" class="btn btn-primary">Lưu</button>            
            <button id='tenementEquipmentGroupDelete' type="button" class="btn btn-danger">Xóa</button> &nbsp;
            <a href="{!! route('Group') !!} " class="btn btn-info">Trở về màn hình trước</a>
            <form id='frmCom' action="{!! route('Group.destroy') !!}" method="POST" role="form" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input id="id" name='id' type="hidden" value="{!! $tenementEquipmentGroup->id !!}">                        
            </form>
        </div>
    </div>
</div>
@endsection

