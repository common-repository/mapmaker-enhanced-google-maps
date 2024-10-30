<?php
$panel = new ECF_Panel('map-location-options', 'Options', 'map-location', 'normal', 'high');
$panel->add_fields(array(
	ECF_Field::factory('select', 'map_location_tooltip_enabled', 'Enable Popup')
		->add_options(array( 'yes' => 'Yes', 'no' => 'No' )),
	ECF_Field::factory('textarea', 'map_location_tooltip', 'Tooltip')->help_text('You may use basic html text formatting, such as &lt;br&gt;, &lt;i&gt;, etc.'),
	ECF_Field::factory('textarea', 'map_location_video_embed', 'Video Embed Code')->help_text('Use Vimeo or YouTube iframe video embed codes.'),
	ECF_Field::factory('textarea', 'map_location_video_description', 'Video Description'),

	ECF_Field::factory('media', 'map_location_gallery_image', 'Images (for location\'s gallery)')
		->multiply(),

	ECF_Field::factory('map', 'map_location_google_location', 'Location (Google Maps)'),
	ECF_Field::factory('plantmap', 'map_location_image_location', 'Location (Image)'),
));
?>