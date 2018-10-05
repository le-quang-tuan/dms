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
        
        $("#tenementEquipmentSubmit").click(function(){
            bootbox.confirm("Thông tin Máy Móc - Thiết Bị - Vật Tư sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmtenementEquipment").submit();
                }
            });
        }); 

        $("#tenementEquipmentDelete").click(function(){
            bootbox.confirm("Thông tin Máy Móc - Thiết Bị - Vật Tư sẽ được xóa?", function(result) {
                if(result){
                    $("#frmCom").submit();
                }
            });
        });

        $("#tenementEquipmentRefresh").manualRefresh('frmtenementEquipment');

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
        <h2>Cập nhật thông tin Máy Móc - Thiết Bị - Vật Tư</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementEquipment-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('tenementEquipment-alert-' . $msg) }}<br>
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
        <form id='frmtenementEquipment' action="{!! route('Equipment.update') !!}" method="POST" role="form">
            <table class="table table-striped table-bordered table-hover table-condensed">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
                <input id="id" name='id' type="hidden" value="{!! $tenementEquipment->id !!}">
                <tbody>
                    <tr>
                        <td>Nhóm<em style='color:red'>(*)</em> </td>
                        <td colspan="3">
                            <?php
                                $equipment_group_id = $tenementEquipment->equipment_group_id;

                                echo '<select name="equipment_group_id" id="equipment_group_id" style="width: 150px;">';
                                foreach($tenement_equipment_groups as $tenement_equipment_group) {
                                    if ($tenement_equipment_group->id == $equipment_group_id){
                                        echo '<option selected ="selected" value="'. $tenement_equipment_group->id . '">';
                                        echo $tenement_equipment_group->name;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'. $tenement_equipment_group->id . ' ">';
                                        echo $tenement_equipment_group->name;
                                        echo '</option>';
                                    }
                                }
                            echo '</select>';
                            ?>

                        </td>
                    </tr>
                    <tr>
                        <td>Tên</td>
                        <td><input type="text" value="{!! $tenementEquipment->name !!}" id="name" name="name" size="30%"></td>

                        <td>Nhà cung cấp</td>
                        <td>
                            <?php
                                $producer_id = $tenementEquipment->producer_id;

                                echo '<select name="producer_id" id="producer_id" style="width: 150px;">';
                                foreach($tenement_producers as $tenement_producer) {
                                if ($tenement_producer->id == $producer_id){
                                    echo '<option selected ="selected" value="'. $tenement_producer->id . '">';
                                    echo $tenement_producer->name;
                                    echo '</option>';
                                }
                                else
                                {
                                    echo '<option value="'. $tenement_producer->id . ' ">';
                                    echo $tenement_producer->name;
                                    echo '</option>';
                                }
                            }
                            echo '</select>';
                            ?>
                        </td>

                        <td>Nhãn hiệu</td>
                        <td><input type="text" value="{!! $tenementEquipment->label !!}" id="label" name="label" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Model</td>
                        <td><input type="text" value="{!! $tenementEquipment->model !!}" id="model" name="model" size="30%"></td>

                        <td>Thông số kỹ thuật</td>
                        <td><input type="text" value="{!! $tenementEquipment->specification !!}" id="specification" name="specification" size="30%"></td>

                        <td>Khu vực sử dụng</td>
                        <td><input type="area" value="{!! $tenementEquipment->area !!}" id="area" name="area" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Ghi chú<em style='color:red'>(*)</em></td>
                        <td colspan="5"><input type="text" value="{!! $tenementEquipment->comment !!}" id="comment" name="comment" size="50%"></td>
                    <tr>
                </tbody>
            </table>
        </form>
        <div>
            <br>
            <button id='tenementEquipmentSubmit' type="button" class="btn btn-primary">Lưu</button>            
            <button id='tenementEquipmentDelete' type="button" class="btn btn-danger">Xóa</button> &nbsp;
            <a href="{!! route('Equipment') !!} " class="btn btn-info">Trở về màn hình trước</a>
            <form id='frmCom' action="{!! route('Equipment.destroy') !!}" method="POST" role="form" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input id="id" name='id' type="hidden" value="{!! $tenementEquipment->id !!}">                        
            </form>
        </div>
    </div>
</div>
@endsection

