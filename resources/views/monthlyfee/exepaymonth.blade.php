@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

<style media="all" type="text/css">
    .alignRight { text-align: right; font-size: 14px }
    .numericCol{
        text-align: right;
    }
</style>

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
    $(function () {
        callDatePicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        var year = $("#year").val();
        var month = $("#month").val();
        
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! url("monthlyfee/exepaymonth/anyData") !!}',
            columns: [
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
                        return data.year_month; //data.year_month.substring(4,6) +'/' + data.year_month.substring(0,4);
                    },
                },
                {data: 'flat_count', name: 'flat_count'},
                {data: 'flat_count_finish', name: 'flat_count_finish'},
                {data: 'begin_at', name: 'begin_at'},
                {data: 'paybill', name: 'begin_at'},
                {data: 'paymentnotice', name: 'begin_at'},
            ]
        });        
        // $("#exePaymonthSubmit").click(function(){
        //     bootbox.confirm("Tất cả căn hộ sẽ được thực hiện quyết sổ. <br> <h3>Chú ý: chỉ nên thực hiện khi các chỉ số (điện, nước...) đã được cập nhật trước.</>", function(result) {
        //         if(result){
        //             $("#frmPaymonthSubmit").submit();
        //         }
        //     });
        // });

        $("#exePaymonthSubmit").click(function(){
            var year = $("#year").val();
            var month = $("#month").val();

            bootbox.confirm("Kết sổ sẽ được thực hiện, thời gian mất khoảng 10 phút?", function(result) {
                if(result){
                    $.ajax({
                        type: "POST",
                        cache: false,
                        url : "{!! route('Paymonth.exex_store') !!}",
                        data: { 
                            "year" : year,
                            "month" : month,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(data) {

                        }
                    })
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
        <h2>Thực Hiện Quyết Sổ</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('paymentExe-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('paymentExe-alert-' . $msg) }} &nbsp; <div style="display: none;" > <b id="show-time">2</b> </div>  
        </p>
        @endif
        @endforeach
    </div> 
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
    
    <div class="col-lg-12">
        <form id='frmPaymonthSubmit' action="{!! route('Paymonth.exex_store') !!}" method="POST" role="form" method="post">
        {!! csrf_field() !!}

        <table class="table table-striped table-bordered table-hover table-condensed">
            <tbody>
                <tr>
                    <td>Kết sổ xuất Thông báo Phí và Phiếu Thu Tháng<em style='color:red'>(*)</em> 
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
                                        echo "<option selected=True>$i</option>";
                                    else 
                                        echo "<option>$i</option>";

                                }
                            ?>
                        </select>
                        <button id='exePaymonthSubmit' type="button" class="btn btn-primary">Thực Hiện Quyết Sổ Tháng</button>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
        
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th width="410px" class="group-head info">
                        Quyết Sổ Tháng Năm
                    </th>
                    <th class="group-head day">
                        Tổng Số Căn Hộ <br> Cần Quyết Sổ
                    </th>
                    <th class="group-head day">
                        Tổng Số Căn Hộ <br> Đã Quyết Sổ
                    </th>
                    <th class="group-head day">
                        Thực Hiện Lúc
                    </th>
                    <th class="group-head day">
                        Danh sách file Thông Báo Phí
                    </th>
                    <th class="group-head day">
                        Danh sách file Phiếu Thu
                    </th>

                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

