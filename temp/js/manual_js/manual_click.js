/* The plugin will submit form and scroll to top */
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires+"; path=/";
}
function delCookie(cname) {
    document.cookie = cname+"=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
}
(function ($) {
    $.fn.manualSubmit = function (formId) {
        //this.css( "color", "red" );
        this.click(function () {
            if (formId == 'frmCustomer') {
                if ($(".dayPicker").length > 0) {
                    var pDate = $(".dayPicker").val();
                    if (pDate.length > 0) {
                        var splDate = pDate.split("/");
                        if (splDate.length > 0) {
                            sessionStorage.setItem('dayPicker', splDate[0]);
                        }
                    }
                }
                delCookie("area");
                setCookie("area",window.location.hash,2);
            }
            $('#' + formId).submit();
            jQuery('html, body').animate({scrollTop: 0}, 400);
        });
    };

    $.fn.manualRefresh = function (formId) {
        this.click(function () {
            //console.log($('#'+formId+' input:visible'));
            $('#' + formId + ' input:visible').each(function () {
                $(this).val('');
            });

            $('#' + formId + ' textarea').each(function () {
                $(this).val('');
                CKEDITOR.instances[$(this).attr('name')].setData('');

            });
        });
    };
}(jQuery));

/*How to use it*/
/* Put the code below into $(function(){ put the code in here }); or $( document ).ready(function() { put the code in here }); */
/*
 $(seletor).manualSubmit('formId');
 Example :
 $('#buttonSubmitId').manualSubmit('formId');
 */