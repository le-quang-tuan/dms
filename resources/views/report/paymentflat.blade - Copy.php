@extends('includes.report')

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
        $('#users-table').DataTable({
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": false,
            "bInfo": false,
            "bAutoWidth": false,
            "bSort": false,
            processing: true,
            serverSide: false,
            deferLoading: 0,
            ajax: '{!! url("monthlyfee/paiddetail/{$id}/anyData") !!}',
            columns: [
                {   data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.name + data.year_month.substring(4,6) +'/' + data.year_month.substring(0,4);
                    },
                },
                {data: 'money', name: 'money'},
            ]
        });

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
       <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td class="col-md-2">Công Ty Quản Lý Bất Động Sản:</td>
                        <td class="col-md-2">{!! $tenement_info->manager_company !!}</td>
                        <td class="col-md-2" colspan="3">Liên 2: Giao Khách Hàng</td>
                        <td class="col-md-1">Quyển Số</td>
                        <td  class="col-md-1">{!! $tf_paid_hd->book_bill !!}</td>
                    </tr>
                    <tr>
                        <td>Dự Án</td>
                        <td class="col-md-1">
                            {!! $tenement_info->name !!}
                        </td>
                        <td colspan="3">Phiếu Thu</td>
                        <td>Số</td>
                        <td>{!! $tf_paid_hd->bill_no !!}</td>
                    </tr>
                    <tr>
                        <td>Mã Phiếu</td>
                        <td>{!! $tf_paid_hd->paid_code !!}</td>
                        <td colspan="2">Ngày Xuất PT: {!! Date('d') . ' Tháng' . Date('m') . ' Năm' . Date('Y') !!}</td>
                        <td>
                        </td>
                        <td></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>Họ tên người nộp tiền</td>
                        <td colspan="6">{!! $tf_paid_hd->receive_from !!}</td>
                    </tr>
                    <tr>
                        <td>Địa chỉ</td>
                        <td colspan="6">{!! $flat_info->address !!}</td>
                    </tr>
                    <tr>
                        <td>Lý do nộp tiền</td>
                        <td colspan="6">{!! $tf_paid_hd->comment !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-12">
            <p class="pagetitle">Chi Tiết</p>
        </div>
        <div class="col-md-12">
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Nội dung</th>
                        <th>Số tiền</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td class="col-md-2">Số tiền:</td>
                        <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td>Viết bằng chữ</td>
                        <td colspan="5">Một MộtMộtMộtMộtMộtMộtMộtMột</td>
                    </tr>
                    <tr>
                        <td>Kèm theo</td>
                        <td colspan="5">................Chứng từ gốc</td>
                    </tr>
                    <tr>
                        <td colspan="6">Tp.HCM, Ngày thu tiền: {!! Date('d') . ' Tháng' . Date('m') . ' Năm' . Date('Y') !!}</td>
                    </tr>
                    <tr>
                        <td>Trưởng Ban Quản Lý <br>
                        (Ký, họ tên, đóng dấu)</td>
                        <td>Trưởng Ban Quản Lý <br>
                        (Ký, họ tên, đóng dấu)</td>
                        <td>Trưởng Ban Quản Lý <br>
                        (Ký, họ tên, đóng dấu)</td>
                        <td>Trưởng Ban Quản Lý <br>
                        (Ký, họ tên, đóng dấu)</td>
                        <td>Trưởng Ban Quản Lý <br>
                        (Ký, họ tên, đóng dấu)</td>
                    </tr>
                    <tr>
                        <td colspan="7"><br><br><br><br><br><br></td>
                    </tr>
                    <tr>
                        <td colspan="7">Đã nhận đủ số tiền(viết bằng chữ):.............</td>
                    </tr>
                    <tr>
                        <td colspan="7">+Tỷ giá ngoại tệ(vàng, bạc, đá quý):.............</td>
                    </tr>
                    <tr>
                        <td colspan="7">+Số tiền quy đổi:.............</td>
                    </tr>
                </tbody>
            </table>
        </div>

@endsection