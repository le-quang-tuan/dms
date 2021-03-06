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
            top: 50%; 
            transform: translateY(-50%);
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
            
            /* The plugin will submit form and scroll to top*/
            $("#tenementFlatSubmit").manualSubmit('frmTenementFlat');

            /* The plugin will submit form and scroll to top*/
            $("#tenementFlatDelete").manualSubmit('frmTenFlat');
                    
            $("#tenementFlatRefresh").manualRefresh('frmTenementFlat');
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


<div class="container-fluid time-table-no-margin">
    <div class="row">        
        <div class="col-md-12">
        <p class="pagetitle">Tạo mới Khu căn hộ/Chung cư</p>
            <!-- begin .flash-message -->
            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('tenement-alert-' . $msg))
                <p class="alert alert-{!! $msg !!}">
                    {!! Session::get('tenement-alert-' . $msg) !!}<br>
                    The message will dissable with in <b id="show-time">5</b> seconds                            
                </p>
                @endif
                @endforeach
            </div> 
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
                <table class="table table-striped table-bordered table-hover table-condensed">
                    <tbody>
                        <tr>
                            <td class="col-md-2">Căn hộ<em style='color:red'>(*)</em> </td>
                            <td class="col-md-2"><input type="text" value="{!! old('address') !!}" id="address" name="address" size="50%"></td>
                            <td class="col-md-2">Mã lưu<em style='color:red'>(*)</em> </td>
                            <td class="col-md-6">
                                <div class="form-group">
                                    <label for="flat_area1" class="col-sm-1 control-label">Khu</label>
                                    <div class="col-sm-2">
                                      <?php 
                                        echo '<select name="flat_area1" id="flat_area1"  class="form-control room_no">';
                                        foreach($lsChar as $char) {
                                            echo '<option value="'.$char.'">';
                                            echo $char;
                                            echo '</option>}';
                                        }
                                        echo '</select>';
                                    ?>
                                    </div>
                                    <div class="col-sm-2">
                                        <?php 
                                            echo '<select name="flat_area2" id="flat_area2" class="form-control room_no">';
                                                foreach($lsNum as $num) {
                                                    echo '<option value="'.$num .'">';
                                                    echo $num ;
                                                    echo '</option>';
                                                }
                                            echo '</select>';
                                        ?>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="form-group">
                                            <label for="flat_foor" class="col-sm-1 control-label">Tầng</label>
                                            <div class="col-sm-3">
                                                <?php 
                                                    echo '<select name="flat_foor" id="flat_foor" class="form-control room_no">';
                                                        foreach($lsNum as $num) {
                                                            echo '<option value="'.$num .'">';
                                                            echo $num ;
                                                            echo '</option>';
                                                        }
                                                    echo '</select>';
                                                ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="flat_no" class="col-sm-5 control-label">Số</label>
                                                <input type="text" value="{!! old('flat_no') !!}" id="flat_no" name="flat_no" size="3%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Chủ hộ<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('name') !!}" id="name" name="name" size="50%"></td>

                            <td>Điện thoại<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('phone') !!}" id="phone" name="phone" size="50%"></td>
                        </tr>
                        <tr>
                            <td>Diện tích<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('area') !!}" id="area" name="area" size="50%"></td>
                            <td>Số nhân khẩu<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('persons') !!}" id="persons" name="persons" size="50%"></td>
                        </tr>
                        <tr>
                            <td>Ngày nhận căn hộ<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('receive_date') !!}" id="receive_date" name="receive_date" size="50%" class="date-picker" value="{!! date('d/m/Y') !!}"></td>
                            <td>Hiện đang sử dụng<em style='color:red'>(*)</em></td>
                            <td>
                                <select name="is_stay" id="is_stay" class="form-control editor">
                                    <option value="0">
                                        Đang ở
                                    </option>
                                    <option value="1">
                                        Chưa ở
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Biểu phí</td>
                            <td colspan="3" >
                                <div class="form-group">
                                    <label for="elec_type" class="col-sm-1 control-label">Điện</label>
                                    <?php 
                                        echo '<select name="elec_type" id="elec_type" class="form-control editor">';
                                            foreach($elec_tariffs as $elec_type) {
                                                echo '<option value="'.$elec_type->id.'">';
                                            
                                                echo $elec_type->elec_type;
                                                echo '</option>';
                                            }
                                        echo '</select>';
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label for="elec_type" class="col-sm-1 control-label">Nước</label>
                                    <?php 
                                        echo '<select name="water_type" id="water_type" class="form-control editor">';
                                            foreach($water_tariffs as $water_type) {
                                                echo '<option value="'.$water_type->id.'">';
                                            
                                                echo $water_type->water_type;
                                                echo '</option>';
                                            }
                                        echo '</select>';
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label for="gas_type" class="col-sm-1 control-label">Gas</label>
                                    <?php 
                                        echo '<select name="gas_type" id="gas_type" class="form-control editor">';
                                            foreach($gas_tariffs as $gas_type) {
                                                echo '<option value="'.$gas_type->id.'">';
                                            
                                                echo $gas_type->gas_type;
                                                echo '</option>';
                                            }
                                        echo '</select>';
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Ghi chú</td>
                            <td colspan="3">
                                <textarea name="note" id="comment" name="comment" rows="2" cols="90%">
                                    {!!old('comment') !!}
                                </textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>    
        
        <div class="col-md-12 col-md-offset-2">
            </br>
            <button id='tenementFlatSubmit' type="button" class="btn btn-primary">Create</button>            
            <button id='tenementFlatRefresh' type="button" class="btn btn-default">Refresh</button>
            <a href="{!! route('TenementFlat') !!}">Back To List</a>
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
                minDate: today,
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