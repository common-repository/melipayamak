var $ = jQuery;
$(document).ready(function() {
	$(':checkbox').click(function() {
		var $this = $(this);
		var id = '#' + $this.attr('id') + '_text';
		$(id).fadeToggle();
	});
	$('input.action').click(function() {
		if ($('.actions select').val() == 'reply') {
			var elems = $('.check-column input:checked'),
			    lastID = elems.length - 1,
			    to = [];
			elems.each(function(i) {
				if ($(this).data('to'))
					to.push($(this).data('to'));
				if (i == lastID)
					window.open('admin.php?page=melipayamak_send&to=' + to.join(','), '_blank');
			});
			return false;
		}
		return confirm('آیا مطمئن هستید؟');
	});
	mpinterval = setInterval(function() {
		$('.updated').slideUp();
		clearInterval(mpinterval);
	}, 4000);
	$('.updated').click(function() {
		$(this).slideUp(function() {
			$(this).remove();
		});
	});
	$("code").click(function() {
		mpinsert($(this).parent().parent().find('textarea'), $(this).text());
		if ($(this).hasClass('refresh'))
			refresh_count();
	});
	function mpinsert(sTextAreaID, sText) {
		return $(sTextAreaID).each(function(i) {
			if (document.selection) {
				this.focus();
				document.selection.createRange().text = sText;
				this.focus();
			} else if (this.selectionStart || this.selectionStart == '0') {
				var startPos = this.selectionStart,
				    endPos = this.selectionEnd,
				    scrollTop = this.scrollTop;
				this.value = this.value.substring(0, startPos) + sText + this.value.substring(endPos, this.value.length);
				this.focus();
				this.selectionStart = startPos + sText.length;
				this.selectionEnd = startPos + sText.length;
				this.scrollTop = scrollTop;
			} else {
				this.value += sText;
				this.focus();
			}
		});
	}


	$("#mpsm").keyup(function() {
		refresh_count();
	});
	function GetMsgInfo(sText) {
		var len = sText.length,
		    utf8 = false;
		for ( i = 0; i < len; i++)
			if (sText.charCodeAt(i) > 255) {
				utf8 = true;
				break;
			}
		var max = ( utf8 ? 70 : 160),
		    div = ( utf8 ? 67 : 153);
		var fin = {
			max : max,
			len : len,
			cnt : 1
		};
		if (len > max) {
			fin.cnt = Math.ceil(len / div);
			fin.max = fin.cnt * div;
		}
		return fin;
	}

	function refresh_count() {
		var info = GetMsgInfo($('#mpsm').val());
		$('#mcount').val(info.cnt);
		$('.lenght .one').empty().append(info.len);
		$('.lenght .two').empty().append(info.max);
		$('.count .one').empty().append(info.cnt);
	}


	$('#mpst').change(function() {
		if ($(this).val() == 'users') {
			$('.variable').fadeOut();
			$('#mpsn').fadeOut().empty();
			return false;
		}
		if ($(this).val() == 'custom') {
			$('.variable').fadeOut();
			$('#mpsn').fadeOut().empty().append('<td>شماره های دریافت کننده</td><td><textarea id="mpnumbers" cols="50"  rows="7" name="numbers" required></textarea><p class="description">هر شماره را در یک خط قرار دهید.</p></td>').fadeIn();
			var to = $(this).attr('data-to');
			if (to && to != '') {
				to = to.split(',').join("\n");
				$("#mpnumbers").val(to);
				$(this).attr('data-to', '');
			}
		} else {
			$('.mp_cover').fadeIn();
			$.ajax({
				type : "POST",
				url : "",
				data : {
					mpgc : $(this).val(),
					mpsactionf : $('#mpsactionf').val(),
					_wp_http_referer : $('input[name="_wp_http_referer"]').val()
				}
			}).done(function(data) {
				$('#mpsn').fadeOut().empty().append(data).fadeIn();
				$('.mp_cover').fadeOut();
			}).fail(function(msg) {
				alert('مشکلی پیش آمد. مجددا تلاش کنید.');
			});
			$('.variable').fadeIn();
		}
	});

	if ($('#mpst').val() == 'custom')
		$('#mpst').change();
});
