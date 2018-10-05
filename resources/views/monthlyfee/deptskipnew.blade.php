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
        moneyChange();

        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000); 

        /* The plugin will submit form and scroll to top*/
        $("#DeptSkipFlatSubmit").click(function(){
            bootbox.confirm("Thông tin Phiếu sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmDeptSkipFlat").submit();
                }
            });
        }); 

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

        this.year = function(name, count, value){
            var currentYear = new Date().getFullYear();
            var data = "<td><select name='"+ name + count+"' id='"+ name +"_'"+count+'>';
            for (var i = currentYear; i < currentYear + 5; i++) {
                data+= '<option value="' + i + '">';
                data+= i;
                data+= '</option>';
            }
            data+= '</select>';
        }

        var searchName = 'counter[]';

        var inputs = document.querySelectorAll('input[type="hidden"][name="' + searchName + '"]');

        for (var i = 1; i <= inputs.length; i++) {
            $("#money_" + i).ForceNumericOnly("##,###,###,###", "-");
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

            data+= "<td><select name='year"+count+"' id='year_"+count+"'>";

            for (var i = currentYear; i < currentYear + 5; i++) {
                var month = "00".substring(0, "00".length - i.length) + i;

                data+= '<option value="' + month + '">';
                data+= month;
                data+= '</option>';
            }
            data+= '</select>';

            data+= "<select name='month"+count+"' id='month_"+count+"'>";

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
            data+= "<td><select name='payment_type"+ count +"' id='payment_type_"+ count +"'>";
            var mst_payment_types = <?php echo json_encode($mst_payment_types); ?>;
            for (var i=0; i < mst_payment_types.length; i++) {
                data+= '<option value="' + mst_payment_types[i]['id'] + '">';
                data+= mst_payment_types[i]['name'];
                data+= '</option>';
            }
            data+= '</select></td>';

            //Floor from
            data+="<td><input class='text-right commas price' name='money"+count+"' onchange='moneyChange()' id='money_"+count+"' width='100px'></td>";

            data+="<td><input name='comment"+count+"' id='comment_"+count+"' width='200px'></td>";

            $(data).insertBefore("#servicetotal");        
            i++;
            $("#money_" + count).ForceNumericOnly("##,###,###,###", "-");

        });

    });
</script>

@endsection

@section('content')
<div class="section-tout title" id="title" style="background-color: red;">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2 style="color: white;">Phí Không Thu</h2>
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

        <form id='frmDeptSkipFlat' action="{!! route('DeptSkipFlat.save') !!}" method="POST" role="form" method="post">
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
                        <td>Nội dung không thu<em style='color:red'>(*)</em></td>
                        <td colspan="5"><input type="text" value="{!! old('comment', '') !!}" id="comment" name="comment" size="30%"></td>
                    </tr>
                    <tr>
                        <td>Ngày ghi nhận<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! old('skip_date', date('d/m/Y')) !!}" id="skip_date" name="skip_date" size="10%""></td>
                        <td>Người ghi nhận<em style='color:red'>(*)</em></td>
                        <td>
                            <input type="text" value="{!! old('skip_from', 'Ban Quản Lý') !!}" id="skip_from" name="skip_from" size="30%">
                        </td>
                        <td>Số tiền<em style='color:red'>(*)</em></td>
                        <td><input class='text-right commas price' type="text" value="{!! old('money') !!}" id="money" name="money" size="20%" readonly="readonly"></td>
                    </tr>
                    @endrole
                </tbody>
            </table>
            @role('admin|manager|moderator')
            <p class="pagetitle">Chi tiết Phí không thu</p>
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

                    <?php
                        //Manager
                        $year = date('Y')-5;

                        $step = 1;
                        foreach ($dept as $value) {
                            echo  "<tr class='new'>";
                            echo  "<td class='text-center'><input type='checkbox' class='case'/></td>";
                            echo "<td class='text-center'><span id='snum". $step ."'>" . $step . ".</span><input type='hidden' value='". $step ."' name='counter[]'></td>";
                            echo  "<td>";
                            echo  "<select name='year". $step . "' id='year_" . $step. "'>";
                            for ($i = $year; $i < $year + 10; $i++) {
                                if (intval(substr($value[0],0,4)) == $i)
                                    echo '<option selected=True value="' . $i . '">';
                                else
                                    echo '<option value="' . $i . '">';
                                echo $i;
                                echo '</option>';
                            }
                            echo '</select>';

                            echo  "<select name='month". $step . "' id='month_" . $step. "'>";
                            for ($i = 1; $i <= 12; $i++) {
                                // if (intval(substr($value[0],4,2)) == $i)
                                //     echo '<option selected=True value="' . $i . '">';
                                // else
                                //     echo '<option value="' . $i . '">';

                                // echo $i;
                                // echo '</option>';
                                $m = intval(substr($value[0],4,2));

                                if ($m == $i)
                                    echo "<option selected=True>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                                else 
                                    echo "<option>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                            }
                            echo '</select>';
                            echo '</td>';
                            echo  "<td><select name='payment_type". $step ."' id='payment_type_". $step ."'>";
                            foreach ($mst_payment_types as $mst_payment_type) {
                                if ($mst_payment_type->id == $value[2])
                                    echo  '<option selected=True value="' . $mst_payment_type->id . '">';
                                else
                                    echo  '<option value="' . $mst_payment_type->id . '">';

                                echo  $mst_payment_type->name;
                                echo  '</option>';
                            }
                            echo  '</select></td>';
                            echo "<td><input class='text-right commas price' onchange='moneyChange()' name='money". $step ."' id='money_". $step ."' value = '". $value[1] . "' width='100px'></td>";
                            echo "<td><input name='comment". $step ."' id='comment_". $step ."' width='200px'></td>";
                            echo "</tr>";
                            $step++;
                        }
                    ?>
                    <tr id="servicetotal">
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <button class="btn btn-danger delete" type="button">- Delete</button>&nbsp;
                <button class="btn btn-success addmore" type="button" id='addmore'>+ Add More</button>  &nbsp;

                <button id='DeptSkipFlatSubmit' type="button" class="btn btn-primary">Lưu Phiếu Thu</button>
            </table>
            @endrole
        </form>
    </div>
</div>
@endsection