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
            $("#tenementWaterSubmit").click(function(){
                bootbox.confirm("Thông tin Biểu Phí sẽ được cập nhật?", function(result) {
                    if(result){
                        $("#frmTenementWater").submit();
                    }
                });
            }); 

            $("#tenementWaterDelete").click(function(){
                bootbox.confirm("Thông tin Biểu Phí sẽ được xóa?", function(result) {
                    if(result){
                        $("#frmTenWater").submit();
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
                count=$('#addmore1 tr').length-2;
                //alert(count);

                var data="<tr class='new'><td class='text-center'><input type='checkbox' class='case'/></td>";
                    data+="<td class='text-center'><span id='snum"+i+"'>"+count+".</span><input type='hidden' value='0' name='counter[]'></td>";
                    data+="<td><input name='name"+count+"' id='name_"+count+"' size='15px'></td>";
                    data+="<td><input name='index_from"+count+"' id='index_from_"+count+"' size='5px'></td>";
                    data+="<td><input name='price"+count+"' id='price_"+count+"'  size='5px'></td>";
                    data+="<td><input name='vat"+count+"' id='vat_"+count+"'  size='5px'></td>";
                    data+="<td><input name='vat_price"+count+"' id='vat_price_"+count+"' size='5px'></td>";
                    data+="<td><input name='other_fee01"+count+"' id='other_fee01_"+count+"' size='5px'></td>";
                    data+="<td><input name='other_fee01_price"+count+"' id='other_fee01_price_"+count+"' size='5px'></td>";
                    data+="<td><input name='other_fee02"+count+"' id='other_fee02_"+count+"' size='5px'></td>";
                    data+="<td><input name='other_fee02_price"+count+"' id='other_fee02_price_"+count+"' size='5px'></td>";
                    
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

            var other_fee01_priceNewId = '';
            var other_fee02_priceNewId = '';
            var vat_priceNewId = '';
            
            var nameNewName = '';
            var index_fromNewName = '';
            var priceNewName = '';
            var other_fee01NewName = '';
            var other_fee02NewName = '';
            var vatNewName = '';

            var other_fee01_priceNewName = '';
            var other_fee02_priceNewName = '';
            var vat_priceNewName = '';

            numberTemp = $(this).find('span').html().replace('.', '');
            nameNewId = 'name_'+numberTemp;
            index_fromNewId = 'index_from_'+numberTemp;
            priceNewId = 'price_'+numberTemp;
            other_fee01NewId = 'other_fee01_'+numberTemp;
            other_fee02NewId = 'other_fee02_'+numberTemp;
            vatNewId = 'vat_'+numberTemp;

            other_fee01_priceNewId = 'other_fee01_price_'+numberTemp;
            other_fee02_priceNewId = 'other_fee02_price_'+numberTemp;
            vat_priceNewId = 'vat_price_'+numberTemp;
            
            nameNewName = 'name'+numberTemp;
            index_fromNewName = 'index_from'+numberTemp;
            priceNewName = 'price'+numberTemp;

            other_fee01NewName = 'other_fee01'+numberTemp;
            other_fee02NewName = 'other_fee02'+numberTemp;
            vatNewName = 'vat'+numberTemp;

            other_fee01_priceNewName = 'other_fee01_price'+numberTemp;
            other_fee02_priceNewName = 'other_fee02_price'+numberTemp;
            vat_priceNewName = 'vat_price'+numberTemp;

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

                        case 'other_fee01_price':
                            //apply new id for unit price
                            $(this).attr('id', other_fee01_priceNewId);
                            $(this).attr('name', other_fee01_priceNewName);
                            break;
                        case 'other_fee02_price':
                            //apply new id for unit price
                            $(this).attr('id', other_fee02_priceNewId);
                            $(this).attr('name', other_fee02_priceNewName);
                            break;
                        case 'vat_price':
                            //apply new id for unit price
                            $(this).attr('id', vat_priceNewId);
                            $(this).attr('name', vat_priceNewName);
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

<?php 
/*
    =======================================
        CREATE BY HUNGNGUYEN
    =======================================
*/
?>
<div class="section-tout title" id="title" style="background-color: rgb(11, 155, 28)">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Tạo mới biểu phí Nước</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementWater-alert-' . $msg))
        <p class="alert alert-{!! $msg !!}">
            {{ Session::get('tenementWater-alert-' . $msg) }} &nbsp; <div style="display: none;" > <b id="show-time">2</b> </div>                           
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

    <form id='frmTenementWater' action="{!! route('TenementWater.update') !!}" method="POST" role="form" method="post">
        <table class="table table-striped table-bordered table-hover table-condensed">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
            <input id="tenement_water_id" name='tenement_water_id' type="hidden" value="{!! $tenementWater->id !!}">
            <tbody>
                <tr>
                    <td>Biểu phí nước sử dụng<em style='color:red'>(*)</em> </td>
                    <td><input type="text" value="{!! $tenementWater->water_type !!}" id="water_type" name="water_type" size="35%"></td>
                    <td>Phí tiêu thụ tính theo<em style='color:red'>(*)</em> </td>
                    <td>
                        <input type="radio" name="calculated_by" value="0" {!! $tenementWater->calculated_by == '1' ? '' : 'checked' !!}>Tỉ lệ 
                        <input type="radio" name="calculated_by" value="1" {!! $tenementWater->calculated_by == '0' ? '' : 'checked' !!}>Đơn giá
                    </td>
                    <td>Ghi chú</td>
                    <td><input type="text" value="{!! $tenementWater->comment !!}" id="comment" name="comment" size="50%"></td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered hover" id="addmore1">
            <tbody>
                <tr>
                    <th width="5px" class="info"></th>
                    <th width="60px" class="info text-center">No.</th>
                    <th  class="info text-center">Định mức</th>
                    <th  class="info text-center">Chỉ số <br>tiêu thụ từ</th>
                    <th  class="info text-center">Đơn giá<br><span class="badge unit">VND/m3</span></th>
                    <th class="text-center info" colspan="2">VAT<br><span class="badge unit">Tỉ Lệ</span> - <span class="badge unit">Đơn Giá</span></th>
                    <th class="info text-center" colspan="2">BVMT<br><span class="badge unit">Tỉ Lệ</span> - <span class="badge unit">Đơn Giá</span></th>
                    <th class="info text-center" colspan="2">Phí hao hụt<br><span class="badge unit">Tỉ Lệ</span> - <span class="badge unit">Đơn Giá</span></th>
                </tr>
                <?php
                    $step = 1;
                    if( isset($lsTarrif) && !empty($lsTarrif) ){
                        foreach ($lsTarrif as $value) {
                ?>
                            <tr class='old'>
                                <td class="text-center">
                                    <input type="checkbox" name='deleteid{!! $step !!}' value="{!! ( isset($value->id) && $value->id!='' )?$value->id:'' !!}" class="case">
                                    <input type="hidden" name='oldrecord{!! $step !!}' value="{!! ( isset($value->id) && $value->id!='' )?$value->id:'' !!}">
                                </td>
                                <td class="text-center">
                                    <span id="snum">{!! $step !!}.</span>
                                    <input type="hidden" value='0' name='counter[]'>
                                </td>
                                <td class="text-center">
                                    <input type='text' data-type='charge' id='name_{!! $step !!}' name='name{!! $step !!}' value='{!! $value->name or 0 !!}' size='50px'/>
                                </td>
                                <td>
                                    <input class='text-right commas price' type='text' data-type='charge' id='index_from_{!! $step !!}' name='index_from{!! $step !!}' value='{!! $value->index_from or 0 !!}' size='5px'/>
                                </td>
                            
                                <td class="text-right">
                                    <input class='text-right commas price' type='text' data-type='charge' id='price_{!! $step !!}' name='price{!! $step !!}' value='{!! $value->price or 0 !!}' size='5px'/>
                                </td> 

                                <td>
                                    <input class='text-right commas price' type='text' data-type='charge' id='vat_{!! $step !!}' name='vat{!! $step !!}' value='{!! $value->vat or 0 !!}' size='5px'/>
                                </td>
                                <td>
                                    <input class='text-right commas price' type='text' data-type='charge' id='vat_price_{!! $step !!}' name='vat_price{!! $step !!}' value='{!! $value->vat_price or 0 !!}' size='5px'/>
                                </td>

                                <td>
                                    <input class='text-right commas price' type='text' data-type='charge' id='other_fee01_{!! $step !!}' name='other_fee01{!! $step !!}' value='{!! $value->other_fee01 or 0 !!}' size='5px'/>
                                </td>
                                <td>
                                    <input class='text-right commas price' type='text' data-type='charge' id='other_fee01_price_{!! $step !!}' name='other_fee01_price{!! $step !!}' value='{!! $value->other_fee01_price or 0 !!}' size='5px'/>
                                </td>

                                <td>
                                    <input class='text-right commas price' type='text' data-type='charge' id='other_fee02_{!! $step !!}' name='other_fee02{!! $step !!}' value='{!! $value->other_fee02 or 0 !!}' size='5px'/>
                                </td>  
                                <td>
                                    <input class='text-right commas price' type='text' data-type='charge' id='other_fee02_price_{!! $step !!}' name='other_fee02_price{!! $step !!}' value='{!! $value->other_fee02_price or 0 !!}' size='5px'/>
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
                <button class="btn btn-danger delete" type="button">- Xóa biểu phí định mức đã chọn</button>&nbsp;
                <button class="btn btn-success addmore" type="button" id='addmore'>+ Thêm biểu phí định mức</button>           
            </table>
        @endrole
    </form>
    @role('admin|manager|moderator')
        <div>
            </br>
            <button id='tenementWaterSubmit' type="button" class="btn btn-primary">Lưu Biểu Phí</button>            
            <button id='tenementWaterDelete' type="button" class="btn btn-default">Xóa Biểu Phí</button>&nbsp;
            <a href="{!! route('TenementWater') !!} " class="btn btn-info">Trở về màn hình trước</a>

            <form id='frmTenWater' action="{!! route('TenementWater.destroy') !!}" method="POST" role="form" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
                <input id="tenement_water_id" name='tenement_water_id' type="hidden" value="{!! $tenementWater->id !!}">
            </form>
        </div>
    @endrole
</div>
@endsection