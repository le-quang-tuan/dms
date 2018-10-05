@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

@section('script')
{!! Html::script('js/jquery.dataTables.min.js') !!}

<script>
    $(function () {
        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('Tenement.data') !!}',
            columnDefs: [
              { className: "dt-right", "targets": [1] }
            ],
            columns: [
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.name +'<br>' + data.address;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.manager_fee, 0, ".",",") +'<br>' + number_format(data.loss_avg, 0, ".",",");
                    },
                },
                {data: 'comment', name: 'Tel'},
                {data: 'elec', name: 'action', orderable: false, searchable: false},
                {data: 'water', name: 'Contractrate', orderable: false, searchable: false},
                {data: 'gas', name: 'Note', orderable: false, searchable: false},
                {data: 'parking', name: 'AreaName', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        $(addButtonCreateTenement()).insertAfter($("#users-table_length"));
        function addButtonCreateTenement() {
            var html = "";
            html = "<div class='col-md-2'>";
            html+= '<?php echo link_to_action("Tenement\DetailController@create", $title = "Thêm mới", $parameters = array(), $attributes = array("class" => "btn btn-xs btn-primary", "target" => "_blank")); ?>';
            html+= "</div>";
            return html;
        }
    });
</script>
@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thông tin Chung Cư/Khu Căn Hộ và Thiết lập biểu phí</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
<table class="table table-condensed xs table-striped" id="users-table">
    <thead>
      <tr>
        <th rowspan="2" width="410px" class="group-head info">Khu Căn Hộ/ Dự Án<br />Địa Chỉ</th>
        <th width="185px" rowspan="2" class="group-head day">Phí Quản Lý<span class="badge unit">đ/m2</span><br>Hao hụt chia sẻ<span class="badge unit">%</span></th>
        <th width="185px"  rowspan="2" class="group-head period">Ghi Chú</th>
        <th colspan="4" class="group-head day">Biểu Phí</th>
        <th rowspan="2" class="group-head period">Cập Nhật</th>
      </tr>
      <tr>
        <th class="day-sub">Điện<br/><span class="badge unit">kwh</span></th>
        <th class="day-sub">Nước<br/><span class="badge unit">m3</span></th>
        <th class="day-sub">Gas<br/><span class="badge unit">kg</span></th>
        <th class="day-sub">Gửi Xe<br/><span class="badge unit">Tháng</span></th>
      </tr>
    </thead>
</table>
</div>
@endsection

