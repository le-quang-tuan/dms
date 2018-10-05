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
            ajax: '{!! url("/import/importWater/anyData") !!}',
            columns: [
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.year_month.substring(4,6) +'/' + data.year_month.substring(0,4);
                    },
                },

                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.date_from != "")
                        {
                            var dateString  = data.date_from;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            return  day + '/' + month + '/' + year;
                        }
                        return data.date_from; //data.year_month.substring(4,6) +'/' + data.year_month.substring(0,4);
                    },
                },
                //{data: 'date_from', name: 'date_from'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.date_to != "")
                        {
                            var dateString  = data.date_to;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            return  day + '/' + month + '/' + year;
                        }
                        return data.date_to;
                    },
                },
                {data: 'old_index', name: 'old_index'},
                {data: 'new_index', name: 'new_index'},
                {data: 'comment', name: 'comment'},
            ]
        });

        $('#tenementFlatWaterSubmit').on('click', function(){
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
        $("#tenementFlatWaterSubmit").manualSubmit('frmtenementFlatWater');

        $("#importSubmit").click(function(){
            bootbox.confirm("Dữ liệu nếu đã tồn tại sẽ được chép chồng, dữ liệu chưa có sẽ được tạo mới?", function(result) {
                if(result){
                    $("#frmImportSubmit").submit();
                }
            });
        });

        var year = $("#year").val();
        var month = $("#month").val();

        var date = new Date(year + '-' + month + '-' + '1');
        date.setMonth(month - 2);

        var preMonth = (1 + date.getMonth()).toString();
        preMonth = preMonth.length > 1 ? preMonth : '0' + preMonth;

        var firstDay = ('01/' + preMonth + '/' + date.getFullYear());

        var preLastdayMonth = new Date(date.getFullYear(), preMonth,0);

        var lastDay = (preLastdayMonth.getDate() + '/' + preMonth + '/' + preLastdayMonth.getFullYear());

        $("#date_from").val(firstDay);
        $("#date_to").val(lastDay);
        $("#comment").val("Chỉ số nước sử dụng tháng " + preMonth + "/" + year);
    });

</script>
@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Nhập chỉ số tiêu thụ Nước từ File</h2>
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
        {{ Form::open(['route' => ['ImportWater.download'],  'method' => 'POST', 'id'=>'frmtenementFlatWater']) }}
            {!! csrf_field() !!}
            <h3>1) Tải file mẫu sau khi nhập các thông tin chung</h3>

            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr class="info" style="color: black">
                        <td>Chỉ số nước cho Phiếu Thu Tháng<em style='color:red'>(*)</em> </td>
                        <td>
                            <select name="month" id="month">
                            <?php
                                $y = date('Y');
                                $m = date('m');

                                for($i = 1 ; $i <= 12; $i++){
                                    if ($m == $i)
                                        echo "<option selected=True>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                                    else 
                                        echo "<option>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                                }
                            ?>
                            </select>

                            <select name="year" id="year">
                            <?php
                                for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                                    if ($y == $i)
                                        echo "<option selected>$i</option>";
                                    else 
                                        echo "<option>$i</option>";

                                }
                            ?>
                            </select>
                        </td>

                        <td>Sử dụng từ ngày<em style='color:red'>(*)</em></td>

                        <td><input class="date-picker" type="text" value="{!! old('date_from') !!}" id="date_from" name="date_from" size="10%">~<input class="date-picker" type="text" value="{!! old('date_to') !!}" id="date_to" name="date_to" size="10%"></td>

                        </tr>
                        <tr>
                        <td>Ghi chú</td>
                        <td colspan="2">
                            <input type="text" name="comment" id="comment" name="comment" size="50%" value="{!! old('comment') !!}">
                        </td>
                        <td>
                            <button id='tenementFlatWaterSubmit' type="button" class="btn btn-primary">Tạo File Mẫu</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        {{ Form::close() }}
    </div>
    <div class="col-lg-12">
        <form style="margin-top: 15px;" action="{{ URL::to('import/importWater/store') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <h3>2) Nhập các thông tin cần thiết vào file mẫu và thực hiện upload</h3>        
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr class="info" style="color: black">
                        <td>Chỉ số nước cho Phiếu Thu Tháng<em style='color:red'>(*)</em> </td>
                        <td>
                        <select name="month" id="month">
                        <?php
                            $y = date('Y');
                            $m = date('m');
                            for($i = 1 ; $i <= 12; $i++){
                                if ($m == $i)
                                    echo "<option selected=True>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                                else 
                                    echo "<option>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                            }
                        ?>
                        </select>
                        <select name="year" id="year">
                        <?php
                            for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                                if ($y == $i)
                                    echo "<option selected>$i</option>";
                                else 
                                    echo "<option>$i</option>";
                            }
                        ?>
                        </select>
                    </td>
                        <td>File import<em style='color:red'>(*)</em> </td>
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

        <form id='frmImportSubmit' style="margin-top: 15px;" action="{{ URL::to('import/importWater/save') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
        </form>
        <button id='importSubmit' class="btn btn-primary">Thực hiện lưu dữ liệu</button>
            <label>Chú ý: Dữ liệu nếu đã tồn tại sẽ được chép chồng, dữ liệu chưa có sẽ được tạo mới</label>
    </div>
    <div class="col-lg-12">
        <h3>Chỉ số nước được import từ các căn hộ</h3>
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="info">Năm tháng</th>
                    <th class="info">Từ ngày</th>
                    <th class="info">Đến ngày</th>
                    <th class="info">Chỉ số cũ</th>
                    <th class="info">Chỉ số mới</th>
                    <th class="info">Cập nhật</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

