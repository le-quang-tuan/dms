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
        
        $("#tenementWaterSubmit").click(function(){
            bootbox.confirm("Thông tin Biểu Phí sẽ được xóa?", function(result) {
                if(result){
                    $("#frmTenementWater").submit();
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
            
            $('.check_all').prop("checked", false); 
            //addDangerClass();
        });

        var i = 0;
        var button_my_button = "#addmore";
        $(button_my_button).click(function(){
            count=$('#addmore1 tr').length-1;
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

    });

    
    
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
<div class="section-tout title" id="title">
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

        <form id='frmTenementWater' action="{!! route('TenementWater.store') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td>Biểu phí điện sử dụng<em style='color:red'>(*)</em></td>
                        <td><input type="text" value="{!! old('name') !!}" id="name" name="name" size="35%"></td>
                        <td>
                        <input type="radio" name="calculated_by" value="0" {!! old('calculated_by') == '1' ? '' : 'checked' !!}>Tỉ lệ %
                        <input type="radio" name="calculated_by" value="1" {!! old('calculated_by') == '0' ? '' : 'checked' !!}>Đơn giá
                        </td>
                        <td>Ghi chú</td>
                        <td><input type="text" value="{!! old('comment') !!}" id="comment" name="comment" size="50%"></td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-bordered hover" id="addmore1">
                <tbody>
                    <tr>
                        <th width="5px" class="info" rowspan="2"></th>
                        <th width="60px" class="info text-center" rowspan="2">No.</th>
                        <th width="100px" class="info text-center" rowspan="2">Định mức</th>
                        <th width="100px" class="info text-center" rowspan="2">Chỉ số <br>tiêu thụ từ</th>
                        <th width="100px" class="info text-center" rowspan="2">Đơn giá<span class="badge unit">VND/m3</span></th>
                        <th width="25px" class="text-center info" colspan="2">VAT tính theo</th>
                        <th width="25px" class="info text-center" colspan="2">BVMT tính theo</th>
                        <th width="25px" class="info text-center" colspan="2">Phí hao hụt tính theo</th>
                    </tr>
                    <tr>
                        <th class="info">Tỉ lệ<span class="badge unit">%</span></th>
                        <th class="info">Đơn Giá<span class="badge unit">VND/m3</span></th>
                        <th class="info">Tỉ lệ<span class="badge unit">%</span></th>
                        <th class="info">Đơn Giá<span class="badge unit">VND/m3</span></th>
                        <th class="info">Tỉ lệ<span class="badge unit">%</span></th>
                        <th class="info">Đơn Giá<span class="badge unit">VND/m3</span></th>
                    </tr>
                    

                    <?php 
                        $step = Session::get('counter');
                    ?>
                    @for($i = 1; $i <=  $step ; $i++)
                    <tr class='new'>
                        <td class='text-center'><input type='checkbox' class='case'/></td>
                        <td class='text-center'><span id='snum{{$i}}'>{{ $i }}.</span><input type='hidden' value='0' name='counter[]'></td>
                        <td><input name='name{{$i}}' id='name_{{$i}}' value="{{ old('name' . $i) }}" size="15px" ></td>
                        <td><input name='index_from{{$i}}' id='index_from_{{$i}}' value="{{ old('index_from' . $i) }}" size="5px"></td>
                        <td><input name='price{{$i}}' id='price_{{$i}}' value="{{ old('price' . $i) }}" size="5px" class='commas'></td>
                        <td><input name='vat{{$i}}' id='vat_{{$i}}' value="{{ old('vat' . $i) }}" size="5px"></td>
                        <td><input name='vat_price{{$i}}' id='vat_price_{{$i}}' value="{{ old('vat_price' . $i) }}" size="5px"></td>
                        <td><input name='other_fee01{{$i}}' id='other_fee01_{{$i}}'  value="{{ old('other_fee01' . $i) }}" size="5px"></td>
                        <td><input name='other_fee01_price{{$i}}' id='other_fee01_price_{{$i}}' value="{{ old('other_fee01_price' . $i) }}" size="5px"></td>
                        <td><input name='other_fee02{{$i}}' id='other_fee02_{{$i}}' value="{{ old('other_fee02' . $i) }}" size="5px"></td>
                        <td><input name='other_fee02_price{{$i}}' id='other_fee02_price_{{$i}}' value="{{ old('other_fee02_price' . $i) }}" size="5px"></td>
                    </tr>
                    @endfor
                    <tr id="servicetotal">
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <br>
                <button class="btn btn-danger delete" type="button">- Xóa Định Mức Chọn</button>&nbsp;
                <button class="btn btn-success addmore" type="button" id='addmore'>+ Thêm Định Mức</button>           
            </table>
        </div>
    </form>
    <div>
        <br>    
        <button id='tenementWaterSubmit' type="button" class="btn btn-primary">Lưu mới</button> &nbsp;
        <a href="{!! route('TenementWater') !!} " class="btn btn-info">Trở về màn hình trước</a>
    </div>
</div>
@endsection