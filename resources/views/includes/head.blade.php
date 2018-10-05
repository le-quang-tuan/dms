<!-- head -->
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Quản Lý Thông Tin và Thu Phí Khu Căn Hộ/Chung Cư</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="icon" type="image/ico" href="<?php echo url('/');?>/hotel.ico" />

    <!-- Bootstrap 3.3.2 -->
    {!! Html::style('css/app.css') !!}
    {!! Html::style('css/bootstrap.css') !!}
    {!! Html::style('css/bootstrap.min.css') !!}
    {!! Html::style('css/bootstrap-theme.css') !!} 
    {!! Html::style('css/bootstrap-theme.min.css') !!}
<!--    {!! Html::style('/bootstrap/css/bootstrap.css') !!}
    {!! Html::style('/bootstrap/css/bootstrap.min.css') !!} 
    {!! Html::style('/bootstrap/css/bootstrap-theme.css') !!} 
    {!! Html::style('/bootstrap/css/bootstrap-theme.min.css') !!} 
    {!! Html::style('/bootstrap/css/starter-template.css') !!}     -->
    
    <!-- jQuery 2.1.3 -->
    {!! Html::script('js/jquery-2.1.4.min.js') !!}
    {!! Html::script('js/bootstrap.min.js') !!}
    {!! Html::script('js/bootbox.min.js') !!}
    {!! Html::script('js/main.js') !!}
      
    
    @yield('style')
    
    @yield('script')
</head>
<!-- /.head -->