jQuery(function ($) {
	var clicked = false;
	var orig_send_to_editor = window.send_to_editor;
	$('a[data-type="media-picker"]').click(function() {
		clicked = true;
		var url = $(this).attr('href');
		var button = $(this);
		$.fancybox({
			href: url,
			type: 'iframe',
			width: 681,
			height: 600,
			onCleanup: function (){}
		});
		var input = $('input[name="' + $(this).attr('data-for') + '"]');

		function c2_unserialize(data) {
			return data;
		}

		function cm_fix_img_url(url) {
			return url.replace(window.cm_home_url, '');
		}
		
		window.pb_medialibrary = function(html) {
			var data = c2_unserialize(html);
			
			if ( data.url != undefined && data.url != '' ) {
				$(input).val(cm_fix_img_url(data.url));
				update_img_src(input, button, data.url);
			} else {
				alert('An unexpected problem ocurred. Please fill in the image URL manually.');
			};
			$.fancybox.close();
		}

		window.send_to_editor = ( clicked )? function(html) {
			var a = ( $('a', html).length != 0 )? $('a', html) : $('a', html).prevObject;
			imgurl = ( $('img', html).length != 0 )? $('img', html).attr('src') : $(a).attr('href');

			$(input).val(cm_fix_img_url(imgurl));
			update_img_src(input, button, imgurl, $('img', html).length);

			$.fancybox.close();
			clicked = false;
		} : orig_send_to_editor;
		
		if ( typeof(win) !== 'undefined' ) {
			win.send_to_editor = function(html) {
				var a = ( $('a', html).length != 0 )? $('a', html) : $('a', html).prevObject;
				imgurl = ( $('img', html).length != 0 )? $('img', html).attr('src') : $(a).attr('href');
				
				$(input).val(cm_fix_img_url(imgurl));
				update_img_src(input, button, imgurl, $('img', html).length);
				
				$.fancybox.close();
			}
		};

		return false;
	});
	function update_img_src (input, button, src, is_img) {
		$(input).parent().find('.cm-view-file').attr('href', src);
	}
});