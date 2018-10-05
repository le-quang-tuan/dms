@extends('include.layout')

@section('style')
{!! Html::style('css/jquery.dataTables.min.css') !!}
@endsection

<style media="all" type="text/css">
    .alignRight { text-align: right; font-size: 14px }
    .numericCol{
        text-align: right;
    }

    .loading {
        /*display: block;*/
        display : block;
        position : fixed;
        z-index: 100;
                background-image:url("{!! url('img/gears.gif') !!}");
        background-color:#666;
        opacity : 0.4;
        background-repeat : no-repeat;
        background-position : center;
        left : 0;
        bottom : 0;
        right : 0;
        top : 0;
    }
    /* Hide all the children of the 'loading' element */
    .loading * {
        display: none;  
    }

.square .paidover{
    float:left;
    position: relative;
    width: {!! 100/26 !!}%;
    padding-bottom : {!! 100/26 !!}%; /* = width for a 1:1 aspect ratio */
    margin:0.05%;
    background-color: yellow;
    overflow:hidden;
}

.square .blank{
    float:left;
    position: relative;
    width: {!! 100/26 !!}%;
    padding-bottom : {!! 100/26 !!}%; /* = width for a 1:1 aspect ratio */
    margin:0.05%;
    background-color: white;
    overflow:hidden;
}

.square .dept:hover {
    background-color:gray;
}

.square .paid:hover {
    background-color:gray;
}
.square .paidover:hover {
    background-color:gray;
}
.square .paid{
    float:left;
    position: relative;
    width: {!! 100/26 !!}%;
    padding-bottom : {!! 100/26 !!}%; /* = width for a 1:1 aspect ratio */
    margin:0.05%;
    background-color: #9fe1ff;
    overflow:hidden;
}

.square .dept{
    float:left;
    position: relative;
    width: {!! 100/26 !!}%;
    padding-bottom : {!! 100/26 !!}%; /* = width for a 1:1 aspect ratio */
    margin:0.05%;
    background-color: red;
    overflow:hidden;
}

.content {
    position:absolute;
    height:90%; /* = 100% - 2*5% padding */
    width:90%; /* = 100% - 2*5% padding */
    padding: 5%;
}
.table{
    display:table;
    width:100%;
    height:100%;
}
.table-cell{
    display:table-cell;
    vertical-align:middle;
}
/*  For list */
/*ul{
    text-align:left;
    margin:5% 0 0;
    padding:0;
    list-style-position:inside;
}
li{
    margin: 0 0 0 5%;
    padding:0;
}
*/

/*  For responsive images */

.content .rs{
    width:auto;
    height:auto;
    max-height:90%;
    max-width:100%;
}

.bg{
    background-position:center center;
    background-repeat:no-repeat;
    background-size:cover; /* you change this to "contain" if you don't want the images to be cropped */
    color:#fff;
}

body {
    font-size:12px;
    font-family: 'Lato',verdana, sans-serif;
    color: #fff;
    text-align:center;
    background:#ECECEC;
}
p{
    margin:0;
    padding:0;
    text-align:left;
}

.numbers{
    font-weight:12;
    font-size:12px;
}

#bottom {
    clear:both;
    margin:0 1.66%;
    width:89.68%;
    padding: 3.5%;
    background-color:#1E1E1E;
    color: #fff;
}
#bottom p{
    text-align:center;
    line-height:2em;
}
#bottom a{
    color: #000;
    text-decoration:none;
    border:1px solid #000;
    padding:10px 20px 12px;
    line-height:70px;
    background:#ccc;
    
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}

#bottom a:hover{
    background:#ECECEC;
    border:1px solid #fff;
}


a:not(.btn):not(:empty)::before {
    content: none !important;
}

a.link:not(:empty), .table a:not(.btn):not(:empty) {
    white-space: normal !important;
    color: #ffffff !important;
    background-color: transparent !important ;
    border-color: transparent !important ;
}
</style>
@section('script')
<script>
    $(function () {
        $('#deptnotice1').click(function() {
            bootbox.confirm("Thông báo nhắc nợ lần 1 sẽ được tạo. <br>Thời gian tạo file phụ thuộc vào số lượng căn hộ.", function(result) {
                if(result){
                    var year = $("#year").val();
                    var month = $("#month").val();
                    window.open("../report/deptnotice/1");                   
                }
            });            
        });

        $('#deptnotice2').click(function() {
            bootbox.confirm("Thông báo nhắc nợ lần 2 sẽ được tạo. <br>Thời gian tạo file phụ thuộc vào số lượng căn hộ.", function(result) {
                if(result){
                    var year = $("#year").val();
                    var month = $("#month").val();
                    window.open("../report/deptnotice/2");                   
                }
            });            
        });

        $('#deptnotice3').click(function() {
            bootbox.confirm("Thông báo nhắc nợ lần 3 sẽ được tạo. <br>Thời gian tạo file phụ thuộc vào số lượng căn hộ.", function(result) {
                if(result){
                    var year = $("#year").val();
                    var month = $("#month").val();
                    window.open("../report/deptnotice/3");                   
                }
            });            
        });

        $('#file_deptnotice2').click(function() {
            window.open("../report/deptnoticefiles/2");
        });

        $('#file_deptnotice1').click(function() {
            window.open("../report/deptnoticefiles/1");
        });        

        $('#file_deptnotice3').click(function() {
            window.open("../report/deptnoticefiles/3");
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
        <h2>Thông Tin Phí Phát Sinh Tháng</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
    <table>
        <tr>
            <td style="background-color: red">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
            <td>&nbsp;Cư dân còn công nợ chưa thu</td>
        </tr>

        <tr>
            <td style="background-color: yellow">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
            <td>&nbsp;Cư dân trả vượt quá công nợ</td>
        </tr>

        <tr>
            <td style="background-color: #9fe1ff">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
            <td>&nbsp;Cư dân đã trả công nợ</td>
        </tr>
        <tr>
        <td colspan="2">
            <a id='deptnotice1' class="btn btn-primary">Tạo Nhắc Nợ Lần 1</a>
            <a id='file_deptnotice1' class="btn btn-info">Danh Sách File</a>
            &nbsp;
            &nbsp;
            &nbsp;
            <a id="deptnotice2" class="btn btn-primary">Tạo Nhắc Nợ Lần 2</a>
            <a id="file_deptnotice2" class="btn btn-info">Danh Sách File</a>
            &nbsp;
            &nbsp;
            &nbsp;
            <a id="deptnotice3" class="btn btn-primary">Tạo Nhắc Nợ Lần 3</a>
            <a id="file_deptnotice3" class="btn btn-info">Danh Sách File</a>            
        </td>
        </tr>
    </table>
    
<?php
    $step = 1;
    // for ($i = 0; $i < 24; $i++){
    //     $value = 
    // }
    $blocks = array(0=>array('A',7,20,0,24), 1=>array('B',3,22,0,24));

    foreach ($blocks as $block)
    {

        echo "<div class='square'>";
        echo "<div class='blank'>";
        echo "    <div class='content'>";
        echo "        <div class='table'>";
        echo "            <div class='table-cell' style='font-size: 40px;'>";
        echo $block[0];
        echo "            </div>";
        echo "        </div>";
        echo "    </div>";
        echo "    </div>";
        echo "</div>";
        for ($i=0; $i < $block[4]; $i++){
            echo "<div class='square'>";
            echo "<div class='blank'>";
            echo "    <div class='content'>";
            echo "        <div class='table'>";
            echo "            <div class='table-cell'>";
            // echo $value->address;
            echo "&nbsp;";
            echo "            </div>";
            echo "        </div>";
            echo "    </div>";
            echo "    </div>";
            echo "</div>";
        }

        for($floor = $block[1]; $floor < $block[2]; $floor++){
            echo "<div class='square'>";
            echo "<div class='blank'>";
            echo "    <div class='content'>";
            echo "        <div class='table'>";
            echo "            <div class='table-cell' style='font-size: 30px;'>";
            echo $floor;
            echo "            </div>";
            echo "        </div>";
            echo "    </div>";
            echo "    </div>";
            echo "</div>";

            for($count = $block[3]; $count < $block[4]; $count++){
                if (isset($flats[$block[0] . $floor . $count])) {
                    $value = $flats[$block[0] . $floor . $count];
                    $dept = $value->manager_dept + $value->elec_dept + $value->water_dept + $value->gas_dept + $value->parking_dept + $value->service_dept;
                        $color = "paid";
                        if ($dept < 0){
                            $color = "paidover";
                        } else if ($dept > 0) {
                            $color = "dept";
                        }

                    echo "<div class='square'>";
                    echo "<div class='". $color ."'>";
                    echo "    <div class='content'>";
                    echo "        <div class='table'>";
                    echo "            <div class='table-cell'>";
                    // echo $value->address;
                    echo "<a  class='link' style='text-align: center;color: black !important;' href='status/". $value->id . "'>" . $value->address . "</a>";

                    echo "            </div>";
                    echo "        </div>";
                    echo "    </div>";
                    echo "    </div>";
                    echo "</div>";
                } else {
                    echo "<div class='square'>";
                    echo "<div class='blank'>";
                    echo "    <div class='content'>";
                    echo "        <div class='table'>";
                    echo "            <div class='table-cell'>";
                    // echo $value->address;
                    echo "&nbsp;";
                    echo "            </div>";
                    echo "        </div>";
                    echo "    </div>";
                    echo "    </div>";
                    echo "</div>";
                }
            }
        }
        for ($i=0; $i < $block[4] + 1; $i++){
            echo "<div class='square'>";
            echo "<div class='blank'>";
            echo "    <div class='content'>";
            echo "        <div class='table'>";
            echo "            <div class='table-cell'>";
            // echo $value->address;
            echo "&nbsp;";
            echo "            </div>";
            echo "        </div>";
            echo "    </div>";
            echo "    </div>";
            echo "</div>";
        }
    }
    // foreach ($monthlyFeeFlat as $value) {
    //     $dept = $value->manager_dept + $value->elec_dept + $value->water_dept + $value->gas_dept + $value->parking_dept + $value->service_dept;
    //     $color = "paid";
    //     if ($dept < 0){
    //         $color = "paidover";
    //     } else if ($dept > 0) {
    //         $color = "dept";
    //     }

    //     echo "<div class='square'>";
    //     echo "<div class='". $color ."'>";
    //     echo "    <div class='content'>";
    //     echo "        <div class='table'>";
    //     echo "            <div class='table-cell'>";
    //     // echo $value->address;
    //     echo "<a  class='link' style='text-align: center;' href='../flat/detail/". $value->id . "'>" . $value->address . "</a>";

    //     echo "            </div>";
    //     echo "        </div>";
    //     echo "    </div>";
    //     echo "    </div>";
    //     echo "</div>";
    // }
?>
</form>
@endsection

