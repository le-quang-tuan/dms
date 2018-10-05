<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/ico" href="<?php echo url('/');?>/img/logo.png" />
    <title>Hệ Thống Quản Lý Khu Căn Hộ</title>
    <link href="DMS/css/bootstrap.min.css" rel="stylesheet">
    {!! Html::style('css/datepicker/jquery-ui.css') !!}
    {!! Html::style('css/tageditor/jquery.tag-editor.css') !!}

    {!! Html::script('DMS/js/jquery.min.js') !!}
    {!! Html::script('js/datepicker/jquery-ui.js')  !!}
    {!! Html::script('DMS/js/jquery.min.js') !!}
    {!! Html::script('DMS/js/jquery.js') !!}
    {!! Html::script('DMS/js/bootstrap-datepicker.min.js') !!}
    {!! Html::script('DMS/js/bootstrap-datepicker.ja.min.js') !!}
    {!! Html::style('DMS/css/bootstrap-datepicker.min.css') !!}

    {!! Html::script('DMS/js/colorpicker/spectrum.js') !!}
    {!! Html::script('DMS/js/colorpicker/jscolor.js') !!}
    {!! Html::script('DMS/js/colorpicker/jquery.colorpicker.js') !!}

    {!! Html::style('css/datepicker/jquery-ui.css') !!}

    {!! Html::style('css/tageditor/jquery.tag-editor.css') !!}

    {!! Html::style('css/timepicker/jquery.timepicker.min.css') !!}

    {!! Html::style('DMS/css/colorpicker/spectrum.css') !!}
    {!! Html::style('DMS/css/colorpicker/jquery.colorpicker.js') !!}

    <link href="DMS/css/font-awesome.min.css" rel="stylesheet">
    <link href="DMS/css/main.css" rel="stylesheet">
    <script src="DMS/js/bootstrap.min.js"></script>
    <script src="DMS/js/main.js"></script>
<style>
  .full-spectrum .sp-palette {
    max-width: 200px;
  }

  #trash{
    width:32px;
    height:32px;
    float:left;
    padding-bottom: 15px;
    position: relative;
  }
    
  #wrap {
    width: 1100px;
    margin: 0 auto;
  }
    
  #external-events {
    float: left;
    width: 150px;
    padding: 0 10px;
    border: 1px solid #ccc;
    background: #eee;
    text-align: left;
  }
    
  #external-events h4 {
    font-size: 16px;
    margin-top: 0;
    padding-top: 1em;
  }
    
  #external-events .fc-event {
    margin: 10px 0;
    cursor: pointer;
  }
    
  #external-events p {
    margin: 1.5em 0;
    font-size: 11px;
    color: #666;
  }
    
  #external-events p input {
    margin: 0;
    vertical-align: middle;
  }

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
        top: 30%; 
        transform: translateY(-30%);
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

<script>
  $(function () {
      callDatePicker();

      $(".basic").spectrum({
          color: "white",
          change: function(color) {
              $("#color_code").val(color.toHexString());
          }
      });

      $("#full").spectrum({
          color: "yellow",
          showInput: true,
          className: "full-spectrum",
          showInitial: true,
          showPalette: true,
          showSelectionPalette: true,
          maxSelectionSize: 10,
          preferredFormat: "hex",
          localStorageKey: "spectrum.demo",
          move: function (color) {
              
          },
          show: function () {
              $("#color_code").val(color.toHexString());
          },
          beforeShow: function () {
              $("#color_code").val(color.toHexString());
          },
          hide: function () {
              $("#color_code").val(color.toHexString());
          },
          change: function(color) {
              $("#color_code").val(color.toHexString());
          },
          palette: [
              ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
              "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
              ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
              "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"], 
              ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", 
              "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", 
              "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", 
              "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", 
              "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", 
              "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
              "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
              "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
              "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", 
              "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
          ]
      });

      $("#close").click(function(){
        $('#reportmodAl').hide();
      });
      
      $("#hide").click(function(){
        $('#reportmodAl').hide();
      });
      
      $("#addNote").click(function(){
        $(".basic").spectrum({
            color: "white",
            change: function(color) {
              $("#color_code").val(color.toHexString());
            },
        });
        $("#color_code").val("white");


        $("#content").val("");
        $("#note_date").val("");

        $("#comment").val("");

        $('#reportmodAl').attr('class', "modal fade in"); 
        $('#reportmodAl').show();
      });

      $(".destroy").click(function(){
          var note_id = $(this).attr("note_id");

          var confirmTable = "<h2>Ghi Chú này không cần nữa?</h2>";
          bootbox.confirm(confirmTable, function (result) {
              if (result == true){
                  $.ajax({
                      type: "POST",
                      cache: false,
                      url : "{!! route('Note.destroy') !!}",
                      data: { 
                        "id" : note_id,
                        "_token": "{{ csrf_token() }}",
                      },
                      success: function(data) {
                        location.reload();
                      }
                  })
              }
          });
      });

      $("#exeSubmit").click(function(){
          bootbox.confirm("Thông tin sổ tay ghi chú sẽ được cập nhật?", function(result) {
              if(result){
                  $("#frmSubmit").submit();
              }
          });
      });

      function callDatePicker() {
        var dateFormat = "dd/mm/yy";
        $("#note_date").datepicker({
            dateFormat: dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false,
            autoclose:true
      });
    }
  });
</script>

<script src="https://www.gstatic.com/firebasejs/3.7.2/firebase.js"></script>
  <script>
  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyAWLC2tPwjI54ebJ0rZ4OYn17hVGasL_dc",
    authDomain: "quanlyho-a7415.firebaseapp.com",
    databaseURL: "https://quanlyho-a7415.firebaseio.com",
    projectId: "quanlyho-a7415",
    storageBucket: "quanlyho-a7415.appspot.com",
    messagingSenderId: "992830331732" 
    };
  firebase.initializeApp(config);

  const messaging = firebase.messaging();

  messaging.requestPermission()
  .then(function() {
    console.log('Notification permission granted.');
    return messaging.getToken();
  })
  .then(function(token) {
    console.log(token); // Display user token
  })
  .catch(function(err) { // Happen if user deney permission
    console.log('Unable to get permission to notify.', err);
  });

  messaging.onMessage(function(payload){
    console.log('onMessage',payload);
  })
</script>

    {!! Html::script('DMS/js/bootstrap-datepicker.min.js') !!}
    {!! Html::script('DMS/js/bootstrap-datepicker.ja.min.js') !!}
    {!! Html::script('DMS/js/bootbox.min.js') !!}    
  </head>

  <body>
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
        <h4>
          <span class="fa fa-info-circle fa-fw text-info"></span>Sổ Tay Ghi Nhớ &nbsp;<button id='addNote' type="button" class="btn btn-primary">Thêm Ghi Chú</button>
        </h4>
        <?php
          foreach($notes as $note){
            echo "<div class='panel panel-default'>";
            echo "<div class='panel-body' style='background: ". $note->color .";". (isset($note->color) ? "color:white" : "") . "'>";
            echo "<span id='info'>";
            echo "<strong>". $note->content ."</strong>&nbsp;";
            echo "<button type='button' note_id='". $note->id . "' id='deleteNote' class='btn btn-xs btn-success destroy'>X&nbsp;</button><br/>
            ";
            echo "<small>(". substr($note->note_date,-2) . "/" . substr($note->note_date,-4,2) . "/" . substr($note->note_date,0,4) . ")</small><br/>";
            echo "<small>※" . $note->comment . "</small><br/>";
            echo "</span>";
            echo "</div>";
            echo "</div>";
          }
        ?>
      </div>

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
        <h3 class="text-nowrap"><span class="fa fa-list-alt fa-fw"></span><span id="userType">Quản Lý Thu Phí</span></h3>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-3 col-sm-12" id="page8"><a class="btn btn-success" href="{{ url('monthlyfee') }}"><span class="fa fa-wrench fa-fw"></span>Thông Tin Phí Tháng</a></div>
            </div>
          </div>
        </div>

        <h3 class="text-nowrap"><span class="fa fa-list-alt fa-fw"></span><span id="userType">Quản Lý Kỹ Thuật</span></h3>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-3 col-sm-12" id="page1"><a class="btn btn-primary" href="{{ url('tech/dailyactivitycal') }}"><span class="fa fa-usd fa-fw"></span>Nhật Ký Kỹ Thuật</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-space"></div>
    <footer class="footer text-right">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 footer-content text-nowrap small">Copyright(c) 2017 ViHiTek Co.ltd</div>
        </div>
      </div>
    </footer>
  </body>

<div class="table-responsive">
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
          <h4 class="modal-title" id="reportmodAlLabel">Sổ Tay Ghi Nhớ</h4>
        </div>
        <form id='frmSubmit' action="{!! route('Note.store') !!}" method="POST" role="form">

          <div class="modal-body">
          <table class="table table-striped table-bordered table-hover table-condensed">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
                    <input id="id" name='id' type="hidden" value="">
            <tbody>
                <tr>
                      <td>Tiêu Đề<em style='color:red'>(*)</em> </td>
                      <td><input type="text" value='' id="content" name="content" size="30%"></td>
                </tr>

                <tr>
                      <td>Ngày thực hiện<em style='color:red'>(*)</em> </td>
                      <td><input type="text" class='date-picker' 
                      value="{!! date('d/m/Y') !!}" id="note_date" name="note_date" size="10%"></td>
                </tr>
                <tr>
                      <td>Mô tả<em style='color:red'>(*)</em> </td>
                      <td><input type="text" value="" id="comment" name="comment" size="70%">
                      </td>
                </tr>
                <tr>      
                      <td>Màu hiển thị<em style='color:red'>(*)</em></td>
                      <td><input type='text' id="color" name="color" class="basic" value="blue"/>
                      <input id="color_code" name="color_code" type="hidden"></td>
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
</div>  
</html>
