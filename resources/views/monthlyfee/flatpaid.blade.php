@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

@section('script')
{!! Html::script('js/jquery.dataTables.min.js') !!}

<script>
    $(function () {
        callDatePicker();
        
        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('FlatPaid.anyData') !!}',
            columns: [
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.action +'<br>' + 
                        '<i class="fa fa-map-marker" aria-hidden="true"></i>' + (data.address || '') + '<br>' + 
                        '<i class="fa fa-address-card" aria-hidden="true"></i>' +
                        (data.name || '_______________') + "&nbsp; <i class='fa fa-phone' aria-hidden='true'></i>" + (data.phone || '_______________') + '<br>' + 
                        (data.is_stay || 'Chưa ở') + '<br>' +
                        data.comment;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var receive_date = "Chưa nhận";
                        if (data.receive_date != "" && data.receive_date != null)
                        {
                            var dateString  = data.receive_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            receive_date = day + '/' + month + '/' + year;
                        }

                        return (data.area || '0') +'<span class="badge unit">m2</span> <br>' + 
                        (data.persons || '0') + '<i class="fa fa-user-o" aria-hidden="true"></i><br>' + 
                        receive_date;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.prepaid;
                    },
                },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.payment;
                    },
                },
                {data: null, 
                    render: function ( data, type, row ) {
                        return data.otherpayment;
                    },},
            ]
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
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th width="310px" class="group-head info">
                        Căn Hộ<br>Số<br>Chủ Hộ<br>Đang ở
                    </th>
                    <th class="group-head day">
                        Diện tích<br>Số nhân khẩu<br>Ngày nhận
                    </th>
                    <th class="group-head period">Trả Trước</th>
                    <th class="group-head period">Thu Phí Tháng</th>
                    <th class="group-head period">Thu Hộ</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

