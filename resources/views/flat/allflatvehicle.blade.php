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
            ajax: '{!! url("flat/all/vehicle/anyData/") !!}',
            columns: [
                {   data: 'address' , name: 'address' },
                {   data: 'owner' , name: 'owner' },
                {   data: 'number_plate' , name: 'number_plate' },
                {   data: 'vehicle_type' , name: 'vehicle_type' },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var begin_contract_date = "";
                        if (data.begin_contract_date != "" && data.begin_contract_date != null)
                        {
                            var dateString  = data.begin_contract_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            begin_contract_date = day + '/' + month + '/' + year;
                        }

                        return begin_contract_date;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var end_contract_date = "";
                        if (data.end_contract_date != "" && data.end_contract_date != null)
                        {
                            var dateString  = data.end_contract_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            end_contract_date = day + '/' + month + '/' + year;
                        }

                        return end_contract_date;
                    },
                },
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
                url : '{!! url("flat/vehicle/'+ id +'/show") !!}',
                data: { id : id },
                success: function(data) {
                    var obj = $.parseJSON(data);

                    var begin_contract_date = obj[0]['begin_contract_date'];
                    if (begin_contract_date != "" && begin_contract_date != null)
                    {
                        var dateString  = begin_contract_date;
                        var year        = dateString.substring(0,4);
                        var month       = dateString.substring(4,6);
                        var day         = dateString.substring(6,8);

                        begin_contract_date = day + '/' + month + '/' + year;
                    }

                    var end_contract_date = obj[0]['end_contract_date'];
                    if (end_contract_date != "" && end_contract_date != null)
                    {
                        var dateString  = end_contract_date;
                        var year        = dateString.substring(0,4);
                        var month       = dateString.substring(4,6);
                        var day         = dateString.substring(6,8);

                        end_contract_date = day + '/' + month + '/' + year;
                    }

                    $("#address").val(obj[0]['address']);
                    $("#id").val(id);
                    $("#owner").val(obj[0]['owner']);
                    $("#number_plate").val(obj[0]['number_plate']);
                    $("#name").val(obj[0]['name']);
                    $("#maker").val(obj[0]['maker']);
                    $("#label").val(obj[0]['label']);
                    $("#color").val(obj[0]['color']);
                    $("#activation").prop('checked', false);
                    $("#vehicle_type_id").val(obj[0]['vehicle_type_id']);
                    $("#begin_contract_date").val(begin_contract_date);
                    $("#end_contract_date").val(end_contract_date);
                    $("#comment").val(obj[0]['comment']);
                }
            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
            $('#DescModal').modal("show");
        });

        $('#flatSubmit').click(function() {
            table.clear().draw();
            table.ajax.url('{!! url("flat/all/vehicle/anyData/") !!}').load();
        });

        $("#changeSubmit").click(function(){
            var searchText = $('#users-table_filter input').val();
            var year = $("#year").val();
            var month = $("#month").val();
            var flat_id = $("#flat_id").val();

            var number_plate = $("#number_plate").val();
            var id = $("#id").val();
            var label = $("#label").val();
            var color = $("#color").val();
            var maker = $("#maker").val();
            var name = $("#name").val();
            var vehicle_type_id = $("#vehicle_type_id").val();
            var begin_contract_date = $("#begin_contract_date").val();
            var end_contract_date = $("#end_contract_date").val();
            var comment = $("#comment").val();

            var activation = 1;
            if ($("#activation").is(":checked") == true){
                activation = 0
            }

            bootbox.confirm("Thông tin sẽ được cập nhật?", function(result) {
                if(result){
                    $('#loading').show();

                    $.ajax({
                        type: "POST",
                        cache: false,
                        url : "{!! route('AllVehicle.exex_change') !!}",
                        data: { 
                            "number_plate" : number_plate,
                            "id" : id,
                            "label" : label,
                            "maker" : maker,
                            "color" : color,
                            "activation" : activation,
                            "name" : name,
                            "vehicle_type_id" : vehicle_type_id,
                            "begin_contract_date" : begin_contract_date,
                            "end_contract_date" : end_contract_date,
                            "comment" : comment,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(data) {
                            table.clear().draw();
                            table.ajax.url('{!! url("flat/all/vehicle/anyData/") !!}').load();
                            table.search(searchText).draw();
                            $('#loading').hide();
                        }
                    })
                }
            });
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
        <h2>Danh sách Căn hộ</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <div class="row dataTable">
            <div class="col-md-8">        
                <button id='flatSubmit' type="button" class="btn btn-primary">Làm Mới Dữ Liệu</button>
            </div>
        </div>

        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="group-head info">Căn Hộ</th>
                    <th width="300px" class="group-head info">Chủ Hộ</th>
                    <th width="100px" class="group-head period">Số xe</th>
                    <th width="200px" class="group-head period">Xe</th>
                    <th width="100px" class="group-head period">Ngày gửi</th>
                    <th width="100px" class="group-head period">Ngày kết thúc</th>
                    <th width="100px" class="group-head day">Ghi Chú</th>
                    @role('admin|manager|moderator')
                        <th class="group-head day">Cập Nhật</th>
                    @endrole
                </tr>
            </thead>
        </table>
    </div>
</div>

<form id='frmChange' action="{!! route('AllVehicle.exex_change') !!}" method="POST" role="form" method="post">
    <input type="hidden" id="id" name="id">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="modal fade" id="DescModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                     <h3 class="modal-title">Cập nhật Thông tin xe gửi</h3>
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
                    <div class="col-md-2">Chủ xe </div>
                    <div class="col-md-9">
                        <input type="text" value="{!! old('name') !!}" id="name" name="name" size="35%">
                    </div>
                </div>
                <br>

                <div class="row dataTable">
                    <div class="col-md-2">Biển số </div>
                    <div class="col-md-3">
                        <input type="text" value="{!! old('number_plate') !!}" id="number_plate" name="number_plate" size="15%">
                    </div>

                    <div class="col-md-2">Hiệu xe </div>
                    <div class="col-md-3">
                        <input type="text" value="{!! old('label') !!}" id="label" name="label" size="15%">
                    </div>

                </div>
                <br>
                
                <div class="row dataTable">
                    <div class="col-md-2">Hãng xe </div>
                    <div class="col-md-3">
                        <input type="text" value="{!! old('maker') !!}" id="maker" name="maker" size="15%">
                    </div>

                    <div class="col-md-2">Màu xe </div>
                    <div class="col-md-3">
                        <input type="text" value="{!! old('color') !!}" id="color" name="color" size="15%">
                    </div>
                </div>
                <br>
                
                <div class="row dataTable">
                    <div class="col-md-2">Hợp đồng</div>

                    <div class="col-md-10"><input class="date-picker" type="text" value="{!! old('begin_contract_date') !!}" id="begin_contract_date" name="begin_contract_date"  size="15%">~<input class="date-picker" type="text" value="{!! old('end_contract_date') !!}" id="end_contract_date" name="end_contract_date"  size="15%">

                    Hủy xe <input type="checkbox" name="activation" id="activation">
                    </div>
                </div>
                <br>
                
                <div class="row dataTable">
                    <div class="col-md-2">Loại Xe</div>

                    <div class="col-md-9">
                        <?php 
                            echo '<select name="vehicle_type_id" id="vehicle_type_id">';
                                foreach($parking_tariffs as $parking_type) {
                                    echo '<option value="'.$parking_type->id.'">';
                                
                                    echo $parking_type->name;
                                    echo '</option>';
                                }
                            echo '</select>';
                        ?>
                    </div>
                </div>
                <br>
                
                <div class="row dataTable">
                    <div class="col-md-2">Ghi chú</div>
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

