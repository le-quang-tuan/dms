<!-- top navbar -->

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!--            <a class="navbar-brand" href="#">Laravel</a>-->
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            @if (!Auth::guest())

                <ul class="nav navbar-nav navbar-left">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false">Thiết Lập<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/tenement')}}">Thông Tin Khu Căn Hộ/Chung Cư</a></li>
                            
                            <li><a href="{{ url('/tenement/elec')}}">Biểu phí điện</a></li>

                            <li><a href="{{ url('/tenement/water')}}">Biểu phí nước</a></li>

                            <li><a href="{{ url('/tenement/gas')}}">Biểu phí gas</a></li>

                            <li><a href="{{ url('/tenement/parking')}}">Biểu phí xe tháng</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false">Căn Hộ<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/flat/create')}}">Tạo mới căn hộ</a></li>

                            <li><a href="{{ url('/import/importFlat')}}">Tạo mới căn hộ từ file</a></li>

                            <li><a href="{{ url('/flat')}}">Danh sách căn hộ</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false">Phí tháng<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/import/importElec')}}">Cập nhật chỉ số điện</a></li>

                            <li><a href="{{ url('/import/importWater')}}">Cập nhật chỉ số nước</a></li>

                            <li><a href="{{ url('/import/importGas')}}">Cập nhật chỉ số gas</a></li>

                            <li><a href="{{ url('/import/importService')}}">Cập nhật phí khác</a></li>

                            <li><a href="{{ url('/import/importVehicle')}}">Cập nhật xe tháng</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false">Công nợ<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/monthlyfee')}}">Nợ phát sinh tháng</a></li>

                            <li><a href="{{ url('/import/importWater')}}">Cập nhật chỉ số nước</a></li>

                            <li><a href="{{ url('/import/importGas')}}">Cập nhật chỉ số gas</a></li>

                            <li><a href="{{ url('/import/importService')}}">Cập nhật phí khác</a></li>

                            <li><a href="{{ url('/import/importVehicle')}}">Cập nhật xe tháng</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <div id="btnExport" class="export" style="display: none">
                            <span>Export </span>
                            <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
                        </div>
                    </li>
                </ul>
            @endif
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                        <!-- <li><a href="{{ url('/auth/login') }}">Login</a></li> -->
                <!-- <li><a href="{{ url('/auth/register') }}">Register</a></li> -->
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false">{{ Auth::user()->username }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<script type="text/javascript">
    // Active menu
    $(function () {
        var pgurl = window.location.href.substr(window.location.href).split('/')[3];
        $(".nav li a").each(function () {
            var href = $(this).attr("href");
            var ctr = href.substr(href.lastIndexOf("/") + 1);
            if (ctr == pgurl || ctr == '')
                $(this).parent().addClass("active");
        });
    });
</script>
<!-- /. top navbar -->