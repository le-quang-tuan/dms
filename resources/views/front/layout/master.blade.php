<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{!! asset('favicon.ico') !!}">

    <title>@yield('title')</title>
    <meta name="description"    content="">
    <meta name="keywords"       content=""/>

    <meta property="og:title"   content="" />
    <meta property="og:description"     content="" />
	<meta property="og:url"     content="{!! URL::current() !!}" />
    <meta property="og:type"    content="article" />
    <meta property="og:image"   content="{!! asset('img/avatar_fb.png') !!}" />
    @yield('meta')
    
	<link rel="alternate" href="http://larabase.net/" hreflang="vi-vn"/>

    {!! HTML::style('css/plugins/bootstrap/bootstrap.min.css') !!}
    {!! HTML::style('js/plugins/jquery-ui/jquery-ui.min.css') !!}
    {!! HTML::style('css/font-awesome.min.css') !!}
    {!! HTML::style('js/plugins/fullcalendar/fullcalendar.min.css') !!}
    {!! HTML::style('js/plugins/angular-datatables/datatables.bootstrap.min.css') !!}
    <link rel="stylesheet" href="https://cdn.datatables.net/scroller/1.4.2/css/scroller.dataTables.min.css">
    {!! HTML::style('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') !!}
    {!! HTML::style('js/plugins/toastr/toastr.min.css') !!}
    {!! HTML::style('js/plugins/jquery-confirm/jquery-confirm.min.css') !!}
    {!! HTML::style('css/style.css') !!}

    @yield('styles')
</head>
<body>

    @include('front/layout/navigation')

    <header class="container hidden">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <h1>HEADER<small>.net</small></h1>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12">
                
            </div>
        </div>
    </header>

    <div id="body" class="container">
        @include('front/layout/notifications')

        @yield('content-fullwidth')

        <div class="m-b-20"></div>

    </div>

    <a href="#" class="back-to-top"><i class="fa fa-arrow-up fa-2x"></i></a>

    @include('front/layout/footer')

    {!! HTML::script('js/plugins/jquery/jquery.min.js') !!}
    {!! HTML::script('js/plugins/bootstrap/bootstrap.min.js') !!}

    {!! HTML::script('js/plugins/angular/angular.min.js') !!}
    {!! HTML::script('js/plugins/angular/angular-messages.min.js') !!}
    {!! HTML::script('js/plugins/angular/angular-password.min.js') !!}
    {!! HTML::script('js/plugins/angular/ng-file-upload/ng-file-upload-all.min.js') !!}
    {!! HTML::script('js/plugins/angular/angular-dialog-service.js') !!}
    
    
    {!! HTML::script('js/plugins/fullcalendar/lib/jquery-ui.min.js') !!}
    {!! HTML::script('js/plugins/jquery-ui/jquery.ui.touch-punch.min.js') !!}
    {!! HTML::script('js/plugins/fullcalendar/lib/moment.min.js') !!}
    {!! HTML::script('js/plugins/fullcalendar/lib/moment-with-locales.min.js') !!}
    {!! HTML::script('js/plugins/fullcalendar/fullcalendar.min.js') !!}
    {!! HTML::script('js/plugins/fullcalendar/locale-all.js') !!}
    {!! HTML::script('js/plugins/timepicker/jquery.timepicker.min.js') !!}
    {!! HTML::script('js/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js') !!}

    {!! HTML::script('admin/js/plugins/datatables/jquery.dataTables.min.js') !!}
    {!! HTML::script('js/plugins/angular-datatables/angular-datatables.min.js') !!}
    {!! HTML::script('js/plugins/angular-datatables/plugins/scroller/angular-datatables.scroller.min.js') !!}
    <script src="https://cdn.datatables.net/scroller/1.4.2/js/dataTables.scroller.min.js"></script>

    {!! HTML::script('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}
    {!! HTML::script('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.ja.min.js') !!}
    {!! HTML::script('js/plugins/toastr/toastr.min.js') !!}
    {!! HTML::script('js/plugins/jquery-confirm/jquery-confirm.min.js') !!}

    {!! HTML::script('js/common.js') !!}

    <script type="text/javascript">
        /* GLOBAL VARIBLE */
        var _URL = "{!! url('') !!}";
        if (top.location != self.location) {top.location = self.location};

        // Remove element with class "alert" after 15 seconds (flash messages)
        window.setTimeout(function() {
            $("div#alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 10000);

        jconfirm.defaults = {
            title: null,
            confirmButton: 'はい',
            cancelButton: 'キャンセル',
            confirmButtonClass: 'btn-primary',
            cancelButtonClass: 'btn-danger',
            keyboardEnabled: true,
            confirmKeys: [13], // ENTER key
            cancelKeys: [27], // ESC key
            animation: 'top',
        }
        toastr.options.newestOnTop = false;
    </script>

    @yield('scripts')
</body>
</html>
