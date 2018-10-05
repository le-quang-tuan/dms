@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

@section('script')
{!! Html::script('js/jquery.dataTables.min.js') !!}

<script>
    $(function () {
        //name, contactname, addr, tel, contractrate 0 no 1 yes, note, area_name
        $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! url("/tech/tech/schedule/{$id}") !!}',
            columns: [
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        return data.plan_date +' ' + 
                        data.plan_start_time + '~' + data.plan_end_time ;
                    },
                },
                {data: 'plan_for', name: 'plan_for'},
                {data: 'plan_description', name: 'plan_description'},
                {data: 'plan_company_execute', name: 'plan_company_execute'},
                {data: 'action', name: 'action'},
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
        <h2>Danh Sách Kế Hoạch</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <table class="table table-striped table-bordered table-hover table-condensed">
            <tbody>
                <tr>
                    <td>Nhóm<em style='color:red'>(*)</em> </td>
                    <td colspan="3">
                        <?php
                            $equipment_group_id = $tenementEquipment->equipment_group_id;

                            echo '<select name="equipment_group_id" id="equipment_group_id" style="width: 150px;">';
                            foreach($tenement_equipment_groups as $tenement_equipment_group) {
                                if ($tenement_equipment_group->id == $equipment_group_id){
                                    echo '<option selected ="selected" value="'. $tenement_equipment_group->id . '">';
                                    echo $tenement_equipment_group->name;
                                    echo '</option>';
                                }
                                else
                                {
                                    echo '<option value="'. $tenement_equipment_group->id . ' ">';
                                    echo $tenement_equipment_group->name;
                                    echo '</option>';
                                }
                            }
                        echo '</select>';
                        ?>

                    </td>
                </tr>
                <tr>
                    <td>Tên</td>
                    <td><input type="text" value="{!! $tenementEquipment->name !!}" id="name" name="name" size="30%"></td>

                    <td>Nhà cung cấp</td>
                    <td>
                        <?php
                            $producer_id = $tenementEquipment->producer_id;

                            echo '<select name="producer_id" id="producer_id" style="width: 150px;">';
                            foreach($tenement_producers as $tenement_producer) {
                            if ($tenement_producer->id == $producer_id){
                                echo '<option selected ="selected" value="'. $tenement_producer->id . '">';
                                echo $tenement_producer->name;
                                echo '</option>';
                            }
                            else
                            {
                                echo '<option value="'. $tenement_producer->id . ' ">';
                                echo $tenement_producer->name;
                                echo '</option>';
                            }
                        }
                        echo '</select>';
                        ?>
                    </td>

                    <td>Nhãn hiệu</td>
                    <td><input type="text" value="{!! $tenementEquipment->label !!}" id="label" name="label" size="30%"></td>
                </tr>
                <tr>
                    <td>Model</td>
                    <td><input type="text" value="{!! $tenementEquipment->model !!}" id="model" name="model" size="30%"></td>

                    <td>Thông số kỹ thuật</td>
                    <td><input type="text" value="{!! $tenementEquipment->specification !!}" id="specification" name="specification" size="30%"></td>

                    <td>Khu vực sử dụng</td>
                    <td><input type="area" value="{!! $tenementEquipment->area !!}" id="area" name="area" size="30%"></td>
                </tr>
                <tr>
                    <td>Ghi chú<em style='color:red'>(*)</em></td>
                    <td colspan="5"><input type="text" value="{!! $tenementEquipment->comment !!}" id="comment" name="comment" size="50%"></td>
                <tr>
            </tbody>
        </table>
    </div>
    <div class="col-lg-12">
        <table class="table table-bordered hover" id="users-table">
            <thead>
                <tr>
                    <th class="info">Thời gian kế hoạch</th>
                    <th class="info">Người đảm nhiệm</th>
                    <th class="info">Hạng mục</th>
                    <th class="info">Công ty thực hiện</th>
                    <th class="info">Xem Chi Tiết</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

