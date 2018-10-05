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
        $("#tenementEquipmentSubmit").manualSubmit('frmTenementEquipment');
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
        <h2>Tạo mới Máy Móc - Thiết Bị - Vật Tư</h2>
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

        <form id='frmTenementEquipment' action="{!! route('Equipment.store') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td>Nhóm<em style='color:red'>(*)</em></td>
                        <td>
                            <?php 
                                $oldGroup_id = old('equipment_group_id');
                                echo '<select name="equipment_group_id" id="equipment_group_id" style="width: 150px;">';
                                    foreach($tenement_equipment_groups as $tenement_equipment_group) {
                                        $selected = ($oldGroup_id == $tenement_equipment_group->id ? 'selected' : '');
                                        echo '<option value="' .$tenement_equipment_group->id . '" '. $selected .'>';
                                        echo $tenement_equipment_group->name;
                                        echo '</option>';
                                    }
                                echo '</select>';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tên<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! old('name') !!}" id="name" name="name" size="30%"></td>

                        <td>Nhà cung cấp<em style='color:red'>(*)</em></td>
                        <td>
                            <?php 
                                $oldProducer = old('producer_id');
                                echo '<select name="producer_id" id="producer_id" style="width: 150px;">';
                                    foreach($tenement_producers as $tenement_producer) {
                                        $selected = ($oldProducer == $tenement_producer->id ? 'selected' : '');
                                        echo '<option value="' .$tenement_producer->id . '" '. $selected .'>';
                                        echo $tenement_producer->name;
                                        echo '</option>';
                                    }
                                echo '</select>';
                            ?>
                        </td>

                        <td>Nhãn hiệu</td>
                        <td colspan="3"><input type="text" value="{!! old('label') !!}" id="label" name="label" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Model</td>
                        <td><input type="text" value="{!! old('model') !!}" id="model" name="model" size="30%"></td>

                        <td>Thông số kỹ thuật</td>
                        <td><input type="text" value="{!! old('specification') !!}" id="specification" name="specification" size="30%"></td>

                        <td>Khu vực sử dụng</td>
                        <td><input type="text" value="{!! old('area') !!}" id="area" name="area" size="30%"></td>
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
            <button id='tenementEquipmentSubmit' type="button" class="btn btn-primary">Thêm mới</button>    
            <a href="{!! route('Equipment') !!} " class="btn btn-info">Trở về màn hình trước</a>

        </div>
    </div>
</div>
@endsection