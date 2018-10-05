@extends('include.layout')

@section('style')
{!! Html::style('DMS/css/fullcalendar.css') !!}
{!! Html::style('DMS/js/plugins/fullcalendar/fullcalendar.min.css') !!}
{!! Html::style('DMS/css/fullcalendar.print.css', ['media' => 'print','rel'=>"stylesheet"]) !!}
{!! Html::style('css/datepicker/jquery-ui.css') !!}
{!! Html::style('css/tageditor/jquery.tag-editor.css') !!}
<!-- {!! Html::style('css/timepicker/jquery.timepicker.min.css') !!} -->
{!! Html::style('DMS/css/bootstrap-timepicker.css') !!}
{!! Html::style('DMS/css/bootstrap-timepicker.min.css') !!}

{!! Html::style('css/multiselect/bootstrap-multiselect.css') !!}
{!! Html::style('css/colorpicker/spectrum.css') !!}
<style>

	body {
		margin-top: 40px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
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
@endsection

@section('script')
{!! Html::script('js/ckeditor/ckeditor.js') !!}
{!! Html::script('DMS/js/moment.min.js') !!}
{!! Html::script('DMS/js/jquery.min.js') !!}
<!-- {!! Html::script('DMS/js/jquery-ui.min.js') !!}
{!! Html::script('DMS/js/fullcalendar.min.js') !!} -->
{!! HTML::script('DMS/js/plugins/fullcalendar/lib/jquery-ui.min.js') !!}
{!! HTML::script('DMS/js/plugins/jquery-ui/jquery.ui.touch-punch.min.js') !!}
{!! HTML::script('DMS/js/plugins/fullcalendar/lib/moment-with-locales.min.js') !!}
{!! HTML::script('DMS/js/plugins/fullcalendar/fullcalendar.min.js') !!}
{!! HTML::script('DMS/js/plugins/fullcalendar/locale-all.js') !!}
{!! Html::script('DMS/js/jquery-migrate-3.0.0.js') !!}
<!-- {!! Html::script('js/timepicker/jquery.timepicker.min.js')  !!} -->
{!! Html::script('DMS/js/bootstrap-timepicker.js')  !!}

{!! Html::script('js/datepicker/jquery-ui.js')  !!}
{!! Html::script('js/manual_js/manual_click.js') !!}

{!! Html::script('js/tageditor/jquery.tag-editor.js')  !!}
{!! Html::script('js/tageditor/jquery.caret.min.js')  !!}
<script>

	$(function () {
		callDatePicker();

		$("#exeSubmit").click(function(){
            bootbox.confirm("Thông tin báo cáo sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmSubmit").submit();
                }
            });
	    });

		$("#abc").click(function(){
            bootbox.confirm("Thông tin báo cáo sẽ được cập nhật?", function(result) {
                if(result){
                    $("#frmSubmit").submit();
                }
            });
	    });

        $('.timepicker').timepicker({
            minuteStep: 1,
            template: 'modal',
            appendWidgetTo: 'body',
            showSeconds: true,
            showMeridian: false,
            defaultTime: false
        });

		$("#close").click(function(){
            $('#reportmodAl').hide();
	    });
		$("#hide").click(function(){
            $('#reportmodAl').hide();
	    });

		var zone = "05:30";  //Change this to your timezone

		var currentMousePos = {
		    x: -1,
		    y: -1
		};
		
		jQuery(document).on("mousemove", function (event) {
	        currentMousePos.x = event.pageX;
	        currentMousePos.y = event.pageY;
	    });

        // var dateFormat = "dd/mm/yy";
        // $("#report_date").datepicker({
        //     dateFormat: dateFormat,
        //     changeMonth: true,
        //     changeYear: true,
        //     showButtonPanel: false
        // });

		// $("#reportmodAl").on("show.bs.modal", function (e) {
		//      $("#reportmodAlLabel").html($(e.relatedTarget).data('title'));
		//      $("#fav-title").html($(e.relatedTarget).data('title'));
		// });

		$('#external-events .fc-event').each(function() {
			// store data so the calendar knows to render an event upon drop
			$(this).data('event', {
				title: $.trim($(this).text()), // use the element's text as the event title
				stick: true // maintain when user navigates (see docs on the renderEvent method)
			});

			// make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 0  //  original position after the drag
			});

		});

		/* initialize the calendar
		-----------------------------------------------------------------*/
		$('#calendar').fullCalendar({
			dayRender: function(date, cell){
            	var now = new Date();
    			now.setDate(now.getDate() - 1);
                if (date < now){
                    cell.css("background-color", "#F0F0F0 !important");
                }
            },
			dayClick: function (date, allDay, jsEvent, view) {
	            $("#daily_date").val($.fullCalendar.moment(date).format("DD/MM/YYYY"));
	            $("#id").val("");
	            $("#name").val("");

				$("#start_time").val("");
				$("#end_time").val("");
				$("#charge_for").val("");
				$("#description").val("");
				$("#company_execute").val("");
				$("#note").val("");
				$("#category1").val("");
				$("#note1").val("");

				$("#category2").val("");
				$("#note2").val("");

				$("#category3").val("");
				$("#note3").val("");

				$('#reportmodAl').attr('class', "modal fade in");
				$('#reportmodAl').show();
			}, 
			utc: true,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			eventSources: {
				url: 'dailyactivity/events/1'
			},
			editable: true,
			droppable: true, 
			slotDuration: '00:30:00',
			selectable: true,

			eventReceive: function(event){
	            // $("#report_date").val(date);
	            $("#note3").val(event.title);
				$('#reportmodAl').attr('class', "modal fade in");	
				$('#reportmodAl').show();
			},
			eventDrop: function(event, delta, revertFunc) {
		  //       var title = event.title;
		  //       var start = event.start.format();
		  //       var end = (event.end == null) ? start : event.end.format();
		  //       $.ajax({
				// 	url: 'process.php',
				// 	data: 'type=resetdate&title='+title+'&start='+start+'&end='+end+'&eventid='+event.id,
				// 	type: 'POST',
				// 	dataType: 'json',
				// 	success: function(response){
				// 		if(response.status != 'success')		    				
				// 		revertFunc();
				// 	},
				// 	error: function(e){		    			
				// 		revertFunc();
				// 		alert('Error processing your request: '+e.responseText);
				// 	}
				// });
		    },
		    eventClick: function(event, jsEvent, view) {
				var s = event.daily_date;
				if (s) { 
				    s = s.replace(/(\d{4})-(\d{1,2})-(\d{1,2})/, function(match,y,m,d) { 
				        return d + '/' + m + '/' + y;  
				    });
				}

	            $("#name").val(event.title);
	            $("#daily_date").val(s);
	            $("#id").val(event.id);

				$("#start_time").val(event.start_time);
				$("#end_time").val(event.end_time);
				$("#charge_for").val(event.charge_for);
				$("#description").val(event.description);
				$("#company_execute").val(event.company_execute);
				$("#note").val(event.note);
				$("#category1").val(event.category1_id);
				$("#note1").val(event.category1_note);

				$("#category2").val(event.category2_id);
				$("#note2").val(event.category2_note);

				$("#category3").val(event.category3_id);
				$("#note3").val(event.category3_note);
			},

			eventRender: function(event, element) {
                element.attr('data-toggle', "modal");
                element.attr('data-target', "#reportmodAl");
                element.attr('href', "/details");
            },
		});


			// eventResize: function(event, delta, revertFunc) {
			// 	console.log(event);
			// 	var title = event.title;
			// 	var end = event.end.format();
			// 	var start = event.start.format();
		 //        $.ajax({
			// 		url: 'process.php',
			// 		data: 'type=resetdate&title='+title+'&start='+start+'&end='+end+'&eventid='+event.id,
			// 		type: 'POST',
			// 		dataType: 'json',
			// 		success: function(response){
			// 			if(response.status != 'success')		    				
			// 			revertFunc();
			// 		},
			// 		error: function(e){		    			
			// 			revertFunc();
			// 			alert('Error processing your request: '+e.responseText);
			// 		}
			// 	});
		 //    },
			// eventDragStop: function (event, jsEvent, ui, view) {
			//     if (isElemOverDiv()) {
			//     	var con = confirm('Are you sure to delete this event permanently?');
			//     	if(con == true) {
			// 			$.ajax({
			// 	    		url: 'process.php',
			// 	    		data: 'type=remove&eventid='+event.id,
			// 	    		type: 'POST',
			// 	    		dataType: 'json',
			// 	    		success: function(response){
			// 	    			console.log(response);
			// 	    			if(response.status == 'success'){
			// 	    				$('#calendar').fullCalendar('removeEvents');
   //          						getFreshEvents();
   //          					}
			// 	    		},
			// 	    		error: function(e){	
			// 	    			alert('Error processing your request: '+e.responseText);
			// 	    		}
			//     		});
			// 		}   
			// 	}
			// }
		// });
		// function getFreshEvents(){
		// 	$.ajax({
		// 		url: 'process.php',
		//         type: 'POST', // Send post data
		//         data: 'type=fetch',
		//         async: false,
		//         success: function(s){
		//         	freshevents = s;
		//         }
		// 	});
		// 	$('#calendar').fullCalendar('addEventSource', JSON.parse(freshevents));
		// }


		// function isElemOverDiv() {
	 //        var trashEl = jQuery('#trash');

	 //        var ofs = trashEl.offset();

	 //        var x1 = ofs.left;
	 //        var x2 = ofs.left + trashEl.outerWidth(true);
	 //        var y1 = ofs.top;
	 //        var y2 = ofs.top + trashEl.outerHeight(true);

	 //        if (currentMousePos.x >= x1 && currentMousePos.x <= x2 &&
	 //            currentMousePos.y >= y1 && currentMousePos.y <= y2) {
	 //            return true;
	 //        }
	 //        return false;
	 //    }

	});
</script>
@endsection

@section('content')
<div class="section-tout title" id="title">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <span class="fa fa-calendar-plus-o fa-fw"></span>
        <h2>Thông tin Nhật Ký Kỹ Thuật</h2>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div class="col-lg-12">
		<div id='calendar'></div>
	</div>

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
					<h4 class="modal-title" id="reportmodAlLabel">Nhật Ký Kỹ Thuật</h4>
				</div>
				<form id='frmSubmit' action="{!! route('DailyActivity.report') !!}" method="POST" role="form">

					<div class="modal-body">
					<table class="table table-striped table-bordered table-hover table-condensed">
						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
		                <input id="id" name='id' type="hidden" value="">
						<tbody>
		                    <tr>
		                        <td>Nhật ký kỹ thuật<em style='color:red'>(*)</em> </td>
		                        <td colspan="3"><input type="text" value='' id="name" name="name" size="30%"></td>
		                 	</tr>

		                    <tr>
		                        <td>Ngày thực hiện<em style='color:red'>(*)</em> </td>
		                        <td><input type="text" class='date-picker' value='{!! date('d/m/Y') id="daily_date" name="daily_date" size="30%"></td>
		                        <td>Thời gian<em style='color:red'>(*)</em> </td>
		                        <td>
			                        <div class="input-group bootstrap-timepicker timepicker">
				                        <input type="text" class='timepicker' value="" id="start_time" name="start_time" size="10%"> ~ 
				                        <input type="text" class='timepicker' value="" id="end_time" name="end_time" size="10%">
						        	</div>
							    </td>
		                 	</tr>
		                 	<tr>
		                        <td>Mô tả<em style='color:red'>(*)</em> </td>
		                        <td colspan="3"><input type="text" value="" id="description" name="description" size="30%"></td>
		                 	</tr>
		             	    <tr>
		                        <td>Người phụ trách<em style='color:red'>(*)</em> </td>
		                        <td><input type="text" value="" id="charge_for" name="charge_for" size="30%"></td>
		                        <td>Công ty thực hiện<em style='color:red'>(*)</em></td>
		                        <td><input type="text" value="" id="company_execute" name="company_execute" size="30%"></td>
		                 	</tr>
		                 	<tr>
		                 		<td>Hạng mục bảo trì
		                 		</td>
		                 		<td>
									<?php
	                                    $oldItem = old('category1');
	                                    echo '<select name="category1" id="category1" style="width: 150px;">';
	                                        foreach($mst_daily_activity_types as $item) {
	                                            $selected = ($oldItem == $item->id ? 'selected' : '');
	                                            echo '<option value="'.$item->id . '" '. $selected .'>';
	                                        
	                                            echo $item->name;
	                                            echo '</option>';
	                                        }
	                                    echo '</select>';
	                                ?>
		                 		</td>	                 		
		                 		<td>Chi tiết
		                 		</td>
		                 		<td><input type="text" value="" id="note1" name="note1" size="30%">
		                 		</td>
		                 	</tr>
		                 	<tr>
		                 		<td>Hạng mục bảo trì
		                 		</td>
		                 		<td>
									<?php
	                                    $oldItem = old('category2');
	                                    echo '<select name="category2" id="category2" style="width: 150px;">';
	                                        foreach($mst_daily_activity_types as $item) {
	                                            $selected = ($oldItem == $item->id ? 'selected' : '');
	                                            echo '<option value="'.$item->id . '" '. $selected .'>';
	                                        
	                                            echo $item->name;
	                                            echo '</option>';
	                                        }
	                                    echo '</select>';
	                                ?>
		                 		</td>	                 		
		                 		<td>Chi tiết
		                 		</td>
		                 		<td><input type="text" value="" id="note2" name="note2" size="30%">
		                 		</td>
		                 	</tr>
		                 	<tr>
		                 		<td>Hạng mục bảo trì
		                 		</td>
		                 		<td>
									<?php
	                                    $oldItem = old('category3');
	                                    echo '<select name="category3" id="category3" style="width: 150px;">';
	                                        foreach($mst_daily_activity_types as $item) {
	                                            $selected = ($oldItem == $item->id ? 'selected' : '');
	                                            echo '<option value="'.$item->id . '" '. $selected .'>';
	                                        
	                                            echo $item->name;
	                                            echo '</option>';
	                                        }
	                                    echo '</select>';
	                                ?>
		                 		</td>	                 		
		                 		<td>Chi tiết
		                 		</td>
		                 		<td><input type="text" value="" id="note3" name="note3" size="30%">
		                 		</td>
		                 	</tr>
		             	    <tr>
		                        <td>Ghi chú<em style='color:red'>(*)</em> </td>
		                        <td colspan="3"><input type="text" value="" id="note" name="note" size="30%"></td>
		                 	</tr>
	             		</tbody>
	             	</table>
					</div>
					<div class="modal-footer">
						<button type="button" 
						class="btn btn-default" 
						data-dismiss="modal" id="hide">Close</button>
						<span class="pull-right">
							<button id='exeSubmit' type="button" class="btn btn-primary">Lưu Báo Cáo</button>
						</span>
					</div>
        		</form>
			</div>
		</div>
	</div>
</div>
@endsection

