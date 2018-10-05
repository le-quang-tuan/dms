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
            ajax: '{!! route('Company.data') !!}',
            columns: [
                {data: 'name', name: 'Name'},
                {data: 'contactname', name: 'Contactname'},
                {data: 'address', name: 'Address'},
                {data: 'tel', name: 'Tel'},
                {data: 'tel', name: 'Tel'},
                {data: 'tel', name: 'Tel'},
                {data: 'elec', name: 'action', orderable: false, searchable: false},
                {data: 'water', name: 'Contractrate', orderable: false, searchable: false},
                {data: 'gas', name: 'Note', orderable: false, searchable: false},
                {data: 'parking', name: 'AreaName', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        $(addButtonCreateCompany()).insertAfter($("#users-table_length"));
        function addButtonCreateCompany() {
            var html = "";
            html = "<div class='col-md-2'>";
            html+= '<?php echo link_to_action("CompanyDetailController@create", $title = "Create new Company", $parameters = array(), $attributes = array("class" => "btn btn-xs btn-primary", "target" => "_blank")); ?>';
            html+= "</div>";
            return html;
        }
    });
</script>
@endsection

@section('content')

<div class="container-fluid time-table-no-margin">
    <div class="row">
        <div class="col-lg-12">
            <h1>Thông tin Chung Cư/Khu Căn Hộ và Thiết lập biểu phí</h1>
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th rowspan="2">Khu Căn Hộ</th>
                        <th rowspan="2">Địa Chỉ</th>
                        <th rowspan="2">Phí quản lý m2</th>
                        <th rowspan="2">Hao hụt chia sẻ</th>
                        <th rowspan="2">Chú thích</th>
                        <th rowspan="2">Loại Căn Hộ</th>
                        <th colspan="4" >Biểu Phí</th>
                        <th rowspan="2">Cập Nhật</th>
                    </tr>
                    <tr>
                        <th>Điện</th>
                        <th>Nước</th>
                        <th>Gas</th>
                        <th>Giữ xe</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
