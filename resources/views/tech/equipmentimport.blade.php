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
        //setDatepicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            deferLoading: 0,
            ajax: '{!! url("/tech/importequipment/anyData") !!}',
            columns: [
                {data: 'equipment_group_id', name: 'equipment_group_id'},
                {data: 'producer_id', name: 'producer_id'},
                {data: 'name', name: 'name'},
                {data: 'comment', name: 'comment'},
            ]
        });

        $('#tenementFlatElecSubmit').on('click', function(){
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

        $("#downloadSampleSubmit").manualSubmit('frmDownloadSampleSubmit');

        $("#importSubmit").click(function(){
            bootbox.confirm("Dữ liệu nếu đã tồn tại sẽ được chép chồng, dữ liệu chưa có sẽ được tạo mới?", function(result) {
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
        <h2>Vật tư - Thiết bị - Máy Móc</h2>
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
        <div class="col-lg-12">

        {{ Form::open(['route' => ['ImportEquipment.download'],  'method' => 'POST', 'id'=>'frmDownloadSampleSubmit']) }}
            {!! csrf_field() !!}
            <h3>1) Tải file mẫu sau khi nhập các thông tin chung</h3>
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr class="info" style="color: white">
                        <td colspan="3">
                        Thông tin Header file được import, các Mã phải tồn tại trong hệ thống
                        </td>
                    </tr>
                    <tr class="info" style="color: white">
                        <td colspan="3">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MA_NHA_CUNG_CAP: Mã nhà cung cấp <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NHA_CUNG_CAP: Tên nhà cung cấp <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MA_NHOM: Mã Nhóm  <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;THIET_BI: Tên Thiết Bị vật tư  <br>   
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NHAN: Nhãn hiệu      <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MODEL: Số Model    <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;THONG_SO: Thông số kỹ thuật  <br>   
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KHU_VUC: Vị trí đặt tại Khu Vực  <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GHI_CHU: Ghi chú
                        </td>
                    </tr>
                    <tr class="info" style="color: black">
                        <td>Ghi Chú</td>
                        <td>
                            <input type="text" name="comment" id="comment" name="comment" size="20%" value="{!! old('comment') !!}">
                        </td>
                        <td>
                            <button id='downloadSampleSubmit' type="button" class="btn btn-primary">Tạo File Mẫu</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        {{ Form::close() }}
        </div>
        <div class="col-lg-12">
            <h3>2) Chọn file mẫu và import</h3>        
            <form style="margin-top: 15px;" action="{{ URL::to('tech/importequipment/store') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                {!! csrf_field() !!}

                <table class="table table-striped table-bordered table-hover table-condensed">
                    <tbody>
                        <tr class="info" style="color: black">
                            <td>Chọn File import<em style='color:red'>(*)</em></td>
                            <td>
                                <input type="file" name="import_file" />
                            </td>
                            <td>
                                <button class="btn btn-primary">Thực hiện upload file</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-lg-12">
            <h3>3) Sau khi upload, kiểm tra dữ liệu đã được upload chưa, sau đó thực hiện lưu trữ</h3>

            <form id='frmImportSubmit' style="solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('tech/importequipment/save') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {!! csrf_field() !!} 
            </form>
        
            <button id='importSubmit' class="btn btn-primary">Thực hiện lưu dữ liệu</button>
            <label>Chú ý: Dữ liệu nếu đã tồn tại sẽ được chép chồng, dữ liệu chưa có sẽ được tạo mới</label>
        </div>
        
        <div class="col-lg-12">
            <h3>Chỉ số điện được import từ các căn hộ</h3>
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th class="info">Năm tháng</th>
                        <th class="info">Từ ngày</th>
                        <th class="info">Đến ngày</th>
                        <th class="info">Chỉ số cũ</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

