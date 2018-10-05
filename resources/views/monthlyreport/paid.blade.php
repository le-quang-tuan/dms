@extends('include.layout')

@section('style')
    {!! Html::style('css/datepicker/jquery-ui.css') !!}

    {!! Html::style('css/tageditor/jquery.tag-editor.css') !!}

    {!! Html::style('css/timepicker/jquery.timepicker.min.css') !!}

    {!! Html::style('css/multiselect/bootstrap-multiselect.css') !!}

    {!! Html::style('css/colorpicker/spectrum.css') !!}

<style>    
.cImgPassport{
    max-width: 50%;
}

</style>
<style>
        .btnRemoveRow {
            cursor: pointer;
            display: inline-block;
            height: 30px;
            position: relative;
            width: 28px;
        }

        .editor {
            width: 220px !important;
        }

        .label-date-field {
            float: left;
            width: 130px;
            margin-left: 15px;
        }

        .image-editor {
            height: 180px;
            width: 150px;
        }

        .last-change p {
            color: rgba(255, 0, 102, 1);
            font-style: italic;
        }

        em {
            color: rgba(255, 0, 102, 1);
        }

        .ui-autocomplete {
            max-height: 300px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
        }

        .checkbox {
            margin-top: 0px;
        }

        .tr-ct-35 {
            height: 35px;
        }

        .parent {
            position: relative;
        }

        .child {
            position: absolute; 
            top: 50%; 
            transform: translateY(-50%);
        }

        .sp-preview {
            width: 120px;
        }

        .sp-replacer {
            width: 150px;
        }

        .sp-dd {
            float: right;
        }

        .sp-price {
            text-align: right;
        }

        /* IE 6 doesn't support max-height
        * we use height instead, but this forces the menu to always be this tall
        */
        * html .ui-autocomplete {
            height: 300px;
        }

        .room_no {
            width: 70px !important;
            float:left;
        }
        label {
            clear:both;
        }
        .nopadding {
           padding: 0 !important;
           margin: 0 !important;
        }
    </style>
@endsection

@section('script')
    {!! Html::script('js/ckeditor/ckeditor.js') !!}
    {!! Html::script('js/manual_js/manual_click.js') !!}
    {!! Html::script('js/colorpicker/spectrum.js') !!}
    {!! Html::script('js/datepicker/jquery-ui.js')  !!}
    {!! Html::script('js/tageditor/jquery.tag-editor.js')  !!}
    {!! Html::script('js/tageditor/jquery.caret.min.js')  !!}
    {!! Html::script('js/timepicker/jquery.timepicker.min.js')  !!}
    {!! Html::script('js/multiselect/bootstrap-multiselect.js')  !!}
    {!! Html::script('js/ckeditor/ckeditor.js') !!}
    {!! Html::script('js/manual_js/manual_click.js') !!}
<script>
    $(function () {
        callDatePicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        var year = $("#year").val();
        var month = $("#month").val();
        
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            columnDefs: [
              { className: "dt-right", "targets": [3,4,5] },
              { className: "dt-nowrap", "targets": [0,1] },
              { className: "dt-center", "targets": [2,6] }
            ],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    filename: function(){
                        var month = $("#month").val();
                        var year = $("#year").val();
                        return 'CongNoThang_' + month + year;
                    },
                    text:'Xuất file '+'<i class="fa fa-file-excel-o fa-fw"></i>'
                    ,exportOptions: {
                        columns: [ 0, ':visible' ]
                    }
                },
                {extend:'colvis', text:'Hiển thị dữ liệu'+'<i class="fa fa-angle-down"></i>'}
            ],
            ajax: '{!! url("monthlyreport/paid/anyData") !!}',
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

                {data: 'address', name: 'address'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'area', name: 'area'},
                {data: 'persons', name: 'persons'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var receive_date = "Chưa nhận";
                        if (data.receive_date != "" && data.receive_date != null)
                        {
                            var dateString  = data.receive_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            receive_date = day + '/' + month + '/' + year;
                        }

                        return receive_date;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var paid_receive_date = "";
                        if (data.paid_receive_date != "" && data.paid_receive_date != null)
                        {
                            var dateString  = data.paid_receive_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            paid_receive_date = day + '/' + month + '/' + year;
                        }

                        return paid_receive_date;
                    },
                },
                {data: 'payment_name', name: 'payment_name'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.money, 0, ".",",");
                    },
                },
            ]
        });        
        $("#downloadSubmit").manualSubmit('frmDownloadSubmit');

        $('#searchSubmit').click(function() {
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();
            table.clear().draw();
            table.ajax.url('{!! url("monthlyreport/paid/anyData/?date_from='+ date_from + '&date_to=' + date_to + '") !!}').load();
            // table.search(searchText).draw();
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
        <h2>Thông Tin Phí Phát Sinh Tháng</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <form id='frmDownloadSubmit' action="{!! route('Paid.download') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <table class="table">
                <tbody>
                    <tr>
                        <td>Từ ngày</td>
                        <td>
                            <input type="text" value="{!! old('date_from', date('01/m/Y')) !!}" id="date_from" name="date_from" size="20%" class="date-picker">

                            <input type="text" value="{!! old('date_to', date('d/m/Y')) !!}" id="date_to" name="date_to" size="20%" class="date-picker">
                            
                            <button id='searchSubmit' type="button" class="btn btn-primary">Lọc dữ liệu</button>

                            <button id='downloadSubmit' type="button" class="btn btn-primary">Xuất File Excel</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>

        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th width="410px" class="group-head info">
                        Tháng/Năm
                    </th>
                    <th class="group-head day">
                        Căn Hộ
                    </th>
                    <th class="group-head day">
                        Chủ Hộ
                    </th>
                    <th class="group-head day">
                        Số Điện Thoại
                    </th>
                    <th class="group-head day">
                        Diện Tích
                    </th>
                    <th class="group-head day">
                        Nhân khẩu
                    </th>
                    <th class="group-head day">
                        Ngày bàn giao
                    </th>
                    <th class="group-head period ">
                        Ngày thu phí
                    </th>
                    <th class="group-head period ">
                        Phí Thu
                    </th>
                    <th class="group-head period ">
                        Số Tiền
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

