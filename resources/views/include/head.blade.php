<!-- head -->
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="icon" type="image/ico" href="<?php echo url('/');?>/img/logo.png" />
    <title>@yield('title')</title>
    
    {!! Html::style('DMS/css/bootstrap.min.css') !!}
    {!! Html::style('DMS/css/font-awesome.min.css') !!}
    {!! Html::style('DMS/css/bootstrap-datepicker.min.css') !!}
    {!! Html::style('DMS/css/main.css') !!}
    
    {!! Html::style('DMS/css/buttons.dataTables.min.css') !!}
    {!! Html::style('DMS/css/jquery.dataTables.min.css') !!}

    {!! Html::script('DMS/js/jquery.min.js') !!}
    {!! Html::script('DMS/js/jquery.js') !!}
    {!! Html::script('DMS/js/jquery.floatThead.min.js') !!}
    {!! Html::script('DMS/js/bootstrap.min.js') !!}
    {!! Html::script('DMS/js/bootstrap-datepicker.min.js') !!}
    {!! Html::script('DMS/js/bootstrap-datepicker.ja.min.js') !!}
    {!! Html::script('DMS/js/main.js') !!}
    {!! Html::script('DMS/js/common.js') !!}
    {!! Html::script('DMS/js/bootbox.min.js') !!}
    

    {!! Html::script('DMS/js/jquery.dataTables.min.js') !!}
    {!! Html::script('DMS/js/dataTables.buttons.min.js') !!}
    {!! Html::script('DMS/js/buttons.flash.min.js') !!}
    {!! Html::script('DMS/js/jszip.min.js') !!}
    {!! Html::script('DMS/js/pdfmake.min.js') !!}
    {!! Html::script('DMS/js/vfs_fonts.js') !!}
    {!! Html::script('DMS/js/buttons.html5.min.js') !!}
    {!! Html::script('DMS/js/buttons.print.min.js') !!}
    {!! Html::script('DMS/js/buttons.colVis.js') !!}

    @yield('style')
    @yield('script')
</head>
<!-- /.head -->