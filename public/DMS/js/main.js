$(function() {
	$('.pagination .month')
		.addClass('input-group-addon')
		.parent()
		.addClass('input-group')
		.addClass('date')
		.addClass('month')
		.find('.year')
		.addClass('input-group-addon');

	setDatepicker('.input-group.date');
	setSwitchButton('.section-tout.title .command', '.section-tout.title+.container.bs-component.well');
	setAllCheckBox('.check-all');
	setPageTopButton('.page-top');
	setHorizontalScrollButtons('#left', '#right', '.table-responsive:not(.left)', '.table-responsive.left');
	setTitleFromText('.ellipsis,.table a:not(.btn),.table tbody .nowrap td > span');
	if(!isIE() || !$('.table-fixed-columns').length) setVerticalScrollEvents(upScroll, downScroll, 0, 0);
		setTableTags('.table-responsive:not(.left):not(.right) > .table > tbody > tr');
		setFixedColumns();
	$(window).resize().scroll();

	$('input[type=checkbox]:checked,input[type=radio]:checked').parent('.btn').addClass('active');
	if (!$('.table-wrapper').length) {
		$('.table-responsive').wrap($('<div>').addClass('table-wrapper'));
	}
	try {
		$('.table-responsive .table').floatThead({
			top: getResponsiveTableHeaderTop,
		  responsiveContainer: function($table) {
		    return $table.closest('.table-responsive');
		  }
		});
	} catch(e) {}
	$('.section-tout.title .command').click();
});

/**
 * テーブルヘッダの上位置を取得します。
 * @return 上位置
 */
function getResponsiveTableHeaderTop() {
	var $title = $('.section-tout.title');
	if (!$title.length) {
		return 0;
	}
	return $title.position().top + $title.outerHeight();
}

/**
 * テーブルヘッダの左位置を取得します。
 * @param $container コンテナ
 * @return 左位置
 */
function getResponsiveTableHeaderLeft($container) {
	if (!$container.length) {
		return 0;
	}
	return $container.position().left + toInt($container.css('border-left-width'));
}

/**
 * 上スクロール時の処理をします。
 */
function upScroll() {
	var $navbar = $('.navbar-fixed-top');
	if (!__scrolled && $navbar.is(':hidden')) {
		__scrolled = true;
		moveSubHeaders($navbar.outerHeight(), function() { $navbar.stop().fadeIn(function() { __scrolled = false; }); });
	}
}

/**
 * 下スクロール時の処理をします。
 */
function downScroll() {
	var $navbar = $('.navbar-fixed-top');
	if (!__scrolled && $navbar.is(':visible')) {
		__scrolled = true;
		$navbar.fadeOut(function() { moveSubHeaders($(this).outerHeight() * -1, function() { __scrolled = false; }); });
	}
}

/**
 * 数値に変換します。
 */
function toInt(value) {
	return value ? parseInt(value) : 0;
}

/**
 * クライアントがPCかどうか判定します。
 * @return PCの場合に true を返します。
 */
function isPC() {
  var agent = navigator.userAgent;
	return (agent.search(/iPhone/) == -1 && agent.search(/iPad/) == -1 && agent.search(/iPod/) == -1 && agent.search(/Android/) == -1);
}

/**
 * クライアントがIEかどうか判定します。
 * @return IEの場合に true を返します。
 */
function isIE() {
  var agent = navigator.userAgent.toLowerCase();
	return (agent.indexOf('msie') != -1 || agent.indexOf('trident') != -1);
}

/**
 * 日付選択を設定します。
 * @param selector セレクタ
 */
function setDatepicker(selector) {
	$(selector).each(function() {
		var $target = $(this);
		var $header = $target.closest('.section-tout.title+.container.bs-component.well');
		var $footer = $target.closest('.footer');
		var $parent = $('body');
		var $parent = $header.length ? $header : $parent;
		var $parent = $footer.length ? $footer : $parent;
		var orientation = $footer.length ? 'top' : 'auto';
    var beforeShowDay = function(date) {
      var before = {
	      enabled: true,
	      classes: 'day-weekday'
			};
      if (date.getDay() == 0) {
        before.classes = 'day-sunday';
      }
			if (date.getDay() == 6) {
        before.classes = 'day-saturday';
      }
      return before;
    };
	if ($target.hasClass('month')) {
	  $target.datepicker({
	    format: 'yyyy/mm',
	    language: 'ja',
	    autoclose: true,
	    minViewMode: 'months',
	    container: $parent,
			orientation: orientation,
	    zIndexOffset: 1000,
			beforeShowDay: beforeShowDay
	  });
		if ($footer.length) {
			var $month = $target.find('.month');
			var $year = $target.find('.year');
			$target.on('changeDate', function() {
				var date = $target.datepicker('getDate');
				$month.text(date.getMonth() + 1);
				$year.text(date.getFullYear());
			});
		}
		return;
	}

	 $target.datepicker({
	    format: 'yyyy/mm/dd',
	    language: 'ja',
	    autoclose: true,
	    container: $parent,
			orientation: orientation,
	    zIndexOffset: 1000,
			beforeShowDay: beforeShowDay
	  });

	});
}

/**
 * 切替ボタンを設定します。
 * @param buttonSelector ボタンセレクタ
 * @param switchContainerSelector 切替コンテナセレクタ
 */
function setSwitchButton(buttonSelector, switchContainerSelector) {
	$(window).scroll(function () {
		$(buttonSelector).removeClass('active');
		$(switchContainerSelector).fadeOut();
	});

	$(buttonSelector).click(function() {
		var $container = $(switchContainerSelector);
		if ($container.is(':visible')) {
			$(this).removeClass('active');
			$container.fadeOut();
		} else {
			$(this).addClass('active');
			$container.fadeIn();
		}
	});
}

/**
 * 全選択チェックボックスを設定します。
 * @param selector セレクタ
 */
function setAllCheckBox(selector) {
	$(selector).change(function() {
		$('.' + $(this).data('class')).prop('checked', $(this).prop('checked'));
	});
}

/**
 * ページトップボタンを設定します。
 * @param selector セレクタ
 */
function setPageTopButton(selector) {
	var $button = $(selector);

	$(window).scroll(function () {
		if ($(this).scrollTop() > 150) {
			$button.fadeIn();
		} else {
			$button.fadeOut();
		}
	});

	$button.click(function () {
		$(this).scrollTop(0);
		$('body, html').animate({ scrollTop: 0 }, 500);
		return false;
	});
}

/**
 * 水平スクロールボタンを設定します。
 * @param leftSelector 左スクロールセレクタ
 * @param rightSelector 右スクロールセレクタ
 * @param containerSelector コンテナセレクタ
 * @param containerPairSelector コンテナペアセレクタ
 */
function setHorizontalScrollButtons(leftSelector, rightSelector, containerSelector, containerPairSelector) {
  if (isPC()) {
	  setHorizontalScrollButtonEvents(containerSelector, leftSelector, -30);
	  setHorizontalScrollButtonEvents(containerSelector, rightSelector, 30);
		$(containerPairSelector)
			.mouseover(function() { $(containerSelector).mouseover(); })
			.mouseout(function() { $(containerSelector).mouseout(); });
	}
}

/**
 * 水平スクロールボタンのイベントを設定します。
 * @param containerSelector コンテナセレクタ
 * @param selector スクロールセレクタ
 * @param move スクロール移動量
 */
function setHorizontalScrollButtonEvents(containerSelector, selector, move)
{
  var scrollable = false;
  var scroll = function() { $(containerSelector).scrollLeft($(containerSelector).scrollLeft() + move); if (scrollable) setTimeout(scroll, 50); };
  $(selector)
    .mousedown(function() { scrollable = true; setTimeout(scroll, 50); return false; })
    .mouseup(function() { scrollable = false; clearTimeout(scroll); })
    .mouseleave(function() { scrollable = false; clearTimeout(scroll); })
    .mouseover(function() { $(this).show(); })
    .mouseout(function() { $(this).hide(); });
  $(containerSelector)
    .mouseover(function() {
			if (($('body').get(0).scrollHeight - $(window).innerHeight()) <= 0) {
				return;
			}
			if ($(containerSelector).width() >= $(containerSelector).find('.table').width()) {
				return;
			}
			if (move < 0 && $(this).scrollLeft() == 0) {
				return;
			}
			if (move > 0 && ($(this).find('.table').width() - $(this).scrollLeft() - $(this).width()) <= 0) {
				return;
			}
			$(selector).css('top', $(window).height() / 2 - $(selector).height() / 2).show();
		})
    .mouseout(function() { $(selector).hide(); });
}

/**
 * テキストをタイトル属性に設定します。
 * @param selector セレクタ
 */
function setTitleFromText(selector) {
	$(selector).each(function () {
		$(this).attr('title', $(this).text());
	});
}

var __scrolled = false;
var __scrollTop = 0;
/**
 * 縦スクロールイベントを設定します。
 * @param upCallback 上スクロールのコールバック
 * @param downCallback 下スクロールのコールバック
 * @param upHeight 上スクロール判定高さ
 * @param downHeight 下スクロール判定高さ
 */
function setVerticalScrollEvents(upCallback, downCallback, upHeight, downHeight) {
	upHeight = upHeight || 45;
	downHeight = downHeight || 45;

	var fire = function(scrollTop, callback) {
		if (callback) callback();
		__scrollTop = scrollTop;
	};

	$(window).scroll(function(){
		var event = function() {
			if (__scrolled) {
				return setTimeout(event, 200);
			}
			var current = $(window).scrollTop();
			if (current < __scrollTop && (__scrollTop - current) >= upHeight) {
				return fire(current, upCallback);
			}
			if (current > __scrollTop && (current - __scrollTop) >= downHeight) {
				return fire(current, downCallback);
			}
		};
		event();
	});
}

/**
 * テーブルタグを設定します。
 * @param selector セレクタ
 */
function setTableTags(selector) {
	$(selector).each(function() {
		var $current = $(this);
		var $tag = $('<i>').attr({ 'class': 'table-tag fa fa-tag fa-fw' }).click(function() {
			var rowspan = $current.find('td[rowspan]:first').attr('rowspan') || 1;
			var index = $current.index();
			var $parent = $current.parent();
			for (var i = 0; i < rowspan; i++) {
				var $row = $parent.find('tr:eq(' + (i + index) + ')');
				if ($row.hasClass('focus')) {
					$row.removeClass('focus');
				} else {
					$row.addClass('focus');
				}
			}
		});
		if ($current.find('td').length == $('.table-responsive .table > tbody > tr:first > td').length) {
			$current.find('td:first').addClass('table-tag-cell').prepend($tag);
		}
	});
}

/**
 * 固定列を設定します。
 */
function setFixedColumns() {
	var $wrapper = $('.wrapper-fixed-columns');
	var $table = $('.table-fixed-columns');
	var $left = $table.find('.table-responsive.left');
	var $right = $table.find('.table-responsive.right');

	var setWidth = function() {
		var width = $('body').innerWidth() - toInt($wrapper.css('padding-left')) - toInt($wrapper.css('padding-right')) - toInt($table.css('border-left-width')) - toInt($table.css('border-right-width'));
		$table.width(width);
		$left.width($table.find('.table-responsive.left > .table').outerWidth());
		$right.width(width - $left.outerWidth());
	};
	setWidth();

	$right.find('.table > thead > tr').each(function () {
		var $leftRow = $left.find('.table > thead > tr:eq(' + $(this).index() + ')');
		var height = Math.max($leftRow.height(), $(this).height());
		$leftRow.children('th,td').height(height);
		$(this).children('th,td').height(height);
	});

	$right.find('.table > tbody > tr').each(function () {
		var $leftRow = $left.find('.table > tbody > tr:eq(' + $(this).index() + ')');
		var height = Math.max($leftRow.height(), $(this).height());
		$leftRow.height(height);
		$(this).height(height);
	});

	$(window).resize(setWidth);
	$wrapper.css('visibility', 'visible');
}

/**
 * サブヘッダの位置を設定します。
 * @param additionalTop 加算するトップ位置
 * @param callback コールバック
 */
function moveSubHeaders(additionalTop, callback) {
	$('.section-tout.title').each(function () {
		$(this).stop().animate({'top': toInt($(this).css('top')) + additionalTop}, {'duration': 'fast', 'complete': function() {
			refloatThead();

			$('.section-tout.title+.container.bs-component.well').each(function () {
				$(this).css({'top': toInt($(this).css('top')) + additionalTop, 'margin-top': 0});
			});

			if (callback) callback();
		}});
	});
}

/**
 * テーブルヘッダを再配置します。
 */
function refloatThead() {
	var top = getResponsiveTableHeaderTop();

	var $container = $('.table-responsive');
	if ($container.length) {
		var tableTop = $container.position().top + toInt($container.css('border-top-width'));
		if ((top + $(window).scrollTop()) < tableTop) {
			top = tableTop;
		}
	}

  $('.floatThead-container').each(function() {
  	var transform = 'translateX(' + getResponsiveTableHeaderLeft($(this).closest('.table-responsive')) + 'px) translateY(' + top + 'px)';
		$(this).css({
      '-webkit-transform': transform,
      '-moz-transform': transform,
      '-ms-transform': transform,
      '-o-transform': transform,
      'transform': transform
    });
	});
}
