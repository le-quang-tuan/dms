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

        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            deferLoading: 0,
            ajax: '{!! url("monthlyfee/prepaid/{$flat_id}/anyData") !!}',
            columns: [
                {data: 'bill_no', name: 'bill_no'},
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
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var receive_date = "";
                        if (data.receive_date != "")
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
                {data: 'receive_from', name: 'receive_from'},
                {data: 'receiver', name: 'receiver'},
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
            moneyChange();
            
            $('.check_all').prop("checked", false); 
            //addDangerClass();
        });

        this.years = function(){
            var currentYear = new Date().getFullYear();
            var years = []
            for(var i=currentYear;i<=currentYear+10;i++){
                years.push(i);
            } 
            return years;
        }

        var i = 0;
        var button_my_button = "#addmore";
        $(button_my_button).click(function(){
            count=$('#addmore1 tr').length-1;
            //alert(count);
            var currentYear = new Date().getFullYear();
            //var currentMonth = new Date().getFullMonth();

            var data="<tr class='new'><td class='text-center'><input type='checkbox' class='case'/></td>";
            data+="<td class='text-center'><span id='snum"+i+"'>"+count+".</span><input type='hidden' value='0' name='counter[]'></td>";

            data+= "<td><select name='year" + count + "' id='year_" + count+"'>";

            for (var i = currentYear; i < currentYear + 5; i++) {
                var month = "00".substring(0, "00".length - i.length) + i;

                data+= '<option value="' + month + '">';
                data+= month;
                data+= '</option>';
            }
            data+= '</select>';

            data+= "<select name='month" + count + "' id='month_"+count+"'>";

            var currentMonth = new Date().getMonth();

            for (var i = 1; i <= 12; i++) {
                var month = "00".substring(0, "00".length - i.toString().length) + i.toString();
                if (i == (currentMonth + 1)){
                     data+= '<option selected=True value="' + month + '">';
                }
                else{
                     data+= '<option value="' + month + '">';
                }
                data+= month;
                data+= '</option>';
            }
            data+= '</select></td>';

                //Block Name
                data+= "<td><select name='payment_type"+count+"' id='payment_type_"+count+"'>";
                var mst_payment_types = <?php echo json_encode($mst_payment_types); ?>;
                for (var i=0; i < mst_payment_types.length; i++) {
                    data+= '<option value="' + mst_payment_types[i]['id'] + '">';
                    data+= mst_payment_types[i]['name'];
                    data+= '</option>';
                }
                data+= '</select></td>';

                //Floor from
                data+="<td><input class='text-right commas price' name='money"+count+"' onchange='moneyChange()' id='money_"+count+"' width='50px'></td>";

                data+="<td><input name='comment"+count+"' id='comment_"+count+"' width='100px'></td>";
            $(data).insertBefore("#servicetotal");        
            i++;
            $("#money_" + count).ForceNumericOnly("##,###,###,###", "-");
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
        <h2>Thu Phí Trả Trước Căn Hộ</h2>
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

        <form id='frmpaidFlat' action="{!! route('PrepaidFlat.save') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <input id="id" name='flat_id' type="hidden" value="{!! $flat_id !!}">

            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td>Căn hộ<em style='color:red'>(*)</em> </td>
                        <td><input type="text" value="{!! $flat_info->address !!}" id="address" name="address" size="20%"></td>
                        <td>Chủ hộ<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! $flat_info->name !!}" id="name" name="name" size="20%"></td>

                        <td>Điện thoại<em style='color:red'>(*)</em></td>
                        <td ><input type="text" value="{!! $flat_info->phone !!} " id="phone" name="phone" size="20%"></td>
                    </tr>
                    @role('admin|manager|moderator')
                        <tr>
                            <td>Bill Tháng<em style='color:red'>(*)</em></td>
                            <td>
                                <select name="year" id="year">
                                <?php 
                                   for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                                      echo "<option>$i</option>";
                                   }
                                ?>
                                </select>
                                <select name="month" id="month">
                                <?php 
                                   for($i = -1 ; $i < 10; $i++){
                                      echo "<option>" . date('m', strtotime(" +$i months")) . "</option>";
                                   }
                                ?>
                                </select>
                            </td>
                            <td>Nội dung thu<em style='color:red'>(*)</em></td>
                            <td colspan="3"><input type="text" value="{!! old('comment', 'Thu Phí Trước') !!}" id="comment" name="comment" size="30%"></td>
                        </tr>
                        <tr>
                            <td>Ngày nhận<em style='color:red'>(*)</em></td>
                            <td><input type="text" value="{!! old('receive_date', date('d/m/Y')) !!}" id="receive_date" name="receive_date" size="10%""></td>
                            <td>Người nhận<em style='color:red'>(*)</em></td>
                            <td>
                                <input type="text" value="{!! old('receiver', 'Ban Quản Lý') !!}" id="receiver" name="receiver" size="30%">
                            </td>
                            <td>Nhận từ<em style='color:red'>(*)</em></td>
                            <td>
                                <input type="text" value="{!! old('receive_from', $flat_info->name) !!}" id="receive_from" name="receive_from" size="30%">
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Số tiền<em style='color:red'>(*)</em></td>
                            <td><input class='text-right commas price' type="text" value="{!! old('money') !!}" id="money" name="money" size="20%" readonly="readonly"></td>
                            <td>Lưu Quyển số<em style='color:red'>(*)</em></td>
                            <td>
                                <input type="text" value="{!! old('book_bill', date('ym') . $tenement->tenement_code) !!}" id="book_bill" name="book_bill" size="20%">
                            </td>
                            <td>Số bill<em style='color:red'>(*)</em></td>
                            <td>
                                <input type="text" value="{!! old('bill_no', $flat_info->bill_no) !!}" id="bill_no" name="bill_no" size="20%">
                            </td>
                        </tr>
                    @endrole
                </tbody>
            </table>
            @role('admin|manager|moderator')
            <p class="pagetitle">Chi tiết thu</p>
            <table class="table table-bordered" id="addmore1">
                <tbody>
                    <tr>
                        <th class="info"><input type="checkbox" onclick="select_all()" class="check_all"></th>
                        <th class="info">No.</th>
                        <th class="info">Năm Tháng</th>
                        <th class="info">Loại Phí</th>
                        <th class="info">Số Tiền</th>
                        <th class="info">Ghi Chú</th>
                    </tr>
                    <tr id="servicetotal">
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <button class="btn btn-danger delete" type="button">- Delete</button>&nbsp;
                <button class="btn btn-success addmore" type="button" id='addmore'>+ Add More</button>  &nbsp;

                <button id='paidFlatSubmit' type="button" class="btn btn-primary">Lưu Phiếu Thu</button>
            </table>
            @endrole
        </form>
        <div class="col-md-12">
            <h3>Danh sách Phiếu Thu trả trước</h3>
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th class="info">Quyển số</th>
                        <th class="info">Phí Năm/Tháng</th>
                        <th class="info">Ngày nhận</th>
                        <th class="info">Nhận từ</th>
                        <th class="info">Người nhận</th>
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