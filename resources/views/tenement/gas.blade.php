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
            ajax: '{!! route('TenementGas.data') !!}',
            columns: [
                {data: 'gas_code', name: 'gas_code'},
                {data: 'gas_type', name: 'Name'},
                {data: 'comment', name: 'Address'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        @role('admin|manager|moderator')
            $(addButtonCreateTenement()).insertAfter($("#users-table_length"));
            function addButtonCreateTenement() {
                var html = "";
                html = "<div class='col-md-2'>";
                html+= '<?php echo link_to_action("Tenement\GasDetailController@create", $title = "Thêm Biểu Phí Mới", $parameters = array(), $attributes = array("class" => "btn btn-xs btn-primary")); ?>';
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
                    <th class="info">Biểu Phí</th>
                    <th class="info">Ghi Chú</th>
                    <th class="info">Cập Nhật</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

