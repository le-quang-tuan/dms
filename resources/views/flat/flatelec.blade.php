@extends('include.layout')

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
    var settimmer = 0;

    $(function () {
        callDatePicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! url("flat/elec/{$TfElecUsed}/datatable") !!}',
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
                @role('admin|manager|moderator')
                    {data: 'action', name: 'action'},
                @endrole
            ],
            
            dom: 'Blfrtip',
            buttons: ['pdf', 'excel'],
            colReorder: true,
            select: true
        });
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000); 

        @role('admin|manager|moderator')
            /* The plugin will submit form and scroll to top*/
            $("#tenementFlatElecSubmit").click(function(){
                bootbox.confirm("Chỉ số sử dụng sẽ được thêm mới?", function(result) {
                    if(result){
                        $("#frmtenementFlatElec").submit();
                    }
                });
            });

            $("#tenementFlatElecDestroySubmit").click(function(){
                bootbox.confirm("Hủy chỉ số đã được chọn?", function(result) {
                    if(result){
                        $("#frmtenementFlatElecDestroy").submit();
                    }
                });
            });

            $('#users-table').on('click', '.btn-details', function(){
               //showModalDialog("../../flat");
                var tableHeader = "<thead><tr>" +
                                  "<th>Header 1</th>" +
                                  "<th>Header 2</th>" +
                                  "<th>Header 3</th>" +
                                  "</tr></thead>";
                var urlGet = "flat/elec/"+ $(this).val() +"/destroy";

                var confirmTable = "<h2>Dữ liệu sẽ được xóa?</h2>";
                bootbox.confirm(confirmTable, function (result) {
                    var id = $(this).val();
                    if (result == true){
                        jQuery.ajax({
                        type: "GET",
                        url: '{!! url("'+ urlGet +'") !!}',
                        error:function(msg){
                            alert( "Error !: " + msg );
                        },
                        success:function(data){
                            table.ajax.url('{!! url("flat/elec/{$TfElecUsed}/datatable") !!}').load();      
                        }});
                    }
                });
            });
        @endrole
        $("#old_index").ForceNumericOnly("##,###,###,###", "-");
        $("#new_index").ForceNumericOnly("##,###,###,###", "-");

        var year_month = JSON.parse(<?php echo json_encode($year_month); ?>);

        var year = year_month.toString().substring(0,4);
        var month = year_month.toString().substring(4,6);

        var date = new Date(year + '-' + month + '-' + '1');
        date.setMonth(month - 2);

        var preMonth = (1 + date.getMonth()).toString();
        preMonth = preMonth.length > 1 ? preMonth : '0' + preMonth;

        var firstDay = ('01/' + preMonth + '/' + date.getFullYear());

        var preLastdayMonth = new Date(date.getFullYear(), preMonth,0);

        var lastDay = (preLastdayMonth.getDate() + '/' + preMonth + '/' + preLastdayMonth.getFullYear());

        $("#comment").val("Chỉ số điện sử dụng tháng " + preMonth + "/" + year);
        $("#date_from").val(firstDay);
        $("#date_to").val(lastDay);
        $("#year").val(year);
        $("#month").val(month);
    });

    function callDatePicker() {
        var today = getCurrentDate();
        var dateFormat = "dd/mm/yy";

        $("#date_from").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });

        $("#date_to").datepicker({
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
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Nhập Thông Tin tiêu thụ Điện của Căn hộ</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('tenement-alert-' . $msg))
    <p class="alert alert-{!! $msg !!}">
        {{ Session::get('tenementFlat-alert-' . $msg) }} &nbsp; <div style="display: none;" > <b id="show-time">2</b></div>                        
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

    {{ Form::open(['route' => ['TfElecUsed.store', $TfElecUsed],  'method' => 'POST', 'id'=>'frmtenementFlatElec']) }}
        {!! csrf_field() !!}
        <table class="table table-condensed xs table-striped">
        <input id="id" name='id' type="hidden" value="{!! $TfElecUsed !!}">
            <tbody>
                <tr>
                    <td>Năm/tháng<em style='color:red'>(*)</em> </td>
                    <td>
                        <select name="month" id="month">
                            <?php
                                $y = date('Y');
                                $m = date('m');
                                for($i = 1 ; $i <= 12; $i++){
                                    if ($m == $i)
                                        echo "<option selected=True>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                                    else 
                                        echo "<option>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                                }
                            ?>
                        </select>
                        <select name="year" id="year">
                            <?php
                                for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                                    if ($y == $i)
                                        echo "<option selected>$i</option>";
                                    else 
                                        echo "<option>$i</option>";

                                }
                            ?>
                        </select>
                    </td>
                    <td>Từ ngày<em style='color:red'>(*)</em></td>
                    <td><input class="date-picker" type="text" value="{!! old('date_from') !!}" id="date_from" name="date_from" size="10%">~<input class="date-picker" type="text" value="{!! old('date_to') !!}" id="date_to" name="date_to" size="10%"></td>
                    <td>Chỉ số từ<em style='color:red'>(*)</em></td>
                    <td><input  type="text" value="{!! old('old_index') !!}" id="old_index" name="old_index" size="5%">~<input type="text" value="{!! old('new_index') !!}" id="new_index" name="new_index" size="5%">
                    </td>
                    <td>Ghi chú</td>
                    <td>
                        <input type="text" name="comment" id="comment" name="comment" size="20%" value="{!! old('comment') !!}">
                    </td>
                </tr>
            </tbody>
        </table>
    {{ Form::close() }}

    @role('admin|manager|moderator')
        <div>
            <br>
            <button id='tenementFlatElecSubmit' type="button" class="btn btn-primary">Thêm mới</button>            
            <a href="{!! route('TenementFlat') !!} " class="btn btn-info">Trở về màn hình trước</a>
        </div>
    @endrole
    <div class="col-lg-12">
        <h3>Chỉ số tiêu thụ Điện</h3>
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th>Năm tháng</th>
                    <th>Từ ngày</th>
                    <th>Đến ngày</th>
                    <th>Chỉ số cũ</th>
                    <th>Chỉ số mới</th>
                    <th>Ghi Chú</th>
                    @role('admin|manager|moderator')
                        <th>Hủy</th>
                    @endrole
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

