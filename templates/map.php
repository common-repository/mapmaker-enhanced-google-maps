<?php
$mode = get_option('cm_mode');
$scripts = array();
$errors = array();

if ($mode == 'image') {
	$image_small = get_option('cm_image_small');
	$image_small_size = false;
	$image_big = get_option('cm_image_big');
	$image_big_size = false;

	if ($image_small) {
		$image_small_size = @getimagesize(get_file_real_path($image_small));
	}
	if ($image_big) {
		$image_big_size = @getimagesize(get_file_real_path($image_big));
	}
	
	$zoom_enabled = get_option('cm_enable_zoom') === '1';

	if ($image_big_size[0]) {
		$image_ratio = $image_small_size[0] / $image_big_size[0];
		$image_offset = array(
			round($image_big_size[0] / 2 - $image_small_size[0] / 2),
			round($image_big_size[1] / 2 - $image_small_size[1] / 2),
		);
		$image_center = array(
			round($image_big_size[0] / 2) - round($width / 2),
			round($image_big_size[1] / 2) - round($height / 2),
		);
	}
	
	if (!$image_small_size || !$image_big_size) {
		$errors[] = 'There was a problem with loading your map images.';
	}
}

$pin = get_option('cm_pin');
$pin_size = false;
if ($pin) {
	$pin_size = @getimagesize(get_file_real_path($pin));
}
$pin_anchor = array(
	round($pin_size[0] / 2),
	$pin_size[1],
);
$pins = get_posts('post_type=map-location&posts_per_page=-1');
foreach ($pins as $index => $p) {
	$pins[$index]->image_coords = explode(',', get_post_meta($p->ID, '_map_location_image_location', true));
	$pins[$index]->google_coords = explode(',', get_post_meta($p->ID, '_map_location_google_location', true));
}
$background = get_option('cm_background');
if ($background) {
	$background = '#' . $background;
} else {
	$background = 'none';
}

$momentum_percent = get_option('cm_momentum'); 
?>
<?php if ($errors) : ?>
	<p>
		<?php foreach ($errors as $e) : ?>
			<?php echo $e; ?><br />
		<?php endforeach; ?>
	</p>
<?php else : ?>
	<div class="cm-mode-<?php echo $mode ?>">
	<?php ob_start() ?>
	<script type="text/javascript">
	var image_center_x = <?php echo round($image_big_size[0] / 2); ?> - Math.round(jQuery('.custom-mapping-map').width() / 2);
	jQuery(function($){
		$('.cm-drag-handle').css({
			'left': -image_center_x
		});
	});
	</script>
	<?php $scripts[] = ob_get_clean(); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('map.css', __FILE__); ?>">
	<?php foreach ($pins as $p) : ?>
		<div id="cm-map-pin-<?php echo $p->ID; ?>" class="cm-map-pin" style="margin-left: <?php echo $pin_anchor[0] * 2 + 5; ?>px;">
			<div class="top"></div>
			<div class="center">
				<p>
					<strong><?php echo $p->post_title; ?></strong>
					<br />
					<?php echo get_post_meta($p->ID, '_map_location_tooltip', true); ?>
				</p>
			</div>
			<div class="bottom"></div>
		</div>
	<?php endforeach; ?>
	<?php if ($mode == 'image') : ?>
		<?php ob_start(); ?>
		<script type="text/javascript">
		cm_map_momentum = <?php echo ($momentum_percent) ? $momentum_percent : 100; ?> / 100;
		</script>
		<script type="text/javascript" src="<?php echo plugins_url('map.js', __FILE__); ?>"></script>
		<?php $scripts[] = ob_get_clean(); ?>
		<div class="custom-mapping-map<?php if(!$zoom_enabled) echo ' no-zoom' ?>" style="height: <?php echo $height; ?>px; background: <?php echo $background; ?>;">
			<div class="cm-drag-handle" style="left: -<?php echo $image_center[0]; ?>px; top: -<?php echo $image_center[1]; ?>px;">
				<div class="cm-drag-handle-cnt">
					<div class="cm-map-big" style="width: <?php echo $image_big_size[0]; ?>px; height: <?php echo $image_big_size[1]; ?>px; background: url(<?php echo home_url() . $image_big; ?>) no-repeat center center;">
						<div class="cm-pin-wrap">
							<?php foreach ($pins as $p) : ?>
								<a href="<?php echo get_post_meta($p->ID, '_map_location_tooltip_enabled', true) == 'yes' ? add_query_arg('cm-ajax', '1', get_permalink($p->ID)) : '#'; ?>" class="cm-pin" data-id="<?php echo $p->ID; ?>" class="cm-pin" data-id="<?php echo $p->ID; ?>" style="left: <?php echo $p->image_coords[0] - $pin_anchor[0]; ?>px; top: <?php echo $p->image_coords[1] - $pin_anchor[1]; ?>px;"><img src="<?php echo home_url() . $pin; ?>" alt="" /></a>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="cm-map-small" style="width: <?php echo $image_big_size[0]; ?>px; height: <?php echo $image_big_size[1]; ?>px; background: url(<?php echo home_url() . $image_small; ?>) no-repeat center center;">
						<div class="cm-pin-wrap">
							<?php foreach ($pins as $p) : ?>
								<a href="<?php echo get_post_meta($p->ID, '_map_location_tooltip_enabled', true) == 'yes' ? add_query_arg('cm-ajax', '1', get_permalink($p->ID)) : '#'; ?>" class="cm-pin" data-id="<?php echo $p->ID; ?>" style="left: <?php echo $image_offset[0] + round($p->image_coords[0] * $image_ratio) - $pin_anchor[0]; ?>px; top: <?php echo $image_offset[1] + round($p->image_coords[1] * $image_ratio) - $pin_anchor[1]; ?>px;"><img src="<?php echo home_url() . $pin; ?>" alt="" /></a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="cl">&nbsp;</div>
			<?php if($zoom_enabled): ?>
			<a href="#" class="cm-zoom-in">&nbsp;</a>
			<a href="#" class="cm-zoom-out">&nbsp;</a>
			<?php endif; ?>
		</div>
		<?php ob_start() ?>
		<script type="text/javascript">
		jQuery.CustomMapping.init('.custom-mapping-map', -image_center_x, -Math.abs(<?php echo $image_center[1]; ?>), <?php echo $image_ratio; ?>, image_center_x, <?php echo $image_center[1]; ?>, <?php echo $image_offset[0]; ?>, <?php echo $image_offset[1]; ?>);
		</script>
		<?php $scripts[] = ob_get_clean(); ?>
	<?php else : ?>
		<div style="display: none;">
			<?php foreach ($pins as $p) : ?>
				<a href="<?php echo get_post_meta($p->ID, '_map_location_tooltip_enabled', true) == 'yes' ? add_query_arg('cm-ajax', '1', get_permalink($p->ID)) : '#'; ?>" class="cm-pin" data-id="<?php echo $p->ID; ?>" class="cm-pin" id="cm-pin-id-<?php echo $p->ID; ?>">&nbsp;</a>
			<?php endforeach; ?>
		</div>
		<?php ob_start() ?>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<?php $scripts[] = ob_get_clean(); ?>
		<div id="custom-mapping-google-map" style="height: <?php echo $height; ?>px; overflow: hidden;"></div>
		<?php ob_start() ?>
		<script type="text/javascript" charset="utf-8">
		(function($){
			var mouse = {
				x: 0,
				y: 0
			};

			$(document).mousemove(function(e){
				mouse.x = e.pageX;
				mouse.y = e.pageY;
			})

			$(document).ready(function(){
				var center = '<?php echo get_option('cm_main_location') ? get_option('cm_main_location') : -5,5 ?>';
				center = center.split(',');
				center = new google.maps.LatLng(center[0], center[1]);
				var myOptions = {
			        zoom: <?php echo get_option('cm_initial_zoom') && get_option('cm_main_location') != '0,0,0' ? get_option('cm_initial_zoom') : 2 ?>,
			        center: center,
			        mapTypeId: google.maps.MapTypeId.ROADMAP,
			        // disableDoubleClickZoom: true,
			        panControl: true,
			        zoomControl: true,
			        mapTypeControl: true,
			        scaleControl: true,
			        streetViewControl: false,
			        overviewMapControl: true
			    };
			    var cm_map = new google.maps.Map(document.getElementById("custom-mapping-google-map"), myOptions);

			    <?php foreach ($pins as $p) : ?>
			        var marker = new google.maps.Marker({
			            position: new google.maps.LatLng(<?php echo implode(', ', $p->google_coords); ?>),
			            map: cm_map,
			            <?php if ($pin_size) : ?>
			            icon: "<?php echo home_url() . $pin; ?>",
			            <?php endif; ?>
			            draggable: false
			        });
			        google.maps.event.addListener(marker, 'click', function() {
			        	$('#cm-pin-id-<?php echo $p->ID; ?>').click();
			        });
			        google.maps.event.addListener(marker, 'mouseover', function(){
			        	var tooltip = $('#cm-map-pin-<?php echo $p->ID; ?>');
			        	tooltip.appendTo("body");
			        	tooltip.show().css({
			        		top: mouse.y - tooltip.height() - 20,
			        		left: mouse.x - 130
			        	});
			        });
			        google.maps.event.addListener(marker, 'mouseout', function(){
			        	var tooltip = $('#cm-map-pin-<?php echo $p->ID; ?>');
			        	tooltip.hide();
			        });
			    <?php endforeach; ?>
			})
		})(jQuery);
		</script>
		<?php $scripts[] = ob_get_clean(); ?>
	<?php endif; ?>
	</div>

	<?php ob_start(); ?>
	<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('a.cm-pin').not('[href=#]').fancybox({
				type: 'ajax',
				width: 800,
				padding: 0,
				overlayColor: "#000",
				overlayOpacity: 0.56,
				onStart: function() {
					$('#fancybox-wrap').addClass('cm-override-fancybox');
				},
				onComplete: function(){
					$('.cm-pin-popup .gallery .list li a').click(function() {
						var index = $('.cm-pin-popup .gallery .list li a').index(this);
						$('.cm-pin-popup .gallery .holder .item').hide();
						$('.cm-pin-popup .gallery .holder .item:eq(' + index.toString() + ')').show();
						$('.cm-pin-popup .caption').html($(this).next().html());
						return false;
					});

					$('.cm-pin-popup .gallery .list li a:first').click();

					if ($('.popup-slider .list li').length > 5) {
						$('.popup-slider .list').jcarousel({
					        scroll: 1
					    });
					}
				},
				onClosed: function() {
					$('#fancybox-wrap').removeClass('cm-override-fancybox');
				}
			});
		})
	})(jQuery);
	</script>
	<?php $scripts[] = ob_get_clean() ?>
<?php endif; ?>