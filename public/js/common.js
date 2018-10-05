/*--- FUNCTION BUTTONS ---*/
$('.page-btn-master').append($('.page-btn-container').clone()).html();
$('.page-btn-container').eq("0").remove();
$('.page-btn-container').removeClass("hidden");

/*--- Prevent BACKSPACE from navigating back ---*/
$(document).on("keydown", function (e) {
  if (e.which === 8 && !$(e.target).is("input, textarea")) {
      e.preventDefault();
  }
});

$(document).ready(function() {
    $(".commas").each(function() {
        var numb = Number($(this).val()).toLocaleString('en');
        $(this).val(numb);
    });
});

/*--- AUTO-SELECT ---*/
/* Author: Quyen */
$("input.auto-select").on("blur", function() {
    var option = $(this).val();
    $(this).parent("div").parent("div").find("select").val(option);
}).on("keydown", function(event) {
    if(event.keyCode == 13) {
        var option = $(this).val();
        $(this).parent("div").parent("div").find("select").val(option);
    } else if(event.keyCode == 9) {
        var option = $(this).val();
        $(this).parent("div").parent("div").find("select").val(option);
    }
});
$("select.auto-select").on("change", function() {
    var option = $(this).val();
    $(this).parent("div").parent("div").find("input").val(option);
});


/*--- DATEPICKER ---*/
/* Author: Quyen */
function calculationEndDay(month, year) {
    var end_day = "";
    var arr_30 = ["04", "06", "09", "11"];
    var arr_31 = ["01", "03", "05", "07", "08", "10", "12"];
    
    if(month == "02") {
        if(new Date(year, 1, 29).getMonth() === 1) {
            end_day = "29";
        } else {
            end_day = "28";
        }
    } else if($.inArray(month, arr_30) != "-1") {
        end_day = "30";
    } else if($.inArray(month, arr_31) != "-1") {
        end_day = "31";
    }
    
    return end_day;
}

/* Author: Quyen */
function addOneMoreDay(base_date) {
    var day = "";
    var month = "";
    var year = "";
    var new_date = "";
    var parts_base_date = base_date.split('/');
    
    if(parts_base_date[2] >= 1 && parts_base_date[2] <= 27) {
        day = parseInt(parts_base_date[2]) + 1;
        
        if(day < 10) {
            day = "0"+day;
        }
        
        month = parts_base_date[1];
        year = parts_base_date[0];
        
        new_date = year+"/"+month+"/"+day;
    } else {
        switch(parts_base_date[2]) {
            case "28":
                if(parseInt(parts_base_date[1]) == 2) {
                    if(new Date(parts_base_date[0], 1, 29).getMonth() === 1) {
                        day = "29";
                        
                        month = parts_base_date[1];
                        year = parts_base_date[0];
                        
                        new_date = year+"/"+month+"/"+day;
                    } else {
                        day = "01";
                        
                        month = "03";
                        year = parts_base_date[0];
                        
                        new_date = year+"/"+month+"/"+day;
                    }
                } else {
                    day = "29";
                    
                    month = parts_base_date[1];
                    year = parts_base_date[0];
                    
                    new_date = year+"/"+month+"/"+day;
                }
                break;
            case "29": 
                if(parseInt(parts_base_date[1]) == 2) {
                    if(new Date(parts_base_date[0], 1, 29).getMonth() === 1) { 
                        day = "01";
                        
                        month = "03";
                        year = parts_base_date[0];
                        
                        new_date = year+"/"+month+"/"+day;
                    }
                } else {
                    day = "30";
                    
                    month = parts_base_date[1];
                    year = parts_base_date[0];
                    
                    new_date = year+"/"+month+"/"+day;
                }
                break;
            case "30":
                switch(parseInt(parts_base_date[1]).toString()) {
                    case "1":
                    case "3": 
                    case "5": 
                    case "7": 
                    case "8": 
                    case "10": 
                    case "12":
                        day = "31";
                    
                        month = parts_base_date[1];
                        year = parts_base_date[0];
                        
                        new_date = year+"/"+month+"/"+day;
                        break;
                    case "4":
                    case "6":
                    case "9": 
                    case "11":
                        day = "01";
                    
                        month = parseInt(parts_base_date[1]) + 1;
                        if(month < 10) {
                            month = "0"+month;
                        }
                        
                        year = parts_base_date[0];
                        
                        new_date = year+"/"+month+"/"+day;
                        break;
                }
                break;
            case "31":
                if(parts_base_date[1] == 12) {
                    day = "01";
                    month = "01";
                    year = parseInt(parts_base_date[0]) + 1;
                    
                    new_date = year+"/"+month+"/"+day;
                } else {
                    day = "01";
                    
                    month = parseInt(parts_base_date[1]) + 1;
                    if(month < 10) {
                        month = "0"+month;
                    }
                    
                    year = parts_base_date[0];
                    
                    new_date = year+"/"+month+"/"+day;
                }
                break;
        }
    }
    
    return new_date;
}

$( document ).ajaxStart(function() {
	showLoading();
});

$( document ).ajaxStop(function() {
	hideLoading();
});

/* Author: Quyen */
function inputDate(evt, id) {
    var e = evt || window.event;
    var val = $('#'+id).val();
    
    /* 8: backspace
     * 9: tab
  	 * 13: enter
  	 * 37: left arrow
  	 * 39: right arrow
  	 * 48~57: 0-9
     */
    if ((e.keyCode >= 48 && e.keyCode <= 57) 
            || (e.charCode >= 48 && e.charCode <= 57) 
            || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 13 
            || e.keyCode == 37 || e.keyCode == 39) {
    	
        if(val.length == 4 && e.keyCode != 8) {
            $('#'+id).val(val+"/");
        } else if(val.length == 7 && e.keyCode != 8) {
            $('#'+id).val(val+"/");
        }
        return;
    } else if (e.keyCode == 9) {
        validateDate(id);
    } else {
        e.preventDefault();
    }
}

/* Author: Quyen */
//Date can be empty and can choose the day that is larger than today
function validateDate(id) {
    var date_format = /^\d{4}[\/](0[1-9]|1[012])[\/](0[1-9]|1[0-9]|2[0-9]|3[01])$/;
    var date_value = $('#'+id).val();
    
    if(date_value.match(date_format) != null) {
        var parts = date_value.split('/');
        
        if(parts[1] == 2 && parts[2] >= 30) {
        	$('#'+id).addClass('error');
            showAlert("show", "ERROR_MSG_001");
            
        } else if(parts[1] == 2 && parts[2] == 29) { 
            if(new Date(parts[0], 1, 29).getMonth() !== 1) {
            	$('#'+id).addClass('error');
                showAlert("show", "ERROR_MSG_001");  
            } else {
                if($('#'+id).hasClass('error')) {
                    showAlert("hide");
                    $('#'+id).removeClass('error');
                }
            }
        } else {
            if($('#'+id).hasClass('error')) {
                showAlert("hide");
                $('#'+id).removeClass('error');
            }
        }
    } else {
        if(date_value != "") {
        	$('#'+id).addClass('error');
            showAlert("show", "ERROR_MSG_002");  
         } else {
             if($('#'+id).hasClass('error')) {
                 showAlert("hide");
                 $('#'+id).removeClass('error');
             }
         }
    }
}

/* Author: Quyen */
function inputMonth(evt, id) {
    var e = evt || window.event;
    var val = $('#'+id).val();
    
    /* 8: backspace
     * 9: tab
     * 13: enter
     * 37: left arrow
     * 39: right arrow
     * 48~57: 0-9
     */
    if((e.keyCode >= 48 && e.keyCode <= 57) 
            || (e.charCode >= 48 && e.charCode <= 57)
            || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 13
            || e.keyCode == 37 || e.keyCode == 39) {
    	
        if(val.length == 4 && e.keyCode != 8) {
            $('#'+id).val(val+"/");
        }
        return;
    } else if (e.keyCode == 9) {
        validateMonth(id);
    } else {
        e.preventDefault();
    }
}


/* Author: Quyen */
//Month can be empty and can choose the month that is larger than the current month
function validateMonth(id) {
    var month_format = /^\d{4}[\/](0[1-9]|1[012])$/;
    var month_value = $('#' + id).val();

    if(month_value.match(month_format) != null) {
        if($('#'+id).hasClass('error')) {
            showAlert("hide"); 
            $('#'+id).removeClass('error');
        }
    } else {
        if(month_value != "") {
            if($('#'+id).hasClass('error') == false) {
                $('#'+id).addClass('error');
                showAlert("show", "ERROR_MSG_003");  
            }
        } else {
            if($('#'+id).hasClass('error')) {
                showAlert("hide"); 
                $('#'+id).removeClass('error');
            }
        }   
    }
}
/*--- END DATEPICKER ---*/


/*--- DATERANGE ---*/
/* Author: Quyen */
function inputDateRange(evt, id) {
    var e = evt || window.event;
    var val = $('#'+id).val();
    
    /* 8: backspace
     * 9: tab
	 * 13: enter
	 * 37: left arrow
	 * 39: right arrow
	 * 48~57: 0-9
     */
    if((e.keyCode >= 48 && e.keyCode <= 57) 
            || (e.charCode >= 48 && e.charCode <= 57) 
            || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 13 
            || e.keyCode == 37 || e.keyCode == 39) {
    	
        if(val.length == 4 && e.keyCode != 8) {
            $('#'+id).val(val+"/");
        } else if(val.length == 7 && e.keyCode != 8) {
            $('#'+id).val(val+"/");
        }
        return;
        
    } else if (e.keyCode == 9) {
        validateDateRange(id);
    } else {
        e.preventDefault();
    }
}

/* Author: Quyen */
function validateDateRange(id) {
    var date_format = /^\d{4}[\/](0[1-9]|1[012])[\/](0[1-9]|1[0-9]|2[0-9]|3[01])$/;
    var date_value = $('#' + id).val();

    if (date_value.match(date_format) != null) {
        var parts = date_value.split('/');
    
        if ((parts[1] == 2) && parts[2] >= 30) {
            if($('#'+id).hasClass('error') == false) {
                $('#'+id).addClass('error');
                showAlert("show", "ERROR_MSG_001");   
           }
        } else if(parts[1] == 2 && parts[2] == 29) { 
            if(new Date(parts[0], 1, 29).getMonth() !== 1) {
                if($('#'+id).hasClass('error') == false) {
                    $('#'+id).addClass('error');
                    showAlert("show", "ERROR_MSG_001");  
               }
            } else {
                if($('#'+id).hasClass('error')) {
                    showAlert("hide"); 
                    $('#'+id).removeClass('error');
                }
                
                var id_container = $('#'+id).parent("div").attr("id");
                if ($("#"+id_container+" #"+id_container+"_frm").hasClass('error') == false 
                      && $("#"+id_container+" #"+id_container+"_to").hasClass('error') == false) {
                    var start_date = $("#"+id_container+" #"+id_container+"_frm").val();
                    var end_date = $("#"+id_container+" #"+id_container+"_to").val();
        
                    if ((new Date(start_date).getTime() > new Date(end_date).getTime())) {
                        $("#"+id_container+" #"+id_container+"_frm").datepicker('update', end_date);
                    }
                } 
            }
        } else {
            if($('#'+id).hasClass('error')) {
                showAlert("hide"); 
                $('#'+id).removeClass('error');
            }
            
            var id_container = $('#'+id).parent("div").attr("id");
            if ($("#"+id_container+" #"+id_container+"_frm").hasClass('error') == false 
                  && $("#"+id_container+" #"+id_container+"_to").hasClass('error') == false) {
                var start_date = $("#"+id_container+" #"+id_container+"_frm").val();
                var end_date = $("#"+id_container+" #"+id_container+"_to").val();
    
                if ((new Date(start_date).getTime() > new Date(end_date).getTime())) {
                    $("#"+id_container+" #"+id_container+"_frm").datepicker('update', end_date);
                }
            }
        }
    } else {
        if(date_value != "") {
            if($('#'+id).hasClass('error') == false) {
                $('#'+id).addClass('error');
                showAlert("show", "ERROR_MSG_002"); 
            }
         } else {
             if($('#'+id).hasClass('error')) {
                 showAlert("hide"); 
                 $('#'+id).removeClass('error');
             }
         }
    }
}

/* Author: Quyen */
function inputMonthRange(evt, id) {
    var e = evt || window.event;
    var val = $('#'+id).val();
    
    /* 8: backspace
     * 9: tab
	 * 13: enter
	 * 37: left arrow
	 * 39: right arrow
	 * 48~57: 0-9
     */
    if((e.keyCode >= 48 && e.keyCode <= 57) 
            || (e.charCode >= 48 && e.charCode <= 57) 
            || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 13 
            || e.keyCode == 37 || e.keyCode == 39) {
    	
        if(val.length == 4 && e.keyCode != 8) {
            $('#'+id).val(val+"/");
        }
        return;
        
    } else if (e.keyCode == 9) {
        validateMonthRange(id);
    } else {
        e.preventDefault();
    }
}

/* Author: Quyen */
function validateMonthRange(id) {
    var month_format = /^\d{4}[\/](0[1-9]|1[012])$/;
    var month_value = $('#' + id).val();

    if(month_value.match(month_format) != null) {
        if($('#'+id).hasClass('error')) {
            showAlert("hide"); 
            $('#'+id).removeClass('error');
        }
        
        var id_container = $('#'+id).parent("div").attr("id");
        if ($("#"+id_container+" #"+id_container+"_frm").hasClass('error') == false 
              && $("#"+id_container+" #"+id_container+"_to").hasClass('error') == false) {
            var start_date = $("#"+id_container+" #"+id_container+"_frm").val();
            var end_date = $("#"+id_container+" #"+id_container+"_to").val();

            if ((new Date(start_date).getTime() > new Date(end_date).getTime())) {
                $("#"+id_container+" #"+id_container+"_frm").datepicker('update', end_date);
            }
        }
    } else {
        if(month_value != "") {
            if($('#'+id).hasClass('error') == false) {
                $('#'+id).addClass('error');
                showAlert("show", "ERROR_MSG_003"); 
            }
         } else {
             if($('#'+id).hasClass('error')) {
                 showAlert("hide"); 
                 $('#'+id).removeClass('error');
             }
         }
    }
}
/*--- END DATERANGE ---*/

/*function validateYear(id) {
	var year_format = /^(19|20)\d{2}$/;
    var year_value = $('#' + id).val();
    
    if(year_value.match(year_format) != null) {
    	if($('#'+id).hasClass('error')) {
            showAlert("hide"); 
            $('#'+id).removeClass('error');
        }
    } else {
        if(month_value != "") {
            if($('#'+id).hasClass('error') == false) {
                $('#'+id).addClass('error');
                showAlert("show", "ERROR_MSG_003"); 
            }
         } else {
             if($('#'+id).hasClass('error')) {
                 showAlert("hide"); 
                 $('#'+id).removeClass('error');
             }
         }
    }
	
}*/


/* --- INPUT --- */
/* Author: Quyen */
//Can only input numbers
function inputNumbers(evt) { 
    var e = evt || window.event;
    
    /* 8: backspace
     * 9: tab
	 * 13: enter
	 * 37: left arrow
	 * 39: right arrow
	 * 48~57: 0-9
     */
    if ((e.keyCode >= 48 && e.keyCode <= 57) 
            || (e.charCode >= 48 && e.charCode <= 57) 
            || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 13 
            || e.keyCode == 37 || e.keyCode == 39) {
        return;
    } else {
        e.preventDefault();
    }
}


/* --- FORMAT --- */
/* Author: Quyen */
function formatNumeric(numeric_id) {
    var number = $('#'+numeric_id).val();
    number = number.replace(/[^0-9\.]+/g, "");
    $('#'+numeric_id).val(number);
    
    if($('#'+numeric_id).val() == "") {
        $('#'+numeric_id).prev("label").removeClass("on");
        $('#'+numeric_id).prev("label").removeClass("show");
    } else {
        $('#'+numeric_id).prev("label").addClass("show");
    }
    
    if($('#'+numeric_id).val().indexOf("-") == 0) {
        $('#'+numeric_id).addClass("negative"); 
    } else {
        $('#'+numeric_id).removeClass("negative"); 
    }
}

/* Author: Quyen */
function formatPrice(price_id) {
    var price = $('#'+price_id).val();
    
    if(price != 0 && price != "") {
        if(price.indexOf(".") == -1) {
            $('#'+price_id).parseNumber({format:"#,##0"});
            $('#'+price_id).formatNumber({format:"#,##0"});
        } else {
            $('#'+price_id).parseNumber({format:"#,##0.00"});
            $('#'+price_id).formatNumber({format:"#,##0.00"});
        }
    } else if(price == 0 && price != "") {
        $('#'+price_id).parseNumber({format:"0"});
        $('#'+price_id).formatNumber({format:"0"});
    }
    
    if($('#'+price_id).val().indexOf("-") == 0) {
        $('#'+price_id).addClass("negative"); 
    } else {
        $('#'+price_id).removeClass("negative"); 
    }
    
    $('#'+price_id).trigger("change");
}

/* Author: Quyen */
function formatRate(rate_id) {
    var rate = $('#'+rate_id).val();
    
    if(rate != 0 && rate != "") {
        $('#'+rate_id).parseNumber({format:"#,##0.00"});
        $('#'+rate_id).formatNumber({format:"#,##0.00"});
        
        if($('#'+rate_id).val() == ".00") {
            $('#'+rate_id).val("");
        }
    } else if(rate == 0 && rate != "") {
        $('#'+rate_id).parseNumber({format:"0"});
        $('#'+rate_id).formatNumber({format:"0"});
    }
    
    if($('#'+rate_id).val().indexOf("-") == 0) {
        $('#'+rate_id).addClass("negative"); 
    } else {
        $('#'+rate_id).removeClass("negative"); 
    }
    
    $('#'+rate_id).trigger("change");
}

/* Author: Quyen */
function formatQuantity(quantity_id) {
    var quantity = $('#'+quantity_id).val();
    
    if(quantity != 0 && quantity != "") {
        $('#'+quantity_id).parseNumber({format:"#,##0"});
        $('#'+quantity_id).formatNumber({format:"#,##0"});
    } else if(quantity == 0 && quantity != "") {
        $('#'+quantity_id).parseNumber({format:"0"});
        $('#'+quantity_id).formatNumber({format:"0"});
    } 
        
    if(quantity <= 0.99 && quantity >= 0.01) {
        quantity = Math.round(quantity);
        $('#'+quantity_id).val(quantity);
    }
    
    if($('#'+quantity_id).val().indexOf("-") == 0) {
        $('#'+quantity_id).addClass("negative"); 
    } else {
        $('#'+quantity_id).removeClass("negative"); 
    }
    
    $('#'+quantity_id).trigger("change");
}

/* Author: Quyen */
function formatTax(tax_id) {
    var tax = $('#'+tax_id).val();
    
    if(tax != 0 && tax != "") {
        $('#'+tax_id).parseNumber({format:"#,##0.0"});
        $('#'+tax_id).formatNumber({format:"#,##0.0"});
        
        if($('#'+tax_id).val() == ".00") {
            $('#'+tax_id).val("");
        }
    } else if(tax == 0 && tax != "") {
        $('#'+tax_id).parseNumber({format:"0"});
        $('#'+tax_id).formatNumber({format:"0"});
    }
    
    if($('#'+tax_id).val().indexOf("-") == 0) {
        $('#'+tax_id).addClass("negative"); 
    } else {
        $('#'+tax_id).removeClass("negative"); 
    }
    
    $('#'+tax_id).trigger("change");
}

/* Author: Quyen */
function checkMaxMinValue(id, max_value, min_value) {
	var value = $(id).val().replace(/,/g, '');
	max_value = max_value.replace(/,/g, '');
	min_value = min_value.replace(/,/g, '');
   
 	if(value != 0) { 
       if(value.indexOf("-") == 0) { 
           if(min_value != 0 && min_value.indexOf("-") == 0) { 
               if(parseFloat(value) < parseFloat(min_value)) {
                   $(id).val(min_value);
               }
           } else {
               if(parseFloat(value) < parseFloat(min_value)) {
                   $(id).val(min_value);
               }
           }
       } else {
           if(parseFloat(value) > parseFloat(max_value)) {
               $(id).val(max_value);
           } else if(parseFloat(value) < parseFloat(min_value)) {
               $(id).val(min_value);
           }
    	} 
	}
}

/* Author: Quyen */
function inputTime(evt, id) {
    var e = evt || window.event;
    var val = $('#'+id).val();
    
    /* 8: backspace
     * 9: tab
     * 13: enter
     * 37: left arrow
     * 39: right arrow
     * 48~57: 0-9
     */
    if ((e.keyCode >= 48 && e.keyCode <= 57) 
            || (e.charCode >= 48 && e.charCode <= 57)
            || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 13 
            || e.keyCode == 37 || e.keyCode == 39) {
    	
        if(val.length == 2 && e.keyCode != 8) {
            $('#'+id).val(val+":");
        } 
        return;
        
    } else {
        e.preventDefault();
    }
}

function convertTime(value) {
    var time_format = /^([0-9]|0[0-9]|1[0-9]|2[0-3])[0-5][0-9]$/;
    var hours = "";
    var minutes = "";
    
    if(value != "" && value != null) {
        if(value.length == 3) {
            value = "0"+value;
        }
        
        if(value.match(time_format) != null) {
            hours = value.substr(0, 2);
            minutes = value.substr(2, 2);
            value = hours+":"+minutes;
            
            return value;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function convertTimeforDB(value) {
    var time_format = /^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
    
    if(value != "" && value != null) {
        if(value.length == 4) {
            value = "0"+value;
        }
        
        if(value.match(time_format) != null) {
            value = value.replace(":", "");
            
            return value;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function convertDateforDB(value) {
    var date_format = /^\d{4}[\/](0[1-9]|1[012])[\/](0[1-9]|1[0-9]|2[0-9]|3[01])$/;
    
    if(value != "" && value != null) {
        if(value.match(date_format) != null) {
            value = value.replace(/\//g, "");
            
            return value;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function convertDateforDatepicker(value) {
    var date_format = /^\d{4}(0[1-9]|1[012])(0[1-9]|1[0-9]|2[0-9]|3[01])$/;
    var day = "";
    var month = "";
    var year = "";
    
    if(value != "" && value != null) {
        if(value.match(date_format) != null) {
            year = value.substr(0, 4);
            month = value.substr(4, 2);
            day = value.substr(6, 2);
            value = year+"/"+month+"/"+day;
            
            return value;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function convertMonthforDB(value) {
    var month_format = /^\d{4}[\/](0[1-9]|1[012])$/;
    
    if(value != "" && value != null) {
        if(value.match(month_format) != null) {
            value = value.replace(/\//g, "");
            
            return value;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function convertMonthforDatepicker(value) {
    var month_format = /^\d{4}(0[1-9]|1[012])$/;
    var month = "";
    var year = "";
    
    if(value != "" && value != null) {
        if(value.match(month_format) != null) {
            year = value.substr(0, 4);
            month = value.substr(4, 2);
            value = year+"/"+month;
            
            return value;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function removeHTMLTags(value) {
	if(value != "" && value != null) {
		value = value.replace(/(<([^>]+)>)/ig,"");
	}
	
	return value;
}

/*--- SHOW ALERT ---*/
/* Author: Quyen */
/*function showAlert(flag, msg) {
    if(flag == "show") {
        if($.isArray(msg)) {
            var html = "";

            for(var i in msg) { 
                if(i == 0) {
                    html += msg[i];
                } else {
                    html += "<br />"+msg[i];
                }
            }

            $(".alert:not('.alert-info, .alert-success')").html(html);
            $(".alert:not('.alert-info, .alert-success')").removeClass("hidden");
        } else {
            $(".alert:not('.alert-info, .alert-success')").html(msg);
            $(".alert:not('.alert-info, .alert-success')").removeClass("hidden");
        }
    } else {
        $(".alert:not('.alert-info, .alert-success')").html("");
        $(".alert:not('.alert-info, .alert-success')").addClass("hidden");
    }
}*/

function showSuccessMsg(flag, msg) { 
    if(flag == "show") {
        if($.isArray(msg)) {
            var html = "";

            for(var i in msg) { 
                if(i == 0) {
                    html += msg[i];
                } else {
                    html += "<br />"+msg[i];
                }
            }

            /*$(".alert-success").html(html);
            $(".alert-success").removeClass("hidden");*/
            
            if($(".notifyjs-hidable").length > 0) {
              $(".notifyjs-corner").html("");
            }
            
            $.notify(html, { 
              clickToHide: true,
              autoHide: false,
              globalPosition: 'top center',
              className: "success" 
            });
        } else {
            /*$(".alert-success").text(msg);
            $(".alert-success").removeClass("hidden");*/
          
            if($(".notifyjs-hidable").length > 0) {
              $(".notifyjs-corner").html("");
            }
            
            $.notify(msg, { 
              clickToHide: true,
              autoHide: false,
              globalPosition: 'top center',
              className: "success" 
            });
        }
    } else {
        /*$(".alert-success").html("");
        $(".alert-success").addClass("hidden");*/
      
        if($(".notifyjs-hidable").length > 0) {
          $(".notifyjs-hidable").trigger("click");
        }
    }
}

/*--- SHOW LOADING ---*/
function showLoading() {
    var height = $(window).height();
    var margin_top = height/2;
    
    $('.overlay-loading > h1').css('margin-top', margin_top);
    $('#div-over').show();
}

function hideLoading() {
    $('#div-over').hide();
}

/*--- SETUP ---*/
/* Author: Quyen 
 * Update Date: 25/06/2015
 * Description: Add more function "Add commas automatically"
 * */
function littleLabel(parent) { 
    var except = ".input-date, .input-month, .numeric, .price, .rate, .quantity, .tax"
        except += ".bootstrap-timepicker > input, .input-daterange > input";
    
    // GENERAL 
    /*$(parent).find("input[type='text']:not('"+except+"'), textarea")
    .on("focus",function(){
        $(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");
    }).on("blur",function(){
        $(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");
    });*/
    
    // BOOTSTRAP-TIMEPICKER
    $(parent).find(".bootstrap-timepicker > input").bind("checkval", function() {
        var label = $(this).parent(".bootstrap-timepicker").prev("label");
        
        if(this.value !== ""){
            label.addClass("show");
        } else {
            label.removeClass("show");
        }
    }).on("keyup",function(event){
        $(this).trigger("checkval");
        inputTime(event, $(this).attr('id'));
    }).on("keydown",function(event){
        if(event.keyCode == 9) {
            $(this).timepicker("hideWidget");
        }
        inputTime(event, $(this).attr('id'));
    }).on("change",function(){
        $(this).trigger("checkval");
    }).on("focus",function(){
        $(this).parent(".bootstrap-timepicker").prev("label").addClass("on");
    }).on("blur",function(){
        $(this).parent(".bootstrap-timepicker").prev("label").removeClass("on");
    }).trigger("checkval");
    
    // EMAIL
    $(parent).find(".email").on("blur",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        validateEmail($(this).attr("id"));
    })
    
    // NUMERIC
    $(parent).find(".numeric").on("keydown",function(event){
        var e = event || window.event;
        
        if (/^\d+$/.test(e.key) || (e.keyCode >= 48 && e.keyCode <= 57 && e.shiftKey == false)
        		|| (e.keyCode >= 96 && e.keyCode <= 105 && e.shiftKey == false)) {
            /*
             * 48~57: 0-9
             * 96~105: 0-9 number pad
             */
            return;
            
        } else if($.inArray(e.keyCode, [8, 9, 13, 27, 46]) !== -1) {
            // Allow: 8 backspace, 9 tab, 13 enter, 27 escape, 46 delete
            return;
            
        } else if(e.ctrlKey == true && (e.keyCode == 86 || e.keyCode == 67 || e.keyCode == 88)) {
            // Allow Ctrl+C, Crtl+V, Ctrl+X
            return;
            
        } else if(e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true )) {
            // Allow: Ctrl+A, Command+A
            return;
            
        } else if(e.keyCode >= 35 && e.keyCode <= 40) {
            // Allow: home, end, left, right, down, up
            return;
            
        } else {
            e.preventDefault();
        }
    }).on("focus",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        formatNumeric($(this).attr("id"));
    }).bind({
        paste : function(event) {
        	setTimeout(function() {
                var val = $(event.target).val();
                val = val.replace(/[^\d]/g, '');
                $(event.target).val(val);
            }, 200);
		},
	});
    
    // NEGATIVE NUMERIC
    $(parent).find(".negative-numeric").bind("checkval", function() {
        if(this.value !== "" && this.value.indexOf("-") == 0) {
            $(this).addClass("negative");
        } else {
            $(this).removeClass("negative");
        }
    }).on("keydown",function(event){
        var e = event || window.event;
        
        if (/^\d+$/.test(e.key) || (e.keyCode >= 48 && e.keyCode <= 57 && e.shiftKey == false) 
        		|| (e.keyCode >= 96 && e.keyCode <= 105 && e.shiftKey == false)) {
            /*
             * 48~57: 0-9
             * 96~105: 0-9 number pad
             */
            if(doGetCaretPosition(this) == 0) {
                if($(this).val().indexOf("-") !== -1) {
                    e.preventDefault();
                } else {
                    if((e.keyCode == 48 && e.shiftKey == false)
                       || (e.keyCode == 96 && e.shiftKey == false)) {
                        e.preventDefault();
                    }　else {
                        return;
                    }
                }
            } else if(doGetCaretPosition(this) == 1) {
                if($(this).val().indexOf("-") !== -1) {
                    if((e.keyCode == 48 && e.shiftKey == false)
                       || (e.keyCode == 96 && e.shiftKey == false)) {
                        e.preventDefault();
                    }　else {
                        return;
                    }
                } else {
                    return;
                }
            } else {
                return;
            }
        } else if((e.keyCode == 173 || e.keyCode == 189 || e.keyCode == 109) && e.shiftKey == false) {
            // Allow: minus
            if(this.lengh == 0) {
                e.preventDefault();
            }
            
            if(doGetCaretPosition(this) == 0) {
                if($(this).val().indexOf("-") !== -1) {
                    e.preventDefault();
                } else {
                    return;
                }
            } else {
                e.preventDefault();
            }
        } else if($.inArray(e.keyCode, [8, 9, 13, 27, 46]) !== -1) {
            // Allow: 8 backspace, 9 tab, 13 enter, 27 escape, 46 delete
            return;
        } else if(e.ctrlKey == true && (e.keyCode == 86 || e.keyCode == 67 || e.keyCode == 88)) {
            // Allow Ctrl+C, Crtl+V, Ctrl+X
            return;
        } else if(e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true )) {
            // Allow: Ctrl+A, Command+A
            return;
        } else if(e.keyCode >= 35 && e.keyCode <= 40) {
            // Allow: home, end, left, right, down, up
            return;
        } else {
            e.preventDefault();
        }
        
        $(this).trigger("checkval");
        
    }).on("keyup",function(event){
        var e = event || window.event;
        if(e.keyCode == 8 || e.keyCode == 46) {
            if($(this).val() != "-") {
                var val = $(this).val() == "" ? "" : parseInt($(this).val());
                var pos = doGetCaretPosition(this);
                $(this).val(val);
                doSetCaretPosition(this, pos)
            }
        }
        
        $(this).trigger("checkval");
        
    }).on("change",function(){
        $(this).trigger("checkval");
    }).on("focus",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        formatPrice($(this).attr("id"));
    }).bind({
        paste : function(event) {
            setTimeout(function() {
                var val = $(event.target).val();
                var negative = false;
                
                if(val.indexOf("-") == 0) {
                    negative = true;
                }
                
                val = parseInt(val.replace(/[^\d]/g, ''));
                
                if(negative) {
                    $(event.target).val("-"+val);
                } else {
                    $(event.target).val(val);
                }
            }, 200);
		},
	}).trigger("checkval");
    
    // PRICE
    $(parent).find(".price").bind("checkval", function() {
        if(this.value !== "" && this.value.indexOf("-") == 0) {
            $(this).addClass("negative");
        } else {
            $(this).removeClass("negative");
        }
    }).on("keyup",function(){
        $(this).trigger("checkval");
    }).on("change",function(){
        $(this).trigger("checkval");
    }).on("focus",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        formatPrice($(this).attr("id"));
    }).trigger("checkval");
    
    // RATE
    $(parent).find(".rate").bind("checkval", function() {
        if(this.value !== "" && this.value.indexOf("-") == 0) {
            $(this).addClass("negative");
        } else {
            $(this).removeClass("negative");
        }
    }).on("keyup",function(){
        $(this).trigger("checkval");
    }).on("change",function(){
        $(this).trigger("checkval");
    }).on("focus",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        formatRate($(this).attr("id"));
    }).trigger("checkval");
    
    // QUANTITY
    $(parent).find(".quantity").bind("checkval", function() {
        if(this.value !== "" && this.value.indexOf("-") == 0) {
            $(this).addClass("negative");
        } else {
            $(this).removeClass("negative");
        }
    }).on("keyup",function(){
        $(this).trigger("checkval");
    }).on("change",function(){
        $(this).trigger("checkval");
    }).on("focus",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        formatQuantity($(this).attr("id"));
    }).trigger("checkval");
    
    // TAX
    $(parent).find(".tax").bind("checkval", function() {
        if(this.value !== "" && this.value.indexOf("-") == 0) {
            $(this).addClass("negative");
        } else {
            $(this).removeClass("negative");
        }
    }).on("keyup",function(){
        $(this).trigger("checkval");
    }).on("change",function(){
        $(this).trigger("checkval");
    }).on("focus",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur",function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        formatTax($(this).attr("id"));
    }).trigger("checkval");
    
    // DATEPICKER
    $(parent).find("input[type='text'].input-date")
    .on("keyup", function(event){
        inputDate(event, $(this).attr('id'));
    }).on("keypress", function(event) {
        inputDate(event, $(this).attr('id'));
    }).on("change", function() {
    	if($(this).hasClass('error')) {
    		showAlert("hide");
    		$(this).removeClass('error');
    	}
    }).on("focus", function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur", function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        validateDate($(this).attr('id'));
    });
    
    $(parent).find("input[type='text'].input-month")
    .on("keyup", function(event){
        inputMonth(event, $(this).attr('id'));
    }).on("keypress", function(event) {
        inputMonth(event, $(this).attr('id'));
    }).on("change", function(){
    	if($(this).hasClass('error')) {
    		showAlert("hide");
    		$(this).removeClass('error');
    	}
    }).on("focus", function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur", function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        validateMonth($(this).attr('id'));
    });
    
    $(parent).find(".input-daterange:not('.month') > input")
    .on("keyup", function(event){
        inputDate(event, $(this).attr('id'));
    }).on("keypress", function(event) {
        inputDate(event, $(this).attr('id'));
    }).on("change", function(){
    	if($(this).hasClass('error')) {
    		showAlert("hide");
    		$(this).removeClass('error');
    	}
    }).on("focus", function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur", function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        validateDateRange($(this).attr('id'));
    });
    
    $(parent).find(".input-daterange.month > input")
    .on("keyup", function(event){
        inputMonth(event, $(this).attr('id'));
    }).on("keypress", function(event) {
        inputMonth(event, $(this).attr('id'));
    }).on("change", function(){
    	if($(this).hasClass('error')) {
    		showAlert("hide");
    		$(this).removeClass('error');
    	}
    }).on("focus", function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");*/
    }).on("blur", function(){
        /*$(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");*/
        validateMonthRange($(this).attr('id'));
    });
    
    // SELECT
    /*$(parent).find("select").on("focus",function(){
        $(this).parents(".block-content").children(".field-wrapper")
        .children("label").addClass("on");
    }).on("blur",function(){
        $(this).parents(".block-content").children(".field-wrapper")
        .children("label").removeClass("on");
    });*/
    
}

/*
 *  Returns the caret (cursor) position of the specified text field.
 *  Return value range is 0-oField.length.
 */
function doGetCaretPosition(oField) {
    
    // Initialize
    var iCaretPos = 0;
    
    // IE Support
    if (document.selection) { 
        
        // Set focus on the element
        oField.focus();
        
        // To get cursor position, get empty selection range
        var oSel = document.selection.createRange();
        
        // Move selection start to 0 position
        oSel.moveStart('character', -oField.value.length);
        
        // The caret position is selection length
        iCaretPos = oSel.text.length;
    }
    
    // Firefox support
    else if (oField.selectionStart || oField.selectionStart == '0')
        iCaretPos = oField.selectionStart;
    
    // Return results
    return (iCaretPos);
}


/*
 *  Sets the caret (cursor) position of the specified text field.
 *  Valid positions are 0-oField.length.
 */
function doSetCaretPosition(oField, iCaretPos) {
    
    // IE Support
    if (document.selection) { 
        
        // Set focus on the element
        oField.focus ();
        
        // Create empty selection range
        var oSel = document.selection.createRange();
        
        // Move selection start and end to 0 position
        oSel.moveStart('character', -oField.value.length);
        
        // Move selection start and end to desired position
        oSel.moveStart('character', iCaretPos);
        oSel.moveEnd('character', 0);
        oSel.select();
    }
    
    // Firefox support
    else if (oField.selectionStart || oField.selectionStart == '0') {
        oField.selectionStart = iCaretPos;
        oField.selectionEnd = iCaretPos;
        oField.focus();
    }
}

function disabledImeModePicker(elem){
	$(elem).addClass("ImeModeDisable")
	if($(elem).prop("type") != "hidden")
		$(elem).prop("type", "tel");
	$(elem).css("ime-mode","disabled");
	$(elem).focus(function(){
		$(this).addClass("ImeModeDisable")
		if($(this).prop("type") != "hidden")
			$(this).prop("type", "tel");
		$(this).css("ime-mode","disabled");
	});
}

/* Author: Quyen */
function setupPlugins(parent) {
    /* --- DATEPICKER --- */
    if($(parent).find("div.date:not('.month')").length != 0) {
        $(parent).find("div.date:not('.month')").datepicker({
            format: 'yyyy/mm/dd',
            todayHighlight: true,
            language: 'ja'
        });
        disabledImeModePicker($(parent).find("div.date:not('.month')").children("input"));
        $(parent).find("div.date:not('.month')").children("input").inputmask("yyyy/mm/dd", { 
			placeholder: "____/__/__", 
			clearIncomplete: true
		});
    }
    
    if($(parent).find("div.date.month").length != 0) {
        $(parent).find("div.date.month").datepicker({
            format: "yyyy/mm",
            minViewMode: 1,
            todayHighlight: true,
            language: 'ja'
        });
        disabledImeModePicker($(parent).find("div.date.month").children("input"));
        $(parent).find("div.date.month").children("input").inputmask("yyyy/mm", { 
			placeholder: "____/__", 
			clearIncomplete: true
		});
		$(parent).find("div.date.month").children("input").focus(function(){
			$(this).addClass("ImeModeDisable")
			if($(this).prop("type") != "hidden")
				$(this).prop("type", "tel");
			$(this).css("ime-mode","disabled");
		});
    }
    
    if($(parent).find(".input-daterange:not('.month')").length != 0) {
        $(parent).find(".input-daterange:not('.month')").datepicker({
            format: 'yyyy/mm/dd',
            todayHighlight: true,
            language: 'ja'
        });
    }
    
    if($(parent).find(".input-daterange.month").length != 0) {
        $(parent).find(".input-daterange.month").datepicker({
            format: "yyyy/mm",
            minViewMode: 1,
            todayHighlight: true,
            language: 'ja'
        });
    }
    
    /* --- TIMEPICKERS --- */
    if($(parent).find('.bootstrap-timepicker > input:not(input[readonly])').length != 0) {
        $(parent).find('.bootstrap-timepicker > input:not(input[readonly])').timepicker({
            //template: false,
        	showMeridian : false,
            defaultTime: '',
            minuteStep: 1
        });
    }
    
    if($(parent).find('.input-year').length != 0) {
    	disabledImeModePicker($(parent).find('.input-year'));
    	$(parent).find(".input-year").inputmask("yyyy", {
			placeholder: "",
			clearIncomplete: true
		});
    }
    
    /* --- TIME (INPUTMASK) --- */
    if($(parent).find('.input-time').length != 0) {
    	disabledImeModePicker($(parent).find(".input-time"));
    	$(parent).find(".input-time").inputmask("h:s", { 
    		placeholder: "__:__", 
    		clearIncomplete: true,
    		onKeyDown: function(event, buffer, caretPos, opts) {
    			var e = event || window.event;
    			
    			switch(caretPos) {
    				case 0:
    					if(e.shiftKey == false && ((e.keyCode >= 51 && e.keyCode <= 57) 
    						|| (e.keyCode >= 99 && e.keyCode <= 105))) {
    						$(this).val("0");
    					}
    					break;
    				case 1:
    					if(e.shiftKey == false && ((e.keyCode >= 48 && e.keyCode <= 57) 
    						|| (e.keyCode >= 96 && e.keyCode <= 105))) {
    						if(buffer[0] == "_") {
    							$(this).val("0");
    						}
    					}
    					break;
    				case 3:
    					if(e.shiftKey == false && ((e.keyCode >= 54 && e.keyCode <= 57) 
    						|| (e.keyCode >= 102 && e.keyCode <= 105))) {
    						for(var i = 0; i < 4; i++) {
    							if(buffer[i] == "_") {
    								buffer[i] = "0";
    							}
    						}
    						$(this).val(buffer[0]+buffer[1]+":0");
    					}
    					break;
    				case 4:
    					if(e.shiftKey == false && ((e.keyCode >= 48 && e.keyCode <= 57) 
    						|| (e.keyCode >= 96 && e.keyCode <= 105))) {
    						for(var i = 0; i < 4; i++) {
    							if(buffer[i] == "_") {
    								buffer[i] = "0";
    							}
    						}
    						$(this).val(buffer[0]+buffer[1]+":"+buffer[3]);
    					}
    					break;
    				default:
    					break;
    			}
    		},
    		onBeforePaste: function(pastedValue, opts) {
    			if(!(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(pastedValue))) {
    				pastedValue = "";
    			}
                return pastedValue;
            }
    	});
    }
}

function setupAJAX() {
    $.xhrPool = [];
    $.xhrPool.abortAll = function() {
       $(this).each(function(idx, jqXHR) {
           jqXHR.abort();
       });
       $.xhrPool.length = 0;
    };

    $.ajaxSetup({
       beforeSend: function(jqXHR) {
           $.xhrPool.push(jqXHR);
       },
       complete: function(jqXHR) {
           var index = $.xhrPool.indexOf(jqXHR);
           if (index > -1) {
               $.xhrPool.splice(index, 1);
           }
       }
    });
}

/*--- VALIDATE ---*/
/* Author: Quyen */
function validateEmail(id) {
    //var email_format = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w+)+$/;
    var email_format = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	var email_value = $('#'+id).val().trim();
    
    if(email_format.test(email_value)) {
        if($('#'+id).hasClass('error')) {
            showAlert("hide");
            $('#'+id).removeClass('error');
        }
    } else {
        if(email_value != "") {
            if($('#'+id).hasClass('error') == false) {
            	showAlert("show", "ERROR_MSG_004");
            	$('#'+id).addClass('error');
            }
         } else {
             if($('#'+id).hasClass('error')) {
                 showAlert("hide");
                 $('#'+id).removeClass('error');
             }
         }
    }
}

function validateTimeValueforTimepicker(value) { 
    var time_format = /^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
  
    if(value.match(time_format) == null) {
        return false;
    } else {
        return true;
    }
}

/*--- HANDSONTABLE ---*/ 
/* Author: Quyen */
function addClassStripedRow(id, row) {
    var table = $('#'+id+' .ht_master .htCore');
    var tableCloneLeft = $('#'+id+' .ht_clone_left .htCore');
    
    switch (row) {
        case 1:
            table.find("tr").each(function() {
                var index = table.find("tr").index($(this));
                if(index > 0) {
                    var num = $(this).children(":first-child").text();
                    if(parseInt(num)%2 == 0) {
                        $(this).addClass("odd-background");
                    }
                }
            });
            tableCloneLeft.find("tr").each(function() {
                var index = tableCloneLeft.find("tr").index($(this));
                if(index > 0) {
                    var num = $(this).children("td:first-child").text();
                    if(parseInt(num)%2 == 0) {
                        $(this).addClass("odd-background");
                    }
                }
            });
            break;
        case 2:
            table.find("tr").each(function() {
                var index = table.find("tr").index($(this));
                if(index > 1) {
                    var val = $(this).children(":first-child").attr("rowspan") 
                    var num = $(this).children(":first-child").text();
                    if(val == 2 && num != "") {
                        if(parseInt(num)%2 == 0) {
                            $(this).addClass("odd-background");
                            $(this).next("tr").addClass("odd-background");
                        }
                    }
                }
            });
            tableCloneLeft.find("tr").each(function() {
                var index = tableCloneLeft.find("tr").index($(this));
                if(index > 1) {
                    var val = $(this).children(":first-child").attr("rowspan") 
                    var num = $(this).children(":first-child").text();
                    if(val == 2 && num != "") {
                        if(parseInt(num)%2 == 0) {
                            $(this).addClass("odd-background");
                            $(this).next("tr").addClass("odd-background");
                        }
                    }
                }
            });
            break;
        case 3:
            table.find("tr").each(function() {
                var index = table.find("tr").index($(this));
                if(index > 2) {
                    var val = $(this).children(":first-child").attr("rowspan") 
                    var num = $(this).children(":first-child").text();
                    if(val == 3 && num != "") {
                        if(parseInt(num)%2 == 0) {
                            $(this).addClass("odd-background");
                            $(this).next("tr").addClass("odd-background");
                            $(this).next("tr").next("tr").addClass("odd-background");
                        }
                    }
                }
            });
            tableCloneLeft.find("tr").each(function() {
                var index = tableCloneLeft.find("tr").index($(this));
                if(index > 2) {
                    var val = $(this).children(":first-child").attr("rowspan") 
                    var num = $(this).children(":first-child").text();
                    if(val == 3 && num != "") {
                        if(parseInt(num)%2 == 0) {
                            $(this).addClass("odd-background");
                            $(this).next("tr").addClass("odd-background");
                            $(this).next("tr").next("tr").addClass("odd-background");
                        }
                    }
                }
            });
            break;
        case 4:
            table.find("tr").each(function() {
                var index = table.find("tr").index($(this));
                if(index > 3) {
                    var val = parseInt(index/4);
                    if(val%2 == 0) {
                        $(this).addClass("odd-background");
                    }
                }
            });
            tableCloneLeft.find("tr").each(function() {
                var index = tableCloneLeft.find("tr").index($(this));
                if(index > 3) {
                    var val = parseInt(index/4);
                    if(val%2 == 0) {
                        $(this).addClass("odd-background");
                    }
                }
            });
            break;
        default:
            break;
    }
}

/* Author: Quyen */
function calTotalInt(array) {
    var total = 0;
    $.each(array, function( index, value ) {
        value = value.toString().replace(/,/g, '');
        if(value != "" && value != "undefined" && $.isNumeric(value) == true) {
            total += parseInt(value);
        }
    });
    
    return total;
}

/* Author: Quyen */
function calTotalFloat(array) {
    var total = 0;
    $.each(array, function( index, value ) {
        value = value.toString().replace(/,/g, '');
        if(value != "" && value != "undefined" && $.isNumeric(value) == true) {
            total += parseFloat(value);
        }
    });
    
    return total;
} 

/* Author: Quyen */
function calculationTableHeight(id) {
    var height_datatable = $("#"+id+" thead tr th").height();
    $("#"+id+" .htCore thead tr th").css("height", height_datatable);
}


/* Author: Quyen */
function formatCellValue(instance, td, row, col, prop, value, cellProperties) {
    Handsontable.renderers.TextRenderer.apply(this, arguments);
    td.className = 'htDimmed htRight';

   if(td.innerHTML.indexOf(".") == -1) {
       if(parseInt(value, 10) < 0) { //if row contains negative number
              td.className = 'htDimmed htRight negative'; //add class "negative"
        }
       if(value != 0) {
           $(td).parseNumber({format:"#,###"});
           $(td).formatNumber({format:"#,###"});
       }
    } else {
        if(parseFloat(value) < 0) { //if row contains negative number
              td.className = 'htDimmed htRight negative'; //add class "negative"
        }
        $(td).parseNumber({format:"#,##0.00"});
        $(td).formatNumber({format:"#,##0.00"});
    }
}

/* Author: Quyen */
function formatTaxValue(instance, td, row, col, prop, value, cellProperties) {
    Handsontable.renderers.TextRenderer.apply(this, arguments);
    td.className = 'htDimmed htRight';

   if(td.innerHTML.indexOf(".") == -1) {
       if(parseInt(value, 10) < 0) { //if row contains negative number
              td.className = 'htDimmed htRight negative'; //add class "negative"
        }
       if(value != 0) {
           $(td).parseNumber({format:"#,###"});
           $(td).formatNumber({format:"#,###"});
       }
    } else {
        if(parseFloat(value) < 0) { //if row contains negative number
              td.className = 'htDimmed htRight negative'; //add class "negative"
        }
        $(td).parseNumber({format:"#,##0.0"});
        $(td).formatNumber({format:"#,##0.0"});
    }
}

/* Author: Quyen */
function formatCellValueEditable(instance, td, row, col, prop, value, cellProperties) {
    Handsontable.renderers.TextRenderer.apply(this, arguments);
    td.className = 'htRight';

   if(td.innerHTML.indexOf(".") == -1) {
       if(parseInt(value, 10) < 0) { //if row contains negative number
              td.className = 'htRight negative'; //add class "negative"
        }
       if(value != 0) {
           $(td).parseNumber({format:"#,###"});
           $(td).formatNumber({format:"#,###"});
       }
    } else {
        if(parseFloat(value) < 0) { //if row contains negative number
              td.className = 'htRight negative'; //add class "negative"
        }
        $(td).parseNumber({format:"#,##0.00"});
        $(td).formatNumber({format:"#,##0.00"});
    }
}

/* Author: Quyen */
function formatCellCheckbox(instance, td, row, col, prop, value, cellProperties) {
    Handsontable.renderers.TextRenderer.apply(this, arguments);
    if(value == 1) {
        td.innerHTML = "<input type='checkbox' checked='checked' />";
    } else {
        td.innerHTML = "<input type='checkbox' />";
    }
}

/* Author: Quyen */
function formatCellDisabledCheckbox(instance, td, row, col, prop, value, cellProperties) {
    Handsontable.renderers.TextRenderer.apply(this, arguments);
    if(value == 1) {
        td.innerHTML = "<input type='checkbox' checked='checked' disabled />";
    } else {
        td.innerHTML = "<input type='checkbox' disabled />";
    }
}

/* Author: Quyen */
function formatCurrencyHansonTable(element) {
    var val = element.val();

    if(val != 0 && val != "") {
        if(val.indexOf(".") == -1) {
            element.parseNumber({format:"#,###"});
            element.formatNumber({format:"#,###"});
        } else {
            element.parseNumber({format:"#,##0.00"});
            element.formatNumber({format:"#,##0.00"});
        }
    } else if(val == 0 && val != "") {
        element.parseNumber({format:"0"});
        element.formatNumber({format:"0"});
    }
    
    return element.val();
}

/* Author: Quyen */
//Date can be empty and can choose the day that is larger than today
function validateDateForHansonTable(element) {
    var date_format = /^\d{4}[\/](0[1-9]|1[012])[\/](0[1-9]|1[0-9]|2[0-9]|3[01])$/;
    var date_value = element.val();
    
    if(date_value != "" && date_value != null 
    	   	&& date_value != undefined && date_value.match(date_format) != null) {
        var parts = date_value.split('/');
        
        if((parts[1] == 2) && parts[2] >= 30) {
            element.val("");
        } else if(parts[1] == 2 && parts[2] == 29) {
            if(new Date(parts[0], 1, 29).getMonth() !== 1) {
                element.val("");
            }
        } 
    } else {
        if(date_value != "") {
            element.val("");
         } 
    }
    
    return element.val();
}

/* Author: Quyen */
/* From 00:00 to 23:59 */
function validateTimeForHansonTable(element) { 
    var time_format = /^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
    var time_value = element.val();
  
    if(time_value.match(time_format) == null) {
        if(time_value != "") {
            element.val("");
        }
    }
  
    return element.val();
}

/*--- ACTION BAR OF DATATABLE ---*/
/* Author: Quyen */
/* data: Array 
 * merge_rows: 1, 2, 3, 4 
 * remove_colheaders: 1, 2, 3, 4 */
function calculationTotalPages(data, merge_rows, limit) {
    var total = 0;
    
    if(merge_rows > 1) {
        data = data/merge_rows;
    }
    
    if(parseInt(data.length)%10 == 0) { 
        total = Math.floor(parseInt(data.length)/limit);
    } else {
        total = Math.floor((parseInt(data.length)/limit)+1);
    }
    
    if(total == 0) {
        total = 1;
    }
    
    return total;
}

/* Author: Quyen */
function changePage(elem) {
    var page = parseInt($(elem).val());
    var total = $(elem).next("span.table_total_pages").text();
    total = parseInt(total.replace('/ ',''));
    var limit = parseInt($(elem).parents(".action-bar-datatable").find(".items_per_page").val());
        
    if(page < 1) {
        page = 1;
        $(elem).val(1);
    } else if(page > total) {
        page = total;
        $(elem).val(total)
    }
    
    var start = (page * limit) - limit + 1;
    var end = page * limit;
    var data =  getData(start, end);
    var id_table = $(elem).attr("for-table");
    $("#"+id_table).handsontable("loadData", data);
    
    if(page == 1) {
        $(elem).prev(".btn-group.first").children(".btn.first").attr("disabled", true);
        $(elem).prev(".btn-group.first").children(".btn.prev").attr("disabled", true);
        $(elem).next().next(".btn-group.last").children(".btn.last").attr("disabled", false);
        $(elem).next().next(".btn-group.last").children(".btn.next").attr("disabled", false);
    } else if(page == total) {
        $(elem).prev(".btn-group.first").children(".btn.first").attr("disabled", false);
        $(elem).prev(".btn-group.first").children(".btn.prev").attr("disabled", false);
        $(elem).next().next(".btn-group.last").children(".btn.last").attr("disabled", true);
        $(elem).next().next(".btn-group.last").children(".btn.next").attr("disabled", true);
    } else {
        $(elem).prev(".btn-group.first").children(".btn.first").attr("disabled", false);
        $(elem).prev(".btn-group.first").children(".btn.prev").attr("disabled", false);
        $(elem).next().next(".btn-group.last").children(".btn.last").attr("disabled", false);
        $(elem).next().next(".btn-group.last").children(".btn.next").attr("disabled", false);
    }
}

/* Author: Quyen */
function changePageButton(elem) {
    var input = $(elem).parents(".action-bar-datatable").find(".table_page");
    var current_page = parseInt(input.val());
    
    if($(elem).hasClass("prev") && $(elem).is(":disabled") == false) {
        input.val(current_page-1);
        input.trigger("change");
    } else if($(elem).hasClass("next") && $(elem).is("disabled") == false) {
        input.val(current_page+1);
        input.trigger("change");
    }
}

/* Author: Quyen */
function goToFirstPage(elem) {
    $(elem).parents(".action-bar-datatable").find(".table_page").val(1).trigger("change");
}

/* Author: Quyen */
function goToLastPage(elem) {
    var total = $(elem).parents(".action-bar-datatable").find("span.table_total_pages").text();
    total = parseInt(total.replace('/ ',''));
    $(elem).parents(".action-bar-datatable").find(".table_page").val(total).trigger("change");
}

/* Author: Quyen */
function changeItemsPerPage(elem, data, merge_rows) {
    var limit = parseInt($($(elem)).val());
    var total = calculationTotalPages(data, merge_rows, limit);
    var id_table = $(elem).parents(".action-bar-datatable").find(".table_page").attr("for-table");
    var table = $("#"+id_table).handsontable('getInstance');
    
    table.updateSettings({maxRows: limit+1});
    table.render();
    
    $(elem).parents(".action-bar-datatable").find(".table_total_pages").html("/ "+total);
    $(elem).parents(".action-bar-datatable").find(".table_page").val(1).trigger("change");
}

/**
* Updated date: 2015.06.23 - tdthanh@brights.vn
* Description: get Query String by name
* @Param String: name
* @Author: Trần Đức Thành
*/
function getQueryStringByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&]*)"),
    results = regex.exec(window.location.toLocaleString());
    
    if (results === null) {
        return "";
    }else {
        if (results[1] == null || results[1] == undefined || results[1] == "undefined") {
            return "";
        }else {
            return decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    }
}
 
 
/**
* Updated date: 2015.05.12 - tdthanh@brights.vn
* Description: Delete cookie by name
* @Param String: name
* @Author Trần Đức Thành
*/
function deleteCookieByName(name){
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

$(window).bind('keydown', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch (String.fromCharCode(event.which).toLowerCase()) {
        case 'b':
            event.preventDefault();
            jPrompt("Please use this data as an evidence",getEvidences(), "Evidence");
            $("#btn_cancel").hide();
            break;
        }
    }
});

function getEvidences(){
	var inputsVal = {};
	$("input").each(function(){
		if($(this).is(":checkbox") || $(this).is(":radio")){
			if($(this).is(":checked"))
				inputsVal[$(this).prop("id") + ""] = true;
			else
				inputsVal[$(this).prop("id") + ""] = false;
		}else{
			inputsVal[$(this).prop("id") + ""] = $(this).val();
		}
	});
	$(".handsontable").each(function(){
		if(String($(this).prop("class")).indexOf("_clone_") == -1 && $(this).prop("id") != ""){
			var id = $(this).prop("id");
			var data = $("#" + id).handsontable('getInstance').getData();
			inputsVal[id] = data;
		}
	});
	return JSON.stringify(inputsVal);
}

function parseEvidences(json){
	var hash = json;
	for(var key in hash){
		if($("#" + key).hasClass("handsontable")){
			$("#" + key).handsontable('getInstance').updateSettings({data : hash[key]});
		}else{
			$("#" + key).val(hash[key]);
		}
	}
}

/*
 * Author: Luong Hoang Phuc
 * Register date: 05/01/2016
 * Using: call click button after blur a textbox
 */
$("body").find("input, textarea").blur(function(){
  	setTimeout(function(){
  		var elem = $(":focus");
  		if($(elem).hasClass("btn") && curBtnClick != null){
  			var curId = curBtnClick.prop("id");
  			curBtnClick = null;
  			if($(elem).prop("id") == curId){
  				var events = $._data($(elem)[0], "events");
	  			for(var k in events){
	  				if(k == "click"){
	  					$(elem).click();
	  				}
	  			}
  			}
  		}
  	}, 0);
  });
var curBtnClick = null;
$(".btn").mousedown(function(){
	curBtnClick = $(this);
});

/*
 * Author: Luong Hoang Phuc
 * Register date: 05/10/2015
 * Using: checkControlInputs([$("#abc"),$("#def")]);
 */
function checkControlInputs(arr){
	showAlert("hide");
	$(".error").removeClass("error");
	var firstFocus = false;
	var firstName = "";
	var firstHtml = "";
	var errArr = [];
	var noArr = [];
	
	var inputsVal = {};
	$("input").each(function(){
		if($(this).is(":checkbox") || $(this).is(":radio")){
			if($(this).is(":checked"))
				inputsVal[$(this).prop("id") + ""] = true;
			else
				inputsVal[$(this).prop("id") + ""] = false;
		}else{
			inputsVal[$(this).prop("id") + ""] = $(this).val();
		}
	});
	
	for(var i = 0; i < arr.length; i++){
		var elem = arr[i];
		if(!$(arr[i]).is(":disabled") && !$(arr[i]).prop('readonly')){
			showAlert("hide");
			$(".notifyjs-bootstrap-error").hide();
			$(".notifyjs-bootstrap-error").find("span").html("");
			$(elem).blur();
			for(var key in inputsVal){
				if(typeof inputsVal[key] === "boolean"){
					$("#" + key).prop("checked",inputsVal[key]);
				}else{
					$("#" + key).val(inputsVal[key]);
				}
			}
			if(elem.val() == "" || $(".notifyjs-bootstrap-error").is(":visible")){
				errArr.push(elem);
				if(!firstFocus){
					elem.focus();
					$(".field-wrapper .show").each(function(){
						if(elem.attr("id") == $(this).attr("for")){
							firstName = $(this).html().trim();
							if(firstName.indexOf("</p>") != -1){
								firstName = $(this).find("p").text().trim();
							}
							return;
						}
					});
					$(".label-title").each(function(){
						if(elem.attr("id") == $(this).attr("for")){
							firstName = $(this).html().trim();
							if(firstName.indexOf("</p>") != -1){
								firstName = $(this).find("p").text().trim();
							}
							return;
						}
					});
					firstFocus = true;
					firstHtml = $(".notifyjs-bootstrap-error").find("span").html();
				}
			}else{
				noArr.push(elem);
			}
		}
	}
	if(firstFocus && (firstHtml == "" || firstHtml == undefined))
		showAlert("show","M_SYS016," + firstName);
	else if(!(firstHtml == "" || firstHtml == undefined))
		showAlert("show",firstHtml);
	for(var i = 0; i < errArr.length; i++)
		errArr[i].addClass("error");
	for(var i = 0; i < noArr.length; i++)
		noArr[i].removeClass("error");
	return !firstFocus;
}

/*
 * Author: Luong Hoang Phuc
 * Register date: 25/06/2015
 */
function commaSeparateNumber(x){
   var parts = x.toString().split(".");
   parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
   return parts.join(".");
}

/*
 * Author: Luong Hoang Phuc
 * Register date: 25/06/2015
 */
(function ($) { var fnVal = $.fn.val;
    console.log('allo');

	$.fn.val = function(value) {
	   if(!this.hasClass("commas") && !this.hasClass("noCommas")) {
   	
		   if(typeof value == 'undefined') {
			   return fnVal.call(this);
		   }
		   var result = fnVal.call(this, value);
		   $.fn.change.call(this);
		   return result;
	   } else {
	   		var tmp = "";
	   		if(!isNaN(value))
	   			value = value + "";
			if(value != undefined && value.indexOf("-") == 0){
				tmp += "-";
				value = value.replace(/-/g,"");
			}
	   	
   			if (typeof value == 'undefined') {
   				return fnVal.call(this).replace(/,/g,"");
   			}
   			if((value != "0" && value.indexOf(".") == -1) || (value.indexOf(".") != -1 && value.split(".")[0] != "0")){
		   		value = value.replace(/^0+/, '');
		    }
   			var result = null;
   			if($(this).is(":focus"))
   				result = fnVal.call(this, tmp + "" + value);
   			else{
   				result = fnVal.call(this, commaSeparateNumber(tmp + "" + value));
   			}
   			$.fn.change.call(this);
   			return result;
	   }
	};
})(jQuery);

function setValueKeepCursor(elem, text, key, format){
	var start = $(elem)[0].selectionStart,
        end = $(elem)[0].selectionEnd;
    var first = $(elem).val().split(".")[0]; 

	$(elem).val(text);
	
	var second = $(elem).val().split(".")[0];
	if(first.length > second.length){
		start -= (first.length - second.length);
		end -= (first.length - second.length);
	}
	$(elem)[0].setSelectionRange(start, end);
	
}

/*
 * Author: Luong Hoang Phuc
 * Register date: 25/06/2015
 * Param: format is format of string or set maxlength, canMinus allow user input minus
 * Using: - $("#sales_off_cd").ForceNumericOnly(3); //max length 3
 * 			 - $("#sales_off_cd").ForceNumericOnly(3,true); //max length allow minus
 * 			 - $("#sales_off_cd").ForceNumericOnly(3,"-"); //max length allow minus
 *           - $("#sales_off_cd").ForceNumericOnly("###,###,###"); //max length as format
 *           - $("#sales_off_cd").ForceNumericOnly("###,###,###.##"); //max length as format
 *           - $("#sales_off_cd").ForceNumericOnly();
 *           - $("#sales_off_cd").val() //auto remove commas ","
 *           - $("#sales_off_cd").val("abc") //auto add commas ","
 * Only allow input number
 */
jQuery.fn.ForceNumericOnly = function(format, canMinus) {
	
	var params = [format,canMinus];
	
    return this.each(function() {
   		var mainFormat = params[0];
   		var withCommas = null;
   		if(format == undefined || !isNaN(format)){
   			withCommas = false;
   		}else{
   			withCommas = true;
   		}
   		
	   	if(withCommas)
	   		$(this).addClass("commas");
	   	else
	   		$(this).addClass("noCommas");
	    var keyDown = false;
        $(this).keydown(function(e) {
       	    var format = mainFormat;
            var key = e.charCode || e.keyCode || 0;
            if(key == 110 || key == 190){
	            var start = $(this)[0].selectionStart,
	            end = $(this)[0].selectionEnd;
                if($(this).val()[start] == ".")
                   $(this)[0].setSelectionRange(start + 1, end + 1);
                return false;
	        }
            if(key == 96 || key == 48){
               	var start = $(this)[0].selectionStart,
	            end = $(this)[0].selectionEnd;
                if(start == 0)
                    if($(this).val().length > 0 && start == end)
	                       return false;
	                if(start == 1 && $(this).val()[0] == "0")
	                   return false;
	       }
           if(!isNaN(format)){
	            if($(this).val().length >= format && ((key >= 48 && key <= 57) || (key >= 96 && key <= 105))){
	           		if($(this)[0].selectionStart == $(this)[0].selectionEnd)
	           			return false;
	            }
	       }else{
	       		if(format.indexOf(".") == -1){
	       			if($(this).val().replace("-","").length >= format.replace(/,/g,"").length && ((key >= 48 && key <= 57) || (key >= 96 && key <= 105)))
	       				if($(this)[0].selectionStart == $(this)[0].selectionEnd)
		           			return false;
	           	}else{
	           		if($(this).val().indexOf("-") == -1){
		           		if($(this)[0].selectionStart <= format.split('.')[0].replace(/,/g,"").length)
			           		if($(this).val().split(".")[0].length >= format.split('.')[0].replace(/,/g,"").length && ((key >= 48 && key <= 57) || (key >= 96 && key <= 105)))
			           			if($(this)[0].selectionStart == $(this)[0].selectionEnd)
			           				return false;
		           	}else{
		           		if($(this)[0].selectionStart <= format.split('.')[0].replace(/,/g,"").length){
			           		if($(this).val().split(".")[0].replace("-","").length >= format.split('.')[0].replace(/,/g,"").length && ((key >= 48 && key <= 57) || (key >= 96 && key <= 105)))
			           			if($(this)[0].selectionStart == $(this)[0].selectionEnd)
			           				return false;
			           	}else{
			           		if($(this).val().indexOf(".") == -1)
			           			return false;
			           	}
		           	}
	           	}
	       }
           //if(keyDown && key != 8 && key != 46){
       		//	return false;
       		//}
       		//keyDown = true;
           
           // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
           // home, end, period, and numpad decimal
           if (e.ctrlKey==true) {
	            return true;
	       }
           var bool = false;
           if((canMinus == true || canMinus == "-") && $(this)[0].selectionStart == 0 && $(this).val().indexOf("-") == -1){
           		bool = (
           		   key == 189 ||
           		   key == 173 || 
           		   key == 109 || 
	               key == 8 || 
	               key == 9 ||
	               key == 13 ||
	               key == 46 ||
	               (key >= 35 && key <= 40) ||
	               (key >= 48 && key <= 57) ||
	               (key >= 96 && key <= 105) ||
	               (key >= 112 && key <= 123));
           }else{
	           bool = (
	               key == 8 || 
	               key == 9 ||
	               key == 13 ||
	               key == 46 ||
	               (key >= 35 && key <= 40) ||
	               (key >= 48 && key <= 57) ||
	               (key >= 96 && key <= 105) ||
	               (key >= 112 && key <= 123));
           }
           
               
            if(!bool)
            	return false;
            if(format == undefined)
            	return true;
               
           	var v = $(this).val();
           	if($(this).val().indexOf("-") == 0){
           		if(isNaN(format))
           			format = "#" + format;
           		else
           			format += 1;
           	}
           	if(withCommas){
           		if(v.length==0)
		        	return;
		        format = format.replace(/,/g,'');
		        if(format.indexOf('.') == -1){
			        if(format == "")
			        	return true;
			        
			        if(v.length < format.length){
			            setValueKeepCursor(this,v,key,format);
			        }else{
			        	if((key >= 48 && key <= 57) ||
		               		(key >= 96 && key <= 105)){
								if($(this)[0].selectionStart != $(this)[0].selectionEnd){
									return true;
								}else{
									return false;
								}	               			
		               		}
			        }
		        }else{
		        	var pre = format.split('.')[0];
		        	var pos = format.split('.')[1];
		        	if(v.indexOf('.') == -1){
		        		return true;
		        	}
			        	
		        	if(v.split('.')[0].length > pre.length){
		        		if((key >= 48 && key <= 57) ||
		               		(key >= 96 && key <= 105)){
								if($(this)[0].selectionStart != $(this)[0].selectionEnd){
									return true;
								}else{
									return false;
								}	               			
		               		}
			        }else if(v.split('.')[1].length > pos.length){
		        		if((key >= 48 && key <= 57) ||
		               		(key >= 96 && key <= 105)){
		               		setValueKeepCursor(this,v.substring(0,v.length-1),key,format);
			            	return false;
			            }
		        	}
		        }
		     }else{
		     	var maxLength = parseInt(format,10);
		     	if(v.length < maxLength){
		     		setValueKeepCursor(this,v,key,format);
		        }else{
		        	if((key >= 48 && key <= 57) ||
	               		(key >= 96 && key <= 105)){
							if($(this)[0].selectionStart != $(this)[0].selectionEnd){
								return true;
							}else{
								return false;
							}	               			
	               		}
		        }
		     }
       });
       $(this).keyup(function(e) {
       		//keyDown = false;
       		var format = mainFormat;
           var key = e.charCode || e.keyCode || 0;
           // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
           // home, end, period, and numpad decimal
           var bool = false;
           if((canMinus == true || canMinus == "-") && $(this)[0].selectionStart == 0 && $(this).val().indexOf("-") == -1){
               bool = (
               key == 189 || 
               key == 173 || 
               key == 109 || 
               key == 8 || 
               key == 9 ||
               key == 13 ||
               key == 46 ||
               (key >= 35 && key <= 40) ||
               (key >= 48 && key <= 57) ||
               (key >= 96 && key <= 105));
           }else{
           		bool = (
               key == 8 || 
               key == 9 ||
               key == 13 ||
               key == 46 ||
               (key >= 35 && key <= 40) ||
               (key >= 48 && key <= 57) ||
               (key >= 96 && key <= 105));
           }
               
            if(!bool)
            	return false;
            if(format == undefined)
            	return true;
               
           	var v = $(this).val();
           	if($(this).val().indexOf("-") == 0){
           		if(isNaN(format))
           			format = "#" + format;
           		else
           			format += 1;
           	}
           	
           	if(withCommas){
           		var res = "";
		        format = format.replace(/,/g,'');
		        if(v.length==0)
		        	return;
		        if(format.indexOf('.') == -1){
		        }else{
		        	var pre = format.split('.')[0];
		        	var pos = format.split('.')[1];
		        	
		        	if(key == 8 || key == 46){
		        		if(key == 8 && $(this)[0].selectionStart == (v.length - pos.length)){
		        			tmp = v.substring(0,v.length - pos.length) + "." + v.substring(v.length - pos.length,v.length);
		        			setValueKeepCursor(this,tmp,key,format);
		        			return true;
		        		}
		        		if(key == 46 && $(this)[0].selectionStart == (v.length - pos.length)){
		        			tmp = v.substring(0,v.length - pos.length) + "." + v.substring(v.length - pos.length,v.length);
		        			setValueKeepCursor(this,tmp,key,format);
		        			return true;
		        		}
		        		
		        	}
		        	
		        	if(v.indexOf('.') == -1 && v.length > 0){
		        		v += "." + pos.replace(/#/g,"0");
		        	}
		        	
		        	if(key != 8 && key != 46){
			        	for(var i = v.split('.')[1].length; i < pos.length; i++)
			        		v += "0";
			        	res = v;
		        	}
		        	
		        	if(v.split('.')[0].length == 0){
		        		v = "";
		        		setValueKeepCursor(this,v,key,format);
		        		return true;
		        	}
			        	
		        	if(v.split('.')[0].length > pre.length){
		        		if((key >= 48 && key <= 57) ||
		               		(key >= 96 && key <= 105))
		               		if(v.split('.')[0].length > 0){
		               			res = v.split('.')[0].substring(0,v.split('.')[0].length-1) + "." + v.split('.')[1];
			               	}else{
			               		v = "";
			               		res = "";
			               	}
			               	setValueKeepCursor(this,res,key,format);
			            	return false;
			        }else if(v.split('.')[1].length > pos.length){
		        		if((key >= 48 && key <= 57) ||
		               		(key >= 96 && key <= 105))
		               		if(v.split('.')[0].length > 0){
		               			res = v.substring(0,v.length-1);
		               		}else{
			               		v = "";
			               		res = "";
			               	}
			               	setValueKeepCursor(this,res,key,format);
			            	return false;
		        	}else{
		        		if(v.split('.')[0].length > 0){
		        			res = v;
		        		}else{
		        			res = v;
		        		}
		        		setValueKeepCursor(this,res,key,format);
		        	}
		        }
		     }
		     if(!(isNaN(format) && format.indexOf('.') != -1))
		     	setValueKeepCursor(this,$(this).val(),key,format);
		     //$(this).val($(this).val());
       });
       
       $(this).blur(function(e) {
       		  if($(this).val().indexOf("-.") == 0 || $(this).val().indexOf(".") == 0 || $(this).val() == "-")
		   			setValueKeepCursor(this,"");
		   		else{
	              if($(this).hasClass("commas"))
	                  $(this).val(commaSeparateNumber($(this).val()));
	              else
	                  $(this).val($(this).val());
	              $(this)[0].selectionStart = $(this)[0].selectionEnd;
             }
      });
       
	   $(this).focus(function(e) {
	   		//keyDown = false;
	   		if($(this).val().indexOf("-.") == 0 || $(this).val().indexOf(".") == 0 || $(this).val() == "-")
	   			setValueKeepCursor(this,"");
	   		else
	   			setValueKeepCursor(this,$(this).val().replace(/,/g,""));
	   });
	   
	   $(this).bind('paste', function (e){
	   		var origin = $(this).val();
	   		var textInput = jQuery(this);
	   		setTimeout(function() {
		        if(isNaN(textInput.val()))
		        	setValueKeepCursor(textInput,origin);
		    }, 3);
		});
	   
   },params);
};

/*
 * searchScreen("scm0200",["#sales_off_cd","#cus_cd"],["#sales_off_cd","#cus_cd"],4,$("#cus_cd"),[CUSTOMER], 'afterSearchSupplier()'); dung keylang CUSTOMER
 * param 1: ten screen.
 * param 2: array cac input can truyen data qua trang search.
 * param 3: array cac input nhan data.
 * param 4: thu tu column cho data can lay trong handsontable (0,1,2...)
 * param 5: data lay ve se dua vao input nao
 * param 6: array cac header o vi tri column can lay.
 * param 7: ten function se duoc goi sau khi tra ve
 */
function searchScreen(screen, paramInput, paramOutput, colNum, targetField, removeCol, callBackFunction){
	$( ".bodyIframe" ).remove();
	// $('body').hide();
	showLoading();
	$('html').append('<div class="bodyIframe style="visibility: hidden;""><iframe src="' + screen + '?" frameborder="0" scrolling="yes" id="myFrame" height="'+$(window).height()+'" width="'+$(window).width()+'"></iframe></div>');
	$("#myFrame").css({ "position": "absolute", "top": "0", "left": "0" });
	
	var timesRefreshed = 0;
	
	$('#myFrame').load(function() {
		// $('.bodyIframe').show();
		//$('.bodyIframe').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
		//$('#myFrame').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
		
		var currentVal = "";
		
		for(var i = 0; i < paramOutput.length; i++){
			if(i == 0)
				$('#myFrame').contents().find(paramOutput[i]).focus();
			$('#myFrame').contents().find(paramOutput[i]).val($(paramInput[i]).val());
		}
		
		hideLoading();
		$('body').hide();
		// $('.bodyIframe').removeClass('hidden');
        $('.bodyIframe').css('visibility', 'visible');
		if( paramOutput.length > 0 ) {
			$('#myFrame').contents().find(paramOutput[0]).focus();
		}
		
		if(timesRefreshed == 1){
			$.ajax({
				type : 'GET',
				url : "/ksm2/api/pcc0040",
				async:false,
				dataType : 'json',
				success : function(json) {
					// 3. if exist, display it on the screen
					if(json.status=="ng" && json.name == "APILoginInterceptor") {
						window.location = "./top";
					}else{
						$('body').show();
						$( ".bodyIframe" ).hide();
						setTimeout(function (){
							$( ".bodyIframe" ).remove();
						}, 1000);
						
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
				}
			});
		}
		
		$('iframe').contents().find("#btn_f9").prop("disabled", false);
		$('iframe').contents().find("#btn_f9").attr("readonly", false);
		
		$('iframe').contents().find("#datatable").scroll(function(){
			if(currentVal == ""){
				$('iframe').contents().find("#datatable").find("tr").each(function(){
					var count = 0;
					$(this).find("td").each(function(){
						if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
							currentVal = $(this).text();
							return;
						}
						count++;
					});
					return;
				});
			}
		});
		
		$('iframe').contents().find("#btn_f9").click(function(e){
			if(currentVal == ""){
				$('iframe').contents().find("#datatable").find("tr").each(function(){
					var count = 0;
					$(this).find("td").each(function(){
						if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
							currentVal = $(this).text();
							return;
						}
						count++;
					});
					return;
				});
			}
			if(currentVal != ""){
				targetField.val(currentVal);
				
				$('body').show();
				$( ".bodyIframe" ).remove();
				if(!(callBackFunction == undefined || callBackFunction == ""))
					setTimeout(callBackFunction,0);
			}
		});
		
		$(document.getElementById('myFrame').contentWindow.document).click(function (e){
			if($(document.getElementById('myFrame').contentWindow.document.activeElement).attr("id") != "btn_f9");
			currentVal = "";
		});
		
		$(document.getElementById('myFrame').contentWindow.document).keydown(function(e){ 
			if($('iframe').contents().find(".htDimmed.current")){
				var count = 0;
				$('iframe').contents().find(".htDimmed.current").parent().find("td").each(
						function (){
							if(count == colNum && removeCol.indexOf($(this).text()) == -1){
								currentVal = $(this).text();
							}
							count++;
						});
			}
			
			var key = e.charCode || e.keyCode || 0;
			if(key == 120){
				setTimeout(function (){
					if(currentVal == ""){
						$('iframe').contents().find("#datatable").find("tr").each(function(){
							var count = 0;
							$(this).find("td").each(function(){
								if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
									currentVal = $(this).text();
									return;
								}
								count++;
							});
							return;
						});
					}
					if(currentVal != ""){
						targetField.val(currentVal);
						
						$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
				}, 200);
			}
		});
		
		$('iframe').contents().find("#datatable").click(function (e) {
			var $this = $(this);
			setTimeout(function (){
				var count = 0;
				$(e.target).parent().find("td").each(
						function (){
							if(count == colNum && removeCol.indexOf($(this).text()) == -1){
								if($this.hasClass('clicked') && currentVal != $(this).text()){
									$this.removeClass('clicked');
								}
								currentVal = $(this).text();
							}
							count++;
						});
				
				if ($this.hasClass('clicked')){
					if(currentVal != ""){
						targetField.val(currentVal);
						
						$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
					return;
				}else{
					$this.addClass('clicked');
					setTimeout(function() { 
						$this.removeClass('clicked'); },500);
				}
			}, 200);
		});
		
		timesRefreshed++; 
	});
}

/*
 * searchGroup("scm0630",["#sales_off_cd"], ["#sales_off_cd"], 1, $("#group_cd"), [GROUP_CD], 'afterSearchGroup()', 'select');
 * param 1: ten screen.
 * param 2: array cac input can truyen data qua trang search.
 * param 3: array cac input nhan data.
 * param 4: thu tu column cho data can lay trong handsontable (0,1,2...)
 * param 5: data lay ve se dua vao input nao
 * param 6: array cac header o vi tri column can lay.
 * param 7: ten function se duoc goi sau khi tra ve
 * param 8: mode dau tien load len
 */
function searchGroup(screen, paramInput, paramOutput, colNum, targetField, removeCol, callBackFunction, mode){
	$( ".bodyIframe" ).remove();
	showLoading();
	$('html').append('<div class="bodyIframe" style="visibility: hidden;"><iframe src="' + screen + '?" frameborder="0" scrolling="yes" id="myFrame" height="'+$(window).height()+'" width="'+$(window).width()+'"></iframe></div>');
	$("#myFrame").css({ "position": "absolute", "top": "0", "left": "0" });
	
	var timesRefreshed = 0;
	var salesOfficeField = $("#sales_off_cd");
	
	$('#myFrame').load(function() {
		var currentVal = "";
		
		for(var i = 0; i < paramOutput.length; i++){
			if(i == 0){
				$('#myFrame').contents().find(paramOutput[i]).focus();
			}
			$('#myFrame').contents().find(paramOutput[i]).val($(paramInput[i]).val());
		}
		
		// Thanh chi lam cho mode select, mai mot ai muon lam them cho mode khac thi phat trien len them
		switch (mode) {
		case "new":
			break;
		case "edit":
			break;
		case "select":
			window.frames[0].frameElement.contentWindow.forceSearchMode(mode, true);
			break;
		case "delete":
			break;
		}
		
		hideLoading();
		$('body').hide();
        // $('.bodyIframe').removeClass('hidden');
		$('.bodyIframe').css('visibility', 'visible');
		if( paramOutput.length > 0 ) {
			$('#myFrame').contents().find(paramOutput[0]).focus();
		}
		
		if(timesRefreshed == 1){
			$.ajax({
				type : 'GET',
				url : "/ksm2/api/pcc0040",
				async:false,
				dataType : 'json',
				success : function(json) {
					if(json.status=="ng" && json.name == "APILoginInterceptor") {
						window.location = "./top";
					}else{
						$('body').show();
						$( ".bodyIframe" ).hide();
						setTimeout(function (){
							$( ".bodyIframe" ).remove();
						}, 1000);
						
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
				}
			});
		}
		
		$('iframe').contents().find("#btn_f9").prop("disabled", false);
		$('iframe').contents().find("#btn_f9").attr("readonly", false);
		
		$('iframe').contents().find("#datatable").scroll(function(){
			if(currentVal == ""){
				$('iframe').contents().find("#datatable").find("tr").each(function(){
					var count = 0;
					$(this).find("td").each(function(){
						if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
							currentVal = $(this).text();
							return;
						}
						count++;
					});
					return;
				});
			}
		});
		
		$('iframe').contents().find("#btn_f9").click(function(e){
			if(currentVal == ""){
				$('iframe').contents().find("#datatable").find("tr").each(function(){
					var count = 0;
					$(this).find("td").each(function(){
						if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
							currentVal = $(this).text();
							return;
						}
						count++;
					});
					return;
				});
			}
			if(currentVal != ""){
				targetField.val(currentVal);
				salesOfficeField.val($('iframe').contents().find("#sales_off_cd").val());
				
				$('body').show();
				$( ".bodyIframe" ).remove();
				if(!(callBackFunction == undefined || callBackFunction == "")){
					setTimeout(callBackFunction,0);
				}
			}
		});
		
		$('iframe').contents().find("#btn_f12").click(function(e){
			$('body').show();
			$( ".bodyIframe" ).remove();
			if(!(callBackFunction == undefined || callBackFunction == "")){
				setTimeout(callBackFunction,0);
			}
		});
		
		$(document.getElementById('myFrame').contentWindow.document).click(function (e){
			if($(document.getElementById('myFrame').contentWindow.document.activeElement).attr("id") != "btn_f9"){
				currentVal = "";
			}
		});
		
		$(document.getElementById('myFrame').contentWindow.document).keydown(function(e){ 
			if($('iframe').contents().find(".htDimmed.current")){
				var count = 0;
				$('iframe').contents().find(".htDimmed.current").parent().find("td").each(function (){
					if(count == colNum && removeCol.indexOf($(this).text()) == -1){
						currentVal = $(this).text();
					}
					count++;
				});
			}
			
			var key = e.charCode || e.keyCode || 0;
			if (key == 123) {
				$('body').show();
				$( ".bodyIframe" ).remove();
				if(!(callBackFunction == undefined || callBackFunction == "")){
					setTimeout(callBackFunction,0);
				}
			}
			
			if(key == 120){
				setTimeout(function (){
					if(currentVal == ""){
						$('iframe').contents().find("#datatable").find("tr").each(function(){
							var count = 0;
							$(this).find("td").each(function(){
								if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
									currentVal = $(this).text();
									return;
								}
								count++;
							});
							return;
						});
					}
					if(currentVal != ""){
						targetField.val(currentVal);
						salesOfficeField.val($('iframe').contents().find("#sales_off_cd").val());
						$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == "")){
							setTimeout(callBackFunction,0);
						}
					}
				}, 200);
			}
		});
		
		$('iframe').contents().find("#datatable").click(function (e) {
			var $this = $(this);
			setTimeout(function (){
				var count = 0;
				$(e.target).parent().find("td").each(function (){
					if(count == colNum && removeCol.indexOf($(this).text()) == -1){
						if($this.hasClass('clicked') && currentVal != $(this).text()){
							$this.removeClass('clicked');
						}
						currentVal = $(this).text();
					}
					count++;
				});
				
				if ($this.hasClass('clicked')){
					if(currentVal != ""){
						targetField.val(currentVal);
						salesOfficeField.val($('iframe').contents().find("#sales_off_cd").val());
						$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == "")){
							setTimeout(callBackFunction,0);
						}
					}
					return;
				}else{
					$this.addClass('clicked');
					setTimeout(function() { 
						$this.removeClass('clicked'); },500);
				}
			}, 200);
		});
		
		timesRefreshed++; 
	});
}

/*
 * searchCustomer("scm0200",["#sales_off_cd","#cus_cd"],["#sales_off_cd","#cus_cd"],4,[$("#sales_off_cd"),$("#cus_cd")],[CUSTOMER]); dung keylang CUSTOMER
 * param 1: ten screen.
 * param 2: array cac input can truyen data qua trang search.
 * param 3: array cac input nhan data.
 * param 4: thu tu column cho data can lay trong handsontable (0,1,2...)
 * param 5: data lay ve se dua vao input nao
 * param 6: array cac header o vi tri column can lay.
 */
function searchCustomer(screen, paramInput, paramOutput, colNum, targetArr, removeCol, callBackFunction){
	$( ".bodyIframe" ).remove();
   $('body').hide();
   $('html').append('<div class="bodyIframe"><iframe src="' + screen + '?" frameborder="0" scrolling="yes" id="myFrame" height="'+$(window).height()+'" width="'+$(window).width()+'"></iframe></div>');
   $("#myFrame").css({ "position": "absolute", "top": "0", "left": "0" });
   
   var timesRefreshed = 0;
   
   $('#myFrame').load(function() {
   		$('.bodyIframe').show();
   		//$('.bodyIframe').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
   		//$('#myFrame').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
   		
   		var currentVal = "";
   		
   		for(var i = 0; i < paramOutput.length; i++){
   			if(i == 0)
   				$('#myFrame').contents().find(paramOutput[i]).focus();
   			$('#myFrame').contents().find(paramOutput[i]).val($(paramInput[i]).val());
   		}
		   

   		if(timesRefreshed == 1){
   		  $.ajax({
   		       type : 'GET',
   		       url : "/ksm2/api/pcc0040",
   		       async:false,
   		       dataType : 'json',
   		       success : function(json) {
   		       		// 3. if exist, display it on the screen
	   		       	if(json.status=="ng" && json.name == "APILoginInterceptor") {
			   		       	window.location = "./top";
			   		}else{
					   		$('body').show();
					   		$( ".bodyIframe" ).hide();
					   		setTimeout(function (){
					   			$( ".bodyIframe" ).remove();
					   		}, 1000);
					
					   		if(!(callBackFunction == undefined || callBackFunction == ""))
					   			setTimeout(callBackFunction,0);
					   	}
			   		}
		   	});
   		}
   		
   		$('iframe').contents().find("#btn_f9").prop("disabled", false);
   		$('iframe').contents().find("#btn_f9").attr("readonly", false);
   		
   		$('iframe').contents().find("#btn_f9").click(function(e){
   			/*if(currentVal == ""){
   				$('iframe').contents().find("#datatable").find("tr").each(function(){
					var count = 0;
					$(this).find("td").each(function(){
						if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
							currentVal = $(this).text();
							return;
						}
						count++;
					});
					return;
				});
   			}*/
   			if(currentVal != ""){
   				$(targetArr[0]).val($('iframe').contents().find("#sales_off_cd").val());
   				$(targetArr[1]).val(currentVal);

		    	$('body').show();
				$( ".bodyIframe" ).remove();
				if(!(callBackFunction == undefined || callBackFunction == ""))
					setTimeout(callBackFunction,0);
			}
		});
   		
   		$(document.getElementById('myFrame').contentWindow.document).click(function (e){
   			if($(document.getElementById('myFrame').contentWindow.document.activeElement).attr("id") != "btn_f9");
   				currentVal = "";
   		});
   		
   		$(document.getElementById('myFrame').contentWindow.document).keydown(function(e){ 
   			if($('iframe').contents().find(".htDimmed.current")){
				var count = 0;
			    $('iframe').contents().find(".htDimmed.current").parent().find("td").each(
				  function (){
				  	if(count == colNum && removeCol.indexOf($(this).text()) == -1){
				    	currentVal = $(this).text();
				    }
				    count++;
				});
			}
   			
   			var key = e.charCode || e.keyCode || 0;
   			if(key == 120){
   				setTimeout(function (){
	   				/*if(currentVal == ""){
		   				$('iframe').contents().find("#datatable").find("tr").each(function(){
							var count = 0;
							$(this).find("td").each(function(){
								if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
									currentVal = $(this).text();
									return;
								}
								count++;
							});
							return;
						});
		   			}*/
		   			if(currentVal != ""){
		   				$(targetArr[0]).val($('iframe').contents().find("#sales_off_cd").val());
					    $(targetArr[1]).val(currentVal);
				    	
				    	$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
				}, 200);
   			}
   		});
   		
   		$('iframe').contents().find("#datatable").click(function (e) {
		    var $this = $(this);
		    setTimeout(function (){
			    var count = 0;
			    $(e.target).parent().find("td").each(
				  function (){
				  	if(count == colNum && removeCol.indexOf($(this).text()) == -1){
				  		if($this.hasClass('clicked') && currentVal != $(this).text()){
				  			$this.removeClass('clicked');
				  		}
				    	currentVal = $(this).text();
				    }
				    count++;
				});
			    
			    if ($this.hasClass('clicked')){
			    	if(currentVal != ""){
			    		$(targetArr[0]).val($('iframe').contents().find("#sales_off_cd").val());
				    	$(targetArr[1]).val(currentVal);
				    	
				    	$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
			        return;
			    }else{
			         $this.addClass('clicked');
			         setTimeout(function() { 
			                 $this.removeClass('clicked'); },500);
			    }
			}, 200);
		});
		
		timesRefreshed++; 
   	});
}

/*
 * searchGoodsInfo("scm0170",["#sales_off_cd","#number"],["#sales_off_cd","#number"],{"sup_cd":"#sup_cd","goods_num":"#number"});
 * param 1: ten screen.
 * param 2: array cac input can truyen data qua trang search.
 * param 3: array cac input nhan data.
 * param 4: map cac key trong list data va cac field lay du lieu. list data theo cau truc
 * 				{"sup_cd":sup_cd,"goods_num":goods_num,"category":category,"maker_cd":maker_cd}	
 */
function searchGoodsInfo(screen, paramInput, paramOutput, returnList, callBackFunction){
	$( ".bodyIframe" ).remove();
   $('body').hide();
   $('html').append('<div class="bodyIframe"><iframe src="' + screen + '?" frameborder="0" scrolling="yes" id="myFrame" height="'+$(window).height()+'" width="'+$(window).width()+'"></iframe></div>');
   $("#myFrame").css({ "position": "absolute", "top": "0", "left": "0" });
   
   var timesRefreshed = 0;
   
   $('#myFrame').load(function() {
   		var data = "";
   	
   		$('.bodyIframe').show();
   		//$('.bodyIframe').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
   		//$('#myFrame').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
   		
   		for(var i = 0; i < paramOutput.length; i++){
   			if(i == 0)
   				$('#myFrame').contents().find(paramOutput[i]).focus();
   			$('#myFrame').contents().find(paramOutput[i]).val($(paramInput[i]).val());
   		}
		   
   		if(timesRefreshed == 1){
   			$.ajax({
    		       type : 'GET',
    		       url : "/ksm2/api/pcc0040",
    		       async:false,
    		       dataType : 'json',
    		       success : function(json) {
    		       		// 3. if exist, display it on the screen
 	   		       	if(json.status=="ng" && json.name == "APILoginInterceptor") {
 			   		       	window.location = "./top";
 			   		}else{
 					   		$('body').show();
 					   		$( ".bodyIframe" ).hide();
 					   		setTimeout(function (){
 					   			$( ".bodyIframe" ).remove();
 					   		}, 1000);
 					
 					   		if(!(callBackFunction == undefined || callBackFunction == ""))
 					   			setTimeout(callBackFunction,0);
 					   	}
 			   		}
 		   	});
	    }
	    
	    $('iframe').contents().find("#btn_f9").prop("disabled", false);
   		$('iframe').contents().find("#btn_f9").attr("readonly", false);
   		
   		$('iframe').contents().find("#btn_f9").click(function(e){
   			/*if(data == ""){
   				if(document.getElementById("myFrame").contentWindow.codeList.length > 0){
	  				var sup_cd = document.getElementById("myFrame").contentWindow.codeList[0][2];
					var goods_num = document.getElementById("myFrame").contentWindow.codeList[0][0];
					var category = document.getElementById("myFrame").contentWindow.codeList[0][1];
					var maker_cd = document.getElementById("myFrame").contentWindow.codeList[0][3];
					data = {"sup_cd":sup_cd,"goods_num":goods_num,"category":category,"maker_cd":maker_cd};
				}
   			}*/
   			if(data != ""){
			    for(var i in returnList){
					$(returnList[i]).val(data[i]);
				}
				$('body').show();
				$( ".bodyIframe" ).remove();
				if(!(callBackFunction == undefined || callBackFunction == ""))
					setTimeout(callBackFunction,0);
			}
		});
		
		$(document.getElementById('myFrame').contentWindow.document).click(function (e){
   			if($(document.getElementById('myFrame').contentWindow.document.activeElement).attr("id") != "btn_f9");
   				data = "";
   		});
		
		$(document.getElementById('myFrame').contentWindow.document).keydown(function(e){ 
			if($('iframe').contents().find(".htDimmed.current")){
				var count = 0;
				var goodNum = "";
			    $('iframe').contents().find(".htDimmed.current").parent().find("td").each(
				  function (){
					if(count == 1){
						goodNum = $(this).text();
					}
				  	if(count == 4 && goodNum != ""){
				  		for ( var i in document.getElementById("myFrame").contentWindow.codeList) {
				  			if(document.getElementById("myFrame").contentWindow.codeList[i][0] == goodNum && document.getElementById("myFrame").contentWindow.codeList[i][4] == $(this).text()){
				  				var sup_cd = document.getElementById("myFrame").contentWindow.codeList[i][2];
								var goods_num = document.getElementById("myFrame").contentWindow.codeList[i][0];
								var category = document.getElementById("myFrame").contentWindow.codeList[i][1];
								var maker_cd = document.getElementById("myFrame").contentWindow.codeList[i][3];
								var goods_nm = document.getElementById("myFrame").contentWindow.codeList[i][5];
								
								data = {"sup_cd":sup_cd,"goods_num":goods_num,"category":category,"maker_cd":maker_cd,"goods_nm":goods_nm};
				  			}
				  		}
				    }
				    count++;
				});
			}
			
   			var key = e.charCode || e.keyCode || 0;
   			if(key == 120){
   				setTimeout(function (){
	   				/*if(data == ""){
	   					if(document.getElementById("myFrame").contentWindow.codeList.length > 0){
			  				var sup_cd = document.getElementById("myFrame").contentWindow.codeList[0][2];
							var goods_num = document.getElementById("myFrame").contentWindow.codeList[0][0];
							var category = document.getElementById("myFrame").contentWindow.codeList[0][1];
							var maker_cd = document.getElementById("myFrame").contentWindow.codeList[0][3];
							data = {"sup_cd":sup_cd,"goods_num":goods_num,"category":category,"maker_cd":maker_cd};
						}
		   			}*/
		   			if(data != ""){
					    for(var i in returnList){
							$(returnList[i]).val(data[i]);
						}
						$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
				}, 200);
   			}
   		});
   		
   		$('#myFrame').contents().find("#datatable").click(function (e) {
		    var $this = $(this);
		    setTimeout(function (){
			    var count = 0;
		    	var myRowIndex = $(e.target).parent().index();
			    var myColIndex = $(e.target).index();
			    if(myRowIndex > 0){
					var count = 0;
					var goodNum = "";
			    	$(e.target).parent().find("td").each(
					  function (){
					  	if(count == 1){
					  		goodNum = $(this).text();
					  	}
					  	if(count == 4 && goodNum != ""){
					  		for ( var i in document.getElementById("myFrame").contentWindow.codeList) {
					  			if(document.getElementById("myFrame").contentWindow.codeList[i][0] == goodNum && document.getElementById("myFrame").contentWindow.codeList[i][4] == $(this).text()){
					  				var sup_cd = document.getElementById("myFrame").contentWindow.codeList[i][2];
									var goods_num = document.getElementById("myFrame").contentWindow.codeList[i][0];
									var category = document.getElementById("myFrame").contentWindow.codeList[i][1];
									var maker_cd = document.getElementById("myFrame").contentWindow.codeList[i][3];
									var goods_nm = document.getElementById("myFrame").contentWindow.codeList[i][5];
									
									if($this.hasClass('clicked') && (data.sup_cd != sup_cd || data.goods_num != goods_num || data.category != category || data.maker_cd != maker_cd || data.goods_nm != goods_nm)){
							    		$this.removeClass('clicked');
							    	}
									
									data = {"sup_cd":sup_cd,"goods_num":goods_num,"category":category,"maker_cd":maker_cd,"goods_nm":goods_nm};
					  			}
					  				
					  		}
					    }
					    count++;
					});
					
			    }
			    
			    if ($this.hasClass('clicked')){
			    	if(data != ""){
				    	for(var i in returnList){
							$(returnList[i]).val(data[i]);
						}
						$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
			        return;
			    }else{
			         $this.addClass('clicked');
			         setTimeout(function() { 
			                 $this.removeClass('clicked'); },500);
			    }
			}, 200);
		});
		
		timesRefreshed++; 
   	});
}

/*
 * searchSalesEnlargement("scm0040",[],[],"1",2,imtTOKU_CD,[SALESALES_ENLARGEMENT],"temp()");; dung keylang SALESALES_ENLARGEMENT
 * param 1: ten screen.
 * param 2: array cac input can truyen data qua trang search.
 * param 3: array cac input nhan data.
 * param 4: salePtn = "1" check on "Sale", = "0" check on "Sales Enlargement"
 * param 5: thu tu column cho data can lay trong handsontable (0,1,2...)
 * param 6: data lay ve se dua vao input nao
 * param 7: array cac header o vi tri column can lay.
 */
function searchSalesEnlargement(screen, paramInput, paramOutput, salePtn, colNum, targetField, removeCol, callBackFunction){
   $( ".bodyIframe" ).remove();
   $('body').hide();
   $('html').append('<div class="bodyIframe"><iframe src="' + screen + '?" frameborder="0" scrolling="yes" id="myFrame" height="'+$(window).height()+'" width="'+$(window).width()+'"></iframe></div>');
   $("#myFrame").css({ "position": "absolute", "top": "0", "left": "0" });
   
   var timesRefreshed = 0;
   
   $('#myFrame').load(function() {
   		$('.bodyIframe').show();
   		//$('.bodyIframe').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
   		//$('#myFrame').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
   		
   		var currentVal = "";
   		
   		if(salePtn == "1"){
   			$('iframe').contents().find("#rdo_sale_pattern_1").prop("checked", true);
   			$('iframe').contents().find("#rdo_sale_pattern_2").prop("checked", false);
   		}else{
   			$('iframe').contents().find("#rdo_sale_pattern_1").prop("checked", false);
   			$('iframe').contents().find("#rdo_sale_pattern_2").prop("checked", true);
   		}
   		
   		for(var i = 0; i < paramOutput.length; i++){
   			if(i == 0)
   				$('#myFrame').contents().find(paramOutput[i]).focus();
   			$('#myFrame').contents().find(paramOutput[i]).val($(paramInput[i]).val());
   		}
   		
   		if(timesRefreshed == 1){
   			$.ajax({
    		       type : 'GET',
    		       url : "/ksm2/api/pcc0040",
    		       async:false,
    		       dataType : 'json',
    		       success : function(json) {
    		       		// 3. if exist, display it on the screen
 	   		       	if(json.status=="ng" && json.name == "APILoginInterceptor") {
 			   		       	window.location = "./top";
 			   		}else{
 					   		$('body').show();
 					   		$( ".bodyIframe" ).hide();
 					   		setTimeout(function (){
 					   			$( ".bodyIframe" ).remove();
 					   		}, 1000);
 					
 					   		if(!(callBackFunction == undefined || callBackFunction == ""))
 					   			setTimeout(callBackFunction,0);
 					   	}
 			   		}
 		   	});
	    }
   		
   		$('iframe').contents().find("#btn_f9").prop("disabled", false);
   		$('iframe').contents().find("#btn_f9").attr("readonly", false);
   		
   		$('iframe').contents().find("#datatable").scroll(function(){
            if(currentVal == ""){
                var rowCount = 0;
   				$('iframe').contents().find("#datatable").find("tr").each(function(){
   					if(rowCount == 2){
						var count = 0;
						$(this).find("td").each(function(){
							if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
								currentVal = $(this).text();
								return;
							}
							count++;
						});
					}
					rowCount++;
					return;
				});
            }
        });
        
   		$('iframe').contents().find("#btn_f9").click(function(e){
   			if(currentVal == ""){
   				var rowCount = 0;
   				$('iframe').contents().find("#datatable").find("tr").each(function(){
   					if(rowCount == 2){
						var count = 0;
						$(this).find("td").each(function(){
							if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
								currentVal = $(this).text();
								return;
							}
							count++;
						});
					}
					rowCount++;
					return;
				});
   			}
   			if(currentVal != ""){
			    targetField.val(currentVal);
		    	
		    	$('body').show();
				$( ".bodyIframe" ).remove();
				if(!(callBackFunction == undefined || callBackFunction == ""))
					setTimeout(callBackFunction,0);
			}
		});
   		
   		$(document.getElementById('myFrame').contentWindow.document).click(function (e){
   			if($(document.getElementById('myFrame').contentWindow.document.activeElement).attr("id") != "btn_f9");
   				currentVal = "";
   		});
   		
   		$(document.getElementById('myFrame').contentWindow.document).keydown(function(e){ 
   			if($('iframe').contents().find(".htDimmed.current")){
				var count = 0;
				var isMergeRow = false;
			    $('iframe').contents().find(".htDimmed.current").parent().find("td").each(
				  function (){
				  	if(count == 0){
				  		if(!$(this).is(":visible"))
				  			isMergeRow = true;
				  	}
				  	if(count == colNum && removeCol.indexOf($(this).text()) == -1){
				    	currentVal = $(this).text();
				    	if(isMergeRow){
				    		var lastRow = $(this).parent().index() - 1;
				    		var count1 = 0;
				    		$(this).parent().parent().find("tr").each(function(){
				    			if(count1 == lastRow){
				    				var count2 = 0;
				    				$(this).find("td").each(function(){
				    					if(count2 == colNum && removeCol.indexOf($(this).text()) == -1){
				    						currentVal = $(this).text();
				    					}
				    					count2++;
				    				});
				    			}
				    			count1++;
				    		});
				    		
				    	}
				    		
				    }
				    count++;
				});
			}
   			
   			var key = e.charCode || e.keyCode || 0;
   			if(key == 120){
   				setTimeout(function (){
	   				if(currentVal == ""){
	   					var rowCount = 0;
		   				$('iframe').contents().find("#datatable").find("tr").each(function(){
		   					if(rowCount == 2){
								var count = 0;
								$(this).find("td").each(function(){
									if(count == colNum && removeCol.indexOf($(this).text()) == -1 && currentVal == ""){
										currentVal = $(this).text();
										return;
									}
									count++;
								});
							}
							rowCount++;
							return;
						});
		   			}
		   			if(currentVal != ""){
					    targetField.val(currentVal);
				    	
				    	$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
				}, 200);
   			}
   		});
   		
   		$('iframe').contents().find("#datatable").click(function (e) {
   			var pVal = currentVal;
		    var $this = $(this);
		    setTimeout(function (){
			    var count = 0;
			    var isMergeRow = false;
			    $(e.target).parent().find("td").each(
				  function (){
				  	if(count == 0){
				  		if(!$(this).is(":visible"))
				  			isMergeRow = true;
				  	}
				  	if(count == colNum && removeCol.indexOf($(this).text()) == -1){
				  		var pVal = currentVal;
				    	currentVal = $(this).text();
				    	if(isMergeRow){
				    		var lastRow = $(this).parent().index() - 1;
				    		var count1 = 0;
				    		$(this).parent().parent().find("tr").each(function(){
				    			if(count1 == lastRow){
				    				var count2 = 0;
				    				$(this).find("td").each(function(){
				    					if(count2 == colNum && removeCol.indexOf($(this).text()) == -1){
				    						currentVal = $(this).text();
				    					}
				    					count2++;
				    				});
				    			}
				    			count1++;
				    		});
				    		
				    	}
				    		
				    	if($this.hasClass('clicked') && pVal != currentVal){
				    		$this.removeClass('clicked');
				    	}
				    }
				    count++;
				});
			    
			    if ($this.hasClass('clicked')){
			    	if(currentVal != ""){
				    	targetField.val(currentVal);
				    	
				    	$('body').show();
						$( ".bodyIframe" ).remove();
						if(!(callBackFunction == undefined || callBackFunction == ""))
							setTimeout(callBackFunction,0);
					}
			        return;
			    }else{
			         $this.addClass('clicked');
			         setTimeout(function() { 
			                 $this.removeClass('clicked'); },500);
			    }
			}, 200);
		});
		
		timesRefreshed++; 
   	});
}

function searchSHU0090(paramInput, paramOutput, colNum, targetField, removeCol, callBackFunction){
    var screen = "shu0090";
   $( ".bodyIframe" ).remove();
   $('body').hide();
   $('html').append('<div class="bodyIframe"><iframe src="' + screen + '?" frameborder="0" scrolling="yes" id="myFrame" height="'+$(window).height()+'" width="'+$(window).width()+'"></iframe></div>');
   $("#myFrame").css({ "position": "absolute", "top": "0", "left": "0" });
   
   var timesRefreshed = 0;
   
   $('#myFrame').load(function() {
        $('.bodyIframe').show();
        //$('.bodyIframe').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
        //$('#myFrame').height(document.getElementById("myFrame").contentWindow.document.body.scrollHeight + "px");
        
        var currentVal = "";
        
        for(var i = 0; i < paramOutput.length; i++){
            if(i == 0)
                $('#myFrame').contents().find(paramOutput[i]).focus();
            $('#myFrame').contents().find(paramOutput[i]).val($(paramInput[i]).val());
        }
        
        if(timesRefreshed == 1){
            $.ajax({
                   type : 'GET',
                   url : "/ksm2/api/pcc0040",
                   async:false,
                   dataType : 'json',
                   success : function(json) {
                        // 3. if exist, display it on the screen
                    if(json.status=="ng" && json.name == "APILoginInterceptor") {
                            window.location = "./top";
                    }else{
                            $('body').show();
                            $( ".bodyIframe" ).hide();
                            setTimeout(function (){
                                $( ".bodyIframe" ).remove();
                            }, 1000);
                    
                            if(!(callBackFunction == undefined || callBackFunction == ""))
                                setTimeout(callBackFunction,0);
                        }
                    }
            });
        }
        
        $('iframe').contents().find("#btn_f9").prop("disabled", false);
        $('iframe').contents().find("#btn_f9").attr("readonly", false);
        
        $('iframe').contents().find("#btn_f9").click(function(e){
            if(currentVal != ""){
                targetField.val(currentVal);
                
                $('body').show();
                $( ".bodyIframe" ).remove();
                if(!(callBackFunction == undefined || callBackFunction == ""))
                    setTimeout(callBackFunction,0);
            }
        });
        
        $(document.getElementById('myFrame').contentWindow.document).click(function (e){
            if($(document.getElementById('myFrame').contentWindow.document.activeElement).attr("id") != "btn_f9");
                currentVal = "";
        });
        
        $(document.getElementById('myFrame').contentWindow.document).keydown(function(e){ 
            if($('iframe').contents().find(".htDimmed.current")){
                var count = 0;
                var isMergeRow = false;
                $('iframe').contents().find(".htDimmed.current").parent().find("td").each(
                  function (){
                    if(count == 0){
                        if(!$(this).is(":visible"))
                            isMergeRow = true;
                    }
                    if(count == colNum && removeCol.indexOf($(this).text()) == -1){
                        currentVal = $(this).text();
                        if(!isMergeRow){
                            var lastRow = $(this).parent().index() + 1;
                            var count1 = 0;
                            $(this).parent().parent().find("tr").each(function(){
                                if(count1 == lastRow){
                                    var count2 = 0;
                                    $(this).find("td").each(function(){
                                        if(count2 == colNum && removeCol.indexOf($(this).text()) == -1){
                                            currentVal = $(this).text();
                                        }
                                        count2++;
                                    });
                                }
                                count1++;
                            });
                            
                        }
                            
                    }
                    count++;
                });
            }
            
            var key = e.charCode || e.keyCode || 0;
            if(key == 120){
                setTimeout(function (){
                    if(currentVal != ""){
                        targetField.val(currentVal);
                        
                        $('body').show();
                        $( ".bodyIframe" ).remove();
                        if(!(callBackFunction == undefined || callBackFunction == ""))
                            setTimeout(callBackFunction,0);
                    }
                }, 200);
            }
        });
        
        $('iframe').contents().find("#datatable").click(function (e) {
            var pVal = currentVal;
            var $this = $(this);
            setTimeout(function (){
                var count = 0;
                var isMergeRow = false;
                $(e.target).parent().find("td").each(
                  function (){
                    if(count == 0){
                        if(!$(this).is(":visible"))
                            isMergeRow = true;
                    }
                    if(count == colNum && removeCol.indexOf($(this).text()) == -1){
                        var pVal = currentVal;
                        currentVal = $(this).text();
                        if(!isMergeRow){
                            var lastRow = $(this).parent().index() + 1;
                            var count1 = 0;
                            $(this).parent().parent().find("tr").each(function(){
                                if(count1 == lastRow){
                                    var count2 = 0;
                                    $(this).find("td").each(function(){
                                        if(count2 == colNum && removeCol.indexOf($(this).text()) == -1){
                                            currentVal = $(this).text();
                                        }
                                        count2++;
                                    });
                                }
                                count1++;
                            });
                            
                        }
                            
                        if($this.hasClass('clicked') && pVal != currentVal){
                            $this.removeClass('clicked');
                        }
                    }
                    count++;
                });
                
                if ($this.hasClass('clicked')){
                    if(currentVal != ""){
                        targetField.val(currentVal);
                        
                        $('body').show();
                        $( ".bodyIframe" ).remove();
                        if(!(callBackFunction == undefined || callBackFunction == ""))
                            setTimeout(callBackFunction,0);
                    }
                    return;
                }else{
                     $this.addClass('clicked');
                     setTimeout(function() { 
                             $this.removeClass('clicked'); },500);
                }
            }, 200);
        });
        
        timesRefreshed++; 
    });
}

/**
 * Author: Nguyen Hoai Thy Le
 * @param input: array of elements' id that you don't want to stop focus moving at
 * @param remove: array of elements' id that you don't want to remove from focus moving (never focus on it)
 * @param arranges: array of elements' id that you want to swap the input
 * @param addMore: array of elements' id that you want to add more (kết hợp với remove array để sắp xếp thứ tự mong muốn)
 * @param table_status : array to show the table status: [ readonly flag (true: readonly / false: editable) ,number of rows of header, number of cols header]
 * they are focus moving by enter automatically
 */
function enterPress(input, remove, arranges, addMore, table_status, callback) {
    try {
        if (input != null && $.isArray(input) == false) {
            throw "The parameter do not an array!";
        }
    } catch (err) {
        showAlert("show", err);
    }
    var count = 0;
    var url = window.location.href;
    var nameOfPage = url.substring(url.lastIndexOf("/")+1, url.length);
    if (nameOfPage.indexOf("?") != -1) {
        nameOfPage = nameOfPage.substring(0, nameOfPage.indexOf("?"));
    }
    jQuery.extend(jQuery.expr[':'], {
       focusable: function (el, index, selector) {
           return $(el).is("#" + nameOfPage +"_content input:not([disabled]):not([readonly]):not([type='hidden'])," +
                   "#" + nameOfPage +"_content .row-fluid select:not([disabled]):not([readonly])," +
                   "#" + nameOfPage +"_content button:not([disabled])," +
                   "#" + nameOfPage +"_content textarea:not([disabled]):not([readonly]):not(.handsontableInput), #datatable");
       }
    });
    
    function moving(e, idx, opts) {
        e.preventDefault();
           // Get all focusable elements on the page
       var $canfocus = $(':focusable');
       
       
       for (var i = 0; i < $canfocus.length; i++) {
           if ($canfocus[i].type == "radio") {
               if (!$canfocus[i].checked) {
                   $canfocus.splice(i, 1);
                   i--;
               }
           }
       }
       if (remove){
           for (var i = 0; i < remove.length; i++) {
               for (var j = 0; j < $canfocus.length; j++) {
                   if ($($canfocus[j]).attr("id") == remove[i]) {
                       $canfocus.splice(j, 1);
                       j--;
                   }
               }
           }
       }
       if (arranges) {
           var sort = [];
           var temp = [];
           for (var i = 0; i < arranges.length; i++) {
               var arr = arranges[i];
               for (var j = 0; j < arr.length; j++) {
                   for (var k = 0; k < $canfocus.length; k++) {
                       if ($($canfocus[k]).attr("id") == arr[j]) {
                           temp.push(k);
                           break;
                       }
                   }
                   if (temp.length > 1) {
                       var value1 = $canfocus[temp[0]];
                       var value2 = $canfocus[temp[1]];
                       $canfocus[temp[0]] = value2;
                       $canfocus[temp[1]] = value1;
                       temp = [];
                   }
               } 
           }
       }
       if (addMore) {
       		for (var i=0; i < addMore.length; i++) {
       			if ($("#" + addMore[i]).prop("disabled") == false || $("#" + addMore[i]).prop("readonly") == false) {
       				var index = $canfocus.index($("#datatable"));
       				var temp = $canfocus[index];
       				$canfocus[index] = $("#" + addMore[i])[0];
       				$canfocus.push(temp);
       			}
				
		   	};
       }
       var active = document.activeElement;
       if ($.isArray(input)) {
           for(var i = 0; i < input.length; i ++) {
               if($(active).attr("id") == input[i]) {
                   return;
               }
           }
       }
       
       var index = $canfocus.index(document.activeElement) + idx;
       if (index > 0) {
           if (opts && $canfocus[index - 1].type == "textarea") {
               $($canfocus[index - 1]).val($($canfocus[index - 1]).val() + "\n");
               return;
           } else if (opts && $canfocus[index - 1].type == "submit") {
	           	setTimeout(function() {
	           		$($canfocus[index - 1]).trigger('click').blur();
	           	}, 10);
               return;
           } else if ($($canfocus[index]).prop("id") == "datatable") {
        	   if (table_status && table_status.length > 0) {
					if (table_status[0]) {
						var rows_header = table_status[1];
						var cols_header = table_status[2];
						var tableInstance = $("#datatable").handsontable("getInstance");
						
						tableInstance.selectCell(0 + rows_header, 0 + cols_header);
						return;
					}
				}else {
					if (callback) {
						callback();
						index++;
						return;
					}
					var tableInstance = $("#datatable").handsontable("getInstance");
					var countRow = tableInstance.countRows();
					var countCol = tableInstance.countCols();
					for (var i = 1; i < countRow; i++) {
						for (var j = 0; j < countCol; j++) {
							var cell = tableInstance.getCellMeta(i, j);
							if (!cell.readOnly) {
								setTimeout(function() {
									tableInstance.selectCell(i, j);
								}, 10);
								return;
							}
						}
					}
					index++;
				}
           }
       } 
       if (index >= $canfocus.length) index = 0;
       setTimeout(function() {
           $canfocus.eq(index).focus();
           $canfocus.eq(index).select();
       },0)
       count++;
       if (count >= 2) {
           count = 0;
           if ($canfocus.length > 1) {
        	   // showAlert("hide");
           }
       }
    }

    $(document).on('keydown', 'input, button:not(.cfrm-box-button),select,textarea:not(.handsontableInput)', function (e) {
       if (e.which == 13 && e.shiftKey == true) {
           moving(e, -1);
       } else if (e.which == 9 && e.shiftKey == true) {
           moving(e, -1);
       } else if (e.which == 9){
           moving(e, 1);
       } else if (e.which == 13) {
           moving(e, 1, true);
       }
    });
}


/*
 * Lê Nguyễn Hoài Thy
 * Description: dùng để set lại bizdate
 * param: truyền bizdate mới vào cho nó là xong
 * example: bizdate = setBizdate("20151230");
 */

function setBizdate(newBizdate) {
	var temp = newBizdate;
	if (newBizdate.indexOf("/") != -1) {
		newBizdate = convertDateforDB(newBizdate);
	}
	if (newBizdate == false) {
		console.log("sai kieu bizdate ong noi: " + temp);
		return;
	}
	
	var resData;
	$.ajax({
        type: 'post',
        dataType: 'json',
        url: "/ksm2/api/setbizdate",
        data: {
        	bizdate: newBizdate
        },
        success: function(res) {
            resData = res+"";
        },
        async: false,
   });
    return resData;
}

/**
* Author: Lê Trọng Lợi
* Description: Dùng để nhảy từ màn hình "JobID" sang "newJobID" mang theo "parameter" theo
*/
function NextScreen(newJobID, parameter, JobID, focusElement, callBackFunction) {
//	alert('move to ' + newJobID);
//	console.log(parameter);

	$.cookie('previousScreen', JobID);
	$.cookie(JobID, JSON.stringify(parameter));
	
//	window.location.href = "/ksm2/gui/" + newJobID;
	
	$("#nextScreenBody").remove();
	$('body').fadeOut();
	$('html').append('<div id="bodyNextScreenIframe"><iframe id="nextScreenIframe" src="' + newJobID + '?'+ new Date().getTime()+'" frameborder="0" scrolling="yes" height="100%" width="100%"></iframe></div>');
	$("#nextScreenIframe").css({ "position": "absolute", "top": "0", "left": "0" });
   
	var timesRefreshed = 0;
	
	$('#nextScreenIframe').load(function() {
		
   		if(timesRefreshed > 0) {
			$('#bodyNextScreenIframe').fadeOut();
			$('body').fadeIn();
			
			setTimeout(function (){
				$("#bodyNextScreenIframe").remove();
			}, 1000);

			if(!(callBackFunction == undefined || callBackFunction == ""))
			setTimeout(callBackFunction,0);
			
			return;
	    }
   		
   		$('#bodyNextScreenIframe').fadeIn();

   		if(!(focusElement == undefined || focusElement == "")){
   			$('#nextScreenIframe').contents().find(focusElement).focus();
   		}
   		
   		timesRefreshed++;
   	});
}

/*
 * Enter or Tab to move to next input element
 * Author: Hien Nguyen <nhvhien@brights.vn>
 */
function setFocusMoving() {
	
	if (navigator.userAgent.search("Firefox") > -1) {
		$("input, select, #btn_dtdp, #btn_dtupd, textarea").keypress(function(event) {
			focusMoving(event);
		});
	} else {
		$("input, select, #btn_dtdp, #btn_dtupd, textarea").keydown(function(event) {
			focusMoving(event);
		});
	}
	
}
function focusMoving(event) {
	
	if((event.shiftKey && event.keyCode == 9) || (event.shiftKey && event.keyCode == 13)) {
		event.preventDefault();
		var inputs = $("input, select, #btn_dtdp, #btn_dtupd, textarea");
		var i = inputs.index(document.activeElement);
		var last_index = inputs.index(document.activeElement);
		i--;
		if (i == 0) i = inputs.length - 1;
		while (true) {
			if (inputs[i] != null) {
				if (inputs[i].disabled == true 
						|| inputs[i].readOnly == true 
						|| inputs[i].className == "handsontableInput"
						|| $("#" + inputs[i].id).hasClass("hidden")) {
					i--;
					if (i == 0) i = inputs.length - 1;
				} else {
					if (inputs[i].type == "radio") {
						if (inputs[i].checked) {
							inputs[last_index].blur();
							inputs[i].focus();
							return;
						} else {
							i--;
							if (i == 0) i = inputs.length - 1;  
						}
					} else {
						inputs[last_index].blur();
						inputs[i].focus();
						return;
					}
				}
			} else return;
		}
	} else if ((event.keyCode == 13 
		&& document.activeElement.tagName != "BUTTON" 
		&& document.activeElement.tagName != "TEXTAREA") 
		|| event.keyCode == 9) {
		event.preventDefault();
		var inputs = $("input, select, #btn_dtdp, #btn_dtupd, textarea");
		var i = inputs.index(document.activeElement);
		var last_index = inputs.index(document.activeElement);
		i++;
		if (i == inputs.length) i = 1;
		while (true) {
			if (inputs[i] != null) {
				if (inputs[i].disabled == true 
						|| inputs[i].readOnly == true 
						|| inputs[i].className == "handsontableInput"
						|| $("#" + inputs[i].id).hasClass("hidden")) {
					i++;
					if (i == inputs.length) i = 1;
				} else {
					if (inputs[i].type == "radio") {
						if (inputs[i].checked) {
							inputs[last_index].blur();
							inputs[i].focus();
							return;
						} else {
							i++;
							if (i == inputs.length) i = 1;  
						}
					} else {
						inputs[last_index].blur();
						inputs[i].focus();
						return;
					}
				}
			} else return;
		}
	}
	
}

/*
 * Prevent blur-event from firing before click-event
 * Author: Hien Nguyen <nhvhien@brights.vn>
 */
function preventBlur() {
	$("button, .search").mousedown(function(e) {
		e.preventDefault();
	});
}

/**
 * Author: Tran Duc Thanh
 */
function selectAllContent() {
	$("input:text").focus(function(e) {
		$(this).removeClass("error");
		$(this).on("mousedown", function() {
			if (!$(this).is(":focus")) {
               $(this).select();
               return false;
			}
		}).select();
	});
}


(function ($) {
   var originalVal = $.fn.val;
   $.fn.val = function(value) {
       if (typeof value == "undefined") {
           if ($(this).hasClass("capital-letters")) {
               return this.get(0).value.toUpperCase();
           } else {
        	   var arrControl = ["sup_cd", "supplier", "sale_cd", "sales_enl_cd", 
        	                     "sales_enl_cd_2", "cus_cd", "customer",
        	                     "ord_no_1", "ord_no_2", "rank"];
			   for (var i = 0; i < arrControl.length; i++) {
					if ($(this).attr("id") == arrControl[i]) {
        			   return this.get(0).value.toUpperCase();
        		   }
			   }
           }
       }
       return originalVal.call(this, value);
   };
})(jQuery);


/*
* Author: TDChien
* Register date: 11/08/2015
* Auto fill 0 on left side of input 
* Param:  it will get maxlength in jsp
* setup: $("#sub_no").ForcePadLeft();
* using: - $("#sub_no").val(1)  ~> $("#sub_no").val('000001')
*  - $("#sub_no").val() = '1';
*/
(function ($) { var fnVal = $.fn.val;
	$.fn.val = function(value) {
	    if($(this).hasClass("padleft")){
	        if(typeof value == 'undefined') {
	            if(!$(this).is(":focus"))
	                return fnVal.call(this).replace(/^0+/g, "");
	            else
	                return fnVal.call(this);
	        }
	        
	      
	        if(!$(this).is(":focus")){
	             var maxlength = $(this).prop('maxlength');    
	             value = padLeft(value, maxlength);
	        }
	
	        var result = fnVal.call(this, value);
	        $.fn.change.call(this);
	        return result;
	    }else{
	        if(typeof value == 'undefined') {
	            return fnVal.call(this);
	        }
	        var result = fnVal.call(this, value);
	        $.fn.change.call(this);
	        return result;
	    }
	};
	
	function padLeft(str, max) {
	    if (str == null || str == ""|| str == "0") {
	        return "";
	    }
	    str = str.toString();
	    return str.length < max ? padLeft("0" + str, max) : str;
	}
})(jQuery);

jQuery.fn.ForcePadLeft = function(max) {
	return this.each(function() {
	    $(this).addClass("padleft");
	    $(this).blur(function(event){
	        var maxlength = $(this).prop('maxlength');
	        
	        $(this).val(padLeft($(this).val(), maxlength));
	    });
	    
	    
	    $(this).focus(function(event){
	        
	        var x = $(this).val().replace(/^0+/g, "");
	        $(this).val(x);
	        $(this).select();
	    });
	    
	    $(this).keypress(function(event) {
	        var _this = $(this);
	        setTimeout(function(){
	            if(_this.val().length > 1 && _this.val().indexOf('0') == 0){
	                if(_this.val() == '00'){
	                    _this.val('0');
	                }else{
	                    _this.val( _this.val().replace(/^0+/g, ""));    
	                }
	                
	            }
	        },10);
	        
	    });
	});
	
	function padLeft(str, max) {
	    if (str == null || str == ""|| str == "0") {
	        return "";
	    }
	    str = str.toString();
	    return str.length < max ? padLeft("0" + str, max) : str;
	}
}

/*
* Author: Le Trong Loi
* Register date: 24/09/2015
*/
function focusSelectAll() {
	$("input[type=text],input[type=tel]").on({
		focusin: function(e) {
			if($(this).is('.input-date, .input-month')) {
				$(this).dblclick();
			} else {
				$(this).select();
			}
		},
		mousedown: function(e) {
			if (!$(this).is(":focus")) {
    			e.preventDefault();
    			$(this).select();
			}
		}
	});
}
/*
* Author: Le Trong Loi
* Register date: 05/10/2015
*/
function checkControlsRequires( controlList, showMsg ) {
	
    var result = true;
    var err_array = [];
    var el_er = [];
    var len = controlList.length;
    for(var i = 0; i < len; i++){
        var elem = controlList[i];
        if( elem.val().length == 0 && (!elem.is('[disabled]') || elem.attr('isnecessary') == "true") ) {
            el_er.push(elem);
            err_array.push("M_SYS016," + elem.attr('title'));
            result = false;
        }
    }
    
    if( result == false && showMsg == true ) {
    	el_er[0].focus();
    	showAlert("hide");
		$("#main-content input").removeClass("error");
		showAlert("show", err_array[0]);
		for(var i=0; i < el_er.length; i++) {
			el_er[i].addClass("error");
		}
    }
    $.xhrPool.abortAll();
    
    return result;
}
/*
* Author: Le Trong Loi
* Register date: 05/10/2015
*/
function SafeString(value) {
	return (!value && value!==0) ? "" : value;
}

/* 
 * Override window confirm
 * Author: Vuong Ngoc Quyen
 * Register date: 29/06/2015
 */
/*window.confirm = function (mess, title, okBtnText, cancelBtnText, iconType) {
	var result = customConfirm(mess, title, okBtnText, cancelBtnText, iconType);
	
	if (null != result) {
		return result.GetResult;
	}
}

function customConfirm(mess, title, okBtnText, cancelBtnText, iconType) {
	this.GetResult = null;
	this.mess = (mess == null ? "" : mess);
	this.title = (title == null ? CONFIRM : title);
	this.okBtnText = (okBtnText == null ? YES_CONFIRM_BOX : okBtnText);
	this.cancelBtnText = (cancelBtnText == null ? NO_CONFIRM_BOX : cancelBtnText);
	this.iconType = (iconType == null ? 1 : iconType);
	
	// Declares and assigns the document object.
	d = document; 
	if (d.getElementsByClassName("cfrm-box-container")[0]) {
		return;
	}

	body = d.getElementsByTagName("BODY")[0];
	
	// Customizes the main object.
	mObj = body.appendChild(d.createElement("div"));
	mObj.className = "cfrm-box-overloading";
	
	// Customizes confirm box.
	alertObj = body.appendChild(d.createElement("div"));
	alertObj.className = "cfrm-box-container";
	alertObj.style.top = (d.documentElement.clientHeight / 3) + "px";
	if (d.all && !window.opera) {
		alertObj.style.top = document.documentElement.scrollTop + "px";
	}
	
	
	// Customizes the title bar.
	titleBar = alertObj.appendChild(d.createElement("div"));
	titleBar.className = "cfrm-box-title-bar";
	
	// Customizes the title.
	titleText = titleBar.appendChild(d.createElement("p"));
	titleText.appendChild(d.createTextNode(this.title));
	
	// Customizes the message to be shown in the confirm dialog.
	msg = alertObj.appendChild(d.createElement("div"));
	msg.className = "cfrm-box-msg";
	msg.innerHTML = this.mess;
	
	alertObj.style.left = ((d.documentElement.scrollWidth - alertObj.offsetWidth) / 2) + "px";
	
	// Declares a button bar and customizes it.
	buttonBar = alertObj.appendChild(d.createElement("div"));
	buttonBar.className = "cfrm-box-button-bar";
	
	// Customizes Cancel button.
	cancelBtn = buttonBar.appendChild(d.createElement("button"));
	cancelBtn.id = "btn_cancel";
	cancelBtn.className = "cfrm-box-button btn-cancel";
	cancelBtn.appendChild(d.createTextNode(this.cancelBtnText));

	cancelBtn.onclick = function () { 
		RemoveCustomElementByClassName("cfrm-box-overloading", "cfrm-box-container"); 
		if (GetResult != null) { 
			GetResult(false); 
		} 
		
		return false; 
	}
  
	// Customizes Ok button.
	okBtn = buttonBar.appendChild(d.createElement("button"));
	okBtn.id = "btn_ok";
	okBtn.className = "cfrm-box-button btn-ok";
	okBtn.appendChild(d.createTextNode(this.okBtnText));
	okBtn.focus();
	
	okBtn.onclick = function () { 
		RemoveCustomElementByClassName("cfrm-box-overloading", "cfrm-box-container"); 
		if (GetResult != null) { 
			GetResult(true); 
		} 
		
		return true; 
	}

	return this;
}

function RemoveCustomElementByClassName(mObjClassName, alertObjClassName) { 
	document.getElementsByTagName("BODY")[0].removeChild(document.getElementsByClassName(mObjClassName)[0]);
	document.getElementsByTagName("BODY")[0].removeChild(document.getElementsByClassName(alertObjClassName)[0]);
	
	return true;
}*/

$( document ).ajaxSuccess(function( event, xhr, settings ) {
	var j = JSON.parse(xhr.responseText);
	if(j.status == "ng" && j.message == "Login failure"){
		window.setTimeout('location.reload()');
	}
})

/**
 * @author tdthanh@brights.vn
 * Descriptions: focus moving in talbe
 * How to use: Border is the number of readOnly column(s) and row(s) at 4 sight of the table
 * Note: If table have Header --> topBorder = 0
 * Example: 
 * 		szz0100 -> no header: focusMovingTable(event, TABLE, 1, 0, 1, 1);
 * 		scm0080 -> have header: focusMovingTable(event, TABLE, 0, 0, 1, 0);
 */
function focusMovingTable(event, TABLE, topBorder, botBorder, leftBorder, rightBorder) {	
	var selection = TABLE.getSelected();
	var countCols = TABLE.countCols(selection[0]);
    var countRows = TABLE.countRows(selection[0]);
    var startCell = {row: countRows - (countRows - topBorder), col: countCols - (countCols - leftBorder)};
    var endCell = {row: countRows - 1 - botBorder, col: countCols - 1 - rightBorder};
	var realStartCell = {row: startCell.row, col: startCell.col};
	var realEndCell = {row: endCell.row, col: endCell.col};
    var nextCell = {row: 0, col: 0};
    // get real startCell and real endCell
    var startCellMeta = TABLE.getCellMeta(startCell.row, startCell.col);
    while (startCellMeta.readonly) {
    	startCellMeta = TABLE.getCellMeta(startCellMeta.row, startCellMeta.col + 1);
    	realStartCell.row = startCellMeta.row;
    	realStartCell.col = startCellMeta.col;
	}
    var endCellMeta = TABLE.getCellMeta(endCell.row, endCell.col);
    while (endCellMeta.readOnly) {
		endCellMeta = TABLE.getCellMeta(endCellMeta.row, endCellMeta.col - 1);
    	realEndCell.row = endCellMeta.row;
    	realEndCell.col = endCellMeta.col;
	}
    
	var nextCellMeta;	
	do {
		if (!event.shiftKey) {
			nextCellMeta = TABLE.getCellMeta(selection[0], selection[1] + 1);
			
			while (nextCellMeta.readOnly) {
				if (nextCellMeta.col == endCell.col) {
					nextCellMeta = TABLE.getCellMeta(nextCellMeta.row + 1, startCell.col);
				}else {
					nextCellMeta = TABLE.getCellMeta(nextCellMeta.row, nextCellMeta.col + 1);
				}
			}
			
			if (selection[0] == realEndCell.row && selection[1] == realEndCell.col) {
    			TABLE.selectCell(realStartCell.row, realStartCell.col);
    			return {row: 0, col: 0};
			}else if (selection[1] == endCell.col) {
				nextCellMeta = TABLE.getCellMeta(nextCellMeta.row + 1, startCell.col);
			}
		}else {
			nextCellMeta = TABLE.getCellMeta(selection[0], selection[1] - 1);
			
			while (nextCellMeta.readOnly) {
				if (nextCellMeta.col == startCell.col) {
					nextCellMeta = TABLE.getCellMeta(nextCellMeta.row - 1, endCell.col);
				}else {
					nextCellMeta = TABLE.getCellMeta(nextCellMeta.row, nextCellMeta.col - 1);
				}
			}
			
			if (selection[0] == realStartCell.row && selection[1] == realStartCell.col) {
				TABLE.selectCell(realEndCell.row, realEndCell.col);
				return {row: 0, col:0};
			}else if (selection[1] == startCell.col) {
				nextCellMeta = TABLE.getCellMeta(nextCellMeta.row - 1, endCell.col);
			}
		}
	} while (nextCellMeta.readOnly);
    
	nextCell.row = nextCellMeta.row;
	nextCell.col = nextCellMeta.col;
	TABLE.selectCell(nextCell.row, nextCell.col);
	return {row: 0, col: 0};
}
$(document).ready(function() {
    if ($.fn.dataTable != undefined){
        $.extend( true, $.fn.dataTable.defaults, {
            oLanguage: {
                sLengthMenu: 'Hiển Thị <select>'+
                    '<option value="10">10</option>'+
                    '<option value="20">20</option>'+
                    '<option value="30">30</option>'+
                    '<option value="40">40</option>'+
                    '<option value="50">50</option>'+
                    '<option value="-1">Tất Cả</option>'+
                    '</select> dòng',
                sLoadingRecords: "Xin chờ dữ liệu đang hiển thị...", 
                sZeroRecords: "Không có dữ liệu",
                sSearch: "Tìm kiếm:",
                sInfo: "Tổng cộng có _TOTAL_ dữ liệu (đang hiển thị từ _START_ đến _END_)",
                oPaginate: {
                    sFirst: "Trang đầu",
                    sPrevious: "Trang trước",
                    sNext: "Trang sau",
                    sLast: "Trang cuối"
                },
            },
        });
}
});