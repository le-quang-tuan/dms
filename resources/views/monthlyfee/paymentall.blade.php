@extends('include.layout')

@section('style')
<style>    
.cImgPassport{
    max-width: 100%;
}
</style>
@endsection
@section('script')
<script>
    // Replace the <textarea id="address"> with a CKEditor
    // instance, using default configuration.
    // auto dissable success message.
    var settimmer = 0;

    $(function () {
        callDatePicker();

        $('#users-table').DataTable({
            "fnInitComplete": function(){
                // Disable TBODY scoll bars
                $('.dataTables_scrollBody').css({
                    'overflow': 'hidden',
                    'border': '0'
                });
                
                // Enable TFOOT scoll bars
                $('.dataTables_scrollFoot').css('overflow', 'auto');
                
                $('.dataTables_scrollHead').css('overflow', 'auto');
                
                // Sync TFOOT scrolling with TBODY
                $('.dataTables_scrollFoot').on('scroll', function () {
                    $('.dataTables_scrollBody').scrollLeft($(this).scrollLeft());
                });      
                
                $('.dataTables_scrollHead').on('scroll', function () {
                    $('.dataTables_scrollBody').scrollLeft($(this).scrollLeft());
                });
            },
            "scrollX": true,
            "scrollCollapse": true,
            "colResize": {
                "tableWidthFixed": false,
                //"handleWidth": 10,
                "resizeCallback": function(column)
                {

                }
            },


            processing: true,
            serverSide: false,
            deferLoading: 0,
            ajax: '{!! url("monthlyfee/paid/all/anyData") !!}',
            columns: [
                {data: 'address', name: 'address'},
                {data: 'name', name: 'name'},
                {data: 'bill_no', name: 'bill_no'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var receive_date = "";
                        if (data.receive_date != "" && data.receive_date != null)
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
                {data: 'paidbill', name: 'paidbill'},
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
        // $("#paidFlatSubmit").manualSubmit('frmpaidFlat');

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
            
            $('.check_all').prop("checked", false); 
            //addDangerClass();
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
            <h3>Danh sách Phiếu Thu</h3>
            <table class="table table-bordered hover" id="users-table">
                <thead>
                    <tr>
                        <th class="info">Căn hộ</th>
                        <th class="info">Chủ hộ</th>
                        <th class="info">Quyển số</th>
                        <th class="info">Ngày nhận</th>
                        <th class="info">Nhận từ</th>
                        <th class="info">Người nhận</th>
                        <th class="info">Số tiền</th>
                        <th class="info">Ghi chú</th>
                        <th class="info">Tải Phiếu Thu</th>
                        <th class="info">Chi tiết</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection