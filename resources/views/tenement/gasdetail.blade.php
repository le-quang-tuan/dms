@extends('include.layout')

@section('style')
<style>    
.cImgPassport{
    max-width: 100%;
}

</style>
@endsection

@section('script')
{!! Html::script('js/ckeditor/ckeditor.js') !!}

{!! Html::script('js/manual_js/manual_click.js') !!}
<script>
    // Replace the <textarea id="address"> with a CKEditor
    // instance, using default configuration.
    // auto dissable success message.
    var settimmer = 0;
    $(function(){
        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);
        @role('admin|manager|moderator')                
            $("#tenementGasRefresh").manualRefresh('frmTenementGas');

            $("#tenementGasSubmit").click(function(){
                bootbox.confirm("Thông tin Biểu Phí sẽ được cập nhật?", function(result) {
                    if(result){
                        $("#frmTenementGas").submit();
                    }
                });
            }); 

            $("#tenementGasDelete").click(function(){
                bootbox.confirm("Thông tin Biểu Phí sẽ được xóa?", function(result) {
                    if(result){
                        $("#frmTenGas").submit();
                    }
                });
            });
            /* BEGIN delete and add more */
            $(".delete").on('click', function() {
                $('.case:checkbox:checked').each(function(){
                    if($.isNumeric($(this).val())){
                        $(this).parents("tr").hide();
                    } else {
                        $(this).parents("tr").remove();
                    }
                });
                check();
                $('.check_all').prop("checked", false); 
                //addDangerClass();

            });

            var lsTarrif = <?php echo json_encode($lsTarrif); ?>;
            for (var i=0; i <= lsTarrif.length; i++) {
                $("#index_from_" + i).ForceNumericOnly("##,###,###,###", "-");
                $("#price_" + i).ForceNumericOnly("##,###,###,###", "-");
                $("#vat_" + i).ForceNumericOnly("##,###,###,###", "-");
                $("#other_fee01_" + i).ForceNumericOnly("##,###,###,###", "-");
                $("#other_fee02_" + i).ForceNumericOnly("##,###,###,###", "-");
            }

            var i = 0;
            var button_my_button = "#addmore";
            $(button_my_button).click(function(){
                count=$('#addmore1 tr').length-1;
                //alert(count);

                var data="<tr class='new'><td class='text-center'><input type='checkbox' class='case'/></td>";
                    data+="<td class='text-center'><span id='snum"+i+"'>"+count+".</span><input type='hidden' value='0' name='counter[]'></td>";
                    data+="<td><input name='name"+count+"' id='name_"+count+"' class='form-control service' '></td>";
                    data+="<td><input name='index_from"+count+"' id='index_from_"+count+"' class='form-control text-right commas price'></td>";
                    data+="<td><input name='price"+count+"' id='price_"+count+"' class='form-control text-right commas price'></td>";
                    data+="<td><input name='vat"+count+"' id='vat_"+count+"' class='form-control text-right commas price'></td>";
                    data+="<td><input name='other_fee01"+count+"' id='other_fee01_"+count+"' class='form-control text-right commas price'></td>";
                    data+="<td><input name='other_fee02"+count+"' id='other_fee02_"+count+"' class='form-control text-right commas price'></td>";
                    
                // alert(data);      
                $(data).insertBefore("#servicetotal");        
                i++;
                $("#index_from_" + count).ForceNumericOnly("##,###,###,###", "-");
                $("#price_" + count).ForceNumericOnly("##,###,###,###", "-");
                $("#vat_" + count).ForceNumericOnly("##,###,###,###", "-");
                $("#other_fee01_" + count).ForceNumericOnly("##,###,###,###", "-");
                $("#other_fee02_" + count).ForceNumericOnly("##,###,###,###", "-");
            });
        @endrole
    });

    function check(){        
        var newRow = $('#addmore1 .new');

        obj=$('#addmore1 tr').find('span');
        $.each( obj, function( key, value ) {
            id=value.id;
            $('#'+id).html(key+1+".");
        });

        $.each(newRow, function(){
            var numberTemp = '';
            var nameNewId = '';
            var index_fromNewId = '';
            var priceNewId = '';
            var other_fee01NewId = '';
            var other_fee02NewId = '';
            var vatNewId = '';
            
            var nameNewName = '';
            var index_fromNewName = '';
            var priceNewName = '';
            var other_fee01NewName = '';
            var other_fee02NewName = '';
            var vatNewName = '';

            numberTemp = $(this).find('span').html().replace('.', '');
            nameNewId = 'name_'+numberTemp;
            index_fromNewId = 'index_from_'+numberTemp;
            priceNewId = 'price_'+numberTemp;
            other_fee01NewId = 'other_fee01_'+numberTemp;
            other_fee02NewId = 'other_fee02_'+numberTemp;
            vatNewId = 'vat_'+numberTemp;
            
            nameNewName = 'name'+numberTemp;
            index_fromNewName = 'index_from'+numberTemp;
            priceNewName = 'price'+numberTemp;
            other_fee01NewName = 'other_fee01'+numberTemp;
            other_fee02NewName = 'other_fee02'+numberTemp;
            vatNewName = 'vat'+numberTemp;

            $.each($(this).find("input[type='text']"),function(){
                
                var tempTime = '';
                if($(this).attr('id') != ''){
                    tempTime = $(this).attr('id').split("_");

                    switch(tempTime[0]) {
                        case 'name':
                            //apply new id for time
                            $(this).attr('id', nameNewId);
                            $(this).attr('name', nameNewName);
                            break;
                        case 'index_from':
                            //apply new id for unit price
                            $(this).attr('id', index_fromNewId);
                            $(this).attr('name', index_fromNewName);
                            break;
                        case 'price':
                            //apply new id for unit price
                            $(this).attr('id', priceNewId);
                            $(this).attr('name', priceNewName);
                            break;
                        case 'other_fee01':
                            //apply new id for unit price
                            $(this).attr('id', other_fee01NewId);
                            $(this).attr('name', other_fee01NewName);
                            break;
                        case 'other_fee02':
                            //apply new id for unit price
                            $(this).attr('id', other_fee02NewId);
                            $(this).attr('name', other_fee02NewName);
                            break;
                        case 'vat':
                            //apply new id for unit price
                            $(this).attr('id', vatNewId);
                            $(this).attr('name', vatNewName);
                            break;
                        default:
                            break;
                    } 
                }

            });
            
        });
    }
    
</script>

@endsection

@section('content')
<div class="section-tout title" id="title" style="background-color: black;">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Cập nhật chi tiết biểu phí Điện</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementGas-alert-' . $msg))
        <p class="alert alert-{!! $msg !!}">
            {{ Session::get('tenementGas-alert-' . $msg) }} &nbsp; <div style="display: none;" > <b id="show-time">2</b> </div>
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

    <form id='frmTenementGas' action="{!! route('TenementGas.update') !!}" method="POST" role="form" method="post">
        <table class="table table-striped table-bordered table-hover table-condensed">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
            <input id="tenement_gas_id" name='tenement_gas_id' type="hidden" value="{!! $tenementGas->id !!}">
            <tbody>
                <tr>
                    <td>Biểu phí điện sử dụng<em style='color:red'>(*)</em> </td>
                    <td><input type="text" value="{!! $tenementGas->gas_type !!}" id="gas_type" name="gas_type" size="50%"></td>

                    <td>Ghi chú</td>
                    <td><input type="text" value="{!! $tenementGas->comment !!}" id="comment" name="comment" size="50%"></td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered hover" id="addmore1">
            <tbody>
                <tr>
                    <th class="info"></th>
                    <th width="60px" class="info">No.</th>
                    <th class="info">Định mức</th>
                    <th width="140px" class="info">Chỉ số tiêu thụ từ</th>
                    <th width="200px" class="info">Đơn giá<span class="badge unit">VND/kg</span></th>
                    <th width="100px" class="info">VAT<span class="badge unit">%</span></th>
                    <th width="100px" class="info">Hao Hụt<span class="badge unit">%</span></th>
                    <th width="100px" class="info">Khác<span class="badge unit">%</span></th>
                </tr>
                <?php
                    $step = 1;
                    if( isset($lsTarrif) && !empty($lsTarrif) ){
                        foreach ($lsTarrif as $value) {
                ?>
                            <tr class='old'>
                                <td class="text-center">
                                    <input type="checkbox" class="case" name='deleteid{!! $step !!}' value="{!! ( isset($value->id) && $value->id!='' )?$value->id:'' !!}">
                                    <input type="hidden" name='oldrecord{!! $step !!}' value="{!! ( isset($value->id) && $value->id!='' )?$value->id:'' !!}">
                                </td>
                                <td class="text-center">
                                    <span id="snum">{!! $step !!}.</span>
                                    <input type="hidden" value='0' name='counter[]'>
                                </td>
                                <td>
                                    <input class='form-control' type='text' data-type='charge' id='name_{!! $step !!}' name='name{!! $step !!}' value='{!! $value->name or 0 !!}' />
                                </td>
                                <td>
                                    <input class='form-control text-right commas price' type='text' data-type='charge' id='index_from_{!! $step !!}' name='index_from{!! $step !!}' value='{!! $value->index_from or 0 !!}'/>
                                </td>
                            
                                <td>
                                    <input class='form-control text-right commas price' type='text' data-type='charge' id='price_{!! $step !!}' name='price{!! $step !!}' value='{!! $value->price or 0 !!}'/>
                                </td> 

                                <td>
                                    <input class='form-control text-right commas price' type='text' data-type='charge' id='vat_{!! $step !!}' name='vat{!! $step !!}' value='{!! $value->vat or 0 !!}'/>
                                </td>

                                <td>
                                    <input class='form-control text-right commas price' type='text' data-type='charge' id='other_fee01_{!! $step !!}' name='other_fee01{!! $step !!}' value='{!! $value->other_fee01 or 0 !!}'/>
                                </td>

                                <td>
                                    <input class='form-control text-right commas price' type='text' data-type='charge' id='other_fee02_{!! $step !!}' name='other_fee02{!! $step !!}' value='{!! $value->other_fee02 or 0 !!}'/>
                                </td>               
                            </tr>  
                <?php
                            $step++;
                        }
                    } 
                ?>
                <tr id="servicetotal">
                </tr>
            </tbody>
        </table>
        @role('admin|manager|moderator')
            <table class="table">
                <button class="btn btn-danger delete" type="button">- Xóa Định Mức Chọn</button>&nbsp;
                <button class="btn btn-success addmore" type="button" id='addmore'>+ Thêm Định Mức</button>         
            </table>
        @endrole
    </form>
    @role('admin|manager|moderator')
        <div>
            </br>
            <button id='tenementGasSubmit' type="button" class="btn btn-primary">Update</button>            
            <button id='tenementGasDelete' type="button" class="btn btn-default">Delete</button> &nbsp;
            <a href="{!! route('TenementGas') !!} " class="btn btn-info">Trở về màn hình trước</a>

            <form id='frmTenGas' action="{!! route('TenementGas.destroy') !!}" method="POST" role="form" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
                <input id="tenement_gas_id" name='tenement_gas_id' type="hidden" value="{!! $tenementGas->id !!}">
            </form>
        </div>
    @endrole
</div>
@endsection