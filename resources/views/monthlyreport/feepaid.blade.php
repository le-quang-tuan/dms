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
    {!! Html::script('js/ckeditor/ckeditor.js') !!}
    {!! Html::script('js/manual_js/manual_click.js') !!}
<script>
    $(function () {
        $("#downloadSubmit").manualSubmit('frmDownloadSubmit');
    });

</script>
@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thông Tin Công Nợ Tháng</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
        <form id='frmDownloadSubmit' action="{!! route('FeePaid.download') !!}" method="POST" role="form" method="post">
            {!! csrf_field() !!}
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            Phí phát sinh từ Năm/tháng<em style='color:red'>(*)</em>
                            <select name="month_from" id="month_from">
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

                            <select name="year_from" id="year_from">
                                <?php
                                    for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                                        if ($y == $i)
                                            echo "<option selected=True>$i</option>";
                                        else 
                                            echo "<option>$i</option>";

                                    }
                                ?>
                            </select>
                            ~<em style='color:red'>(*)</em>
                            <select name="month_to" id="month_to">
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

                            <select name="year_to" id="year_to">
                                <?php
                                    for($i = date('Y') -1 ; $i < date('Y') + 5; $i++){
                                        if ($y == $i)
                                            echo "<option selected=True>$i</option>";
                                        else 
                                            echo "<option>$i</option>";

                                    }
                                ?>
                            </select>
                        </td>
                        <td>    
                            <button id='downloadSubmit' type="button" class="btn btn-primary">Xuất File Excel</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
@endsection

