jQuery(function($) {
	var panel = $('#map-location-options');
	var table = panel.find('table');
	var plantMapTr = table.find('.ecf-plantmap');
	var plantMapTd = plantMapTr.find('td.plantmap:first');
	var canvas = plantMapTd.find('.plantmap-canvas:first');
	var pin = canvas.find('.plantmap-marker:first').hide().data('visible', false);
	var input = plantMapTd.find('.plantmap-coordinates:first');
	if ($(pin).attr('data-coordinates')) {
		var coords = $(pin).attr('data-coordinates').split(',');
		$(pin).css({
			'left': coords[0].toString() + 'px',
			'top': coords[1].toString() + 'px'
		}).show();
		input.val($(pin).attr('data-coordinates'));
	}
	
	var dragging = false;

	lastPositionX = 0;
	lastPositionY = 0;

	positionX = 0;
	positionY = 0;
	
	var MAP_WIDTH = -1400 + canvas.width();
	var MAP_HEIGHT = -746 + canvas.height();
	var PIN_X_FIX = 10;
	var PIN_Y_FIX = 19;
	
	if (canvas.length) {
		canvas[0].ondragstart = function() {
			return false;
		}
	}
	
	if (input.val()) {
		var inputCoordinates = /([\d]+),([\d]+)/.exec(input.val());
		if (inputCoordinates && inputCoordinates.length > 1) {
			var inputX = inputCoordinates[1];
			var inputY = inputCoordinates[2];
		}
		
		var pinLeft = inputX - PIN_X_FIX;
		var pinTop = inputY - PIN_Y_FIX;
		
		pin.css({ left: pinLeft + 'px', top: pinTop + 'px' });
		
		var xCoordinate = inputX - parseInt(positionX);
		var yCoordinate = inputY - parseInt(positionY);
		input.val(xCoordinate + ',' + yCoordinate);
	}
	
	canvas.mousedown(function(evt) {
		this.ondragstart = function() {
			return false;
		}
		document.onselectstart = function() {
			return false;
		}
		
		$(this).addClass('map-drag');
		dragging = true;
		
		lastPositionX = evt.pageX - $(canvas).offset().left;
		lastPositionY = evt.pageY - $(canvas).offset().top;
	}).mouseup(function(evt) {
		if (!dragging) return;
		
		document.onselectstart = function() {
			return true;
		}
		
		$(this).removeClass('map-drag');
		dragging = false;
	}).mousemove(function(evt) {
		if (!dragging) return;
		
		relativeX = (evt.pageX - $(canvas).offset().left) - lastPositionX;
		relativeY = (evt.pageY - $(canvas).offset().top) - lastPositionY;
		
		positionX = parseInt(positionX) + parseInt(relativeX);
		positionY = parseInt(positionY) + parseInt(relativeY);
		
		$(this).css('background-position', positionX + 'px ' + positionY + 'px');

		var pinCurrentLeft = parseInt(pin.css('left'));
		var pinCurrentTop = parseInt(pin.css('top'));
		pin.css({ left: pinCurrentLeft + relativeX + 'px', top: pinCurrentTop + relativeY + 'px' });
		
		lastPositionX = evt.pageX - $(canvas).offset().left;
		lastPositionY = evt.pageY - $(canvas).offset().top;
	}).dblclick(function(evt) {
		var pinLeft = (evt.pageX - $(canvas).offset().left) - PIN_X_FIX;
		var pinTop = (evt.pageY - $(canvas).offset().top) - PIN_Y_FIX;
		
		pin.show().data('visible', true).css({ left: pinLeft + 'px', top: pinTop + 'px' });
		
		var xCoordinate = (evt.pageX - $(canvas).offset().left) - parseInt(positionX);
		var yCoordinate = (evt.pageY - $(canvas).offset().top) - parseInt(positionY);
		input.val(xCoordinate + ',' + yCoordinate);
	});
});