@extends('include.layout')

@section('style')
    {!! Html::style('css/datepicker/jquery-ui.css') !!}

    {!! Html::style('css/tageditor/jquery.tag-editor.css') !!}

    {!! Html::style('css/timepicker/jquery.timepicker.min.css') !!}

    {!! Html::style('css/multiselect/bootstrap-multiselect.css') !!}

    {!! Html::style('css/colorpicker/spectrum.css') !!}

<style>    
.cImgPassport{
    max-width: 50%;
}

</style>
<style>
    .btnRemoveRow {
        cursor: pointer;
        display: inline-block;
        height: 30px;
        position: relative;
        width: 28px;
    }

    .editor {
        width: 220px !important;
    }

    .label-date-field {
        float: left;
        width: 130px;
        margin-left: 15px;
    }

    .image-editor {
        height: 180px;
        width: 150px;
    }

    .last-change p {
        color: rgba(255, 0, 102, 1);
        font-style: italic;
    }

    em {
        color: rgba(255, 0, 102, 1);
    }

    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }

    .checkbox {
        margin-top: 0px;
    }

    .tr-ct-35 {
        height: 35px;
    }

    .parent {
        position: relative;
    }

    .child {
        position: absolute; 
        top: 50%; 
        transform: translateY(-50%);
    }

    .sp-preview {
        width: 120px;
    }

    .sp-replacer {
        width: 150px;
    }

    .sp-dd {
        float: right;
    }

    .sp-price {
        text-align: right;
    }

    /* IE 6 doesn't support max-height
    * we use height instead, but this forces the menu to always be this tall
    */
    * html .ui-autocomplete {
        height: 300px;
    }

    .room_no {
        width: 70px !important;
        float:left;
    }
    label {
        clear:both;
    }
    .nopadding {
       padding: 0 !important;
       margin: 0 !important;
    }

    div.tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons inside the tab */
    div.tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
    }

    /* Change background color of buttons on hover */
    div.tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    div.tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }        
</style>
@endsection

@section('script')
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
    $(function(){        
        //auto dissable success message
        document.getElementById("defaultOpen").click();
        var resident_table;

        var owner_table = $('#owner-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "tfowner/" + {!! $tenementFlat->id  !!} + "",
            dom: 'lBfrtip',
            buttons: [
                'excel', 'pdf'
            ],
            columns: [
                { data: 'content' , name: 'content' },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var input_date = "Chưa nhận";
                        if (data.input_date != "" && data.input_date != null)
                        {
                            var dateString  = data.input_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            input_date = day + '/' + month + '/' + year;
                        }

                        return input_date;
                    },
                },
                { data: 'content' , name: 'content' },
                { data: 'addRes' , name: 'addRes' },
                { data: 'destroy' , name: 'destroy' },                
            ]
        });

        var rent_table = $('#rent-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "tfrent/" + {!! $tenementFlat->id  !!} + "",
            dom: 'lBfrtip',
            buttons: [
                'excel', 'pdf'
            ],
            columns: [
                { data: 'content' , name: 'content' },
                {
                    data: null, 
                    render: function ( data, type, row ) {
                        var input_date = "Chưa nhận";
                        if (data.input_date != "" && data.input_date != null)
                        {
                            var dateString  = data.input_date;
                            var year        = dateString.substring(0,4);
                            var month       = dateString.substring(4,6);
                            var day         = dateString.substring(6,8);

                            input_date = day + '/' + month + '/' + year;
                        }

                        return input_date;
                    },
                },
                { data: 'content' , name: 'content' },
                { data: 'addRes' , name: 'addRes' },
                { data: 'destroy' , name: 'destroy' },
            ]
        });
        $('#rent-table').on('click', '.addres', function () {
            $("#res_name").val("");
            $("#res_id").val($(this).attr("id"));
            $("#res_dob").val("");
            $("#res_sex").val("");
            $("#res_type").val("");
            $("#res_email").val("");
            $("#res_phone").val("");
            $("#res_identity_card").val("");
            $("#date_issue").val("");
            $("#issued_by").val("");
            $("#res_comment").val("");
            $('#residentModal').attr('class', "modal fade in"); 
            $('#residentModal').show();

            if (!$.fn.dataTable.isDataTable( '#resident-table' ) ) {
                resident_table = $('#resident-table').DataTable({
                    scrollY: 200,
                    processing: true,
                    serverSide: false,
                    ajax: "resident/anyData/" + $(this).attr("id"),
                    dom: 'lBfrtip',
                    buttons: [
                        'excel', 'pdf'
                    ],
                    columns: [
                    {
                        data: null, 
                        render: function ( data, type, row ) {
                            var dob = "";
                            if (data.dob != "" && data.dob != null)
                            {
                                var dateString  = data.dob;
                                var year        = dateString.substring(0,4);
                                var month       = dateString.substring(4,6);
                                var day         = dateString.substring(6,8);

                                dob = day + '/' + month + '/' + year;
                            }

                            var sex = "Nam";
                            if (data.sex != "0")
                            {
                                sex = "Nữ";
                            }

                            return data.name +'<br>' + 
                            dob + '<br>' + sex;
                        },
                    },
                    {
                        data: null, 
                        render: function ( data, type, row ) {
                            var date_issue = "";
                            if (data.date_issue != "" && data.date_issue != null)
                            {
                                var dateString  = data.date_issue;
                                var year        = dateString.substring(0,4);
                                var month       = dateString.substring(4,6);
                                var day         = dateString.substring(6,8);

                                date_issue = day + '/' + month + '/' + year;
                            }

                            var sex = "Nam";
                            if (data.sex != "0")
                            {
                                sex = "Nữ";
                            }

                            return data.identity_card +'<br>' + 
                            date_issue + '<br>' + data.issued_by;
                        },
                    },
                    {
                        data: null, 
                        render: function ( data, type, row ) {
                            return data.email +'<br>' + 
                            (data.phone || '') + '<br>' + 
                            (data.comment || '_______________')
                        },
                    },
                    ]
                });     
            }    
            else {
                resident_table.clear().draw();
                resident_table.ajax.url("resident/anyData/" + $(this).attr("id")).load();
            }   
        });
        $('#owner-table').on('click', '.destroy', function () {
            var urlGet = "flat/detail/resident/destroy";
            var id = $(this).attr("id");
            var flat_id = $(this).attr("flat_id");
            var confirmTable = "<h2>Dữ liệu sẽ được xóa?</h2>";
            bootbox.confirm(confirmTable, function (result) {
                if (result == true){
                    jQuery.ajax({
                        type: "POST",
                        url: '{!! url("'+ urlGet +'") !!}',
                        data: { 
                            "id" : id,
                            "flat_id" : flat_id,
                            "_token": "{{ csrf_token() }}",
                        },
                        error:function(msg){
                            alert( "Error !: " + msg );
                        },
                        success:function(data){
                            owner_table.clear().draw();
                            owner_table.ajax.url("tfowner/" + {!! $tenementFlat->id  !!},).load();
                        }
                    });
                }
            });
        });
        $('#rent-table').on('click', '.destroy', function () {
            var urlGet = "flat/detail/resident/destroy";
            var id = $(this).attr("id");
            var flat_id = $(this).attr("flat_id");
            var confirmTable = "<h2>Dữ liệu sẽ được xóa?</h2>";
            bootbox.confirm(confirmTable, function (result) {
                if (result == true){
                    jQuery.ajax({
                        type: "POST",
                        url: '{!! url("'+ urlGet +'") !!}',
                        data: { 
                            "id" : id,
                            "flat_id" : flat_id,
                            "_token": "{{ csrf_token() }}",
                        },
                        error:function(msg){
                            alert( "Error !: " + msg );
                        },
                        success:function(data){
                            rent_table.clear().draw();
                            rent_table.ajax.url("tfrent/" + {!! $tenementFlat->id  !!},).load();
                        }
                    });
                }
            });
        });
        $('#owner-table').on('click', '.addres', function () {
            $("#res_name").val("");
            $("#res_id").val($(this).attr("id"));
            $("#res_dob").val("");
            $("#res_sex").val("");
            $("#res_type").val("");
            $("#res_email").val("");
            $("#res_phone").val("");
            $("#res_identity_card").val("");
            $("#date_issue").val("");
            $("#issued_by").val("");
            $("#res_comment").val("");

            $('#residentModal').attr('class', "modal fade in"); 
            $('#residentModal').show();
            if (!$.fn.dataTable.isDataTable( '#resident-table' ) ) {
                resident_table = $('#resident-table').DataTable({
                    scrollY: 200,
                    processing: true,
                    serverSide: false,
                    ajax: "resident/anyData/" + $(this).attr("id"),
                    dom: 'lBfrtip',
                    buttons: [
                        'excel', 'pdf'
                    ],
                    columns: [
                    {
                        data: null, 
                        render: function ( data, type, row ) {
                            var dob = "";
                            if (data.dob != "" && data.dob != null)
                            {
                                var dateString  = data.dob;
                                var year        = dateString.substring(0,4);
                                var month       = dateString.substring(4,6);
                                var day         = dateString.substring(6,8);

                                dob = day + '/' + month + '/' + year;
                            }

                            var sex = "Nam";
                            if (data.sex != "0")
                            {
                                sex = "Nữ";
                            }

                            return data.name +'<br>' + 
                            dob + '<br>' + sex;
                        },
                    },
                    {
                        data: null, 
                        render: function ( data, type, row ) {
                            var date_issue = "";
                            if (data.date_issue != "" && data.date_issue != null)
                            {
                                var dateString  = data.date_issue;
                                var year        = dateString.substring(0,4);
                                var month       = dateString.substring(4,6);
                                var day         = dateString.substring(6,8);

                                date_issue = day + '/' + month + '/' + year;
                            }

                            var sex = "Nam";
                            if (data.sex != "0")
                            {
                                sex = "Nữ";
                            }

                            return data.identity_card +'<br>' + 
                            date_issue + '<br>' + data.issued_by;
                        },
                    },
                    {
                        data: null, 
                        render: function ( data, type, row ) {
                            return data.email +'<br>' + 
                            (data.phone || '') + '<br>' + 
                            (data.comment || '_______________')
                        },
                    },
                    ]
                });     
            }    
            else {
                resident_table.clear().draw();
                table.ajax.url("resident/anyData/" + $(this).attr("id")).load();
            }  
        });

        window.setInterval(function () {
            var timeCounter = $("b[id=show-time]").html();
            var updateTime = eval(timeCounter) - eval(1);
            $("b[id=show-time]").html(updateTime);            
            if (updateTime == 0) {
                $(".flash-message").html('');
            }
        }, 1000);        
        @role('admin|manager|moderator')
            $("#tenementFlatSubmit").click(function(){
                bootbox.confirm("Thông tin Căn hộ sẽ được cập nhật?", function(result) {
                    if(result){
                        $("#frmtenementFlat").submit();
                    }
                });
            }); 

            $("#tenementFlatDelete").click(function(){
                bootbox.confirm("Thông tin Căn hộ sẽ được xóa?", function(result) {
                    if(result){
                        $("#frmCom").submit();
                    }
                });
            });
        @endrole

        @role('admin|manager|moderator')      
            $(addButtonCreateOwner()).insertAfter($("#owner-table_length"));
            function addButtonCreateOwner() {
                var html = "";
                html = "<div class='col-md-2'>";
                html+= "<button id='changeOwner' type='button' class='btn btn-xs btn-primary'>Thay đổi chủ hộ</button>";
                return html;
            }

            $(addButtonCreateRent()).insertAfter($("#rent-table_length"));
            function addButtonCreateRent() {
                var html = "";
                html = "<div class='col-md-2'>";
                html+= "<button id='changeRent' type='button' class='btn btn-xs btn-primary'>Thay đổi khách thuê</button>";
                return html;
                return html;
            }            
        @endrole   
        $("#exeSubmit").click(function(){
            bootbox.confirm("Thông tin sổ tay ghi chú sẽ được cập nhật?", function(result) {
                    if(result){
                        $("#frmSubmit").submit();
                    }
            });
        });
        $("#addResSubmit").click(function(){
            bootbox.confirm("Thông tin nhân khẩu sẽ được cập nhật?", function(result) {
                    if (result == true){
                        jQuery.ajax({
                            type: "POST",
                            url: "{!! route('TfResAdd.create') !!}",
                            data: { 
                                "_token": "{{ csrf_token() }}",
                                "res_name": $("#res_name").val(),
                                "res_id" : $("#res_id").val(),
                                "res_dob" : $("#res_dob").val(),
                                "res_sex" : $("input[name=res_sex]").val(),
                                "res_type" : $("input[name=res_type]").val(),
                                "res_email" : $("#res_email").val(),
                                "res_phone" : $("#res_phone").val(),
                                "res_identity_card" : $("#res_identity_card").val(),
                                "date_issue" : $("#date_issue").val(),
                                "issued_by" : $("#issued_by").val(),
                                "res_comment" : $("#res_comment").val(),
                            },
                            error:function(msg){
                                alert( "Error !: " + msg );
                            },
                            success:function(data){
                                resident_table.clear().draw();
                                resident_table.ajax.url("resident/anyData/" + data).load();
                            }
                        });
                    }
            });
        });                

        $("#res_close").click(function(){
            $('#residentModal').hide();
        });

        $("#res_hide").click(function(){
            $('#residentModal').hide();
        });

        $("#close").click(function(){
            $('#reportmodAl').hide();
        });

        $("#hide").click(function(){
            $('#reportmodAl').hide();
        });
      
        $("#changeOwner").click(function(){
            $("#content").val("");
            $("#input_date").val("");
            $("#stay_type").val("0");

            $("#comment").val("");

            $('#reportmodAl').attr('class', "modal fade in"); 
            $('#reportmodAl').show();
        });          
        $("#changeRent").click(function(){
            $("#content").val("");
            $("#input_date").val("");
            $("#stay_type").val("1");

            $("#comment").val("");

            $('#reportmodAl').attr('class', "modal fade in"); 
            $('#reportmodAl').show();
        });                      
    });
    function openCity(evt, cityName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }
</script>

@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thông Tin Căn hộ</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('tenementFlat-alert-' . $msg))
        <p class="alert alert-{{ $msg }}">
            {{ Session::get('tenementFlat-alert-' . $msg) }} &nbsp; <div style="display: none;" > <b id="show-time">2</b> </div>  
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

    <form id='frmtenementFlat' action="{!! route('TenementFlatDetail.update') !!}" method="POST" role="form" method="post">
        <table class="table table-condensed xs table-striped">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">                
            <input id="id" name='id' type="hidden" value="{!! $tenementFlat->id !!}">
            <tbody>
                <tr>
                    <td>Căn Hộ<em style='color:red'>(*)</td>
                    <td colspan="3">
                        <div class="col-sm-10 nopadding">
                        <div class="col-sm-1 nopadding" style="text-align: left;">Khu</div>
                        <div class="col-sm-2 nopadding" style="text-align: left;">
                            <?php
                                $block_name = substr($tenementFlat->block_name,0,1);
                                $block_sub = substr($tenementFlat->block_name,1,strlen($tenementFlat->block_name));

                                $floor_num = $tenementFlat->floor_num;
                                $floor_name = $tenementFlat->floor_name;
                                $flat_num = $tenementFlat->flat_num;
                                echo '<select name="block_name" id="block_name">';
                                foreach($lsChar as $char) {
                                    echo $block_name;
                                    if ($char == $block_name){
                                        echo '<option selected ="selected" value="'. $char . '">';
                                        echo $char;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'.$char . '">';
                                        echo $char;
                                        echo '</option>';
                                    }
                                }
                                echo '</select>';

                                echo '<select name="block_sub" id="block_sub">';

                                if ('' == $block_sub){
                                    echo '<option selected ="selected" value="">';
                                    echo '';
                                    echo '</option>';
                                }
                                else
                                {
                                    echo '<option value="">';
                                    echo '';
                                    echo '</option>';
                                }

                                foreach($lsBlockSub as $char) {
                                    echo $block_sub;
                                    if ($char == $block_sub && '' != $block_sub){
                                        echo '<option selected ="selected" value="'. $char . '">';
                                        echo $char;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'.$char . '">';
                                        echo $char;
                                        echo '</option>';
                                    }
                                }
                                echo '</select>';
                            ?>
                        </div>
                        <div class="col-sm-1 nopadding" style="text-align: right;">Tầng &nbsp;</div>
                        <div class="col-sm-2 nopadding">
                            <?php 
                                echo '<select name="floor_num" id="floor_num">';
                                foreach($lsNum as $num) {
                                    echo $floor_num;
                                    if ($num == $floor_num){
                                        echo '<option selected ="selected" value="'. $num . '">';
                                        echo $num;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'.$num . '">';
                                        echo $num;
                                        echo '</option>';
                                    }
                                }
                                echo '</select>';

                                echo '<select name="floor_name" id="floor_name">';
                                if ('' == $floor_name){
                                    echo '<option selected ="selected" value="">';
                                    echo '';
                                    echo '</option>';
                                }
                                else
                                {
                                    echo '<option value="">';
                                    echo '';
                                    echo '</option>';
                                }


                                foreach($lsChar as $char) {
                                    echo $floor_name;
                                    if ($char == $floor_name){
                                        echo '<option selected ="selected" value="'. $char . '">';
                                        echo $char;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'. $char . '">';
                                        echo $char;
                                        echo '</option>';
                                    }
                                }
                                echo '</select>';
                            ?>
                        </div>
                        <div class="col-sm-1 nopadding" style="text-align: right;">Hộ Số&nbsp;</div>
                        <div class="col-sm-1 nopadding"><input type="text" value="{!! $flat_num !!}" id="flat_num" name="flat_num" size="3%">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Địa chỉ căn hộ thực tế<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenementFlat->address !!}" id="address" name="address" size="30%"></td>
                    <td>Phí quản lý<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenementFlat->manager_price !!}" id="manager_price" name="manager_price" size="30%"></td>
                </tr>
                <tr>
                    <td>Diện tích<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenementFlat->area !!}" id="area" name="area" size="30%"></td>
                    <td>Số nhân khẩu<em style='color:red'>(*)</em></td>
                    <td colspan="5"><input type="text" value="{!! $tenementFlat->persons !!}" id="persons" name="persons" size="30%"></td>
                </tr>
                <tr>
                    <td>Ngày nhận căn hộ<em style='color:red'>(*)</em></td>
                    <td>

                    @if ($tenementFlat->receive_date != '')
                    <input type="text" value="{!! 
                    isset($tenementFlat->receive_date) ? 
                        DateTime::createFromFormat('Ymd',$tenementFlat->receive_date)->format('d/m/Y') : ''
                     !!}" id="receive_date" name="receive_date" size="10%" class="date-picker">
                    @else
                    <input type="text" value="{!! $tenementFlat->receive_date
                     !!}" id="receive_date" name="receive_date" size="10%" class="date-picker">                    
                    @endif
                    <input type="radio" name="manager_fee_recal_flg" value="0"
                    {!! ($tenementFlat->manager_fee_recal_flg || '0') == '0' ? 'checked' : '' !!}
                    >Tính lại phí quản lý &nbsp;
                    <input type="radio" name="manager_fee_recal_flg" value="1" {!! ($tenementFlat->manager_fee_recal_flg || '0') == '1' ? 'checked' : '' !!}>Không tính lại phí quản lý
                    </td>
                    <td>Hiện đang sử dụng<em style='color:red'>(*)</em></td>
                    <td colspan="5">
                        <select name="is_stay" id="is_stay">
                            <option value="0">
                                Đang ở
                            </option>
                            <option value="1">
                                Chưa ở
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Biểu phí tiêu thụ</td>
                        <td colspan="3">
                            <div class="col-sm-10 nopadding">
                                <div class="col-sm-1 nopadding" style="text-align: left;">Điện</div>
                                <div class="col-sm-2 nopadding" style="text-align: left;">
                                <?php
                                    $elec_type_id = $tenementFlat->elec_type_id;
                                    $water_type_id = $tenementFlat->water_type_id;
                                    $gas_type_id = $tenementFlat->gas_type_id;

                                    echo '<select name="elec_type" id="elec_type" style="width: 150px;">';
                                    foreach($elec_tariffs as $elec_type) {
                                    if ($elec_type->id == $elec_type_id){
                                        echo '<option selected ="selected" value="'. $elec_type->id . '">';
                                        echo $elec_type->elec_type;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'. $elec_type->id . ' ">';
                                        echo $elec_type->elec_type;
                                        echo '</option>';
                                    }
                                }
                                echo '</select>';
                                ?>
                                </div>
                            <div class="col-sm-1 nopadding" style="text-align: right;">Nước</div>
                            <div class="col-sm-2">
                                <?php
                                    echo '<select name="water_type" id="water_type" style="width: 150px;">';
                                    foreach($water_tariffs as $water_type) {
                                    if ($water_type->id == $water_type_id){
                                        echo '<option selected ="selected" value="'. $water_type->id . '">';
                                        echo $water_type->water_type;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'. $water_type->id . ' ">';
                                        echo $water_type->water_type;
                                        echo '</option>';
                                    }
                                }
                                echo '</select>';
                                ?>
                            </div>
                            <div class="col-sm-1 nopadding" style="text-align: right;">Gas</div>
                            <div class="col-sm-2">
                                <?php
                                    echo '<select name="gas_type" id="gas_type" style="width: 150px;">';
                                    foreach($gas_tariffs as $gas_type) {
                                    if ($gas_type->id == $gas_type_id){
                                        echo '<option selected ="selected" value="'. $gas_type->id . '">';
                                        echo $gas_type->gas_type;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'. $gas_type->id . ' ">';
                                        echo $gas_type->gas_type;
                                        echo '</option>';
                                    }
                                }
                                echo '</select>';
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Biểu phí</td>
                    <td colspan="3">                            
                        <div class="col-sm-4 nopadding" style="text-align: right;">Nước dự định thay dổi</div>
                        <div class="col-sm-2">
                            <?php
                                $next_water_type_id = $tenementFlat->next_water_type_id;

                                echo '<select name="next_water_type" id="next_water_type" style="width: 150px;">';
                                echo '<option value="" selected ="selected"></option>';
                                foreach($water_tariffs as $water_type) {
                                    if ($water_type->id == $next_water_type_id){
                                        echo '<option selected ="selected" value="'. $water_type->id . '">';
                                        echo $water_type->water_type;
                                        echo '</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'. $water_type->id . ' ">';
                                        echo $water_type->water_type;
                                        echo '</option>';
                                    }
                                }
                                echo '</select>';
                            ?>
                        </div>
                        <div>
                            Tháng
                            <select name="month" id="month">
                            <?php
                                $next_water_type_year_month = $tenementFlat->next_water_type_year_month;

                                $y = substr($next_water_type_year_month, 0, 4);
                                $m = substr($next_water_type_year_month, -2, 2);
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
                            sẽ thay đổi
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Chủ hộ<em style='color:red'>(*)</em></td>
                    <td><input type="text" value="{!! $tenementFlat->name !!}" id="name" name="name" size="30%"></td>
                    <td>Email</td>
                    <td><input type="text" value="{!! $tenementFlat->email !!}" id="email" name="email" size="30%"></td>
                </tr>
                <tr>
                    <td>Điện thoại<em style='color:red'>(*)</em></td>
                    <td colspan="3"><input type="text" value="{!! $tenementFlat->phone !!}" id="phone" name="phone" size="30%"></td>
                </tr>
                <tr>
                    <td>Ghi chú</td>
                    <td colspan="7">
                        <textarea name="note" id="comment" name="comment" rows="2" cols="90%">{!! $tenementFlat->comment !!}</textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" style="background-color: orange;">Thông Tin Cho Thuê</td>
                </tr>
                <tr>
                    <td>Hiện</td>
                    <td colspan="7">
                        <input type="radio" name="rent_status" value="0"
                        {!! $tenementFlat->rent_status == '0' ? 'checked' : '' !!}
                        >Không có nhu cầu cho thuê &nbsp;
                        <input type="radio" name="rent_status" value="1" {!! $tenementFlat->rent_status == '1' ? 'checked' : '' !!}>Có nhu cầu cho thuê &nbsp;
                        <input type="radio" name="rent_status" value="2" {!! $tenementFlat->rent_status == '2' ? 'checked' : '' !!}>Khách đang thuê &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>Đang cho thuê từ ngày</td>
                    <td colspan="7">
                    @if ($tenementFlat->rent_from != '')
                    <input type="text" value="{!! 
                    isset($tenementFlat->rent_from) ? 
                        DateTime::createFromFormat('Ymd',$tenementFlat->rent_from)->format('d/m/Y') : ''
                     !!}" id="rent_from" name="rent_from" size="10%" class="date-picker">
                    @else
                    <input type="text" value="{!! $tenementFlat->rent_from
                     !!}" id="rent_from" name="rent_from" size="10%" class="date-picker">                    
                    @endif

                    Đến ngày
                    @if ($tenementFlat->rent_to != '')
                    <input type="text" value="{!! 
                    isset($tenementFlat->rent_to) ? 
                        DateTime::createFromFormat('Ymd',$tenementFlat->rent_to)->format('d/m/Y') : ''
                     !!}" id="rent_to" name="rent_to" size="10%" class="date-picker">
                    @else
                    <input type="text" value="{!! $tenementFlat->rent_to
                     !!}" id="rent_to" name="rent_to" size="10%" class="date-picker">                    
                    @endif
                </tr>
                <tr>
                    <td>Ghi chú</td>
                    <td colspan="7">
                        <textarea name="rent_note" id="rent_note" name="rent_note" rows="2" cols="90%">{!! $tenementFlat->rent_note !!}</textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    @role('admin|manager|moderator')
        <div>
            <br>
            <button id='tenementFlatSubmit' type="button" class="btn btn-primary">Lưu</button>            
            <button id='tenementFlatDelete' type="button" class="btn btn-danger">Xóa</button> &nbsp;
            <a href="{!! route('TenementFlat') !!} " class="btn btn-info">Trở về màn hình trước</a>
            <form id='frmCom' action="{!! route('TenementFlatDetail.destroy') !!}" method="POST" role="form" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input id="id" name='id' type="hidden" value="{!! $tenementFlat->id !!}">                        
            </form>
        </div>
    @endrole

    <div class="tab">
      <button class="tablinks" onclick="openCity(event, 'owner')" id="defaultOpen">Chủ hộ</button>
      <button class="tablinks" onclick="openCity(event, 'rent')">Khách thuê</button>
    </div>

    <div id="owner" class="tabcontent">
        <h3>Danh sách các đợt thay đổi Khách thuê</h3>
        <div class="table-responsive">
            <div class="col-lg-12">
                <table class="table table-bordered hover" id="owner-table">
                    <thead>
                        <tr>
                            <th width="270px" class="group-head info">
                                Đợt cập nhật</th>
                            <th width="270px" class="group-head info">
                                Ngày cập nhật</th>
                            <th class="group-head day">
                                Ghi chú
                            </th>
                            <th class="group-head day">
                                Thêm nhân khẩu
                            </th>
                            <th class="group-head day">
                                Hủy
                            </th>                                                        
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div id="rent" class="tabcontent">
        <h3>Danh sách các đợt thay đổi chủ hộ</h3>
        <div class="table-responsive">
            <div class="col-lg-12">
                <table class="table table-bordered hover" id="rent-table">
                    <thead>
                        <tr>
                            <th width="270px" class="group-head info">
                                Đợt cập nhật</th>
                            <th width="270px" class="group-head info">
                                Ngày cập nhật</th>
                            <th class="group-head day">
                                Ghi chú
                            </th>
                            <th class="group-head day">
                                Thêm nhân khẩu
                            </th>
                            <th class="group-head day">
                                Hủy
                            </th> 
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

<script type="text/javascript">
    $(function () {
        callDatePicker();
    });

    function callDatePicker() {
        var today = getCurrentDate();
        var dateFormat = "dd/mm/yy";

        $("#input_date").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });
        $("#receive_date").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });
        $("#res_dob").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });
        $("#rent_from").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });
        $("#rent_to").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });
        $("#date_issue").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });        
        $(".dobpicker").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false
        });

        $('.time-picker').timepicker({
            timeFormat: 'HH:mm:ss',
            //minTime: '11:45:00' // 11:45:00 AM,
            //maxHour: 20,
            //maxMinutes: 30,
            //startTime: new Date(0,0,0,15,0,0) // 3:00:00 PM - noon
            //interval: 15 // 15 minutes
        });

        $('.time-picker').on('change', function () {
            var regExp = /^(\d{1,2})(\:)(\d{1,2})(\:)(\d{1,2})$/;
            var val = this.value.match(regExp);
            if (jQuery.isEmptyObject(val)) {
                this.value = '';
            }
        });
    }

    function getCurrentDate() {
        var newDate = new Date();
        var date = newDate.getDate() < 10 ? "0" + newDate.getDate() : newDate.getDate();
        var month = (newDate.getMonth() + 1) < 10 ? "0" + (newDate.getMonth() + 1) : (newDate.getMonth() + 1);
        var year = newDate.getYear() + 1900;
        var today = date + "/" + month + "/" + year;
        return today;
    }

    function getDate( element ) {
        var date;
        var dateFormat = "dd/mm/yy";
        try {
            date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
            date = null;
        }

        return date;
    }
</script>

<!-- <div class="table-responsive"> -->
  <div class="modal fade" id="reportmodAl" 
     tabindex="-1" role="dialog" 
     aria-labelledby="reportmodAlLabel">
    <div class="modal-dialog" role="document" style="width: 800px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" id="close"
            data-dismiss="modal" 
            aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="reportmodAlLabel">Thêm Mới Thay Đổi</h4>
        </div>
        <form id='frmSubmit' action="{!! route('TfResChange.create') !!}" method="POST" role="form">

          <div class="modal-body">
          <table class="table table-striped table-bordered table-hover table-condensed">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
                    <input id="flat_id" name='flat_id' type="hidden" value="{!! $tenementFlat->id  !!}">
                    <input id="stay_type" name='stay_type' type="hidden" value="">
            <tbody>
                <tr>
                      <td>Đợt cập nhật<em style='color:red'>(*)</em> </td>
                      <td><input type="text" value='' id="content" name="content" size="30%"></td>
                </tr>

                <tr>
                      <td>Ngày thực hiện<em style='color:red'>(*)</em> </td>
                      <td><input type="text" class='date-picker' 
                      value="{!! date('d/m/Y') !!}" id="input_date" name="input_date" size="10%" class="date-picker"></td>
                </tr>
                <tr>
                      <td>Ghi chú<em style='color:red'>(*)</em> </td>
                      <td><input type="text" value="" id="comment" name="comment" size="70%">
                      </td>
                </tr>
            </tbody>
          </table>
          </div>
          <div class="modal-footer">
            <button type="button" 
            class="btn btn-default" 
            data-dismiss="modal" id="hide">Close</button>
            <span class="pull-right">
              <button id='exeSubmit' type="button" class="btn btn-primary">Lưu</button>
            </span>
          </div>
        </form>
      </div>
    </div>
  </div>
<!-- </div> -->

<!-- <div class="table-responsive"> -->
  <div class="modal fade" id="residentModal" 
     tabindex="-1" role="dialog" 
     aria-labelledby="reportmodAlLabel">
    <div class="modal-dialog" role="document" style="width: 800px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" id="res_close"
            data-dismiss="modal" 
            aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="reportmodAlLabel">Thêm Mới Nhân Khẩu</h4>
        </div>
        <form id='frmAddResSubmit' action="{!! route('TfResAdd.create') !!}" method="POST" role="form">

          <div class="modal-body">
          <table class="table table-striped table-bordered table-hover table-condensed">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
                    <input id="res_id" name='res_id' type="hidden" value="">
                    <input id="flat_id" name='flat_id' type="hidden" value="{!! $tenementFlat->id  !!}">
            <tbody>
                <tr>
                      <td>Họ tên:<em style='color:red'>(*)</em></td>
                      <td><input type="text" value='' id="res_name" name="res_name" size="30%">
                      Ngày sinh:<input type="text" value='' id="res_dob" name="res_dob" size="10%" class="date-picker">
                      <input type="radio" name="res_sex" value="0" checked>Nam
                        <input type="radio" name="res_sex" value="1">Nữ
                      </td>
                </tr>
                <tr>
                      <td>CMND:</td>
                      <td><input type="text" value='' id="res_identity_card" name="res_identity_card" size="10%">
                      Ngày cấp:<input type="text" value='' id="date_issue" name="date_issue" size="10%" class="date-picker">Tại<input type="text" value='' id="issued_by" name="issued_by" size="10%"></td>
                </tr>
                <tr>
                      <td>Cư trú:</td>
                      <td><input type="radio" name="res_type" value="0" checked>Chưa xác định
                      <input type="radio" name="res_type" value="1">Thường trú
                        <input type="radio" name="res_type" value="2">Tạm trú</td>
                </tr>
                <tr>
                      <td>Email:</td><td><input type="text" value='' id="res_email" name="res_email" size="30%">Điện Thoại:<input type="text" value='' id="res_phone" name="res_phone" size="30%"></td>
                </tr>
                <tr>
                      <td>Ghi chú:</td><td><input type="text" value="" id="res_comment" name="res_comment" size="60%">
                      </td>
                </tr>
            </tbody>
          </table>
          <div class="modal-footer">
            <button type="button" 
            class="btn btn-default" 
            data-dismiss="modal" id="res_hide">Close</button>
            <span class="pull-right">
              <button id='addResSubmit' type="button" class="btn btn-primary">Lưu</button>
            </span>
          </div>          
          <table class="table table-bordered hover" id="resident-table">
            <thead>
                <tr>
                    <th width="270px" class="group-head info">
                        Họ tên<br>Ngày sinh<br>Giới tính
                    </th>
                    <th class="group-head day">
                        CMND<br>Ngày cấp<br>Tại
                    </th>
                    <th class="group-head day">
                        Email<br>phone<br>Ghi chú
                    </th>
                </tr>
            </thead>
        </table>
          </div>
        </form>
      </div>
    </div>
  </div>
<!-- </div> -->
@endsection

