<!-- header -->
<style>
.navbar-default .navbar-nav > li > a {
    background-color: #027ab7;
    color: #ffffff;
    margin : 5px 5px 5px 0;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
    width: auto;
    padding: 8px 8px 8px 8px;
}

.navbar-default .navbar-nav > li > a:hover, .navbar-default .navbar-nav > li > a:focus {
    background-color: #01437d;
    color: #ffffff;
}

.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:focus, .navbar-default .navbar-nav > .active > a:hover {
    background-color: #01437d;
    color: #ffffff;
}

.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .open > a {
    background-image: none;
    background-repeat: no-repeat;
    box-shadow: none;
}

.time-table{
        height: auto;
        margin-top: 20px;
        overflow: visible;
        padding-top: 50px;
        position: relative;
        width: 100%;
}

.time-table-no-margin{
        height: auto;        
        overflow: hidden;
        padding-top: 50px;
        /*position: relative;*/
        width: 100%;
}

.back-to-top {
    position: fixed;
    bottom: 2em;
    right: 0px;
    text-decoration: none;
    color: #ffffff;
    background-color: #027ab7;
    font-size: 12px;
    padding: 1em;
    display: none;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
}

.back-to-top:hover {    
    background-color: #01437d;
    color: #ffffff;    
}

.pagetitle {
    color: #027ab7;
    font-size: 19px;
    font-weight: bold;
    margin-bottom: 20px;
    padding-top: 20px;
}
</style>

<script type="text/javascript">
    $(function(){
        backToTop();
    });

    function backToTop() {
        var offset = 220;
        var duration = 500;

        jQuery(window).scroll(function() {
            if (jQuery(this).scrollTop() > offset) {
                jQuery('.back-to-top').fadeIn(duration);
            } else {
                jQuery('.back-to-top').fadeOut(duration);
            }
        });

        jQuery('.back-to-top').click(function(event) {
            event.preventDefault();
            jQuery('html, body').animate({scrollTop: 0}, duration);
            return false;
        });
    }   
</script>
<!-- /.header -->