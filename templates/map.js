(function($){

jQuery.CustomMapping = {
	target_selector: '',
	target: null,
	target_drag_handle: null,
	dragging: false,
	position_last: [0, 0],
	position_current: [0, 0],

	init: function(target_selector, x, y, image_ratio, image_center_x, image_center_y, image_offset_x, image_offset_y) {
		this.target_selector = target_selector;
		this.image_ratio = image_ratio;
		this.image_center_x = image_center_x;
		this.image_center_y = image_center_y;
		this.image_offset_x = image_offset_x;
		this.image_offset_y = image_offset_y;
		this.momentum = {
			momentum: [0, 0],
			animation_interval: null,
			animation_timeout: null
		};

		this.target = $(this.target_selector);
		this.target_drag_handle = $(this.target).find('.cm-drag-handle:first');

		position_last = [x, y];
		jQuery.CustomMapping.position_current = [-jQuery.CustomMapping.image_center_x, -jQuery.CustomMapping.image_center_y];

		this.target_drag_handle.ondragstart = function() {
			return false;
		}

		$(this.target_drag_handle).mousedown(function(evt) {
			if( $(this).is('.no-zoom *') )
				return false;
			
			if ($(this).find('.cm-map-big:first').css('visibility') == 'hidden') {
				var clone = $(me.target_drag_handle).clone();
				$(clone).css({
					'position': 'absolute',
					'left': $(me.target_drag_handle).position().left,
					'top': $(me.target_drag_handle).position().top,
					'z-index': 10
				}).appendTo(me.target);
				$(clone).animate({
					'opacity': 0
				}, 600, function() {
					$(this).remove();
				});

				var center = [
					Math.round((jQuery.CustomMapping.image_offset_x - (evt.pageX - $(this).offset().left)) / jQuery.CustomMapping.image_ratio + jQuery.CustomMapping.target.width() / 2),
					Math.round((jQuery.CustomMapping.image_offset_y - (evt.pageY - $(this).offset().top)) / jQuery.CustomMapping.image_ratio + jQuery.CustomMapping.target.height() / 2) 
				];
				jQuery.CustomMapping.position_current = [center[0], center[1]];
				$(this).css({
					'left': (center[0]).toString() + 'px',
					'top': (center[1]).toString() + 'px'
				});

				$(this).find('.cm-map-big:first').css({
					'visibility': 'visible',
					'opacity': 0
				}).fadeTo(600, 1);
				$(this).find('.cm-map-small:first').css('visibility', 'hidden');
				// $(this).closest('.custom-mapping-map').find('.cm-zoom-out').css('visibility', 'visible');
				return true;
			}
			this.ondragstart = function() {
				return false;
			}
			document.onselectstart = function() {
				return false;
			}
			
			$(this).addClass('cm-map-drag');
			jQuery.CustomMapping.dragging = true;
			me.momentum.momentum = [0, 0];
			clearInterval(me.momentum.animation_interval);
			clearTimeout(me.momentum.animation_timeout);
				
			jQuery.CustomMapping.position_last = [evt.pageX - $(this).offset().left, evt.pageY - $(this).offset().top];
		});

		this.target_drag_handle.mouseup(function(evt) {
			if (!jQuery.CustomMapping.dragging) return;
			
			document.onselectstart = function() {
				return true;
			}
			
			$(this).removeClass('cm-map-drag');
			jQuery.CustomMapping.dragging = false;
			var interval = 13;
			var coords_step = [
				parseFloat(((me.momentum.momentum[0] * 1.5) / interval).toFixed(2)) * cm_map_momentum,
				parseFloat(((me.momentum.momentum[1] * 1.5) / interval).toFixed(2)) * cm_map_momentum
			];
			me.momentum.animation_interval = setInterval(function() {
				jQuery.CustomMapping.position_last = jQuery.CustomMapping.position_current;
				jQuery.CustomMapping.position_current = [
					parseInt(jQuery.CustomMapping.position_current[0]) + coords_step[0],
					parseInt(jQuery.CustomMapping.position_current[1]) + coords_step[1]
				];
				
				var max_x = -$(me.target_drag_handle).find('.cm-map-big:first').width() + jQuery.CustomMapping.target.width();
				var max_y = -$(me.target_drag_handle).find('.cm-map-big:first').height() + jQuery.CustomMapping.target.height();
				jQuery.CustomMapping.position_current[0] = Math.round(Math.min(0, Math.max(max_x, jQuery.CustomMapping.position_current[0])));
				jQuery.CustomMapping.position_current[1] = Math.round(Math.min(0, Math.max(max_y, jQuery.CustomMapping.position_current[1])));
				
				$(me.target_drag_handle).css({
					'left': Math.round(jQuery.CustomMapping.position_current[0]).toString() + 'px',
					'top': Math.round(jQuery.CustomMapping.position_current[1]).toString() + 'px'
				});
			}, interval);
			me.momentum.animation_timeout = setTimeout(function() {
				clearInterval(me.momentum.animation_interval);
			}, interval * 15);
		});

		this.target_drag_handle.mousemove(function(evt) {
			if (!jQuery.CustomMapping.dragging) return;

			relative_x = (evt.pageX - $(this).offset().left) - jQuery.CustomMapping.position_last[0];
			relative_y = (evt.pageY - $(this).offset().top) - jQuery.CustomMapping.position_last[1];
			
			jQuery.CustomMapping.position_current = [parseInt(jQuery.CustomMapping.position_current[0]) + parseInt(relative_x), parseInt(jQuery.CustomMapping.position_current[1]) + parseInt(relative_y)];
			
			var max_x = -$(me.target_drag_handle).find('.cm-map-big:first').width() + jQuery.CustomMapping.target.width();
			var max_y = -$(me.target_drag_handle).find('.cm-map-big:first').height() + jQuery.CustomMapping.target.height();
			jQuery.CustomMapping.position_current[0] = Math.min(0, Math.max(max_x, jQuery.CustomMapping.position_current[0]));
			jQuery.CustomMapping.position_current[1] = Math.min(0, Math.max(max_y, jQuery.CustomMapping.position_current[1]));
			
			$(this).css({
				'left': jQuery.CustomMapping.position_current[0].toString() + 'px',
				'top': jQuery.CustomMapping.position_current[1].toString() + 'px'
			});

			jQuery.CustomMapping.position_last = [evt.pageX - $(this).offset().left, evt.pageY - $(this).offset().top];
			
			me.momentum.momentum = [
				(relative_x),
				(relative_y)
			];
		});

		this.target_drag_handle.dblclick(function(evt) {
			/*
			if ($(this).find('.cm-map-big:first').css('visibility') == 'hidden') {
				var center = [
					Math.round((jQuery.CustomMapping.image_offset_x - (evt.pageX - $(this).offset().left)) / jQuery.CustomMapping.image_ratio + jQuery.CustomMapping.target.width() / 2),
					Math.round((jQuery.CustomMapping.image_offset_y - (evt.pageY - $(this).offset().top)) / jQuery.CustomMapping.image_ratio + jQuery.CustomMapping.target.height() / 2) 
				];
				jQuery.CustomMapping.position_current = [center[0], center[1]];
				$(this).css({
					'left': (center[0]).toString() + 'px',
					'top': (center[1]).toString() + 'px'
				});

				$(this).find('.cm-map-big:first').css('visibility', 'visible');
				$(this).find('.cm-map-small:first').css('visibility', 'hidden');
			} else {
				$(this).css({
					'left': (-jQuery.CustomMapping.image_center_x).toString() + 'px',
					'top': (-jQuery.CustomMapping.image_center_y).toString() + 'px'
				});
				jQuery.CustomMapping.position_current = [-jQuery.CustomMapping.image_center_x, -jQuery.CustomMapping.image_center_y];

				$(this).find('.cm-map-big:first').css('visibility', 'hidden');
				$(this).find('.cm-map-small:first').css('visibility', 'visible');
			}
			*/
		});

		var me = this;
		this.target.find('.cm-zoom-in').click(function() {
			if ($(me.target_drag_handle).find('.cm-map-big:first').css('visibility') != 'hidden') {
				return false;
			}
			
			var clone = $(me.target_drag_handle).clone();
			$(clone).css({
				'position': 'absolute',
				'left': $(me.target_drag_handle).position().left,
				'top': $(me.target_drag_handle).position().top,
				'z-index': 10
			}).appendTo(me.target);
			$(clone).animate({
				'opacity': 0
			}, 600, function() {
				$(this).remove();
			});
			
			var center = [
				Math.round(-$(me.target_drag_handle).find('.cm-map-big:first').width() / 2 ),
				Math.round(-$(me.target_drag_handle).find('.cm-map-big:first').height() / 2 )
			];
			jQuery.CustomMapping.position_current = [center[0], center[1]];
			$(me.target_drag_handle).css({
				'left': (center[0]).toString() + 'px',
				'top': (center[1]).toString() + 'px'
			});

			$(me.target_drag_handle).find('.cm-map-big:first').css({
				'visibility': 'visible',
				'opacity': 0
			}).fadeTo(600, 1);
			$(me.target_drag_handle).find('.cm-map-small:first').css('visibility', 'hidden');
			
			return false;
		});

		this.target.find('.cm-zoom-out').click(function() {
			if ($(me.target_drag_handle).find('.cm-map-big:first').css('visibility') == 'hidden') {
				return false;
			}
			var clone = $(me.target_drag_handle).clone();
			$(clone).css({
				'position': 'absolute',
				'left': $(me.target_drag_handle).position().left,
				'top': $(me.target_drag_handle).position().top,
				'z-index': 10
			}).appendTo(me.target);
			$(clone).animate({
				'opacity': 0
			}, 600, function() {
				$(this).remove();
			});

			$(me.target_drag_handle).css({
				'left': (-jQuery.CustomMapping.image_center_x).toString() + 'px',
				'top': (-jQuery.CustomMapping.image_center_y).toString() + 'px'
			});
			jQuery.CustomMapping.position_current = [-jQuery.CustomMapping.image_center_x, -jQuery.CustomMapping.image_center_y];

			$(me.target_drag_handle).find('.cm-map-big:first').css('visibility', 'hidden');
			$(me.target_drag_handle).find('.cm-map-small:first').css({
				'visibility': 'visible',
				'opacity': 0
			}).fadeTo(600, 1);
			// $(me.target_drag_handle).find('.cm-map-small:first').css('visibility', 'visible');

			// $(this).css('visibility', 'hidden');
			return false;
		});

		this.target.find('.cm-pin').hover(function() {
			var pin = $('#cm-map-pin-' + $(this).attr('data-id'));
			pin.appendTo('body');
			pin.css({
				'left': $(this).offset().left - 122,
				'top': $(this).offset().top - pin.height(),
				'display': 'block'
			});
		}, function() {
			$('#cm-map-pin-' + $(this).attr('data-id')).css({
				'display': 'none'
			});
		});

		this.target.find('.cm-pin').click(function() {
			return false;
		});

		this.target.find('.cm-pin').click(function() {
			return false;
		});
	}
};

})(jQuery);