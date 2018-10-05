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

        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            deferLoading: 0,
            columnDefs: [
              { className: "dt-right", "targets": [3,4] },
            ],

            ajax: '{!! url("monthlyfee/paybillall/{$year_month}/anyData") !!}',
            columns: [
                {data: 'bill_no', name: 'bill_no'},
                {data: 'address', name: 'address'},
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
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var money = (data.money = data.money || 0);
                        return number_format(money, 0, ".",",");
                    },
                },

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

        $('#flatSubmit').click(function() {
            var year = $("#year").val();
            var month = $("#month").val();
            table.ajax.url('{!! url("monthlyfee/paybillall/'+ year + month + '/anyData") !!}').load();
        });

        $('select#month').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var year = $("#year").val();
            var month = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("monthlyfee/paybillall/'+ year + month + '/anyData") !!}').load();
        });

        $('select#year').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var month = $("#month").val();
            var year = optionSelected.val()
            table.clear().draw();
            table.ajax.url('{!! url("monthlyfee/paybillall/'+ year + month + '/anyData") !!}').load();
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
        <h2>Danh Sách Phí Đã Thu</h2>
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

        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover table-condensed">
                <tbody>
                    <tr>
                        <td>Chọn Phiếu Thu Tháng để lưu thông tin trả phí Tháng<em style='color:red'>(*)</em>
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
                            <button id='flatSubmit' type="button" class="btn btn-primary">Làm Mới Dữ Liệu</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h3>Danh sách Phiếu Thu</h3>
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th class="info">Quyển số</th>
                        <th class="info">Căn Hộ</th>
                        <th class="info">Phí Năm/Tháng</th>
                        <th class="info">Số tiền</th>
                        <th class="info">Chi tiết</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection