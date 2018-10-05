@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

<style media="all" type="text/css">
    .alignRight { text-align: right; font-size: 14px }
</style>

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
    $(function () {
        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        var year = $("#year").val();
        var month = $("#month").val();
        
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! url("monthlyfee/anyData/'+ year + month + '") !!}',
            columns: [
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.action +'<br>' + 
                        '<i class="fa fa-map-marker" aria-hidden="true"></i>' + (data.address || '') + '<br>' + 
                        '<i class="fa fa-address-card" aria-hidden="true"></i>' +
                        (data.name || '_______________') + "&nbsp; <i class='fa fa-phone' aria-hidden='true'></i>" + (data.phone || '_______________') + '<br>' + 
                        (data.is_stay || 'Chưa ở');
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var receive_date = "Chưa nhận";
                        if (data.receive_date != "")
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
                {
                    data: null, 
                    sClass: "alignRight",
                    render: function ( data, type, row ) {
                        var money = data.manager_fee +
                                    data.elec_fee +
                                    data.water_fee +
                                    data.gas_fee +
                                    data.parking_fee +
                                    data.service_fee;

                        var paid = data.manager_fee_paid +
                                    data.elec_fee_paid +
                                    data.water_fee_paid +
                                    data.gas_fee_paid +
                                    data.parking_fee_paid +
                                    data.service_fee_paid;

                        return money +'<br>' + paid;
                    },
                },
                {
                    data: null, 
                    sClass: "alignRight",
                    render: function ( data, type, row ) {
                        var fee = (data.manager_fee = data.manager_fee || 0);
                        
                        var paid = (data.manager_fee_paid = data.manager_fee_paid || 0);

                        return fee +'<br>' + paid;
                    },
                },

                {
                    data: null, 
                    sClass: "alignRight",
                    render: function ( data, type, row ) {
                        var fee = (data.elec_fee = data.elec_fee || 0);
                        
                        var paid = (data.elec_fee_paid = data.elec_fee_paid || 0);

                        return fee +'<br>' + paid;
                    },
                },

                {
                    data: null, 
                    sClass: "alignRight",
                    render: function ( data, type, row ) {
                        var fee = (data.water_fee = data.water_fee || 0);
                        
                        var paid = (data.water_fee_paid = data.water_fee_paid || 0);

                        return fee +'<br>' + paid;
                    },
                },
                {
                    data: null, 
                    sClass: "alignRight",
                    render: function ( data, type, row ) {
                        var fee = (data.gas_fee = data.gas_fee || 0);
                        
                        var paid = (data.gas_fee_paid = data.gas_fee_paid || 0);

                        return fee +'<br>' + paid;
                    },
                },
                {
                    data: null, 
                    sClass: "alignRight",
                    render: function ( data, type, row ) {
                        var fee = (data.parking_fee = data.parking_fee || 0);
                        
                        var paid = (data.parking_fee_paid = data.parking_fee_paid || 0);

                        return fee +'<br>' + paid;
                    },
                },
                {
                    data: null, 
                    sClass: "alignRight",
                    render: function ( data, type, row ) {
                        var fee = (data.service_fee = data.service_fee || 0);
                        
                        var paid = (data.service_fee_paid = data.service_fee_paid || 0);

                        return fee +'<br>' + paid;
                    },
                },
                {
                    data: null, 
                    sClass: "alignRight",
                    render: function ( data, type, row ) {
                        var money = data.manager_fee +
                                    data.elec_fee +
                                    data.water_fee +
                                    data.gas_fee +
                                    data.parking_fee +
                                    data.service_fee;

                        var paid = data.manager_fee_paid +
                                    data.elec_fee_paid +
                                    data.water_fee_paid +
                                    data.gas_fee_paid +
                                    data.parking_fee_paid +
                                    data.service_fee_paid;
                        //return money.format();
                        return (money - paid) + "<br>" + 
                        data.payment + "<br>" +
                        data.paymentnotice;
                    },
                },
            ]
        });        
        $('#flatSubmit').click(function() {
            var year = $("#year").val();
            var month = $("#month").val();
            //alert(table.ajax.url);
            table.ajax.url('{!! url("monthlyfee/anyData/'+ year + month + '") !!}').load();
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
        <table class="table table-striped table-bordered table-hover table-condensed">
            <tbody>
                <tr>
                    <td>Năm/tháng<em style='color:red'>(*)</em> </td>
                    <td>
                        <select name="year" id="year">
                        <?php 
                           for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                              echo "<option>$i</option>";
                           }
                        ?>
                        </select>
                        <select name="month" id="month">
                        <?php 
                           for($i = -1 ; $i < 11; $i++){
                              echo "<option>" . date('m', strtotime(" +$i months")) . "</option>";
                           }
                        ?>
                        </select>
                    </td>
                    <td>
                        <button id='flatSubmit' type="button" class="btn btn-primary">Chọn</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th width="410px" class="group-head info">
                        Căn Hộ<br>Số<br>Chủ Hộ<br>Đang ở
                    </th>
                    <th class="group-head day">
                        Diện tích<br>Số nhân khẩu<br>Ngày nhận
                    </th>
                    <th class="group-head day">Phát Sinh<br>Đã Thu</th>
                    <th class="group-head period">Quản Lý</th>
                    <th class="group-head period">Điện</th>
                    <th class="group-head period">Nước</th>
                    <th class="group-head period">Gas</th>
                    <th class="group-head period">Xe Tháng</th>
                    <th class="group-head period">Phí Khác</th>
                    <th class="group-head day">Công Nợ<br>Thu Phí</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

