@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

@section('script')
{!! Html::script('js/jquery.dataTables.min.js') !!}

<script>
    $(function () {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            order: [[1, 'asc']],
            ajax: '{!! route('User.data') !!}',
                
            columns: [
                {data: 'id', name: 'id'},
                {data: 'fullname', name: 'fullname'},
                {data: 'email', name: 'email'},
                {data: 'username', name: 'username'},
                {data: 'authorize', name: 'authorize'},
                {data: 'tenement', name: 'tenement'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        $(addButtonCreateUser()).insertAfter($("#users-table_length"));
        function addButtonCreateUser() {
            var html = "";
            html = "<div class='col-md-2'>";
            html+= '<a href="user/new" class="btn btn-xs btn-primary" target="_blank">Thêm mới</a>';
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
        <h2>Thông tin Người dùng</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered hover" id="users-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Họ Tên</th>
                <th>Email</th>
                <th>username</th>
                <th>Quyền hạn</th>
                <th>Tham Gia Dự Án</th>
                <th>Cập nhật</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

