<div class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <h1><a href="{{ url('/index') }}" style="color: white;">
      {!! $tenement->name !!}{!! HTML::image('img/logo.png', 'alt', array('height' => '30px')) !!}</a></h1>
      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="navbar-main">
      <ul class="nav navbar-nav navbar-right">
        @role('admin|manager|moderator|reporter')
        <li class="dropdown">
          <a class="dropdown-toggle" role="button" aria-expanded="false" href="#" data-toggle="dropdown">
            <span class="fa fa-folder-open-o"></span>Danh Mục<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li class="primary"><a href="{{ url('tenement') }}"><span class="fa fa-info-circle fa-fw"></span>Thông Tin Dự Án</a></li>
            <li class="primary"><a href="{{ url('tenement/detail') }}"><span class="fa fa-pencil fa-fw"></span>Chỉnh Sửa Thông Tin</a></li>
            <li class="primary"><a href="{{ url('tenement/elec') }}"><span class="fa fa-wrench fa-fw"></span>Biểu Phí Điện<span class="fa fa-bolt fa-fw"></span></a></li>
            <li class="primary"><a href="{{ url('tenement/water') }}"><span class="fa fa-wrench fa-fw"></span>Biểu Phí Nước<span class="fa fa-tint fa-fw"></span></a></li>
            <li class="primary"><a href="{{ url('tenement/gas') }}"><span class="fa fa-wrench fa-fw"></span>Biểu Phí Gas<span class="fa fa-fire fa-fw"></span></a></li>
            <li class="primary"><a href="{{ url('tenement/parking') }}"><span class="fa fa-wrench fa-fw"></span>Biểu Phí giữ Xe Tháng<span class="fa fa-motorcycle fa-fw"></span><span class="fa fa-car fa-fw"></span></a></li>
            <li class="divider"></li>
            <li class="info"><a href="{{ url('flat') }}"><span class="fa fa-table fa-fw"></span>Danh Sách Căn Hộ</a></li>
            @role('admin|manager|moderator')
              <li class="info"><a href="{{ url('flat/create') }}"><span class="fa fa-home fa-fw"></span>Tạo Mới Căn Hộ</a></li>
              <li class="info"><a href="{{ url('import/importFlat') }}"><span class="fa fa-building fa-fw"></span>Tạo Mới Căn Hộ Từ File</a></li>
            @endrole
          </ul>
        </li>

        <li class="dropdown">
          <a class="dropdown-toggle" role="button" aria-expanded="false" href="#" data-toggle="dropdown">
            <span class="fa fa-book"></span>Kết Sổ Tháng<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li class="primary"><a href="{{ url('flat/all/elec/all') }}"><span class="fa fa-bolt fa-fw"></span>Cập nhật Chỉ Số Điện</a></li>

            <li class="primary"><a href="{{ url('flat/all/water/all') }}"><span class="fa fa-tint fa-fw"></span>Cập nhật Chỉ Số Nước</a></li>

            <li class="primary"><a href="{{ url('flat/all/gas/all') }}"><span class="fa fa-fire fa-fw"></span>Cập nhật Chỉ Số Gas</a></li>

            <li class="primary"><a href="{{ url('flat/all/vehicle') }}"><span class="fa fa-motorcycle fa-fw"></span>Cập nhật Thông Tin Gửi Xe<span class="fa fa-car fa-fw"></span></a></li>

            <li class="primary"><a href="{{ url('flat/all/service/all') }}"><span class="fa fa-usd fa-fw"></span>Cập nhật Thông Tin Phí Khác</a></li>

            @role('admin|manager|moderator')
              <li class="divider"></li>

              <li class="primary"><a href="{{ url('import/importElec') }}"><span class="fa fa-file-excel-o fa-fw"></span>Upload Chỉ Số Điện</a></li>
              <li class="primary"><a href="{{ url('import/importWater') }}"><span class="fa fa-file-excel-o fa-fw"></span>Upload Chỉ Số Nước</a></li>
              <li class="primary"><a href="{{ url('import/importGas') }}"><span class="fa fa-file-excel-o fa-fw"></span>Upload Chỉ Số Gas</a></li>
              <li class="primary"><a href="{{ url('import/importVehicle') }}"><span class="fa fa-file-excel-o fa-fw"></span>Upload Xe Gửi Mới</a></li>
              <li class="primary"><a href="{{ url('import/importService') }}"><span class="fa fa-file-excel-o fa-fw"></span>Upload Phí Khác Mới</a></li>

              <li class="divider"></li>
              <li class="info"><a href="{{ url('monthlyfee/exepaymonth') }}"><span class="fa fa-table fa-fw"></span>Kết Sổ Tháng</a></li>
            @endrole
            <li class="divider"></li>
            <li class="primary"><a href="{{ url('monthlyfee/status') }}"><span class="fa fa-file-pdf-o fa-fw"></span>Tình Trạng Công Nợ</a></li>
            <li class="primary"><a href="{{ url('monthlyfee') }}"><span class="fa fa-file-pdf-o fa-fw"></span>Thông Tin Phí Tháng</a></li>
            <li class="primary"><a href="{{ url('monthlyfee/paybillall/ALL') }}"><span class="fa fa-money"></span>Thu Tiền Từ Phiếu Thu Tháng</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a class="dropdown-toggle" role="button" aria-expanded="false" href="#" data-toggle="dropdown">
            <span class="fa fa-usd fa-fw"></span>Thu Phí<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li class="primary"><a href="{{ url('monthlyfee/flatpaid') }}"><span class="fa fa-info-circle fa-fw"></span>Phí Tháng - Trả Trước - Thu Hộ</a></li>
            <li class="divider"></li>
            <li class="info"><a href="{{ url('monthlyfee/paid') }}"><span class="fa fa-table fa-fw"></span>Danh Sách Phiếu Thu</a></li>
            <li class="info"><a href="{{ url('monthlyfee/paiddetaillist') }}"><span class="fa fa-table fa-fw"></span>Danh Sách Khoản Thu</a></li>

          </ul>
        </li>
        @endrole
        @role('admin|manager|moderator|technical|reporter')        
        <li class="dropdown">
          <a class="dropdown-toggle" role="button" aria-expanded="false" href="#" data-toggle="dropdown">
            <span class="fa fa-wrench"></span>Kỹ Thuật<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li class="primary"><a href="{{ url('tech/group') }}"><span class="fa fa-info-circle fa-fw"></span>Nhóm Máy Móc - Thiết Bị - Vật Tư</a></li>
            <li class="primary"><a href="{{ url('tech/producer') }}"><span class="fa fa-info-circle fa-fw"></span>Nhà Cung Cấp</a></li>
            <li class="divider"></li>
            <li class="primary"><a href="{{ url('tech/equipment') }}"><span class="fa fa-info-circle fa-fw"></span>Đăng ký: Máy móc - Thiết Bị - Vật Tư &amp; Kế hoạch bảo trì</a></li>
            <li class="info"><a href="{{ url('tech/importequipment') }}"><span class="fa fa-table fa-fw"></span>Đăng ký Máy móc - Thiết Bị - Vật Tư từ File</a></li>
            <li class="primary"><a href="{{ url('tech/schedulecal') }}"><span class="fa fa-info-circle fa-fw"></span>Báo cáo kết quả bảo trì</a></li>
            <li class="divider"></li>
            <li class="primary"><a href="{{ url('tech/dailyactivity') }}"><span class="fa fa-info-circle fa-fw"></span>Đăng ký Nhật ký kỹ thuật</a></li>
            <li class="primary"><a href="{{ url('tech/dailyactivitycal') }}"><span class="fa fa-info-circle fa-fw"></span>Thông tin Lịch nhật ký kỹ thuật</a></li>
          </ul>
        </li>
        @endrole
        @role('admin|manager|moderator|reporter')
        <li class="dropdown">
          <a class="dropdown-toggle" role="button" aria-expanded="false" href="#" data-toggle="dropdown">
            <span class="fa fa-list-alt fa-fw"></span>Báo Cáo<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li class="primary"><a href="{{ url('monthlyreport/vehicle_fee') }}"><span class="fa fa-list-ol fa-fw"></span>Công Nợ - Phí Thu</a></li>
            <li class="primary"><a href="{{ url('monthlyreport/feepaid') }}"> <span class="fa fa-list-ol fa-fw"></span>Công nợ Phát sinh/Thu</a></li>
            <li class="primary"><a href="{{ url('monthlyreport/paid') }}"> <span class="fa fa-list-ol fa-fw"></span>Công nợ</a></li>
            <li class="primary"><a href="{{ url('monthlyreport/monthdept') }}"><span class="fa fa-list-ol fa-fw"></span>Công Nợ Tháng</a></li>
            <li class="primary"><a href="{{ url('monthlyreport/alldept') }}"><span class="fa fa-list-ol fa-fw"></span>Công Nợ Toàn Phần</a></li>
            <li class="primary"><a href="{{ url('monthlyreport/prepaid') }}"><span class="fa fa-list-ol fa-fw"></span>Trả Trước</a></li>
            <li class="primary"><a href="{{ url('monthlyreport/vehicle') }}"><span class="fa fa-list-ol fa-fw"></span>Xe Tháng</a></li>
          </ul>
        </li>
        @endrole
        <li>
          <p class="navbar-text navbar-right user" title="{!! $userDetailInfo->name !!}" data-placement="bottom">
          <span class="fa fa-user fa-fw"></span><a href="{{ url('user/detail/'. $userDetailInfo->id) }}" style="color: #0c0504;">{!! $userDetailInfo->first_name !!}</a>
          </p>
        </li>
        
        <li><a href="{{ url('auth/logout') }}"><span class="fa fa-sign-out fa-fw"></span>Logout</a></li>
        @role('admin|manager|moderator|accountant')
        <li><a href="{{ url('user') }}"><span class="fa fa-sign-out fa-fw"></span>Người dùng</a></li>
        @endrole
      </ul>

    </div>
  </div>
</div>