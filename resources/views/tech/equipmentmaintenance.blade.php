@extends('include.layout')

@section('style')
    {!! Html::style('css/datepicker/jquery-ui.css') !!}

    {!! Html::style('css/tageditor/jquery.tag-editor.css') !!}

    {!! Html::style('css/timepicker/jquery.timepicker.min.css') !!}

    {!! Html::style('css/multiselect/bootstrap-multiselect.css') !!}

    {!! Html::style('css/colorpicker/spectrum.css') !!}
<style>    
.cImgPassport{
    max-width: 100%;
}

</style>

<style>
    .btnRemoveRow {
        cursor: pointer;
        display: inline-block;
        height: 30px;
        position: relative;
        width: 28px;
    }

    .editor {
        width: 220px !important;
    }

    .label-date-field {
        float: left;
        width: 130px;
        margin-left: 15px;
    }

    .image-editor {
        height: 180px;
        width: 150px;
    }

    .last-change p {
        color: rgba(255, 0, 102, 1);
        font-style: italic;
    }

    em {
        color: rgba(255, 0, 102, 1);
    }

    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }

    .checkbox {
        margin-top: 0px;
    }

    .tr-ct-35 {
        height: 35px;
    }

    .parent {
        position: relative;
    }

    .child {
        position: absolute; 
        top: 30%; 
        transform: translateY(-30%);
    }

    .sp-preview {
        width: 120px;
    }

    .sp-replacer {
        width: 150px;
    }

    .sp-dd {
        float: right;
    }

    .sp-price {
        text-align: right;
    }

    /* IE 6 doesn't support max-height
    * we use height instead, but this forces the menu to always be this tall
    */
    * html .ui-autocomplete {
        height: 300px;
    }

    .room_no {
        width: 70px !important;
        float:left;
    }
    label {
        clear:both;
    }

    .nopadding {
       padding: 0 !important;
       margin: 0 !important;
    }
</style>
@endsection

@section('script')
    {!! Html::script('js/ckeditor/ckeditor.js') !!}

    {!! Html::script('js/manual_js/manual_click.js') !!}

    {!! Html::script('js/colorpicker/spectrum.js') !!}

    {!! Html::script('js/datepicker/jquery-ui.js')  !!}

    {!! Html::script('js/tageditor/jquery.tag-editor.js')  !!}

    {!! Html::script('js/tageditor/jquery.caret.min.js')  !!}

    {!! Html::script('js/timepicker/jquery.timepicker.min.js')  !!}

    {!! Html::script('js/multiselect/bootstrap-multiselect.js')  !!}

<script>
    // Replace the <textarea id="address"> with a CKEditor
    // instance, using default configuration.
    // auto dissable success message.
    var settimmer = 0;
    $(function(){        
        var i = 0;
        var button_my_button = "#addmore";
        $(button_my_button).click(function(){
            count=$('#addmore1 tr').length-1;
            //alert(count);

            var data="<tr class='new'><td><input type='checkbox' class='case'/></td>";
            data+="<td><span id='snum"+i+"'>"+count+".</span><input type='hidden' value='0' name='counter[]'></td>";

            data+="<td><input style='width: 100px;' name='plan_date"+count+"' id='plan_date_"+count+"' class='date-picker' value='{!! date('d/m/Y') !!}'></td>";

            data+="<td><input style='width: 100px;' name='plan_start_time"+count+"' id='plan_start_time_"+count+"' class='time-picker'></td>";

            data+="<td><input style='width: 100px;' name='plan_end_time"+count+"' id='plan_end_time_"+count+"' class='time-picker'></td>";

            data+="<td><input style='width: 200px;' name='plan_company_execute"+count+"' id='plan_company_execute_"+count+"' ></td>";

            data+="<td><input style='width: 200px;' name='plan_for"+count+"' id='plan_for_"+count+"' ></td>";

            data+="<td><input style='width: 200px;' name='plan_description"+count+"' id='plan_description_"+count+"' ></td>";
            $(data).insertBefore("#servicetotal");        
            i++;

            var dateFormat = "dd/mm/yy";
            $("#plan_date_" + count).datepicker({
                dateFormat: dateFormat,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: false
            });

            $('.time-picker').timepicker({
                timeFormat: 'HH:mm:ss'
            });
        });  

        $('.time-picker').timepicker({
            timeFormat: 'HH:mm:ss'
        });

        $(".delete").on('click', function() {
            $('.case:checkbox:checked').each(function(){
                if($.isNumeric($(this).val())){
                    $(this).parents("tr").hide();
                } else {
                    $(this).parents("tr").remove();
                }
            });
            
            $('.check_all').prop("checked", false); 
            //addDangerClass();
        });

        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        
        
        $("#equipMainteSubmit").click(function(){
            bootbox.confirm("Thông tin Kế Hoạch sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmEquipMainteSubmit").submit();
                }
            });
        });

        var counter = "{{ Session::get('counter') }}";
        if (counter != ""){
            for (var i=1; i <= counter; i++) {
                var plan_date = '#plan_date_' + i;
                var dateFormat = "dd/mm/yy";
                $(plan_date).datepicker({
                    dateFormat: dateFormat,
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: false
                });

                $('.time-picker').timepicker({
                    timeFormat: 'HH:mm:ss'
                });
            }
        }
    });
    
</script>

@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thiết lập kế hoạch bảo trì Thiết Bị</h2>
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
        <form id='frmtenementEquipment' action="{!! route('EquipMainte.store') !!}" method="POST" role="form" method="post">
            <table class="table table-striped table-bordered table-hover table-condensed">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
                <input id="id" name='id' type="hidden" value="{!! $tenementEquipment->id !!}">
                <tbody>
                    <tr>
                        <td>Nhóm<em style='color:red'>(*)</em> </td>
                        <td colspan="4">
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
        <div class="col-lg-12">
            <form id='frmEquipMainteSubmit' action="{!! route('EquipMainte.store') !!}" method="POST" role="form" method="post">
                <input id="id" name='id' type="hidden" value="{!! $tenementEquipment->id !!}">
                {!! csrf_field() !!}
                <h3>Kế Hoạch Bảo Trì</h3>
                <table class="table table-bordered hover" id="addmore1">
                    <tbody>
                        <tr>
                            <th class="info"><input type="checkbox" onclick="select_all()" class="check_all"></th>
                            <th class="info">No.</th>
                            <th class="info">Kế Hoạch ngày</th>
                            <th class="info">Thời Gian từ</th>
                            <th class="info">Thời Gian đến</th>
                            <th class="info">Thực hiện bởi</th>
                            <th class="info">Người Phụ Trách</th>
                            <th class="info">Hạng mục</th>
                        </tr>
                        <?php 
                            $step = Session::get('counter');
                        ?>
                        
                        @for($i = 1; $i <=  $step ; $i++)
                        <tr class='new'>
                            <td class='text-center'><input type='checkbox' class='case'/></td>
                            <td class='text-center'><span id='snum{{$i}}'>{{ $i }}.</span><input type='hidden' value='0' name='counter[]'></td>
                            <td><input name='plan_date{{$i}}' id='plan_date_{{$i}}' value="{{ old('plan_date' . $i) }}" class='form-control service'></td>
                            <td><input name='plan_start_time{{$i}}' id='plan_start_time_{{$i}}' value="{{ old('plan_start_time' . $i) }}" class='form-control service time-picker'></td>
                            <td><input name='plan_end_time{{$i}}' id='plan_end_time_{{$i}}' value="{{ old('plan_end_time' . $i) }}" class='form-control service time-picker'></td>
                            <td><input name='plan_company_execute{{$i}}' id='plan_company_execute_{{$i}}' value="{{ old('plan_company_execute' . $i) }}" class='form-control service'></td>
                            <td><input name='plan_for{{$i}}' id='plan_for_{{$i}}'  value="{{ old('plan_for' . $i) }}" class='form-control service'></td>
                            <td><input name='plan_description{{$i}}' id='plan_description_{{$i}}' value="{{ old('plan_description' . $i) }}" class='form-control service'></td>
                        </tr>
                        @endfor

                        <tr id="servicetotal">
                        </tr>
                    </tbody>
                </table>
                <table class="table">
                    <button class="btn btn-danger delete" type="button">- Xóa Kế Hoạch Đã Chọn</button>&nbsp;
                    <button class="btn btn-success addmore" type="button" id='addmore'>+ Thêm Kế Hoạch</button>
                </table>
            </form>
        </div>

        <div>
            <button id='equipMainteSubmit' type="button" class="btn btn-primary">Lưu Kế Hoạch</button>
            <a href="{!! route('Equipment') !!} " class="btn btn-info">Trở về màn hình trước</a>
        </div>
    </div>
</div>
@endsection

