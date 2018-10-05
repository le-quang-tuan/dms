@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

<style media="all" type="text/css">
    .alignRight { text-align: right; font-size: 14px }
    .numericCol{
        text-align: right;
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
                        return 'CongNoToanPhanDenThang_' + month + year;
                    },
                    text:'Xuất file '+'<i class="fa fa-file-excel-o fa-fw"></i>'
                    ,exportOptions: {
                        columns: [ 0, ':visible' ]
                    }
                },
                {extend:'colvis', text:'Hiển thị dữ liệu'+'<i class="fa fa-angle-down"></i>'}
            ],
            ajax: '{!! url("monthlyreport/alldept/anyData") !!}',
            columns: [
                {data: 'flat_code', name: 'flat_code'},
                {data: 'address', name: 'address'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'area', name: 'area'},
                {data: 'persons', name: 'persons'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.manager_fee, 0, ".",",");
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.elec_fee, 0, ".",",");
                    },
                    "bVisible": false
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.water_fee, 0, ".",",");
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.gas_fee, 0, ".",",");
                    },
                    "bVisible": false
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.parking_fee, 0, ".",",");
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.service_fee, 0, ".",",");
                    },
                    "bVisible": false
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.monthfee, 0, ".",",");
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.fee_skip, 0, ".",",");
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.paidfee, 0, ".",",");
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.deptfee, 0, ".",",");
                    },
                },
                {data: 'elec_type', name: 'elec_type'
                    ,"bVisible": false
                },
                {data: 'elec_mount', name: 'elec_mount'
                    ,"bVisible": false
                },

                {data: 'water_type', name: 'water_type'},
                {data: 'water_mount', name: 'water_mount'},

                {data: 'gas_type', name: 'gas_type'
                    ,"bVisible": false
                },
                {data: 'gas_mount', name: 'gas_mount'
                    ,"bVisible": false
                },
            ]
        }); 

        $('#flatSubmit').click(function() {
            var year = $("#year").val();
            var month = $("#month").val();
            table.clear().draw();
            table.ajax.url('{!! url("monthlyreport/alldept/anyData/?to_month='+ year + month + '") !!}').load();
        }); 

        $('select#month').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var year = $("#year").val();
            var month = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("monthlyreport/alldept/anyData/?to_month='+ year + month + '") !!}').load();
        });

        $('select#year').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var month = $("#month").val();
            var year = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("monthlyreport/alldept/anyData/?to_month='+ year + month + '") !!}').load();
        });
    });

</script>
@endsection

@section('title')
    Công Nợ Toàn Phần
@stop

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
        <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td>Năm/tháng<em style='color:red'>(*)</em>
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
                    <th class="group-head day">
                        Mã Căn Hộ
                    </th>
                    <th class="group-head day">
                        Căn Hộ
                    </th>
                    <th class="group-head day">
                        Chủ Hộ
                    </th>
                    <th class="group-head day">
                        Số Điện Thoại
                    </th>
                    <th class="group-head day">
                        Diện Tích
                    </th>
                    <th class="group-head day">
                        Số Người
                    </th>
                    <th class="group-head day">
                        Phí Quản Lý
                    </th>
                    <th class="group-head day">
                        Phí Điện Sử Dụng
                    </th>
                    <th class="group-head day">
                        Phí Nước Sinh Hoạt
                    </th>
                    <th class="group-head day">
                        Phí Gas Sử Dụng
                    </th>
                    <th class="group-head day">
                        Phí Giữ Xe
                    </th>
                    <th class="group-head day">
                        Phí Khác
                    </th>
                    <th class="group-head day">
                        Số Tiền Phải Trả
                    </th>
                    <th class="group-head day">
                        Phí Không Thu
                    </th>
                    <th class="group-head day">
                        Số Tiền Đã Trả
                    </th>
                    <th class="group-head day">
                        Số Tiền Còn Nợ
                    </th>
                    <th class="group-head day">
                        Sử Dụng Điện
                    </th>
                    <th class="group-head day">
                        Tiêu Thụ
                    </th>

                    <th class="group-head day">
                        Sử Dụng Nước
                    </th>
                    <th class="group-head day">
                        Tiêu Thụ
                    </th>

                    <th class="group-head day">
                        Sử Dụng Gas
                    </th>
                    <th class="group-head day">
                        Tiêu Thụ
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

