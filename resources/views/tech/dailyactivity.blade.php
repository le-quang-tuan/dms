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
            count = $('#addmore1 tr').length-1;
            var data="<tr class='new'><td><input type='checkbox' class='case'/></td>";
            data+="<td><span id='snum"+i+"'>"+count+".</span><input type='hidden' value='0' name='counter[]'></td>";

            data+="<td><input style='width: 150px;' name='name"+count+"' id='name_"+count+"' ></td>";

            data+="<td><input style='width: 80px;' name='daily_date"+count+"' id='daily_date_"+count+"' class='date-picker' value='{!! date('d/m/Y') !!}'></td>";
            data+="<td><input style='width: 80px;' name='start_time"+count+"' id='start_time_"+count+"' class='time-picker'></td>";
            data+="<td><input style='width: 80px;' name='end_time"+count+"' id='end_time_"+count+"' class='time-picker'></td>";

            data+="<td><input style='width: 150px;' name='company_execute"+count+"' id='company_execute_"+count+"' ></td>";

            data+="<td><input style='width: 150px;' name='charge_for"+count+"' id='charge_for_"+count+"' ></td>";

            data+="<td><input style='width: 250px;' name='description"+count+"' id='description_"+count+"' ></td>";

            data+= "<td><select name='category1_id"+count+"' id='category1_id_"+count+"'>";
            var mst_daily_activity_types = <?php echo json_encode($mst_daily_activity_types); ?>;
            for (var i=0; i < mst_daily_activity_types.length; i++) {
                data+= '<option value="' + mst_daily_activity_types[i]['id'] + '">';
                data+= mst_daily_activity_types[i]['name'];
                data+= '</option>';
            }
            data+= '</select></td>';
            data+="<td><input style='width: 300px;' name='category1_note"+count+"' id='category1_note_"+count+"' ></td>";

            data+= "<td><select name='category2_id"+count+"' id='category2_id_"+count+"'>";
            for (var i=0; i < mst_daily_activity_types.length; i++) {
                data+= '<option value="' + mst_daily_activity_types[i]['id'] + '">';
                data+= mst_daily_activity_types[i]['name'];
                data+= '</option>';
            }
            data+= '</select></td>';

            data+="<td><input style='width: 150px;' name='category2_note"+count+"' id='category2_note_"+count+"' ></td>";

            data+= "<td><select name='category3_id"+count+"' id='category3_id_"+count+"'>";
            for (var i=0; i < mst_daily_activity_types.length; i++) {
                data+= '<option value="' + mst_daily_activity_types[i]['id'] + '">';
                data+= mst_daily_activity_types[i]['name'];
                data+= '</option>';
            }
            data+= '</select></td>';
            data+="<td><input style='width: 150px;' name='category3_note"+count+"' id='category3_note_"+count+"' ></td>";

            $(data).insertBefore("#servicetotal");
            i++;

            var dateFormat = "dd/mm/yy";
            $("#daily_date_" + count).datepicker({
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
        
        $("#dailyActivitySubmit").click(function(){
            bootbox.confirm("Thông tin Kế Hoạch sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmDailyActivitySubmit").submit();
                }
            });
        });

        var counter = "{{ Session::get('counter') }}";
        if (counter != ""){
            for (var i=1; i <= counter; i++) {
                var daily_date = '#daily_date_' + i;
                var dateFormat = "dd/mm/yy";
                $(daily_date).datepicker({
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
        <div class="col-lg-12">
            <form id='frmDailyActivitySubmit' action="{!! route('DailyActivity.store') !!}" method="POST" role="form" method="post">
                {!! csrf_field() !!}
                <h3>Nhật ký kỹ thuật</h3>
                <table class="table table-bordered hover" id="addmore1">
                    <tbody>
                        <tr>
                            <th class="info"><input type="checkbox" onclick="select_all()" class="check_all"></th>
                            <th class="info">No.</th>
                            <th class="info">Nhật ký kỹ thuật</th>
                            <th class="info">Ngày</th>
                            <th class="info">Từ</th>
                            <th class="info">Đến</th>
                            <th class="info">Thực hiện bởi</th>
                            <th class="info">Người Phụ Trách</th>
                            <th class="info">Hạng mục</th>
                            <th class="info">Công việc 1</th>
                            <th class="info">Ghi chú</th>
                            <th class="info">Công việc 2</th>
                            <th class="info">Ghi chú</th>
                            <th class="info">Công việc 3</th>
                            <th class="info">Ghi chú</th>
                        </tr>
                        <?php 
                            $step = Session::get('counter');
                        ?>
                        
                        @for($i = 1; $i <=  $step ; $i++)
                        <tr class='new'>


            <td><input type='checkbox' class='case'/></td>";
            <td class='text-center'><span id='snum{{$i}}'>{{ $i }}.</span><input type='hidden' value='0' name='counter[]'></td>

            <td><input style='width: 150px;' name='name{{$i}}' id='name_{{$i}}' value="{{ old('name' . $i) }}"></td>

            <td><input style='width: 80px;' name='daily_date{{$i}}' id='daily_date_{{$i}}' class='date-picker'  value="{{ old('daily_date' . $i) }}"></td>
            <td><input style='width: 80px;' name='start_time{{$i}}' id='start_time_{{$i}}' class='time-picker' value="{{ old('start_time' . $i) }}"></td>
            <td><input style='width: 80px;' name='end_time{{$i}}' id='end_time_{{$i}}' class='time-picker' value="{{ old('end_time' . $i) }}"></td>

            <td><input style='width: 150px;' name='company_execute{{$i}}' id='company_execute_{{$i}}' value="{{ old('company_execute' . $i) }}"></td>

            <td><input style='width: 150px;' name='charge_for{{$i}}' id='charge_for_{{$i}}' value="{{ old('charge_for' . $i) }}"></td>

            <td><input style='width: 250px;' name='description{{$i}}' id='description_{{$i}}' value="{{ old('description' . $i) }}"></td>

            <td>
                <select name="category1_id{{$i}}" id="category1_id_{{$i}}">

                <?php 
                    $oldCategory1_id = old('category1_id' . $i);
                    foreach($mst_daily_activity_types as $type) {
                        $selected = $oldCategory1_id == $type->id ? 'selected' : '';
                        echo '<option value="'.$type->id .'" '. $selected .'>';
                        echo $type->name ;
                        echo '</option>';
                    }
                ?>
                </select>
            </td>
            <td><input style='width: 300px;' name='category1_note{{$i}}' id='category1_note_{{$i}}' value="{{ old('category1_note' . $i) }}">
            </td>

            <td>
                <select name="category2_id{{$i}}" id="category2_id_{{$i}}">
                <?php 
                    $oldCategory2_id = old('category2_id' . $i);
                    foreach($mst_daily_activity_types as $type) {
                        $selected = $oldCategory2_id == $type->id ? 'selected' : '';
                        echo '<option value="'.$type->id .'" '. $selected .'>';
                        echo $type->name ;
                        echo '</option>';
                    }
                ?>
                </select>
            </td>

            <td><input style='width: 150px;' name='category2_note{{$i}}' id='category2_note_{{$i}}' value="{{ old('category2_note' . $i) }}"></td>

            <td>
                <select name="category3_id{{$i}}" id="category3_id_{{$i}}">
                <?php 
                    $oldCategory3_id = old('category3_id' . $i);
                    foreach($mst_daily_activity_types as $type) {
                        $selected = $oldCategory3_id == $type->id ? 'selected' : '';
                        echo '<option value="'.$type->id .'" '. $selected .'>';
                        echo $type->name ;
                        echo '</option>';
                    }
                ?>
                </select>
            </td>
            <td><input style='width: 150px;' name='category3_note{{$i}}' id='category3_note_{{$i}}' value="{{ old('category3_note' . $i) }}"></td>




                            <!-- <td class='text-center'><input type='checkbox' class='case'/></td>
                            <td class='text-center'><span id='snum{{$i}}'>{{ $i }}.</span><input type='hidden' value='0' name='counter[]'></td>
                            <td><input name='daily_date{{$i}}' id='daily_date_{{$i}}' value="{{ old('daily_date' . $i) }}" class='form-control service'></td>
                            <td><input name='start_time{{$i}}' id='start_time_{{$i}}' value="{{ old('start_time' . $i) }}" class='form-control service time-picker'></td>
                            <td><input name='end_time{{$i}}' id='end_time_{{$i}}' value="{{ old('end_time' . $i) }}" class='form-control service time-picker'></td>
                            <td><input name='company_execute{{$i}}' id='company_execute_{{$i}}' value="{{ old('company_execute' . $i) }}" class='form-control service'></td>
                            <td><input name='charge_for{{$i}}' id='charge_for_{{$i}}'  value="{{ old('charge_for' . $i) }}" class='form-control service'></td>
                            <td><input name='description{{$i}}' id='description_{{$i}}' value="{{ old('description' . $i) }}" class='form-control service'></td> -->
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
            <button id='dailyActivitySubmit' type="button" class="btn btn-primary">Lưu Kế Hoạch</button>
            <a href="{!! route('Equipment') !!} " class="btn btn-info">Trở về màn hình trước</a>
        </div>
    </div>
</div>
@endsection

