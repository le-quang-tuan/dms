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
            ajax: '{!! route('Producer.data') !!}',
            columnDefs: [
              { className: "dt-right", "targets": [2] }
            ],
            columns: [
                {data: 'producer_code', name: 'parking_code'},
                {data: 'name', name: 'name'},
                {data: 'address', name: 'address'},
                {data: 'contact_name', name: 'contact_name'},
                {data: 'tel', name: 'tel'},
                {data: 'email', name: 'email'},
                {data: 'comment', name: 'Comment'},
                {data: 'action', name: 'Action'},
            ]
        });
        $(addButtonCreateTenement()).insertAfter($("#users-table_length"));
        function addButtonCreateTenement() {
            var html = "";
            html = "<div class='col-md-2'>";
            html+= '<?php echo link_to_action("Tech\ProducerDetailController@create", $title = "Tạo Mới Nhà Cung Cấp - Sản Xuất", $parameters = array(), $attributes = array("class" => "btn btn-xs btn-primary")); ?>';
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
        <h2>Danh Sách Nhà Cung Cấp - Sản Xuất</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="info">Mã Nhà Cung Cấp</th>
                    <th class="info">Tên</th>
                    <th class="info">Địa Chỉ</th>
                    <th class="info">Người Liên Hệ</th>
                    <th class="info">Số Điện Thoại</th>
                    <th class="info">Email</th>
                    <th class="info">Ghi Chú</th>
                    <th class="info">Cập Nhật</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

