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
            $("#persons").ForceNumericOnly("##,###,###,###", "-");
            $("#area").ForceNumericOnly("##,###,###,###.##", "-");
            
            $("#tenementFlatSubmit").click(function(){
                bootbox.confirm("Thông tin sẽ được thêm mới?", function(result) {
                    if(result){
                        $("#frmTenementFlat").submit();
                    }
                });
            });
        });
        
    </script>

@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Tạo Mới Thông Tin Căn hộ</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenement-alert-' . $msg))
        <p class="alert alert-{!! $msg !!}">
            {!! Session::get('tenement-alert-' . $msg) !!}<br>
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

        <form id='frmTenementFlat' action="{!! route('TenementFlatDetail.store') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <table class="table table-condensed xs table-striped">
                <tbody>
                    <tr>
                        <td>Căn hộ<em style='color:red'>(*)</td>
                        <td colspan="3">
                            <div class="col-sm-10 nopadding">
                                <div class="col-sm-1 nopadding" style="text-align: left;">Khu</div>
                                <div class="col-sm-2 nopadding" style="text-align: left;">
                                    <?php 
                                    echo '<select name="block_name" id="block_name">';
                                    $oldBlock_name = old('block_name');

                                    foreach($lsChar as $char) {
                                        $selected = $oldBlock_name == $char ? 'selected' : '';

                                        echo '<option value="'.$char.'"'. $selected .'>';
                                        echo $char;
                                        echo '</option>}';
                                    }
                                    echo '</select>';

                                    echo '<select name="block_sub" id="block_sub">';                   
                                    $oldBlock_sub = old('block_sub');

                                    if ('' == $oldBlock_sub){
                                        echo '<option selected ="selected" value="">';
                                        echo '';
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="">';
                                        echo '';
                                        echo '</option>';
                                    }


                                    foreach($lsBlockSub as $char) {
                                        $selected = $oldBlock_name == $char ? 'selected' : '';
                                        echo '<option value="'.$char.'"'. $selected .'>';
                                        echo $char;
                                        echo '</option>}';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="col-sm-1 nopadding" style="text-align: right;">Tầng &nbsp;</div>
                                <div class="col-sm-2 nopadding">
                                    <?php 
                                        echo '<select name="floor_num" id="floor_num">';
                                            $oldfloor_num = old('floor_num');

                                            foreach($lsNum as $num) {
                                                $selected = $oldfloor_num == $num ? 'selected' : '';

                                                echo '<option value="'.$num .'" '. $selected .'>';
                                                echo $num ;
                                                echo '</option>';
                                            }
                                        echo '</select>';

                                        echo '<select name="floor_name" id="floor_name">';
                                        $oldfloor_name = old('floor_name');
                                        echo '<option value="">';
                                        echo '';
                                        echo '</option>';

                                        foreach($lsChar as $char) {
                                            $selected = $oldfloor_name == $char ? 'selected' : '';

                                            echo '<option value="'.$char.'"'. $selected .'>';
                                            echo $char;
                                            echo '</option>}';
                                        }
                                        echo '</select>';
                                    ?>
                                </div>
                                <div class="col-sm-1 nopadding" style="text-align: right;">Hộ Số&nbsp;</div>
                                <div class="col-sm-1 nopadding">
                                    <input type="text" value="{!! old('flat_num') !!}" id="flat_num" name="flat_num" size="3%">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Địa chỉ căn hộ thực tế<em style='color:red'>(*)</em> </td>
                        <td><input type="text" value="{!! old('address') !!}" id="address" name="address" size="30%"></td>
                        <td>Phí quản lý</td>
                        <td><input type="text" value="{!! old('manager_price') !!}" id="manager_price" name="manager_price" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Diện tích<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! old('area') !!}" id="area" name="area" size="30%"></td>
                        <td>Số nhân khẩu<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! old('persons') !!}" id="persons" name="persons" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Ngày nhận căn hộ<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! old('receive_date') !!}" id="receive_date" name="receive_date" size="30%" class="date-picker" value="{!! date('d/m/Y') !!}"></td>
                        <td>Hiện đang sử dụng<em style='color:red'>(*)</em></td>
                        <td>
                            <?php 
                                $oldGas_type = old('is_stay');
                            echo '<select name="is_stay" id="is_stay">';
                            echo '<option value="0"';
                            if ($oldGas_type == 0) 
                                echo ' selected>Đang ở</option>
                                <option value="1">
                                    Chưa ở
                                </option>';
                            else 
                                echo '>Đang ở</option>
                                <option value="1" selected>
                                    Chưa ở
                                </option>';

                            echo '</select>';
                            ?>

                        </td>
                    </tr>
                    <tr>
                        <td>Biểu phí tiêu thụ</td>
                        <td colspan="3">
                            <div class="col-sm-10 nopadding">
                                <div class="col-sm-1 nopadding" style="text-align: left;">Điện</div>
                                <div class="col-sm-2 nopadding" style="text-align: left;">
                                    <?php 
                                        $oldElec_type = old('elec_type');
                                        echo '<select name="elec_type" id="elec_type" style="width: 150px;">';
                                            foreach($elec_tariffs as $elec_type) {
                                                $selected = ($oldElec_type == $elec_type->id ? 'selected' : '');
                                                echo '<option value="' .$elec_type->id . '" '. $selected .'>';
                                                echo $elec_type->elec_type;
                                                echo '</option>';
                                            }
                                        echo '</select>';
                                    ?>
                                </div>
                                <div class="col-sm-1 nopadding" style="text-align: right;">Nước</div>
                                <div class="col-sm-2">
                                    <?php
                                        $oldWater_type = old('water_type');
                                        echo '<select name="water_type" id="water_type" style="width: 150px;">';
                                            foreach($water_tariffs as $water_type) {
                                                $selected = ($oldWater_type == $water_type->id ? 'selected' : '');
                                                echo '<option value="'.$water_type->id . '" '. $selected .'>';
                                            
                                                echo $water_type->water_type;
                                                echo '</option>';
                                            }
                                        echo '</select>';
                                    ?>
                                </div>
                                <div class="col-sm-1 nopadding" style="text-align: right;">Gas</div>
                                <div class="col-sm-2">
                                    <?php 
                                        $oldGas_type = old('gas_type');

                                        echo '<select name="gas_type" id="gas_type" style="width: 150px;">';
                                            foreach($gas_tariffs as $gas_type) {
                                                $selected = ($oldWater_type == $gas_type->id ? 'selected' : '');
                                                echo '<option value="'.$gas_type->id . '" '. $selected .'>';
                                                echo $gas_type->gas_type;
                                                echo '</option>';
                                            }
                                        echo '</select>';
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Chủ hộ<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! old('name') !!}" id="name" name="name" size="30%"></td>
                        <td>Email</td>
                        <td><input type="text" value="{!! old('email') !!}" id="email" name="email" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Điện thoại<em style='color:red'>(*)</em></td>
                        <td colspan="3"><input type="text" value="{!! old('phone') !!}" id="phone" name="phone" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Ghi chú</td>
                        <td colspan="3">
                            <textarea name="note" id="comment" name="comment" rows="2" cols="100%">
                                {!!old('comment') !!}
                            </textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <div>
            <br>
            <button id='tenementFlatSubmit' type="button" class="btn btn-primary">Thêm mới</button> &nbsp;
            <a href="{!! route('TenementFlat') !!} " class="btn btn-info">Trở về màn hình trước</a>

        </div>
    </div>
</div>
    <script type="text/javascript">
        $(function () {
            callDatePicker();
        });

        function callDatePicker() {
            var today = getCurrentDate();
            var dateFormat = "dd/mm/yy";

            $("#receive_date").datepicker({
                    dateFormat: dateFormat,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: false
            });

            $(".dobpicker").datepicker({
                dateFormat: dateFormat,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: false
            });

            $('.time-picker').timepicker({
                timeFormat: 'HH:mm:ss',
                //minTime: '11:45:00' // 11:45:00 AM,
                //maxHour: 20,
                //maxMinutes: 30,
                //startTime: new Date(0,0,0,15,0,0) // 3:00:00 PM - noon
                //interval: 15 // 15 minutes
            });

            $('.time-picker').on('change', function () {
                var regExp = /^(\d{1,2})(\:)(\d{1,2})(\:)(\d{1,2})$/;
                var val = this.value.match(regExp);
                if (jQuery.isEmptyObject(val)) {
                    this.value = '';
                }
            });
        }

        function getCurrentDate() {
            var newDate = new Date();
            var date = newDate.getDate() < 10 ? "0" + newDate.getDate() : newDate.getDate();
            var month = (newDate.getMonth() + 1) < 10 ? "0" + (newDate.getMonth() + 1) : (newDate.getMonth() + 1);
            var year = newDate.getYear() + 1900;
            var today = date + "/" + month + "/" + year;
            return today;
        }

        function getDate( element ) {
            var date;
            var dateFormat = "dd/mm/yy";
            try {
                date = $.datepicker.parseDate( dateFormat, element.value );
            } catch( error ) {
                date = null;
            }

            return date;
        }
    </script>
@endsection