@extends('include.layout')

@section('style')
    {!! Html::style('css/jquery.dataTables.min.css') !!}

    {!! Html::style('css/datepicker/jquery-ui.css') !!}

    {!! Html::style('css/tageditor/jquery.tag-editor.css') !!}

    {!! Html::style('css/timepicker/jquery.timepicker.min.css') !!}

    {!! Html::style('css/multiselect/bootstrap-multiselect.css') !!}

    {!! Html::style('css/colorpicker/spectrum.css') !!}
    
<style>    
.cImgPassport{
    max-width: 100%;
}

</style>
@endsection

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
    // Replace the <textarea id="address"> with a CKEditor
    // instance, using default configuration.
    // auto dissable success message.
    var settimmer = 0;

    $(function () {
        callDatePicker();
        moneyChange();

        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            deferLoading: 0,
            ajax: '{!! url("monthlyfee/deptskip/{$flat_id}/anyData") !!}',
            columns: [
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var skip_date = "";
                        if (data.skip_date != "" && data.skip_date != null)
                        {
                            var dateString  = data.skip_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            skip_date = day + '/' + month + '/' + year;
                        }

                        return skip_date;
                    },
                },
                {data: 'skip_from', name: 'skip_from'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.money, 0, ".",",");
                    },
                },
                {data: 'comment', name: 'comment'},
                {data: 'action', name: 'comment'},
            ]
        });
    });
</script>

@endsection

@section('content')
<div class="section-tout title" id="title" style="background-color: red;">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2 style="color: white;">Phí Không Thu</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <form id='frmDeptSkipFlat' action="{!! route('DeptSkipFlat.save') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <input id="id" name='flat_id' type="hidden" value="{!! $flat_id !!}">

            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td>Căn hộ<em style='color:red'>(*)</em> </td>
                        <td><input type="text" value="{!! $flat_info->address !!}" id="address" name="address" size="20%"></td>
                        <td>Chủ hộ<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! $flat_info->name !!}" id="name" name="name" size="30%"></td>
                        <td>Điện thoại<em style='color:red'>(*)</em></td>
                        <td ><input type="text" value="{!! $flat_info->phone !!} " id="phone" name="phone" size="30%"></td>
                    </tr>
                </tbody>
            </table>
        </form>
        <div class="col-md-12">
            <h3>Danh sách Phí Không Thu</h3>
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th class="info">Ngày ghi nhận</th>
                        <th class="info">Người ghi nhận</th>
                        <th class="info">Số tiền</th>
                        <th class="info">Ghi chú</th>
                        <th class="info">Chi tiết</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection