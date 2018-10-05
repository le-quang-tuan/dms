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
    var settimmer = 0;

    $(function () {
        callDatePicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            deferLoading: 0,
            ajax: '{!! url("/import/importVehicle/anyData") !!}',
            columns: [
                {data: 'flat_code', name: 'flat_code'},
                {data: 'name', name: 'name'},
                {data: 'number_plate', name: 'number_plate'},
                {data: 'label', name: 'label'},
                {data: 'maker', name: 'maker'},
                {data: 'color', name: 'color'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.begin_contract_date != "")
                        {
                            var dateString  = data.begin_contract_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            return  day + '/' + month + '/' + year;
                        }
                        return data.begin_contract_date; //data.year_month.substring(4,6) +'/' + data.year_month.substring(0,4);
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.end_contract_date != null && 
                            data.end_contract_date != "")
                        {
                            var dateString  = data.end_contract_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            return  day + '/' + month + '/' + year;
                        }
                        return data.end_contract_date;
                    },
                },
                {data: 'vehicle_type_id', name: 'vehicle_type_id'},
                {data: 'comment', name: 'comment'}
            ]
        });

        $('#tenementFlatVehicleSubmit').on('click', function(){
            // alert("aaaaa");
            $('#users-table').DataTable().draw(false);
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
        $("#tenementFlatVehicleSubmit").manualSubmit('frmtenementFlatVehicle');

        $("#importSubmit").click(function(){
            bootbox.confirm("Thực hiện thêm danh sách xe gửi tháng cho các căn hộ?", function(result) {
                if(result){
                    $("#frmImportSubmit").submit();
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
        <h2>Nhập thông tin xe được gửi từ File</h2>
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
    </div>
    <div class="col-lg-12">    
        {{ Form::open(['route' => ['ImportVehicle.download'],  'method' => 'POST', 'id'=>'frmtenementFlatVehicle']) }}
            {!! csrf_field() !!}
            <h3>1) Tải file mẫu sau khi nhập các thông tin chung</h3>

            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr class="info">
                        <td class="col-md-1">
                            <button id='tenementFlatVehicleSubmit' type="button" class="btn btn-primary">Tạo File Mẫu Cho Tất Cả Căn Hộ</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        {{ Form::close() }}
    </div>

    <div class="col-lg-12">
        <form style="margin-top: 15px;" action="{{ URL::to('import/importVehicle/store') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <h3>2) Nhập các thông tin cần thiết vào file mẫu và thực hiện upload</h3>        

            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr class="info">
                        <td class="col-md-1">
                            <input type="file" name="import_file" />
                        </td>
                        <td>
                            <button class="btn btn-primary">Thực Hiện Upload File</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="col-lg-12">
        <h3>3) Sau khi upload, kiểm tra dữ liệu đã được upload chưa, sau đó thực hiện lưu trữ</h3>
        <form id='frmImportSubmit' style="margin-top: 15px;" action="{{ URL::to('import/importVehicle/save') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
        </form>
        <button id='importSubmit' class="btn btn-primary">Thực hiện lưu dữ liệu</button>
        <label>Chú ý: Dữ liệu nếu đã tồn tại sẽ được chép chồng, dữ liệu chưa có sẽ được tạo mới</label>
    </div>
    <div class="col-lg-12">
        <h3>Danh sách xe tháng các căn hộ đã được tải lên</h3>
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="info">Mã Căn Hộ</th>
                    <th class="info">Chủ Xe</th>
                    <th class="info">Biển Số</th>
                    <th class="info">Hiệu Xe</th>
                    <th class="info">Hãng Xe</th>
                    <th class="info">Màu Xe</th>
                    <th class="info">Giữ Xe Từ Ngày</th>
                    <th class="info">Đến Ngày</th>
                    <th class="info">Loại Phí</th>
                    <th class="info">Ghi Chú</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

