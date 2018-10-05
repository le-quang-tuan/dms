<script>
$(function() {
  switchPeriod(false);
});
function switchPeriod(visible) {
  if (visible) {
    $('.group-period').show();
    $('.group-period-visible').addClass('active');
    $('.group-period-invisible').removeClass('active');
  } else {
    $('.group-period').hide();
    $('.group-period-visible').removeClass('active');
    $('.group-period-invisible').addClass('active');
  }
  $(window).resize();
}
</script>