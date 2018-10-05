@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

<style media="all" type="text/css">
    .alignRight { text-align: right; font-size: 14px }
    .numericCol{
        text-align: right;
    }

    .loading {
        /*display: block;*/
        display : block;
        position : fixed;
        z-index: 100;
                background-image:url("{!! url('img/gears.gif') !!}");
        background-color:#666;
        opacity : 0.4;
        background-repeat : no-repeat;
        background-position : center;
        left : 0;
        bottom : 0;
        right : 0;
        top : 0;
    }
    /* Hide all the children of the 'loading' element */
    .loading * {
        display: none;  
    }
    .table tbody td {
      vertical-align: top;
    }
</style>

@section('script')
<script>
    $(function () {
        callDatePicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        var year = $("#year").val();
        var month = $("#month").val();
        
        var table = $('#users-table').DataTable({
            // scrollY: 400,
            scrollX: true,

            processing: true,
            serverSide: false,
            columnDefs: [
              { className: "dt-right", "targets": [3,4,5] },
              { className: "dt-nowrap", "targets": [0,1] },
              { className: "dt-center", "targets": [2,6] },
            ],

            ajax: '{!! url("monthlyfee/anyData/'+ year + month + '") !!}',
            columns: [
                @role('admin')
                    {data: 'id', name: 'id'},
                @endrole
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
                        
                        return data.action +'&nbsp;' + 
                        (data.area || '0') +'<span class="badge unit">m2</span> &nbsp;' + receive_date + '<br>' +
                        (data.persons || '0') + '<i class="fa fa-user-o" aria-hidden="true"></i>' + '<br>' +
                        (data.name || '_______________') + '<br>' +  "<i class='fa fa-phone' aria-hidden='true'></i>" + (data.phone || '_______________') + '<br>' + 
                        (data.is_stay || 'Chưa ở') + '<br>' +
                        data.elec + '&nbsp;' + data.water + '&nbsp;'+ data.gas + '&nbsp;' + data.service + '&nbsp;' + data.vehicle;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return 'Quản Lý' + "<br>" +
                        'Điện' + "<br>" +
                        'Nước' + "<br>" +
                        'Gas' + "<br>" +
                        'Xe Tháng' + "<br>" +
                        'Phí Khác' +'<br>' + '&nbsp;'+'<br>' + '&nbsp;';
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var m_fee = (data.manager_fee = data.manager_fee || 0);

                        var e_fee = (data.elec_fee = data.elec_fee || 0);

                        var w_fee = (data.water_fee = data.water_fee || 0);

                        var g_fee = (data.gas_fee = data.gas_fee || 0);

                        var p_fee = (data.parking_fee = data.parking_fee || 0);

                        var s_fee = (data.service_fee = data.service_fee || 0);
                        
                        return number_format(m_fee, 0, ".",",") +'<br>' + 
                            number_format(e_fee, 0, ".",",") +'<br>' + 
                            number_format(w_fee, 0, ".",",") +'<br>' + 
                            number_format(g_fee, 0, ".",",") +'<br>' + 
                            number_format(p_fee, 0, ".",",") +'<br>' + 
                            number_format(s_fee, 0, ".",",") +'<br>' + '&nbsp;'+'<br>' + '&nbsp;';
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var m_fee = (data.manager_fee_paid = data.manager_fee_paid || 0);

                        var e_fee = (data.elec_fee_paid = data.elec_fee_paid || 0);

                        var w_fee = (data.water_fee_paid = data.water_fee_paid || 0);

                        var g_fee = (data.gas_fee_paid = data.gas_fee_paid || 0);

                        var p_fee = (data.parking_fee_paid = data.parking_fee_paid || 0);

                        var s_fee = (data.service_fee_paid = data.service_fee_paid || 0);
                        
                        return number_format(m_fee, 0, ".",",") +'<br>' + 
                            number_format(e_fee, 0, ".",",") +'<br>' + 
                            number_format(w_fee, 0, ".",",") +'<br>' + 
                            number_format(g_fee, 0, ".",",") +'<br>' + 
                            number_format(p_fee, 0, ".",",") +'<br>' + 
                            number_format(s_fee, 0, ".",",") +'<br>' +
                            data.paid;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var m_fee = (data.manager_fee_skip = data.manager_fee_skip || 0);

                        var e_fee = (data.elec_fee_skip = data.elec_fee_skip || 0);

                        var w_fee = (data.water_fee_skip = data.water_fee_skip || 0);

                        var g_fee = (data.gas_fee_skip = data.gas_fee_skip || 0);

                        var p_fee = (data.parking_fee_skip = data.parking_fee_skip || 0);

                        var s_fee = (data.service_fee_skip = data.service_fee_skip || 0);
                        
                        return number_format(m_fee, 0, ".",",") +'<br>' + 
                            number_format(e_fee, 0, ".",",") +'<br>' + 
                            number_format(w_fee, 0, ".",",") +'<br>' + 
                            number_format(g_fee, 0, ".",",") +'<br>' + 
                            number_format(p_fee, 0, ".",",") +'<br>' + 
                            number_format(s_fee, 0, ".",",") +'<br>' +
                            data.deptskip;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var money = parseInt(data.manager_fee) +
                                    parseInt(data.elec_fee) +
                                    parseInt(data.water_fee) +
                                    parseInt(data.gas_fee) +
                                    parseInt(data.parking_fee) +
                                    parseInt(data.service_fee);

                        var paid =  parseInt(data.manager_fee_paid) +
                                    parseInt(data.elec_fee_paid) +
                                    parseInt(data.water_fee_paid) +
                                    parseInt(data.gas_fee_paid) +
                                    parseInt(data.parking_fee_paid) +
                                    parseInt(data.service_fee_paid);

                        var skip =  parseInt(data.manager_fee_skip) +
                                    parseInt(data.elec_fee_skip) +
                                    parseInt(data.water_fee_skip) +
                                    parseInt(data.gas_fee_skip) +
                                    parseInt(data.parking_fee_skip) +
                                    parseInt(data.service_fee_skip);

                        var tmp = 
                        '<br>' +
                        number_format(money, 0, ".",",") +'<br>' + '-' + 
                        number_format(paid, 0, ".",",") +'<br>' + '-' +
                        number_format(skip, 0, ".",",") +'<br>' + 
                        '-------------' +'<br>' + (number_format(money - paid - skip, 0, ".",",")) + "<br>";
                        @role('admin|manager|moderator')
                            tmp += data.recalculate;
                        @endrole
                        return tmp;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.manager_dept, 0, ".",",") +'<br>' + 
                            number_format(data.elec_dept, 0, ".",",") +'<br>' + 
                            number_format(data.water_dept, 0, ".",",") +'<br>' + 
                            number_format(data.gas_dept, 0, ".",",") +'<br>' + 
                            number_format(data.parking_dept, 0, ".",",") +'<br>' + 
                            number_format(data.service_dept, 0, ".",",") +'<br>' +
                            '-------------' +'<br>' + (number_format(parseInt(data.manager_dept) + parseInt(data.elec_dept) + parseInt(data.water_dept) + parseInt(data.gas_dept) + parseInt(data.parking_dept) + parseInt(data.service_dept), 0, ".",",")) + "<br>";
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var tmp = data.paymentnotice + "<br>" +
                            data.payment + "<br>" + "<br>";

                        @role('admin|manager|moderator')
                            tmp += data.paidnew + "<br>";
                            tmp += data.deptskipnew;
                        @endrole
                        return tmp;
                        // data.payment + "<br>" + "<br>" + "<br>" + '<a href="#" onclick="openPopup()">test</a>';
                    },
                },
            ],
            paginatorLocation: ['header', 'footer']
        });        
        $('#users-table').on('click', '.recalculate', function () {
            var year = $("#year").val();
            var month = $("#month").val();
            var table = $('#users-table').DataTable();
            $("#year_month").val(month + '/' + year);
            $("#address").val($(this).attr("address"));
            $("#name").val($(this).attr("name"));
            $("#flat_id").val($(this).attr("id"));
            $("#recal_year").val(year);
            $("#recal_month").val(month);

            $('#DescModal').modal("show");
        });

        $('#users-table').on('click', '.elec', function () {
            var year = $("#year").val();
            var month = $("#month").val();
            window.open("flat/elec/" + $(this).attr("id") + "?year_month=" + year + month);
        });

        $('#users-table').on('click', '.water', function () {
            var year = $("#year").val();
            var month = $("#month").val();
            window.open("flat/water/" + $(this).attr("id") + "?year_month=" + year + month);
        });

        $('#users-table').on('click', '.gas', function () {
            var year = $("#year").val();
            var month = $("#month").val();
            window.open("flat/gas/" + $(this).attr("id") + "?year_month=" + year + month);
        });

        $('#users-table').on('click', '.service', function () {
            var year = $("#year").val();
            var month = $("#month").val();
            window.open("flat/service/" + $(this).attr("id") + "?year_month=" + year + month);
        });

        $('#users-table').on('click', '.vehicle', function () {
            var year = $("#year").val();
            var month = $("#month").val();
            window.open("flat/vehicle/" + $(this).attr("id"));
        });

        $('#flatSubmit').click(function() {
            var searchText = $('#users-table_filter input').val();
            var year = $("#year").val();
            var month = $("#month").val();
            table.clear().draw();
            table.ajax.url('{!! url("monthlyfee/anyData/'+ year + month + '") !!}').load();
            table.search(searchText).draw();
        });

        $('#flatStatus').click(function() {
            window.open('{!! url("monthlyfee/status") !!}');
        });
        $('#paybillall').click(function() {
            bootbox.confirm("Phiếu thu Khu Căn Hộ/Chung Cư sẽ được tạo. <br>Thời gian tạo file phụ thuộc vào số lượng căn hộ. <br> Thời gian tạo phiếu thu cho 1 căn hộ mất khoảng 2 giây.", function(result) {
                if(result){
                    var year = $("#year").val();
                    var month = $("#month").val();
                    window.open("report/paybillall/" + year + month + "/all");
                }
            });
        });

        $('#paymentnotice').click(function() {
            bootbox.confirm("Thông báo phí Khu Căn Hộ/Chung Cư sẽ được tạo. <br>Thời gian tạo file phụ thuộc vào số lượng căn hộ. <br> Thời gian tạo phiếu thu cho 1 căn hộ mất khoảng 2 giây.", function(result) {
                if(result){
                    var year = $("#year").val();
                    var month = $("#month").val();
                    window.open("report/paymentnotice/" + year + month);                   
                }
            });            
        });

        $('#filepaybillall').click(function() {
            var year = $("#year").val();
            var month = $("#month").val();
            window.open("report/paybill/" + year + month);
        });

        $('#filepaymentnotice').click(function() {
            var year = $("#year").val();
            var month = $("#month").val();
            window.open("report/paymentnoticefiles/" + year + month);
        });

        $("#recalSubmit").click(function(){
            var searchText = $('#users-table_filter input').val();
            var year = $("#year").val();
            var month = $("#month").val();
            var flat_id = $("#flat_id").val();
            bootbox.confirm("Thông tin sẽ được thêm mới?", function(result) {
                if(result){
                    //$("#frmRecal").submit();
                    $('#loading').show();


                    $.ajax({
                        type: "POST",
                        cache: false,
                        url : "{!! route('Paymonth.exex_recalculate') !!}",
                        data: { 
                            "recal_year" : year,
                            "recal_month" : month,
                            "flat_id" : flat_id,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(data) {
                            table.clear().draw();
                            table.ajax.url('{!! url("monthlyfee/anyData/'+ year + month + '") !!}').load();
                            table.search(searchText).draw();
                $('#loading').hide();

                        }
                    })

                    // table.clear().draw();
                    // table.ajax.url('{!! url("monthlyfee/anyData/'+ year + month + '") !!}').load();
                    // table.search(searchText).draw();
                }
            });
            table.search(searchText).draw();

        });

        $('select#month').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var year = $("#year").val();
            var month = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("monthlyfee/anyData/'+ year + month + '") !!}').load();
        });

        $('select#year').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var month = $("#month").val();
            var year = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("monthlyfee/anyData/'+ year + month + '") !!}').load();
        });

        $('#loading')
            .hide()  // hide it initially
            .ajaxStart(function() {
                $(this).show();
            })
            .ajaxStop(function() {
                $(this).hide();
            })
        ;

    });

</script>
@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thông Tin Phí Phát Sinh Tháng</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
    <div id="loading" class="loading"><b>Dữ Liệu đang được cập nhật, xin chờ trong giây lát...</b></div>   
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
                        <button id='flatStatus' type="button" class="btn btn-warning">Tình Trạng Công Nợ</button>
                        &nbsp;
                        <a id='paymentnotice' class="btn btn-primary">Tạo Thông Báo Phí</a>
                        <a id='filepaymentnotice' class="btn btn-info">Danh Sách File</a>
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        <a id="paybillall" class="btn btn-primary">Tạo Phiếu Thu</a>
                        <a id="filepaybillall" class="btn btn-info">Danh Sách File</a>

                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                @role('admin')
                    <th width="20px" class="group-head info">
                        ID
                    </th>
                @endrole
                    <th width="410px" class="group-head info">
                        Căn Hộ/ Diện tích/ Ngày Nhận<br>Số định mức nước<br>Chủ Hộ<br>Điện thoại<br>Đang ở
                    </th>
                    <th class="group-head period">Phí Tháng</th>
                    <th class="group-head period">Tiền<br>Phát Sinh</th>
                    <th class="group-head period">
                    Phí Đã Thu
                    </th>
                    <th class="group-head day">
                    Phí Không Thu
                    </th>
                    <th class="group-head period">
                        Phát Sinh Tháng<br>
                        - Đã Thu<br>
                        - Phí Không Thu<br>
                        --------------<br>
                        Công Nợ Tháng</th>
                    </th>
                    <th class="group-head day">
                        Tổng Công Nợ</th>
                    <th class="group-head day">Xuất File<br>Thông Báo Phí<br>Phiếu Thu</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<form id='frmRecal' action="{!! route('Paymonth.exex_recalculate') !!}" method="POST" role="form" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="modal fade" id="DescModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                     <h3 class="modal-title">Kết sổ</h3>

                </div>
                <div class="modal-body">
                
                <div class="row dataTable">
                    <div class="col-md-4">
                        <label class="control-label">Căn hộ</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" maxlength="50" id="address" name="adress">
                    </div>
                </div>

                <br>

                <div class="row dataTable">
                    <div class="col-md-4">
                        <label class="control-label">Chủ Hộ</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" maxlength="50" id="name" name="name">
                    </div>
                </div>

                <br>

                <div class="row dataTable">
                    <div class="col-md-4">
                        <label class="control-label">Năm Tháng</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" maxlength="30" id="year_month" name="year_month">
                    </div>
                    <input type="hidden" id="flat_id" name="flat_id">
                    <input type="hidden" id="recal_year" name="recal_year">
                    <input type="hidden" id="recal_month" name="recal_month">
                </div>

                <br>
                </div>
                <div class="modal-footer">
                    <button type="button" type="submit" id="recalSubmit" class="btn btn-default " data-dismiss="modal">Kết Sổ</button>
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