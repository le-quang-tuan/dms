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

        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000); 

        $("#paybillFlatSubmit").click(function(){
            bootbox.confirm("Thông tin thu phí sẽ được lưu?", function(result) {
                if(result){
                    $("#frmpaybillFlat").submit();
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
    });
</script>

@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Chi Tiết Phiếu Thu</h2>
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
                Message sẽ tự động đóng trong <b id="show-time">5</b> giây      
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

        <form id='frmpaybillFlat' action="{!! route('PaybillFlat.save') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <input id="id" name='id' type="hidden" value="{!! $id !!}">

            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td class="col-md-3">Căn hộ<em style='color:red'>(*)</em> </td>
                        <td class="col-md-2">{!! $flat_info->flat_code !!}</td>
                        <td class="col-md-3">Chủ hộ<em style='color:red'>(*)</em></td>
                        <td class="col-md-2">{!! $flat_info->name !!}</td>

                        <td class="col-md-3">Điện thoại<em style='color:red'>(*)</em></td>
                        <td class="col-md-2">{!! $flat_info->phone !!}</td>
                    </tr>
                    <tr>
                        <td>Bill Tháng<em style='color:red'>(*)</em></td>
                        <td class="col-md-1">
                            {!! $tf_paybill_hd->year_month !!}
                        </td>
                        <td>Nội dung thu<em style='color:red'>(*)</em></td>
                        <td colspan="3">
                            <input type="text" value="{!! old('comment', 'Thu Phí Tháng') !!}" id="comment" name="comment" size="30%">
                        </td>
                    </tr>
                    <tr>
                        <td>Ngày nhận<em style='color:red'>(*)</em></td>
                        <td>
                            <input type="text" value="{!! old('receive_date', date('d/m/Y')) !!}" id="receive_date" name="receive_date" size="10%">
                        </td>
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
                        <td>
                        <!-- {!! $tf_paybill_hd->money !!} -->
                            <?php $step = 1; 
                                $sum = 0;?>
                                @foreach($tf_paybill_dt as $paybill)
                                    <?php $step++; $sum += $paybill->money; ?>
                                @endforeach

                            <input type="text" value="{!! old('money', number_format($sum)) !!}" id="money" name="money" size="10%"  readonly="readonly">
                        </td>
                        <td>Lưu Quyển số<em style='color:red'>(*)</em></td>
                        <td>
                            {!! $tf_paybill_hd->book_bill !!}
                        </td>
                        <td>Số bill<em style='color:red'>(*)</em></td>
                        <td>
                            {!! $tf_paybill_hd->bill_no !!}
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        @if ($tf_paybill_hd->paid_flag == 0)
        <div>
            <br>
            <button id='paybillFlatSubmit' type="button" class="btn btn-primary">Thu Phí</button>            
        </div>
        @endif
    </div>
    <div class="col-lg-12">
        <h3>Phí Thu</h3>
        <table class="table table-bordered hover dataTable no-footer" id="users-table">
            <tbody>
                <tr role="row">
                    <th class="info">STT</th>
                    <th class="info">Phí Năm/Tháng</th>
                    <th class="info">Phí Thu</th>
                    <th class="info">Số tiền</th>
                </tr>
            
            <?php $step = 1;?>
            @foreach($tf_paybill_dt as $paybill)
                <tr>                    
                    <td align="center"><span id="snum">{!! $step !!}.</span></td>
                    <td class="text-center">
                        {!! 
                        substr($paybill->year_month, 4, 2) . '/' . substr($paybill->year_month, 0,4)
                         !!}
                    </td>

                    <td class="text-center">
                        {!! 
                        $paybill->name !!}
                    </td>
                    <td class="text-center">
                        {!! number_format($paybill->money) !!}
                    </td>
                </tr>
                <?php $step++;?>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection