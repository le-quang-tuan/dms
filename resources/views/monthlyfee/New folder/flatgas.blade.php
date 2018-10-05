@extends('include.layout')

@section('style')
    {!! Html::style('css/jquery.dataTables.min.css') !!}

    {!! Html::style('css/datepicker/jquery-ui.css') !!}

    {!! Html::style('css/tageditor/jquery.tag-editor.css') !!}

    {!! Html::style('css/timepicker/jquery.timepicker.min.css') !!}

    {!! Html::style('css/multiselect/bootstrap-multiselect.css') !!}

    {!! Html::style('css/colorpicker/spectrum.css') !!}

<style>    
.cImgPassport{
    max-width: 50%;
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
    {!! Html::script('js/jquery.dataTables.min.js') !!}
    {!! Html::script('js/ckeditor/ckeditor.js') !!}
    {!! Html::script('js/manual_js/manual_click.js') !!}
    {!! Html::script('js/colorpicker/spectrum.js') !!}
    {!! Html::script('js/datepicker/jquery-ui.js')  !!}
    {!! Html::script('js/tageditor/jquery.tag-editor.js')  !!}
    {!! Html::script('js/tageditor/jquery.caret.min.js')  !!}
    {!! Html::script('js/timepicker/jquery.timepicker.min.js')  !!}
    {!! Html::script('js/multiselect/bootstrap-multiselect.js')  !!}
<script>
    var settimmer = 0;

    $(function () {
        callDatePicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! url("flat/gas/{$TfGasUsed}/datatable") !!}',
            columns: [
                //{data: 'year_month', name: 'year_month'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.year_month.substring(4,6) +'/' + data.year_month.substring(0,4);
                    },
                },

                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.date_from != "")
                        {
                            var dateString  = data.date_from;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            return  day + '/' + month + '/' + year;
                        }
                        return data.date_from; //data.year_month.substring(4,6) +'/' + data.year_month.substring(0,4);
                    },
                },
                //{data: 'date_from', name: 'date_from'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.date_to != "")
                        {
                            var dateString  = data.date_to;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            return  day + '/' + month + '/' + year;
                        }
                        return data.date_to; //data.year_month.substring(4,6) +'/' + data.year_month.substring(0,4);
                    },
                },
                {data: 'old_index', name: 'old_index'},
                {data: 'new_index', name: 'new_index'},
                {data: 'comment', name: 'comment'},
            ]
        });

        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000); 

        /* The plugin will submit form and scroll to top*/
        $("#tenementFlatGasSubmit").manualSubmit('frmtenementFlatGas');

        /* The plugin will submit form and scroll to top*/
        $("#tenementFlatGasDelete").manualSubmit('frmCom');
                
        $("#tenementFlatGasRefresh").manualRefresh('frmtenementFlatGas');
    });

    function callDatePicker() {
        var today = getCurrentDate();
        var dateFormat = "dd/mm/yy";

        $("#date_from").datepicker({
            minDate: today,
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });

        $("#date_to").datepicker({
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

@section('content')

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

            {{ Form::open(['route' => ['TfGasUsed.store', $TfGasUsed],  'method' => 'POST', 'id'=>'frmtenementFlatGas']) }}

            
                {!! csrf_field() !!}
                <table class="table table-striped table-bordered table-hover table-condensed">               
                    <input id="id" name='id' type="hidden" value="{!! $TfGasUsed !!}">
                    <tbody>
                        <tr>
                            <td class="col-md-1">Năm/tháng<em style='color:red'>(*)</em> </td>
                            <td class="col-md-1">
                                <select name="year" id="year">
                                <?php 
                                   for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                                      echo "<option>$i</option>";
                                   }
                                ?>
                                </select>
                                <select name="month" id="month">
                                <?php 
                                   for($i = -1 ; $i < 10; $i++){
                                      echo "<option>" . date('m', strtotime(" +$i months")) . "</option>";
                                   }
                                ?>
                                </select>
                            </td>

                            <td class="col-md-1">Từ ngày<em style='color:red'>(*)</em></td>

                            <td class="col-md-2"><input class="date-picker" type="text" value="{!! old('date_from') !!}" id="date_from" name="date_from" size="10%">~<input class="date-picker" type="text" value="{!! old('date_to') !!}" id="date_to" name="date_to" size="10%"></td>

                            <td class="col-md-1">Chỉ số từ<em style='color:red'>(*)</em></td>

                            <td class="col-md-1"><input  type="text" value="{!! old('old_index') !!}" id="old_index" name="old_index" size="5%">~<input type="text" value="{!! old('new_index') !!}" id="new_index" name="new_index" size="5%"></td>
                            <td class="col-md-1">Ghi chú</td>
                            <td  class="col-md-1">
                                <input type="text" name="comment" id="comment" name="comment" size="20%">
                                    {!!old('comment') !!}
                                </input>
                            </td>
                        </tr>
                    </tbody>
                </table>
            {{ Form::close() }}
        </div>    
        
        <div class="col-md-12 col-md-offset-2">
            </br>
            <button id='tenementFlatGasSubmit' type="button" class="btn btn-primary">Create</button>            
            <button id='tenementFlatGasRefresh' type="button" class="btn btn-default">Refresh</button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h1>Thông tin Chung Cư/Khu Căn Hộ và Thiết lập biểu phí</h1>
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th>Năm tháng</th>
                        <th>Từ ngày</th>
                        <th>Đến ngày</th>
                        <th>Chỉ số cũ</th>
                        <th>Chỉ số mới</th>
                        <th>Cập nhật</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

