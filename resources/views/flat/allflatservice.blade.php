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
    $(function () {
        callDatePicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        var year = $("#year").val();
        var month = $("#month").val();
        
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    filename: function(){
                        return 'DanhSachXe';
                    },
                    exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                }
                }
            ],
            ajax: '{!! url("flat/all/service/anyData/'+ year + month + '") !!}',
            columns: [
                {   data: 'address' , name: 'address' },
                {   data: 'owner' , name: 'owner' },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var date_from = "";
                        if (data.date_from != "" && data.date_from != null)
                        {
                            var dateString  = data.date_from;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            date_from = day + '/' + month + '/' + year;
                        }

                        return date_from;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var date_to = "";
                        if (data.date_to != "" && data.date_to != null)
                        {
                            var dateString  = data.date_to;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            date_to = day + '/' + month + '/' + year;
                        }

                        return date_to;
                    },
                },
                { data: 'name' , name: 'name' },
                { data: 'mount' , name: 'mount' },
                { data: 'unit' , name: 'unit' },
                { data: 'price' , name: 'price' },
                { data: 'total' , name: 'total' },
                { data: 'comment' , name: 'comment' },
                @role('admin|manager|moderator')
                    { data: 'change', name: 'change', orderable: false, searchable: false}
                @endrole
            ]
        });        
        $('#users-table').on('click', 'button', function () {
            // var year = $("#year").val();
            // var month = $("#month").val();

            // var date = new Date(year + '-' + month + '-' + '1');
            // date.setMonth(month - 2);

            // var preMonth = (1 + date.getMonth()).toString();
            // preMonth = preMonth.length > 1 ? preMonth : '0' + preMonth;

            // var firstDay = ('01/' + preMonth + '/' + date.getFullYear());

            // var preLastdayMonth = new Date(date.getFullYear(), preMonth,0);

            // var lastDay = (preLastdayMonth.getDate() + '/' + preMonth + '/' + preLastdayMonth.getFullYear());

            // var table = $('#users-table').DataTable();
            // $("#year_month").val(month + '/' + year);
            // $("#address").val($(this).attr("address"));
            // $("#name").val($(this).attr("name"));
            // $("#flat_id").val($(this).attr("flat_id"));
            // $("#bill_year").val(year);
            // $("#bill_month").val(month);
            // $("#comment").val("Chỉ số nước sử dụng tháng " + month + "/" + year);
            // $("#old_index").val($(this).attr("old_index"));
            // $("#new_index").val($(this).attr("new_index"));
            // $("#date_from").val(firstDay);
            // $("#date_to").val(lastDay);

            var id = $(this).attr("id");
            $.ajax({
                type: "GET",
                cache: false,
                url : '{!! url("flat/service/'+ id +'/show") !!}',
                data: { id : id },
                success: function(data) {
                    var obj = $.parseJSON(data);

                    var date_from = obj[0]['date_from'];
                    if (date_from != "" && date_from != null)
                    {
                        var dateString  = date_from;
                        var year        = dateString.substring(0,4);
                        var month       = dateString.substring(4,6);
                        var day         = dateString.substring(6,8);

                        date_from = day + '/' + month + '/' + year;
                    }

                    var date_to = obj[0]['date_to'];
                    if (date_to != "" && date_to != null)
                    {
                        var dateString  = date_to;
                        var year        = dateString.substring(0,4);
                        var month       = dateString.substring(4,6);
                        var day         = dateString.substring(6,8);

                        date_to = day + '/' + month + '/' + year;
                    }

                    $("#address").val(obj[0]['address']);
                    $("#id").val(id);
                    $("#owner").val(obj[0]['owner']);
                    $("#mount").val(obj[0]['mount']);
                    $("#price").val(obj[0]['price']);
                    $("#unit").val(obj[0]['unit']);
                    $("#name").val(obj[0]['name']);
                    $("#date_from").val(date_from);
                    $("#date_to").val(date_to);
                    var year = $("#year").val();
                    var month = $("#month").val();
                    $("#activation").prop('checked', false);

                    $("#year_month").val(month + '/' + year);
                    $("#comment").val(obj[0]['comment']);
                    $("#bill_year").val(year);
                    $("#bill_month").val(month);
                }
            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
            $('#DescModal').modal("show");
        });

        $('#flatSubmit').click(function() {
            var searchText = $('#users-table_filter input').val();

            var year = $("#year").val();
            var month = $("#month").val();
            table.clear().draw();
            table.ajax.url('{!! url("flat/all/service/anyData/'+ year + month + '") !!}').load();
            table.search(searchText).draw();

        });

        $("#changeSubmit").click(function(){
            var searchText = $('#users-table_filter input').val();
            var year = $("#year").val();
            var month = $("#month").val();
            var flat_id = $("#flat_id").val();

            var address = $("#address").val();
            var id = $("#id").val();
            var owner = $("#owner").val();
            var mount = $("#mount").val();
            var price = $("#price").val();
            var unit = $("#unit").val();
            var name = $("#name").val();
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();
            var comment = $("#comment").val();
            var bill_year = year;
            var bill_month = month;
            var activation = 1;
            if ($("#activation").is(":checked") == true){
                activation = 0
            }

            bootbox.confirm("Thông tin sẽ được cập nhật?", function(result) {
                if(result){
                    //$("#frmRecal").submit();
                    $('#loading').show();

                    $.ajax({
                        type: "POST",
                        cache: false,
                        url : "{!! route('AllService.exex_change') !!}",
                        data: { 
                            "address" : address,
                            "id" : id,
                            "owner" : owner,
                            "mount" : mount,
                            "price" : price,
                            "unit" : unit,
                            "activation" : activation,
                            "name" : name,
                            "date_from" : date_from,
                            "date_to" : date_to,
                            "comment" : comment,
                            "bill_year" : year,
                            "bill_month" : month,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(data) {
                            table.clear().draw();
                            table.ajax.url('{!! url("flat/all/service/anyData/'+ year + month + '") !!}').load();
                            table.search(searchText).draw();
                            $('#loading').hide();
                        }
                    })
                }
            });
            table.search(searchText).draw();
            // bootbox.confirm("Thông tin sẽ được thêm mới?", function(result) {
            //     if(result){
            //         var searchText = $('#users-table_filter input').val();
            //         var year = $("#year").val();
            //         var month = $("#month").val();

            //         $("#frmChange").submit();

            //         table.clear().draw();
            //         table.ajax.url('{!! url("flat/all/service/anyData/'+ year + month + '") !!}').load();
            //         table.search(searchText).draw();
            //     }
            // });
        });

        $('select#month').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var year = $("#year").val();
            var month = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("flat/all/service/anyData/'+ year + month + '") !!}').load();
        });

        $('select#year').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var month = $("#month").val();
            var year = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("flat/all/service/anyData/'+ year + month + '") !!}').load();
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
        <h2>Danh sách Phí Khác</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <div class="row dataTable">
            <div class="col-md-8">
                    
            Năm/tháng<em style='color:red'>(*)</em>
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
            </div>
        </div>

        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="group-head info">Căn Hộ</th>
                    <th width="300px" class="group-head info">Chủ Hộ</th>
                    <th width="300px" class="group-head info">Từ Ngày</th>
                    <th width="300px" class="group-head info">Đến Ngày</th>
                    <th width="100px" class="group-head period">Dịch vụ</th>
                    <th width="200px" class="group-head period">Số lượng</th>
                    <th width="100px" class="group-head period">Đơn vị</th>
                    <th width="100px" class="group-head period">Đơn giá</th>
                    <th width="100px" class="group-head period">Thành tiền</th>
                    <th width="100px" class="group-head day">Ghi Chú</th>
                    @role('admin|manager|moderator')
                        <th class="group-head day">Cập Nhật</th>
                    @endrole
                </tr>
            </thead>
        </table>
    </div>
</div>

<form id='frmChange' action="{!! route('AllService.exex_change') !!}" method="POST" role="form" method="post">
    <input type="hidden" id="id" name="id">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="modal fade" id="DescModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                     <h3 class="modal-title">Cập nhật Thông tin Phí Khác</h3>
                </div>
                <div class="modal-body">

                <div class="modal-header">
                    <div class="row dataTable">
                        <div class="col-md-3">
                            <label class="control-label">Căn hộ</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" maxlength="50" id="address" name="adress">
                        </div>
                    </div>

                    <br>

                    <div class="row dataTable">
                        <div class="col-md-3">
                            <label class="control-label">Chủ Hộ</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" maxlength="50" id="owner" name="owner" size="40%">
                        </div>
                    </div>
                </div>
                
                <div class="row dataTable">
                    <div class="col-md-3">Dịch vụ</div>
                    <div class="col-md-9">
                        <input type="text" value="{!! old('name') !!}" id="name" name="name" size="35%">
                    </div>
                </div>
                <br>

                <div class="row dataTable">
                    <div class="col-md-3">Số lượng</div>
                    <div class="col-md-3">
                        <input type="text" value="{!! old('mount') !!}" id="mount" name="mount" size="15%">
                    </div>
                    <div class="col-md-2">Đơn vị</div>
                    <div class="col-md-3">
                        <input type="text" value="{!! old('unit') !!}" id="unit" name="unit" size="15%">
                    </div>
                </div>
                <br>
                
                <div class="row dataTable">
                    <div class="col-md-3">Đơn giá</div>
                    <div class="col-md-3">
                        <input type="text" value="{!! old('price') !!}" id="price" name="price" size="15%">
                    </div>
                </div>
                <br>
                
                <div class="row dataTable">
                    <div class="col-md-3">Thời gian</div>

                    <div class="col-md-9"><input class="date-picker" type="text" value="{!! old('date_from') !!}" id="date_from" name="date_from"  size="15%">~<input class="date-picker" type="text" value="{!! old('date_to') !!}" id="date_to" name="date_to"  size="15%">
                    Hủy<input type="checkbox" name="activation" id="activation">
                    </div>
                </div>
                <br>
                
                <div class="row dataTable">
                    <div class="col-md-3">
                        Năm Tháng
                    </div>
                    <div class="col-md-8">
                        <input type="text" maxlength="30" id="year_month" name="year_month">
                    </div>
                    <input type="hidden" id="flat_id" name="flat_id">
                    <input type="hidden" id="bill_year" name="bill_year">
                    <input type="hidden" id="bill_month" name="bill_month">
                </div>
                <br>
                
                <div class="row dataTable">
                    <div class="col-md-3">Ghi chú</div>
                    <div class="col-md-9">
                        <input type="text" name="comment" id="comment" name="comment">
                            {!!old('comment') !!}
                        </input>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" type="submit" id="changeSubmit" class="btn btn-default " data-dismiss="modal">Cập nhật</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</form>
<!-- /.modal -->

@endsection

