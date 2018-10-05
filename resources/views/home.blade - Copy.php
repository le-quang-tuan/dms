<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ Thống Quản Lý Khu Căn Hộ</title>
    <link href="DMS/css/bootstrap.min.css" rel="stylesheet">
    <link href="DMS/css/font-awesome.min.css" rel="stylesheet">
    <link href="DMS/css/main.css" rel="stylesheet">
    <script src="DMS/js/jquery.min.js"></script>
    <script src="DMS/js/bootstrap.min.js"></script>
    <script src="DMS/js/main.js"></script>
  </head>

  <body onload="javascript: pramWrite();">
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <h1>{!! $tenement->name !!}</h1>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse" id="navbar-main">
          <ul class="nav navbar-nav navbar-right">
              <li><a href="{{ url('/user/detail').'/'.Auth::user()->id }}"></a></li>
              <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
          </ul>
          <p class="navbar-text navbar-right user" title="{!! $userDetailInfo->name !!}" data-placement="bottom">
          <span class="fa fa-user fa-fw"></span>{!! $userDetailInfo->name !!}</p>
        </div>
      </div>
    </div>

    <div class="section-tout title">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <span class="fa fa-home fa-fw"></span>
            <h2>Trang Chủ</h2>
          </div>
        </div>
      </div>
    </div>
    <div class="container font-well">
      <div class="well">
        <h4><span class="fa fa-exclamation-triangle fa-fw text-warning"></span>Hỗ Trợ Kỹ Thuật Hệ Thống 24/7</h4>
        <div class="panel panel-default">
          <div class="panel-body" style="max-height: 150px; overflow-y: scroll;">
            <div class="row">
              <div class="col-sm-3 col-xs-12"><a class="link" href="https://www.facebook.com/7uckyLeq">Liên lạc Facebook</a></div>
              <div class="col-sm-9 col-xs-12 text-danger">Liên lạc Chat Facebook nếu hệ thống phát sinh lỗi (giờ hành chính)</div>
            </div>
            <br />
            <div class="row">
              <div class="col-sm-3 col-xs-12"><a class="link" href="skype:le-quang-tuan0606?chat">le-quang-tuan0606</a></div>
              <div class="col-sm-9 col-xs-12 text-danger">Liên lạc Skype nếu hệ thống phát sinh lỗi (giờ hành chính)</div>
            </div>
            <br />
            <div class="row">
              <div class="col-sm-3 col-xs-12"><a class="link" href="tel:0909653060">090-965-3060 (Mr.Tuấn)</a></div>
              <div class="col-sm-9 col-xs-12 text-danger">Hỗ trợ 24/7 nếu hệ thống phát sinh lỗi (sau giờ hành chính)</div>
            </div>
          </div>
        </div>
      </div>

      <div class="well">
        <h4>
          <span class="fa fa-info-circle fa-fw text-info"></span>Thông Báo Từ Hệ Thống
        </h4>
        <div class="panel panel-default">
          <div class="panel-body">
            <span id="info">
              <strong>Hệ thống hỗ trợ thu phí bắt đầu hoạt động từ</strong><br />
              <small>(2016/12/31 9:00)</small><br />
              <!-- <br />
              Hệ thống mới bắt đầu sử dụng với <br />
              詳細に関してはシステム管理者までお問い合わせください。<br />
              <br />
              <small>※メンテナンスの状況によって停止時間は30分ほど前後する恐れがありますので、あらかじめご了承ください。</small><br /> -->
            </span>
            <!-- <br />
            <small><span class="fa fa-user fa-fw"></span>システム　管太郎（更新日時：2017/04/28 09:13:20）</small> -->
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <span id="info">
              <strong>Chức năng quản lý kỹ thuật</strong><br />
              <small>(2017/02/01 9:00)</small><br />
              <!-- <br />
              Hệ thống mới bắt đầu sử dụng với <br />
              詳細に関してはシステム管理者までお問い合わせください。<br />
              <br />
              <small>※メンテナンスの状況によって停止時間は30分ほど前後する恐れがありますので、あらかじめご了承ください。</small><br /> -->
            </span>
            <!-- <br />
            <small><span class="fa fa-user fa-fw"></span>システム　管太郎（更新日時：2017/04/28 09:13:20）</small> -->
          </div>
        </div>
      </div>

      <div class="well container-menu">
        <h3 class="text-nowrap"><span class="fa fa-list-alt fa-fw"></span><span id="userType">Quản Lý Thông Tin</span></h3>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('tenement/detail') }}" /><span class="fa fa-usd fa-fw"/></span>Dự Án</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('tenement/elec') }}" /><span class="fa fa-usd fa-fw"></span>Chỉnh Sửa Thông Tin</a></div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-12" id="page7"><a class="btn btn-info" href="{{ url('tenement/elec') }}" /><span class="fa fa-bolt fa-fw"></span>Biểu Phí Điện</a></div>
              <div class="col-md-3 col-sm-12" id="page7"><a class="btn btn-info" href="{{ url('tenement/water') }}" /><span class="fa fa-bath fa-fw"></span>Biểu Phí Nước</a></div>
              <div class="col-md-3 col-sm-12" id="page7"><a class="btn btn-info" href="{{ url('tenement/gas') }}" /><span class="fa fa-free-code-camp fa-fw"></span>Biểu Phí Gas</a></div>
              <div class="col-md-3 col-sm-12" id="page7"><a class="btn btn-info" href="{{ url('tenement/parking') }}" /><span class="fa fa-car fa-fw"></span>Xe Tháng</a></div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('flat') }}" /><span class="fa fa-building-o fa-fw"></span>Danh Sách Căn Hộ</a></div>

              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('flat/create') }}" /><span class="fa fa-building-o fa-fw"></span>Thêm Căn Hộ</a></div>

              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('import/importFlat') }}" /><span class="fa fa-building-o fa-fw"></span>Thêm Căn Hộ Từ File</a></div>
            </div>
          </div>
        </div>

        <h3 class="text-nowrap"><span class="fa fa-list-alt fa-fw"></span><span id="userType">Kết Sổ Tháng</span></h3>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('import/importElec') }}" /><span class="fa fa-usd fa-fw"></span>Upload Chỉ Số Điện</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('import/importWater') }}" /><span class="fa fa-usd fa-fw"></span>Upload Chỉ Số Nước</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('import/importGas') }}" /><span class="fa fa-usd fa-fw"></span>Upload Chỉ Số Gas</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('import/importVehicle') }}" /><span class="fa fa-usd fa-fw"></span>Upload Xe Gửi Mới</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('import/importService') }}" /><span class="fa fa-usd fa-fw"></span>Upload Phí Khác Mới</a></div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-info" href="{{ url('monthlyfee/exepaymonth') }}" /><span class="fa fa-building-o fa-fw"></span>Kết Sổ Tháng</a></div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-12" id="page8"><a class="btn btn-success" href="{{ url('monthlyfee') }}" /><span class="fa fa-wrench fa-fw"></span>Xuất Thông Báo Phí Tháng</a></div>
              <div class="col-md-3 col-sm-12" id="page8"><a class="btn btn-success" href="{{ url('monthlyfee/paybillall/ALL') }}" /><span class="fa fa-wrench fa-fw"></span>Xuất Phiếu Thu Tháng</a></div>
            </div>
          </div>
        </div>

        <h3 class="text-nowrap"><span class="fa fa-list-alt fa-fw"></span><span id="userType">Thu Phí</span></h3>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('monthlyfee/flatpaid') }}" /><span class="fa fa-usd fa-fw"></span>Phí Tháng-Thu Trước</a></div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-12" id="page7"><a class="btn btn-info" href="{{ url('monthlyfee/paid') }}" /><span class="fa fa-clipboard fa-fw"></span>Danh Sách Phiếu Thu</a></div>
            </div>
          </div>
        </div>
        <h3 class="text-nowrap"><span class="fa fa-list-alt fa-fw"></span><span id="userType">Báo Cáo</span></h3>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="monthlyfee" /><span class="fa fa-usd fa-fw"></span>BC Tài Chính</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="monthlyfee" /><span class="fa fa-usd fa-fw"></span>Đối Chiếu Công Nợ</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="monthlyfee" /><span class="fa fa-usd fa-fw"></span>Phân Tích Công Nợ</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="monthlyfee" /><span class="fa fa-usd fa-fw"></span>Bảng Kê Phí</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="monthlyfee" /><span class="fa fa-usd fa-fw"></span>BC Xe Tháng</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="monthlyfee" /><span class="fa fa-usd fa-fw"></span>BC Công Nợ</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="/import/importWater" /><span class="fa fa-bolt fa-fw"></span>BC Trả Trước</a></div>
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="/import/importWater" /><span class="fa fa-bath fa-fw"></span>BC TC Tổng Hợp</a></div>
            </div>
            
            <div class="row">
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="footer-space"></div>
    <footer class="footer text-right">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 footer-content text-nowrap small">Copyright(c) 2017 Hung Vuong Property</div>
        </div>
      </div>
    </footer>
  </body>
</html>
