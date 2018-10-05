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
<script>
    $(function () {
        callDatePicker();

        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        var year = $("#year").val();
        var month = $("#month").val();
        
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            columnDefs: [
              { className: "dt-right", "targets": [3,4,5] },
              { className: "dt-nowrap", "targets": [0,1] },
              { className: "dt-center", "targets": [2,6] }
            ],
            dom: 'lBfrtip',
            buttons: [
                'excel'
            ],
            ajax: '{!! url("monthlyreport/prepaid/anyData/'+ year + month + '") !!}',
            columns: [
                {data: 'address', name: 'address'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'area', name: 'area'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var receive_date = "";
                        if (data.receive_date != "")
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
                {data: 'payment_name', name: 'payment_name'},
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return number_format(data.money, 0, ".",",");
                    },
                },
                {data: 'comment', name: 'comment'},
            ]
        });        
        $('#flatSubmit').click(function() {
            var year = $("#year").val();
            var month = $("#month").val();
            table.ajax.url('{!! url("monthlyreport/prepaid/anyData/'+ year + month + '") !!}').load();
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
        <h2>Danh sách Căn hộ</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <table class="table table-striped table-bordered table-hover table-condensed">
            <tbody>
                <tr>
                    <td>Năm/tháng<em style='color:red'>(*)</em>
                        <select name="year" id="year">
                            <?php
                                $y = date('Y', strtotime(date('Y-m')." -1 month"));
                                $m = date('m', strtotime(date('Y-m')." -1 month"));
                                
                                for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                                    if ($y == $i)
                                        echo "<option selected=True>$i</option>";
                                    else 
                                        echo "<option>$i</option>";

                                }
                            ?>
                            </select>
                            <select name="month" id="month">
                            <?php 
                                for($i = 1 ; $i <= 12; $i++){
                                    if ($m == $i)
                                        echo "<option selected=True>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                                    else 
                                        echo "<option>" . str_pad($i,2,'0', STR_PAD_LEFT) . "</option>";
                                }
                            ?>
                        </select>
                        <button id='flatSubmit' type="button" class="btn btn-primary">Lọc Dữ Liệu Tháng</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="group-head day">
                        Căn Hộ
                    </th>
                    <th class="group-head day">
                        Chủ Hộ
                    </th>
                    <th class="group-head day">
                        Số Điện Thoại
                    </th>
                    <th class="group-head day">
                        Diện Tích
                    </th>
                    <th class="group-head day">
                        Ngày Thu Phí
                    </th>
                    <th class="group-head day">
                        Thu Từ
                    </th>
                    <th class="group-head day">
                        Người Nhận
                    </th>
                    <th class="group-head day">
                        Loại Phí
                    </th>
                    <th class="group-head day">
                        Số Tiền
                    </th>
                    <th class="group-head day">
                        Ghi Chú
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection