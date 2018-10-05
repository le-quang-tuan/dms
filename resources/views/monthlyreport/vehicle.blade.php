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
        var a = {!! json_encode($parking_tariffs) !!};
        var colum = [
                {data: 'address', name: 'address'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'area', name: 'area'},
                ];

        for (var i = 0; i < a.length; i++){
            colum.push({data: 'Vehicle' + a[i].id, name: a[i].id});
        }

        colum.push({data: 'owner', name: 'owner'});
        colum.push({data: 'label', name: 'label'});
        colum.push({data: 'maker', name: 'maker'});
        colum.push({data: 'color', name: 'color'});
        colum.push({data: 'begin_contract_date', name: 'begin_contract_date'});
        colum.push({data: 'end_contract_date', name: 'end_contract_date'});
        colum.push({data: 'v_comment', name: 'v_comment'});

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
                {
                    extend: 'excel',
                    filename: function(){
                        var month = $("#month").val();
                        var year = $("#year").val();
                        return 'XePhatSinhThang_' + month + year;
                    },
                }
            ],
            ajax: '{!! url("monthlyreport/vehicle/anyData/'+ year + month + '") !!}',
            columns: colum
        });        
        $('#flatSubmit').click(function() {
            var year = $("#year").val();
            var month = $("#month").val();
            table.ajax.url('{!! url("monthlyreport/vehicle/anyData/'+ year + month + '") !!}').load();
        });
    });

</script>
@endsection

@section('title', 'DanhSachXePhatSinhThang')
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
                    @foreach($parking_tariffs as $parking_tariff)
                        <th  class="group-head day">
                            {!! $parking_tariff->name !!}
                        </th>    
                    @endforeach
                    
                    <th class="group-head day">
                        Chủ xe
                    </th>
                    <th class="group-head day">
                        Hiệu Xe
                    </th>
                    <th class="group-head day">
                        Loại Xe
                    </th>
                    <th class="group-head day">
                        Màu Xe
                    </th>
                    <th class="group-head day">
                        Ký Nhận
                    </th>
                    <th class="group-head day">
                        Ngày Hủy
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