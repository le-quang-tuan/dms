@extends('includes.report')

@section('style')
    {!! Html::style('css/jquery.dataTables.min.css') !!}

    {!! Html::style('css/datepicker/jquery-ui.css') !!}

    {!! Html::style('css/tageditor/jquery.tag-editor.css') !!}

    {!! Html::style('css/timepicker/jquery.timepicker.min.css') !!}

    {!! Html::style('css/multiselect/bootstrap-multiselect.css') !!}

    {!! Html::style('css/colorpicker/spectrum.css') !!}
    
<style>    
body {
  font-family: "Helvetica Neue", Helvetica, Arial;
  font-size: 10px;
  line-height: 20px;
  font-weight: 400;
  color: black;
  -webkit-font-smoothing: antialiased;
  font-smoothing: antialiased;
  background: white;
}

.wrapper {
  margin: 0 auto;
  padding: 40px;
  max-width: 800px;
}

.table {
  margin: 0 0 40px 0;
  width: 100%;
  box-shadow: 0 1px 3px rgba(111, 0, 0, 0.2);
  display: table;
}
@media screen and (max-width: 580px) {
  .table {
    display: block;
  }
}

.row {
  display: table-row;
  background: #f6f6f6;
}
.row:nth-of-type(odd) {
  background: #e9e9e9;
}
.row.header {
  font-weight: 900;
  color: #ffffff;
  background: #ea6153;
}
.row.green {
  background: #27ae60;
}
.row.blue {
  background: #2980b9;
}
@media screen and (max-width: 580px) {
  .row {
    padding: 8px 0;
    display: block;
  }
}

.cell {
  padding: 1px 1px;
  display: table-cell;
}
@media screen and (max-width: 580px) {
  .cell {
    padding: 2px 12px;
    display: block;
  }
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
        
        <div>
            <table>
                <tbody>
                    <tr>
                        <td>Công Ty Quản Lý Bất Động Sản:{!! $tenement_info->manager_company !!}</td>
                    </tr>
                    <tr>
                        <td>Dự Án: {!! $tenement_info->name !!}
                        </td>
                    </tr>
                    <tr>
                        <td>Mã Phiếu: {!! $tf_paid_hd->paid_code !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <table>
                <tbody>
                    <tr>
                        <td>Liên 2: Giao Khách Hàng</td>
                    </tr>
                    <tr>
                        <td>Phiếu Thu</td>
                    </tr>
                    <tr>
                        <td>Ngày Xuất PT: {!! Date('d') . ' Tháng' . Date('m') . ' Năm' . Date('Y') !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div>
            <table>
                <tbody>
                    <tr>
                        <td >Quyển Số: {!! $tf_paid_hd->book_bill !!}</td>
                    </tr>
                    <tr>
                        <td>Số: {!! $tf_paid_hd->bill_no !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div>
            <table>
                <tbody>
                    <tr>
                        <td>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>Họ tên người nộp tiền: {!! $tf_paid_hd->receive_from !!}</td>
                    </tr>
                    <tr>
                        <td>Địa chỉ: {!! $flat_info->address !!}</td>
                    </tr>
                    <tr>
                        <td>Lý do nộp tiền: {!! $tf_paid_hd->comment !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-12">
            <p class="pagetitle">Chi Tiết</p>
        </div>
            <table class="table" id="users-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Nội dung</th>
                        <th>Số tiền</th>
                    </tr>
                </thead>
            </table>
            <div>
                
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="cell">Số tiền: 123456</td>
                        </tr>
                        <tr>
                            <td>Viết bằng chữ: Một MộtMộtMộtMộtMộtMộtMộtMột</td>
                        </tr>
                        <tr>
                            <td>Kèm theo:................Chứng từ gốc</td>
                        </tr>
                        <tr>
                            <td>Tp.HCM, Ngày thu tiền: {!! Date('d') . ' Tháng' . Date('m') . ' Năm' . Date('Y') !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
            <table>
                <tbody>
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

@endsection