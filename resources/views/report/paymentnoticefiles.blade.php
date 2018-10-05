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
    $(function () {
        var tableData = JSON.parse(<?php echo json_encode($json); ?>);
        var year_month = JSON.parse(<?php echo json_encode($year_month); ?>);
        var tenement_id = JSON.parse(<?php echo json_encode($tenement_id); ?>);
        var table = $('#users-table').DataTable({
            // processing: true,
            // serverSide: false,
            // deferLoading: 0,
            data: tableData,
            columns: [
                { data: 0, title: "No" },
                {   data: null, 
                    render: function ( data, type, row ) {
                        var yearmonth = data[1];
                        if (yearmonth != "" && yearmonth != null)
                        {
                            var dateString  = data[1];
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            yearmonth = month + '/' + year;
                        }
                        return yearmonth;
                    }
                    , title: "Tháng/Năm" 
                },
                { data: 2, title: "Căn Hộ" },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return '<a href="../pv_paymentnoticefiles/'+ tenement_id + '/' + year_month + '/' + data[3] + '">'+ data[3] + '</a>';
                    },
                    title: "Xem trước"
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return '<a href="../dl_paymentnoticefiles/'+ tenement_id + '/' + year_month + '/' + data[3] + '">'+ data[3] + '</a>';
                    },
                    title: "Tải file"
                },
            ]
        });  
        $("#exeMerge").click(function(){
            bootbox.confirm("Tất cả file trong folder này sẽ được tạo thành 1 file.</>", function(result) {
                if(result){
                    $("#frmMerge").submit();
                }
            });
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
        <h2>Danh Sách Phiếu Thu Đã Được Tạo mới</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <form id='frmMerge' action="{!! route('File.exe_merge', $year_month) !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}

            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td>
                        <button id='exeMerge' type="button" class="btn btn-primary">Kết thành 1 file PDF</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form> 

        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="info">STT</th>
                    <th class="info">Tháng Năm</th>
                    <th class="info">Mã Căn Hộ</th>
                    <th class="info">Xem Trước</th>
                    <th class="info">Tải File</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection