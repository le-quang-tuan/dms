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
    max-width: 100%;
}

</style>
@endsection

@section('script')
<!--     {!! Html::script('js/jquery.dataTables.min.js') !!}
    {!! Html::script('js/ckeditor/ckeditor.js') !!}
    {!! Html::script('js/manual_js/manual_click.js') !!}
    {!! Html::script('js/colorpicker/spectrum.js') !!}
    {!! Html::script('js/datepicker/jquery-ui.js')  !!}
    {!! Html::script('js/tageditor/jquery.tag-editor.js')  !!}
    {!! Html::script('js/tageditor/jquery.caret.min.js')  !!}
    {!! Html::script('js/timepicker/jquery.timepicker.min.js')  !!}
    {!! Html::script('js/multiselect/bootstrap-multiselect.js')  !!} -->
<script>
    // Replace the <textarea id="address"> with a CKEditor
    // instance, using default configuration.
    // auto dissable success message.
    var settimmer = 0;

    $(function () {
        callDatePicker();

        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            deferLoading: 0,
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    filename: function(){
                        var month = $("#month").val();
                        var year = $("#year").val();
                        return 'CongNoToanPhanDenThang_' + month + year;
                    },
                    text:'Xuất file '+'<i class="fa fa-file-excel-o fa-fw"></i>'
                    ,exportOptions: {
                        columns: [ 0, ':visible' ]
                    }
                },
                {extend:'colvis', text:'Hiển thị dữ liệu'+'<i class="fa fa-angle-down"></i>'}
            ],
            ajax: '{!! url("monthlyfee/paiddetaillist/anyData") !!}',
            columns: [
                {data: 'address', name: 'address'},            
                {data: 'bill_no', name: 'bill_no'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.year_month != "")
                        {
                            var dateString  = data.year_month;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);

                            return  month + '/' + year;
                        }
                        return data.year_month;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var receive_date = "";
                        if (data.receive_date != "" && data.receive_date != null)
                        {
                            var dateString  = data.receive_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            receive_date = day + '/' + month + '/' + year;
                        }

                        return receive_date;
                    },
                },
                {data: 'receive_from', name: 'receive_from'},
                {data: 'receiver', name: 'receiver'},
                // {
                //     data: null, 
                //     render: function ( data, type, row ) {
                //         return data.money;
                //         //return number_format(data.money, 0, ".",",");
                //     },
                // },
                {data: 'money', name: 'money'},

                {data: 'comment', name: 'comment'},
                @role('admin|manager|moderator')
                    {data: 'action', name: 'action'},
                @endrole
            ],
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;
     
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
     
                // Total over all pages
                total = api
                    .column(6)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
     
                // Total over this page
                pageTotal = api
                    .column(6, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
     
                // Update footer
                $( api.column(6).footer() ).html(
                    '$'+pageTotal +' ( $'+ total +' total)'
                );
            }
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
        $('#users-table').on('click', '.btn-details', function(){
            var urlGet = "monthlyfee/paiddetail/"+ $(this).val() +"/destroy";

            var confirmTable = "<h2>Dữ liệu sẽ được xóa?</h2>";
            bootbox.confirm(confirmTable, function (result) {
                if (result == true){
                    jQuery.ajax({
                        type: "GET",
                        url: '{!! url("'+ urlGet +'") !!}',
                        error:function(msg){
                            alert( "Error !: " + msg );
                        },
                        success:function(data){
                            var year = $("#year").val();
                            var month = $("#month").val();
                            table.clear().draw();
                            table.ajax.url('{!! url("monthlyfee/paiddetaillist/anyData?year_month='+ year + month + '") !!}').load();
                        }
                    });
                }
            });
        });
        @endrole

        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        

        /* BEGIN delete and add more */
        $(".delete").on('click', function() {
            $('.case:checkbox:checked').each(function(){
                if($.isNumeric($(this).val())){
                    $(this).parents("tr").hide();
                } else {
                    $(this).parents("tr").remove();
                }
            });
            moneyChange();

            $('.check_all').prop("checked", false); 
            //addDangerClass();
        });

        var i = 0;
        var button_my_button = "#addmore";
        $(button_my_button).click(function(){
            count=$('#addmore1 tr').length-1;
            //alert(count);

            var data="<tr class='new'><td><input type='checkbox' class='case'/></td>";
                data+="<td><span id='snum"+i+"'>"+count+".</span><input type='hidden' value='0' name='counter[]'></td>";

                //Block Name
                data+= "<td><select name='payment_type"+count+"' id='payment_type_"+count+"' class='form-control room_no' '>";
                var mst_payment_types = <?php echo json_encode($mst_payment_types); ?>;
                for (var i=0; i < mst_payment_types.length; i++) {
                    data+= '<option value="' + mst_payment_types[i]['id'] + '">';
                    data+= mst_payment_types[i]['name'];
                    data+= '</option>';
                }
                data+= '</select></td>';

                //Floor from
                data+="<td><input class='text-right commas price' name='money"+count+"' id='money_" + count+"' onchange='moneyChange()' class='form-control service'></td>";

                data+="<td><input name='comment"+count+"' id='comment_"+count+"' class='form-control service'></td>";

            $(data).insertBefore("#servicetotal");
            i++;
            $("#money_" + count).ForceNumericOnly("##,###,###,###", "-");
        });
        
        $('select#month').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var year = $("#year").val();
            var month = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("monthlyfee/paiddetaillist/anyData?year_month='+ year + month + '") !!}').load();
        });

        $('select#year').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var month = $("#month").val();
            var year = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("monthlyfee/paiddetaillist/anyData?year_month='+ year + month + '") !!}').load();
        });

        $('#flatSubmit').click(function() {
            var searchText = $('#users-table_filter input').val();
            var year = $("#year").val();
            var month = $("#month").val();
            table.clear().draw();
            table.ajax.url('{!! url("monthlyfee/paiddetaillist/anyData?year_month='+ year + month + '") !!}').load();
            table.search(searchText).draw();
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
        <h2>Thu Công Nợ</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
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

        <div class="col-md-12">
            <h3>Danh sách Các Khoảng Thu Phí</h3>
            <table class="table table-striped table-bordered table-hover table-condensed">
            <tbody>
                <tr>
                    <td>Phí tháng <em style='color:red'>(*)</em> </td>
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
                                        echo "<option selected=True>$i</option>";
                                    else 
                                        echo "<option>$i</option>";

                                }
                            ?>
                        </select>
                        <button id='flatSubmit' type="button" class="btn btn-primary">Làm Mới Dữ Liệu</button>
                    </td>
                </tr>
            </tbody>
        </table>

            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th class="info">Căn Hộ</th>
                        <th class="info">Quyển số</th>
                        <th class="info">Phí Năm/Tháng</th>
                        <th class="info">Ngày nhận</th>
                        <th class="info">Nhận từ</th>
                        <th class="info">Người nhận</th>
                        <th class="info">Số tiền</th>
                        <th class="info">Ghi chú</th>
                        @role('admin|manager|moderator')
                        <th class="info">Hủy</th>
                        @endrole
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align:right">Total:</th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection