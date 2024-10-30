<?php  
register_post_type('map-location', array(
	'labels' => array(
		'name'	 => 'Map Locations',
		'singular_name' => 'Map Location',
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Add new Map Location' ),
		'view_item' => 'View Map Location',
		'edit_item' => 'Edit Map Location',
	    'new_item' => __('New Map Location'),
	    'view_item' => __('View Map Location'),
	    'search_items' => __('Search Map Locations'),
	    'not_found' =>  __('No Map Locations found'),
	    'not_found_in_trash' => __('No Map Locations found in Trash'),
	),
	'public' => true,
	'exclude_from_search' => true,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => true,
	'_edit_link' =>  'post.php?post=%d',
	'rewrite' => false,
	'query_var' => true,
	'supports' => array('title', 'editor'),
));
?>