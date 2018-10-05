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
            ajax: '{!! route('TenementParking.data') !!}',
            columnDefs: [
              { className: "dt-right", "targets": [2] }
            ],
            columns: [
                {data: 'parking_code', name: 'parking_code'},
                {data: 'name', name: 'Name'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.price, 0, ".",",");
                    },
                },
                {data: 'comment', name: 'Comment'},
                {data: 'action', name: 'Action'},
            ]
        });
        @role('admin|manager|moderator')
            $(addButtonCreateTenement()).insertAfter($("#users-table_length"));
            function addButtonCreateTenement() {
                var html = "";
                html = "<div class='col-md-2'>";
                html+= '<?php echo link_to_action("Tenement\ParkingDetailController@create", $title = "Tạo Mới Biểu Phí", $parameters = array(), $attributes = array("class" => "btn btn-xs btn-primary")); ?>';
                html+= "</div>";
                return html;
            }
        @endrole
    });
</script>
@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thiết Lập Biểu Phí Điện</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="info">Mã Biểu Phí</th>
                    <th class="info">Loại xe</th>
                    <th class="info">Đơn giá</th>
                    <th class="info">Ghi Chú</th>
                    <th class="info">Cập Nhật</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

