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

        $("#tenementElecSubmit").click(function(){
            bootbox.confirm("Thông tin Biểu Phí sẽ được xóa?", function(result) {
                if(result){
                    $("#frmTenementElec").submit();
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
                data+="<td><input name='name"+count+"' id='name_"+count+"' class='form-control service' '></td>";
                data+="<td><input name='index_from"+count+"' id='index_from_"+count+"' class='form-control service' '></td>";
                data+="<td><input name='price"+count+"' id='price_"+count+"' class='form-control service commas' '></td>";
                data+="<td><input name='vat"+count+"' id='vat_"+count+"' class='form-control service' '></td>";
                data+="<td><input name='other_fee01"+count+"' id='other_fee01_"+count+"' class='form-control service' '></td>";
                data+="<td><input name='other_fee02"+count+"' id='other_fee02_"+count+"' class='form-control service''></td></tr>";
                
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
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Tạo mới biểu phí Điện</h2>
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
                {{ Session::get('tenementElec-alert-' . $msg) }} &nbsp; <div style="display: none;" > <b id="show-time">2</b> </div>                            
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

        <form id='frmTenementElec' action="{!! route('TenementElec.store') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <table class="table table-condensed xs table-striped">
                <tbody>
                    <tr>
                        <td>Biểu phí điện sử dụng<em style='color:red'>(*)</em> </td>
                        <td><input type="tel" value="{!! old('name') !!}" id="name" name="name" size="50%"></td>
                        <td>Ghi chú</td>
                        <td><input type="text" value="{!! old('comment') !!}" id="comment" name="comment" size="50%"></td>
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
                        <th width="200px" class="info">Đơn giá<span class="badge unit">VND/kwh</span></th>
                        <th width="100px" class="info">VAT<span class="badge unit">%</span></th>
                        <th width="100px" class="info">Hao Hụt<span class="badge unit">%</span></th>
                        <th width="100px" class="info">Khác<span class="badge unit">%</span></th>
                    </tr>
                    <?php 
                        $step = Session::get('counter');
                    ?>
                    @for($i = 1; $i <=  $step ; $i++)
                    <tr class='new'>
                        <td class='text-center'><input type='checkbox' class='case'/></td>
                        <td class='text-center'><span id='snum{{$i}}'>{{ $i }}.</span><input type='hidden' value='0' name='counter[]'></td>
                        <td><input name='name{{$i}}' id='name_{{$i}}' value="{{ old('name' . $i) }}" class='form-control service'></td>
                        <td><input name='index_from{{$i}}' id='index_from_{{$i}}' value="{{ old('index_from' . $i) }}"  class='form-control service'></td>
                        <td><input name='price{{$i}}' id='price_{{$i}}' value="{{ old('price' . $i) }}" class='form-control service commas'></td>
                        <td><input name='vat{{$i}}' id='vat_{{$i}}' value="{{ old('vat' . $i) }}" class='form-control service'></td>
                        <td><input name='other_fee01{{$i}}' id='other_fee01_{{$i}}'  value="{{ old('other_fee01' . $i) }}" class='form-control service'></td>
                        <td><input name='other_fee02{{$i}}' id='other_fee02_{{$i}}' value="{{ old('other_fee02' . $i) }}" class='form-control service'></td>
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
        </form>
        <div>
            <br>    
            <button id='tenementElecSubmit' type="button" class="btn btn-primary">Lưu mới</button> &nbsp;
            <a href="{!! route('TenementElec') !!} " class="btn btn-info">Trở về màn hình trước</a>
        </div>
    </div>
</div>
@endsection