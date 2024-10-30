<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	update_option('cm_mode', $_POST['cm_mode']);
	update_option('cm_pin', $_POST['cm_pin']);
	update_option('cm_image_small', $_POST['cm_image_small']);
	update_option('cm_image_big', $_POST['cm_image_big']);
	update_option('cm_background', $_POST['cm_background']);
	update_option('cm_momentum', $_POST['cm_momentum']);
	update_option('cm_initial_zoom', $_POST['cm_initial_zoom']);
	update_option('cm_main_location', $_POST['cm_main_location']);
	update_option('cm_enable_zoom', $_POST['cm_enable_zoom']);
}
?>
<script type="text/javascript">
window.cm_home_url = "<?php echo home_url(); ?>";
</script>
<div class="wrap custom-mapping-shell">
	<div class="icon32" id="icon-options-general"><br></div><h2>Custom Mapping</h2>

	<form action="" method="post">
		<h3>General</h3>
		<table class="form-table">
			<tr>
				<td class="cm-label" style="width: 200px;">
					<label for="cm_mode">Mode:</label>
				</td>
				<td class="cm-field">
					<select name="cm_mode">
						<option value="image" <?php echo (get_option('cm_mode') == 'image') ? 'selected="selected"' : ''; ?> >Image</option>
						<option value="google" <?php echo (get_option('cm_mode') == 'google') ? 'selected="selected"' : ''; ?> >Google Maps</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="cm-label">
					<label for="cm_pin">Pin Image:</label>
				</td>
				<td class="cm-field">
					<input type="text" name="cm_pin" value="<?php echo get_option('cm_pin'); ?>" readonly="readonly" />
					<a href="<?php echo get_admin_url(); ?>media-upload.php" class="button" data-type="media-picker" data-for="cm_pin">Select Image</a>
					<a href="<?php echo get_option('cm_pin'); ?>" class="cm-view-file" target="_blank" style="<?php echo (!get_option('cm_pin')) ? 'display: none;' : ''; ?>">Preview</a>
				</td>
			</tr>
		</table>

		<h3>Map "Image Mode" settings</h3>
		<table class="form-table">
			<tr>
				<td class="cm-label" style="width: 200px;">
					<label for="cm_image_small">Map Image (default view):</label>
				</td>
				<td class="cm-field">
					<input type="text" name="cm_image_small" value="<?php echo get_option('cm_image_small'); ?>" readonly="readonly" />
					<a href="<?php echo get_admin_url(); ?>media-upload.php" class="button" data-type="media-picker" data-for="cm_image_small">Select Image</a>
					<a href="<?php echo get_option('cm_image_small'); ?>" class="cm-view-file" target="_blank" style="<?php echo (!get_option('cm_image_small')) ? 'display: none;' : ''; ?>">Preview</a>
				</td>
			</tr>
			<tr>
				<td class="cm-label">
					<label for="cm_image_big">Map Image (zoomed in):</label>
				</td>
				<td class="cm-field">
					<input type="text" name="cm_image_big" value="<?php echo get_option('cm_image_big'); ?>" readonly="readonly" />
					<a href="<?php echo get_admin_url(); ?>media-upload.php" class="button" data-type="media-picker" data-for="cm_image_big">Select Image</a>
					<a href="<?php echo get_option('cm_image_big'); ?>" class="cm-view-file" target="_blank" style="<?php echo (!get_option('cm_image_big')) ? 'display: none;' : ''; ?>">Preview</a>
					<div class="cl">&nbsp;</div>
					<em>Note: images must have same aspect ratio to display properly.</em>
				</td>
			</tr>
			<tr>
				<td class="cm-label">
					<label for="cm_background">Background Color:</label>
				</td>
				<td class="cm-field">
					#<input type="text" name="cm_background" value="<?php echo get_option('cm_background'); ?>" style="width: 55px;" /> <a href="#" class="cm_background_transparent">Transparent</a>
					<div class="cl">&nbsp;</div>
					<em>You can set the background color to your map (though not in Google Maps mode). This is useful when using a transparent image, such as a png.</em>
				</td>
			</tr>
			<tr>
				<td class="cm-label">
					<label for="cm_background">Map Drag Momentum:</label>
				</td>
				<td class="cm-field">
					<input type="text" name="cm_momentum" value="<?php echo get_option('cm_momentum'); ?>" style="width: 55px;" />%
					<div class="cl">&nbsp;</div>
				</td>
			</tr>
			<tr>
				<td class="cm-label">
					<label for="cm_background">Enable Zoom:</label>
				</td>
				<td class="cm-field">
					<select name="cm_enable_zoom">
						<option value="0"<?php if(get_option('cm_enable_zoom')==0) echo ' selected="selected"' ?>>No</option>
						<option value="1"<?php if(get_option('cm_enable_zoom')==1) echo ' selected="selected"' ?>>Yes</option>
					</select>
				</td>
			</tr>
		</table>

		<h3>Map "Google Maps" mode settings</h3>
		<table class="form-table">
			<tr>
				<td class="cm-label">
					<label for="cm_main_location">Main Location</label>
				</td>
				<td class="cm-field">
					<select name="cm_main_location">
						<?php
						$current = get_option('cm_main_location');
						if(!$current)
							$current = '0,0,0';

						$pairs = array('0,0,0' => 'Select One');
						$locations = get_posts('post_type=map-location&posts_per_page=-1&order=ASC&orderby=post_title');
						foreach($locations as $l) {
							$p = get_post_meta($l->ID, '_map_location_google_location', true);
							$pairs[$p] = $l->post_title;
						}

						foreach($pairs as $l=>$t) {
							echo '<option value="' . $l . '"' . ($current == $l ? ' selected="selected"' : '') . '>' . $t . '</option>';
						}
						?>
					</select>
					<div class="cl">&nbsp;</div>
					<em>Optional. This location will be used to center the map in &quot;Google Maps&quot; Mode.</em>
				</td>
			</tr>
			<tr>
				<td class="cm-label">
					<label for="cm_initial_zoom">Initial Zoom</label>
				</td>
				<td class="cm-field">
					<input type="text" name="cm_initial_zoom" size="5" class="disabled" style="width:30px" value="<?php echo get_option('cm_initial_zoom') ? get_option('cm_initial_zoom') : 2 ?>" />
					<div class="zoom-slider"></div>
					<div class="cl">&nbsp;</div>
					<em>This will be the initial zoom of the map in &quot;Google Maps&quot; mode.</em>
					<div id="zoom-map" style="width:400px; height:400px;"></div>
				</td>
			</tr>
		</table>
		<?php /* ?>
		<h3>Google Maps Mode</h3>
		<table class="form-table">
			<tr>
				<td class="cm-label">
					<label for="cm_mode">API Key:</label>
				</td>
				<td class="cm-field">
					<input type="text" name="cm_google_api_key" value="" />
				</td>
			</tr>
		</table>
		<?php */ ?>

		<p class="submit"><input type="submit" value="Save Changes" class="button-primary" id="submit" name="submit"></p>
	</form>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
	(function($){
		$('input[name="cm_background"]').ColorPicker({
			color: $('input[name="cm_background"]').val(),
			livePreview: true,
			onChange: function(hsb, hex, rgb) {
				$('input[name="cm_background"]').val(hex);
			}
		});
		$('.cm_background_transparent').click(function() {
			$('input[name="cm_background"]').val('');
			return false;
		});

		var zoomLocation = $('select[name=cm_main_location]').val().split(',');
		if(zoomLocation[0] == 0 && zoomLocation[1] == 0) {
			zoomLocation = [40.712861,-74.013113];
		}
		var zoomCenter = new google.maps.LatLng(parseFloat(zoomLocation[0]), parseFloat(zoomLocation[1]));
		var zoomMap = new google.maps.Map($('#zoom-map')[0], {
	        center: zoomCenter,
	        zoom: parseInt($('input[name=cm_initial_zoom]').val()),
	        mapTypeId: google.maps.MapTypeId.ROADMAP,
	        panControl: false,
	        zoomControl: true,
	        mapTypeControl: false,
	        scaleControl: true,
	        draggable: false,
	        streetViewControl: false,
	        overviewMapControl: false,
	        scrollwheel: true
		});
		var zoomMarker = new google.maps.Marker({
			map: zoomMap,
			position: zoomCenter
		});
		$('select[name=cm_main_location]').change(function() {
			var zoomLocation = $(this).val().split(',');
			if(zoomLocation[0] == 0 && zoomLocation[1] == 0) {
				zoomLocation = [40.712861,-74.013113];
			}
			var center = new google.maps.LatLng(zoomLocation[0], zoomLocation[1]);
			zoomMap.setCenter(center);
			zoomMarker.setPosition(center);
		});
		google.maps.event.addListener(zoomMap, 'zoom_changed', function() {
			$('input[name=cm_initial_zoom]').val( zoomMap.getZoom() );
		});
	})(jQuery);
	</script>
</div>