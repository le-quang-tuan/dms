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
        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            deferLoading: 0,
            ajax: '{!! url("/import/importFlat/anyData") !!}',
            columns: [
                {data: 'flat_code', name: 'flat_code'},
                {data: 'name', name: 'name'},
                {data: 'area', name: 'area'},
                // {data: 'receive_date', name: 'receive_date'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        if (data.receive_date != null && 
                            data.receive_date != "")
                        {
                            var dateString  = data.receive_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            return  day + '/' + month + '/' + year;
                        }
                        return data.receive_date;
                    },
                }
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
         $("#tenementFlatSubmit").manualSubmit('frmImportSubmit');

        // /* The plugin will submit form and scroll to top*/
        // $("#tenementFlatGasDelete").manualSubmit('frmCom');
                
        // $("#tenementFlatGasRefresh").manualRefresh('frmtenementFlatGas');

        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        

        /* BEGIN delete and add more */
        $(".delete").on('click', function() {
            $('.case:checkbox:checked').each(function(){
                if($.isNumeric($(this).val())){
                    $(this).parents("tr").hide();
                } else {
                    $(this).parents("tr").remove();
                }
            });
            
            $('.check_all').prop("checked", false); 
            //addDangerClass();
        });

        var i = 0;
        var button_my_button = "#addmore";
        $(button_my_button).click(function(){
            count=$('#addmore1 tr').length-1;
            //alert(count);

            var data="<tr class='new'><td><input type='checkbox' class='case'/></td>";
                data+="<td><span id='snum"+i+"'>"+count+".</span><input type='hidden' value='0' name='counter[]'></td>";

                //Block Name
                data+= "<td><select name='block_name"+count+"' id='block_name"+count+"'>";
                var charArr = <?php echo json_encode($lsChar); ?>;
                for (var i=0; i < charArr.length; i++) {
                    data+= '<option value="' + charArr[i] + '">';
                    data+= charArr[i];
                    data+= '</option>';
                }
                data+= '</select></td>';

                //Floor from
                data+="<td><input style='width: 50px;' name='floor_num_from"+count+"' id='floor_num_from_"+count+"' ></td>";

                data+="<td><input style='width: 50px;' name='floor_num_to"+count+"' id='floor_num_to_"+count+"' ></td>";

                data+="<td><input style='width: 50px;' name='flat_num_from"+count+"' id='flat_num_from_"+count+"' ></td>";

                data+="<td><input style='width: 50px;' name='flat_num_to"+count+"' id='flat_num_to_"+count+"' ></td>";

                //Elec Tariff
                data+= "<td><select name='elec_tariff"+count+"' id='elec_tariff_"+count+"'>";
                var elecTarrifs = <?php echo json_encode($elec_tariffs); ?>;
                for (var i=0; i < elecTarrifs.length; i++) {
                    data+= '<option value="' + elecTarrifs[i]['elec_code'] + '">';
                    data+= elecTarrifs[i]['elec_type'];
                    data+= '</option>';
                }
                data+= '</select></td>';

                //Water Tariff
                data+= "<td><select name='water_tariff"+count+"' id='water_tariff_"+count+"'>";
                var water_tariffs = <?php echo json_encode($water_tariffs); ?>;
                for (var i=0; i < water_tariffs.length; i++) {
                    data+= '<option value="' + water_tariffs[i]['water_code'] + '">';
                    data+= water_tariffs[i]['water_type'];
                    data+= '</option>';
                }
                data+= '</select></td>';

                //Gas Tariff
                data+= "<td><select name='gas_tariff"+count+"' id='gas_tariff_"+count+"'>";
                var gas_tariffs = <?php echo json_encode($gas_tariffs); ?>;
                for (var i=0; i < gas_tariffs.length; i++) {
                    data+= '<option value="' + gas_tariffs[i]['gas_code'] + '">';
                    data+= gas_tariffs[i]['gas_type'];
                    data+= '</option>';
                }
                data+= '</select></td>';

            $(data).insertBefore("#servicetotal");        
            i++;
        });
        
        $("#importSubmit").click(function(){
            bootbox.confirm("Dữ liệu nếu đã tồn tại sẽ không được thực hiện?", function(result) {
                if(result){
                    $("#frmTenementFlat").submit();
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
        <h2>Tạo mới Căn Hộ từ File</h2>
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
        <form id='frmImportSubmit' action="{!! route('ImportFlat.download') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <h3>Xuất file Excel mẫu dựa theo tiêu chí được chọn</h3>
            <table class="table table-bordered hover" id="addmore1">
                <tbody>
                    <tr>
                        <th class="info"><input type="checkbox" onclick="select_all()" class="check_all"></th>
                        <th class="info">No.</th>
                        <th class="info">Khu/Block</th>
                        <th class="info">Tầng</th>
                        <th class="info">~</th>
                        <th class="info">Số căn hộ</th>
                        <th class="info">~</th>
                        <th class="info">Điện</th>
                        <th class="info">Nước</th>
                        <th class="info">Gas</th>
                    </tr>
                    <tr id="servicetotal">
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <button class="btn btn-danger delete" type="button">- Xóa Tiêu Chí Đã Chọn</button>&nbsp;
                <button class="btn btn-success addmore" type="button" id='addmore'>+ Thêm Tiêu Chí Xuất File</button>  &nbsp;

                <button id='tenementFlatSubmit' type="button" class="btn btn-primary">Xuất File Excel mẫu</button>
            </table>
        </form>
    </div>
    <div class="col-lg-12">
        <form style="margin-top: 15px;" action="{{ URL::to('import/importFlat/store') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <h3>Import file Danh Sách Căn Hộ Đã Tạo Hoàn Thành</h3>
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td class="col-md-1">
                            <input type="file" name="import_file" />
                        </td>
                        <td>
                            <button class="btn btn-primary">Import File</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="col-lg-12">
        <form id='frmTenementFlat' style="margin-top: 15px;" action="{{ URL::to('import/importFlat/save') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!} 
        </form>
        <button id='importSubmit' class="btn btn-primary">Thực hiện lưu dữ liệu</button>
            <label>Chú ý: Dữ liệu nếu đã tồn tại sẽ được thực hiện</label>  
    </div>
    <div class="col-lg-12">
        <h3>Các căn hộ được import</h3>
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="info">Căn Hộ</th>
                    <th class="info">Chủ Hộ</th>
                    <th class="info">Diện Tích</th>
                    <th class="info">Ngày Nhận</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection