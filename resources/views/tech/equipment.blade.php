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
            ajax: '{!! route('Equipment.data') !!}',
            columnDefs: [
              { className: "dt-right", "targets": [2] }
            ],
            columns: [
                {data: 'equipment_code', name: 'parking_code'},
                {data: 'name', name: 'name'},
                {data: 'producer', name: 'producer'},
                {data: 'label', name: 'label'},
                {data: 'model', name: 'model'},
                {data: 'specification', name: 'specification'},
                {data: 'area', name: 'area'},
                {data: 'comment', name: 'Comment'},
                {data: 'action', name: 'Action'},
                {data: 'maintenance', name: 'maintenance'},
            ]
        });
        $(addButtonCreateTenement()).insertAfter($("#users-table_length"));
        function addButtonCreateTenement() {
            var html = "";
            html = "<div class='col-md-2'>";
            html+= '<?php echo link_to_action("Tech\EquipmentDetailController@create", $title = "Tạo Mới Máy Móc - Thiết Bị - Vật Tư", $parameters = array(), $attributes = array("class" => "btn btn-xs btn-primary")); ?>';
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
        <h2>Danh Sách Máy Móc - Thiết Bị - Vật Tư</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="info">Mã Thiết Bị</th>
                    <th class="info">Tên</th>
                    <th class="info">Nhà cung cấp</th>
                    <th class="info">Nhãn hiệu</th>
                    <th class="info">Model</th>
                    <th class="info">Mô tả thông số</th>
                    <th class="info">Khu vực</th>
                    <th class="info">Ghi Chú</th>
                    <th class="info">Cập Nhật</th>
                    <th class="info">Lên Kế Hoạch</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

