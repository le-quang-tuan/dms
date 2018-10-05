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
            columnDefs: [
              { className: "dt-right", "targets": [3,4,5] },
              { className: "dt-nowrap", "targets": [0,1] },
              { className: "dt-center", "targets": [2,6] }
            ],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    filename: function(){
                        var month = $("#month").val();
                        var year = $("#year").val();
                        return 'ChiSoNuocThang_' + month + year;
                    },
                }
            ],
            ajax: '{!! url("flat/all/elec/anyData/'+ year + month + '") !!}',
            columns: [
                { data: 'address' , name: 'address' },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.action +'<br>' + 
                        '<i class="fa fa-map-marker" aria-hidden="true"></i>' + (data.address || '') + '<br>' + 
                        '<i class="fa fa-address-card" aria-hidden="true"></i>' +
                        (data.name || '_______________') + '<br>' +  "<i class='fa fa-phone' aria-hidden='true'></i>" + (data.phone || '_______________') + '<br>' + 
                        (data.is_stay || 'Chưa ở');
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var receive_date = "Chưa nhận";
                        if (data.receive_date != "" && data.receive_date != null)
                        {
                            var dateString  = data.receive_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            receive_date = day + '/' + month + '/' + year;
                        }

                        return (data.area || '0') +'<span class="badge unit">m2</span> <br>' + 
                        (data.persons || '0') + '<i class="fa fa-user-o" aria-hidden="true"></i><br>' + 
                        receive_date;
                    },
                },
                { data: 'year_month' , name: 'year_month' },
                { data: 'old_index' , name: 'old_index' },
                { data: 'new_index' , name: 'new_index' },
                { data: 'comment' , name: 'comment' },
                @role('admin|manager|moderator')
                    { data: 'change', name: 'change', orderable: false, searchable: false}
                @endrole
            ]
        });        
        $('#users-table').on('click', 'button', function () {
            var bill_year = $("#year").val();
            var bill_month = $("#month").val();

            var date = new Date(bill_year + '-' + bill_month + '-' + '1');
            date.setMonth(bill_month - 2);

            var preMonth = (1 + date.getMonth()).toString();
            preMonth = preMonth.length > 1 ? preMonth : '0' + preMonth;

            var firstDay = ('01/' + preMonth + '/' + date.getFullYear());

            var preLastdayMonth = new Date(date.getFullYear(), preMonth,0);

            var lastDay = (preLastdayMonth.getDate() + '/' + preMonth + '/' + preLastdayMonth.getFullYear());

            if ($(this).attr("date_from") != ''){
                var dateString  = $(this).attr("date_from");
                var year        = dateString.substring(0,4);
                var month       = dateString.substring(4,6);
                var day         = dateString.substring(6,8);

                firstDay = day + '/' + month + '/' + year;
            }
            if ($(this).attr("date_to") != ''){
                var dateString  = $(this).attr("date_to");
                var year        = dateString.substring(0,4);
                var month       = dateString.substring(4,6);
                var day         = dateString.substring(6,8);

                lastDay = day + '/' + month + '/' + year;
            }
            var comment = "Chỉ số nước sử dụng tháng " + preMonth + "/" + preLastdayMonth.getFullYear();
            if ($(this).attr("comment") != ''){
                comment = $(this).attr("comment");
            }

            var table = $('#users-table').DataTable();
            $("#year_month").val(bill_month + '/' + bill_year);
            $("#address").val($(this).attr("address"));
            $("#name").val($(this).attr("name"));
            $("#flat_id").val($(this).attr("flat_id"));
            $("#bill_year").val(bill_year);
            $("#bill_month").val(bill_month);
            $("#comment").val(comment);
            $("#old_index").val($(this).attr("old_index"));
            $("#new_index").val($(this).attr("new_index"));
            $("#date_from").val(firstDay);
            $("#date_to").val(lastDay);

            $('#DescModal').modal("show");
        });

        $('#flatSubmit').click(function() {
            var year = $("#year").val();
            var month = $("#month").val();
            table.clear().draw();
            table.ajax.url('{!! url("flat/all/elec/anyData/'+ year + month + '") !!}').load();
        });

        $("#changeSubmit").click(function(){
            var searchText = $('#users-table_filter input').val();
            var year_month = $("#year_month").val();
            var address = $("#address").val();
            var name = $("#name").val();
            var flat_id = $("#flat_id").val();
            var bill_year = $("#bill_year").val();
            var bill_month = $("#bill_month").val();
            var comment = $("#comment").val();
            var old_index = $("#old_index").val();
            var new_index = $("#new_index").val();
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();

            bootbox.confirm("Thông tin sẽ được cập nhật?", function(result) {
                if(result){
                    $('#loading').show();

                    $.ajax({
                        type: "POST",
                        cache: false,
                        url : "{!! route('Elec.exex_change') !!}",
                        data: { 
                            "address" : address,
                            "old_index" : old_index,
                            "new_index" : new_index,
                            "date_from" : date_from,
                            "date_to" : date_to,
                            "comment" : comment,
                            "bill_year" : bill_year,
                            "bill_month" : bill_month,
                            "flat_id" : flat_id,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(data) {
                            table.clear().draw();
                            table.ajax.url('{!! url("flat/all/elec/anyData/'+ year + month + '") !!}').load();
                            table.search(searchText).draw();
                            $('#loading').hide();
                        }
                    })
                }
            });
            table.search(searchText).draw();
        });

        $('select#month').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var year = $("#year").val();
            var month = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("flat/all/elec/anyData/'+ year + month + '") !!}').load();
        });

        $('select#year').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var month = $("#month").val();
            var year = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("flat/all/elec/anyData/'+ year + month + '") !!}').load();
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
        <h2>Danh sách Căn hộ</h2>
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
                    <th width="410px" class="group-head info">
                        Căn Hộ
                    </th>
                    <th width="410px" class="group-head info">
                        ID<br>Chủ Hộ<br>Đang ở
                    </th>
                    <th class="group-head day">
                        Diện tích<br>Số nhân khẩu<br>Ngày nhận
                    </th>
                    
                    <th class="group-head period">Tháng/Năm</th>
                    <th class="group-head period">Chỉ Số Cũ</th>
                    <th class="group-head period">Chỉ Số Mới</th>
                    <th class="group-head day">Ghi Chú</th>
                    @role('admin|manager|moderator')
                        <th class="group-head day">Cập Nhật</th>
                    @endrole
                </tr>
            </thead>
        </table>
    </div>
</div>

<form id='frmChange' action="{!! route('Elec.exex_change') !!}" method="POST" role="form" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="modal fade" id="DescModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                     <h3 class="modal-title">Cập nhật chỉ số nước sử dụng</h3>

                </div>
                <div class="modal-body">
                
                <div class="row dataTable">
                    <div class="col-md-3">
                        <label class="control-label">Căn hộ</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" maxlength="50" id="address" name="adress">
                    </div>
                </div>

                <br>

                <div class="row dataTable">
                    <div class="col-md-3">
                        <label class="control-label">Chủ Hộ</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" maxlength="50" id="name" name="name">
                    </div>
                </div>

                <br>

                <div class="row dataTable">
                    <div class="col-md-3">
                        <label class="control-label">Năm Tháng</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" maxlength="30" id="year_month" name="year_month">
                    </div>
                    <input type="hidden" id="flat_id" name="flat_id">
                    <input type="hidden" id="bill_year" name="bill_year">
                    <input type="hidden" id="bill_month" name="bill_month">
                </div>

                <div class="row dataTable">
                    <div class="col-md-3">
                        <label class="control-label">Thời gian</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="date_to" name="date_to">
                    </div>
                </div>

                <div class="row dataTable">
                    <div class="col-md-3">
                        <label class="control-label">Chỉ Số</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="old_index" name="old_index">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="new_index" name="new_index">
                    </div>
                </div>

                <div class="row dataTable">
                    <div class="col-md-3">
                        <label class="control-label">Ghi chú</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" maxlength="30" id="comment" name="comment">
                    </div>
                </div>

                <br>
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

