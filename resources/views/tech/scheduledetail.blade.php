@extends('include.layout')

@section('style')
<style>    
.cImgPassport{
    max-width: 50%;
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
        //CKEDITOR.replace( 'comment' );    

        //auto dissable success message
        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        
        
        $("#updateSubmit").click(function(){
            bootbox.confirm("Thông tin Kế Hoạch - Báo Cáo sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmUpdate").submit();
                }
            });
        }); 

        $("#deleteSubmit").click(function(){
            bootbox.confirm("Thông tin Kế Hoạch sẽ được xóa?", function(result) {
                if(result){
                    $("#frmDelete").submit();
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
        <h2>Thông tin Kế Hoạch và Thực Tế đã thực hiện</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementEquipment-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('tenementEquipment-alert-' . $msg) }}<br>
            The message will dissable with in <b id="show-time">2</b> seconds                            
        </p>
        @endif
        @endforeach
        <!-- end .flash-message -->
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    <div>
        <form id='frmUpdate' action="{!! route('EquipMainte.update') !!}" method="POST" role="form">
            <div class="modal-body">
                <table class="table table-striped table-bordered table-hover table-condensed">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
                    <input id="id" name='id' type="hidden" value="{!! $schedules->id !!}">
                    <tbody>
                        <tr>
                            <td>Ngày Kế Hoạch/ Thực hiện<em style='color:red'>(*)</em> </td>
                            <td><input type="text" id="plan_date" name="plan_date" value="{!! $schedules->plan_date !!}" size="30%"></td>
                            <td><input type="text" id="report_date" name="report_date" value="{!! $schedules->report_date !!}" size="30%"></td>
                        </tr>
                        <tr>
                            <td>Thời gian<em style='color:red'>(*)</em> </td>
                            <td><input type="text" value="{!! $schedules->plan_start_time !!}" id="plan_start_time" name="plan_start_time" size="10%"> ~ 
                            <input type="text" value="{!! $schedules->plan_end_time !!}" id="plan_end_time" name="plan_end_time" size="10%"></td>

                            <td><input type="text" value="{!! $schedules->report_start_time !!}" id="report_start_time" name="report_start_time" size="10%"> ~ 
                            <input type="text" value="{!! $schedules->report_end_time !!}" id="report_end_time" name="report_end_time" size="10%"></td>
                        </tr>
                        <tr>
                            <td>Người phụ trách<em style='color:red'>(*)</em> </td>
                            <td><input type="text" value="{!! $schedules->plan_for !!}" id="plan_for" name="plan_for" size="30%"></td>

                            <td><input type="text" value="{!! $schedules->report_for !!}" id="report_for" name="report_for" size="30%"></td>
                        </tr>
                        <tr>
                            <td>Mô tả<em style='color:red'>(*)</em> </td>
                            <td><input type="text" value="{!! $schedules->plan_description !!}" id="plan_description" name="plan_description" size="30%"></td>
                            <td><input type="text" value="{!! $schedules->report_description !!}" id="report_description" name="report_description" size="30%"></td>
                        </tr>
                        <tr>
                            <td>Công ty thực hiện<em style='color:red'>(*)</em> </td>
                            <td><input type="text" value="{!! $schedules->plan_company_execute !!}" id="plan_company_execute" name="plan_company_execute" size="30%"></td>
                            <td><input type="text" value="{!! $schedules->report_company_execute !!}" id="report_company_execute" name="report_company_execute" size="30%"></td>
                        </tr>
                        <tr>
                            <td>Hạng mục bảo trì
                            </td>
                            <td colspan="2">
                                <?php
                                    $oldItem = $schedules->category1_id;
                                    echo '<select name="category1" id="category1" style="width: 150px;">';
                                        foreach($mst_maintenance_items as $item) {
                                            $selected = ($oldItem == $item->id ? 'selected' : '');
                                            echo '<option value="'.$item->id . '" '. $selected .'>';
                                        
                                            echo $item->name;
                                            echo '</option>';
                                        }
                                    echo '</select>';
                                ?>
                            </td>                           
                        </tr>
                        <tr>
                            <td>Chi tiết
                            </td>
                            <td colspan="2"><input type="text" value="{!! $schedules->category1_note !!}" id="note1" name="note1" size="30%">
                            </td>
                        </tr>
                        <tr>
                            <td>Hạng mục bảo trì
                            </td>
                            <td colspan="2">
                                <?php
                                    $oldItem = $schedules->category2_id;
                                    echo '<select name="category2" id="category2" style="width: 150px;">';
                                        foreach($mst_maintenance_items as $item) {
                                            $selected = ($oldItem == $item->id ? 'selected' : '');
                                            echo '<option value="'.$item->id . '" '. $selected .'>';
                                        
                                            echo $item->name;
                                            echo '</option>';
                                        }
                                    echo '</select>';
                                ?>
                            </td>                           
                        </tr>
                        <tr>
                            <td>Chi tiết
                            </td>
                            <td colspan="2"><input type="text" value="{!! $schedules->category2_note !!}" id="note2" name="note2" size="30%">
                            </td>
                        </tr>
                        <tr>
                            <td>Hạng mục bảo trì
                            </td>
                            <td colspan="2">
                                <?php
                                    $oldItem = $schedules->category3_id;
                                    echo '<select name="category3" id="category3" style="width: 150px;">';
                                        foreach($mst_maintenance_items as $item) {
                                            $selected = ($oldItem == $item->id ? 'selected' : '');
                                            echo '<option value="'.$item->id . '" '. $selected .'>';
                                        
                                            echo $item->name;
                                            echo '</option>';
                                        }
                                    echo '</select>';
                                ?>
                            </td>                           
                        </tr>
                        <tr>
                            <td>Chi tiết
                            </td>
                            <td colspan="2"><input type="text" value="{!! $schedules->category3_note !!}" id="note3" name="note3" size="30%">
                            </td>
                        </tr>
                        <tr>
                            <td>Ghi chú<em style='color:red'>(*)</em> </td>
                            <td colspan="2"><input type="text" value="{!! $schedules->note !!}" id="note" name="note" size="30%"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>

        <div>
            <br>
            <button id='updateSubmit' type="button" class="btn btn-primary">Lưu</button>            
            <button id='deleteSubmit' type="button" class="btn btn-danger">Xóa</button> &nbsp;
            <a href="{!! route('EquipMainte.report') !!} " class="btn btn-info">Trở về màn hình trước</a>
            <form id='frmDelete' action="{!! route('EquipMainte.destroy') !!}" method="POST" role="form" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input id="id" name='id' type="hidden" value="{!! $schedules->id !!}">                        
            </form>
        </div>
    </div>
</div>
@endsection

