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
<script>
    // Replace the <textarea id="address"> with a CKEditor
    // instance, using default configuration.
    // auto dissable success message.
    var settimmer = 0;

    $(function () {
        callDatePicker();

        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            deferLoading: 0,
            columnDefs: [
                { className: "dt-right", "targets": [2] },
                { className: "dt-center", "targets": [0] },
            ],
            ajax: '{!! url("monthlyfee/deptskipdetail/{$id}/anyData") !!}',
            columns: [
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.year_month != "")
                        {
                            var dateString  = data.year_month;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);

                            return  month + '/' + year;
                        }
                        return data.year_month;
                    },
                },
                {data: 'name', name: 'name'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.money, 0, ".",",");
                    },
                },
                {data: 'comment', name: 'comment'},
                @role('admin|manager|moderator')
                {data: 'action', name: 'action'},
                @endrole
            ]
        });

        @role('admin|manager|moderator')
        $('#users-table').on('click', '.btn-details', function(){
            var urlGet = "monthlyfee/deptskipdetail/"+ $(this).val() +"/destroy";

            var confirmTable = "<h2>Dữ liệu sẽ được xóa?</h2>";
            bootbox.confirm(confirmTable, function (result) {
                var id = $(this).val();
                if (result == true){
                    jQuery.ajax({
                    type: "GET",
                    url: '{!! url("'+ urlGet +'") !!}',
                    error:function(msg){
                        alert( "Error !: " + msg );
                    },
                    success:function(data){
                        table.ajax.url('{!! url("monthlyfee/deptskipdetail/{$id}/anyData") !!}').load();      
                    }});
                }
            });
        });
        @endrole
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000); 

        /* The plugin will submit form and scroll to top*/
         $("#paidFlatSubmit").manualSubmit('frmpaidFlat');

        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        
    });
</script>

@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Chi Tiết Phí Không Thu</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <div class="flash-message">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('tenement-alert-' . $msg))
            <p class="alert alert-{!! $msg !!}">
                {!! Session::get('tenement-alert-' . $msg) !!}<br>
                The message will dissable with in <b id="show-time">5</b> seconds                            
            </p>
            @endif
            @endforeach
        </div> 
        <!-- end .flash-message -->
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id='frmpaidFlat' action="{!! route('PaidFlat.save') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <input id="id" name='id' type="hidden" value="{!! $id !!}">

            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td class="col-md-1">Căn hộ<em style='color:red'>(*)</em> </td>
                        <td class="col-md-2">{!! $flat_info->flat_code !!}</td>
                        <td class="col-md-2">Chủ hộ<em style='color:red'>(*)</em></td>
                        <td class="col-md-2">{!! $flat_info->name !!}</td>

                        <td class="col-md-1">Điện thoại<em style='color:red'>(*)</em></td>
                        <td  class="col-md-2">{!! $flat_info->phone !!}</td>
                    </tr>
                    <tr>
                        <td>Nội dung không thu<em style='color:red'>(*)</em></td>
                        <td colspan="6">{!! $tf_dept_skip_hd->comment !!}</td>
                    </tr>
                    <tr>
                        <td>Ngày ghi nhận<em style='color:red'>(*)</em></td>
                        <td>
                            {!! substr($tf_dept_skip_hd->skip_date, 6,2) !!}
                            /{!! substr($tf_dept_skip_hd->skip_date, 4,2) !!}
                            /{!! substr($tf_dept_skip_hd->skip_date, 0,4) !!}
                        </td>
                        <td>Người ghi nhận<em style='color:red'>(*)</em></td>
                        <td>
                            {!! $tf_dept_skip_hd->skip_from !!}
                        </td>
                        <td>Số tiền<em style='color:red'>(*)</em></td>
                        <td>{!! number_format($tf_dept_skip_hd->money) !!}</td>
                    </tr>
                </tbody>
            </table>
        </form>
        <table class="table">
            <a href="../../monthlyfee/paidpayment/{{ $tf_dept_skip_hd->id }}" class="btn btn-xs btn-danger">Chỉnh sửa lại Phiếu Thu</a>
        </table>

        <div class="col-md-12">
            <h3>Các khoản thu</h3>
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th class="info">Phí Năm/Tháng</th>
                        <th class="info">Phí Thu</th>
                        <th class="info">Số tiền</th>
                        <th class="info">Ghi chú</th>
                        @role('admin|manager|moderator')
                        <th class="info">Hủy</th>
                        @endrole
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection