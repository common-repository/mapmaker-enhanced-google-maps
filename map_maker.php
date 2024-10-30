<?php
/*
Plugin Name: MapMaker
Plugin URI: http://scissorbox.com/mapmaker/
Description: Need more features like unlimited maps, advanced image mode, bulk uploading of locations and more?  <a href="http://www.mapifypro.com" target="_blank">Get the Pro Version</a>
Version: 1.0
Author: Josh Sears
Author URI: http://www.MapifyPro.com
License: 
*/

define('MAPIFYIT_PLUGIN_FILE', __FILE__);

include_once('lib/video-functions.php');

add_action('init', 'cm_plugin_init');
function cm_plugin_init() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('fancybox', plugins_url('resources/fancybox/jquery.fancybox-1.3.4.pack.js', MAPIFYIT_PLUGIN_FILE));
	wp_enqueue_script('jcarousel', plugins_url('resources/jcarousel/lib/jquery.jcarousel.min.js', MAPIFYIT_PLUGIN_FILE));
	wp_enqueue_style('fancybox-css', plugins_url('resources/fancybox/jquery.fancybox-1.3.4.css', MAPIFYIT_PLUGIN_FILE));
	// wp_enqueue_style('jcarousel-css', plugins_url('resources/jcarousel/style.css', MAPIFYIT_PLUGIN_FILE));
}

add_action('admin_menu', 'cm_plugin_admin_init');
function cm_plugin_admin_init() {
	add_menu_page('MapMaker', 'MapMaker', 'manage_options', 'custom-mapping', 'cm_admin_page');
	wp_enqueue_style('cm-colorpicker-css', plugins_url('resources/colorpicker/css/colorpicker.css' , MAPIFYIT_PLUGIN_FILE));
	wp_enqueue_style('cm-admin-css', plugins_url('css/admin.css' , MAPIFYIT_PLUGIN_FILE));
	wp_enqueue_script('cm-colorpicker', plugins_url('resources/colorpicker/js/colorpicker.js', MAPIFYIT_PLUGIN_FILE));
	wp_enqueue_script('cm-admin', plugins_url('admin/js/admin.js', MAPIFYIT_PLUGIN_FILE));
}

function cm_admin_page() {
	include_once('admin/page.main.php');
}

add_action('wp_loaded', 'cm_attach_custom_fields', 1000);
function cm_attach_custom_fields() {
	include_once('enhanced-custom-fields/enhanced-custom-fields.php');
	include_once('options/cm-custom-fields.php');
	include_once('options/cm-post-types.php');
}

function cm_shortcode_custom_mapping($atts, $content) {
	extract( shortcode_atts( array(
		'width'=>500,
		'height'=>300,
	), $atts));
	$width = intval($width);
	$width = ($width < 1) ? 500 : $width;
	$height = intval($height);
	$height = ($height < 1) ? 300 : $height;

	$scripts = array();
	ob_start();
	include_once('templates/map.php');
	define('CUSTOM_MAPPING_SCRIPTS', implode("\n", $scripts));
	$cnt = ob_get_clean();
	$cnt = preg_replace('~>\s*<~s', '><', $cnt);
	$cnt = preg_replace('~\s*<br \/>\s*~i', '<br />', $cnt);
	return $cnt;
}
add_shortcode('custom-mapping', 'cm_shortcode_custom_mapping');

function cm_add_scripts() {
	if (defined('CUSTOM_MAPPING_SCRIPTS')) {
		echo CUSTOM_MAPPING_SCRIPTS;
	}
}
add_action('wp_footer', 'cm_add_scripts');

function cm_filter_single_template($template) {
	global $post;
	if ($post->post_type == 'map-location') {
		return dirname(MAPIFYIT_PLUGIN_FILE) . '/templates/single-map-location.php';
	}

	return $template;
}
add_filter('single_template', 'cm_filter_single_template');

function cm_get_thumb($src, $w, $h, $zc = '1') {
	$src = str_replace(home_url('/'), '', $src);
	return plugins_url('lib/timthumb.php', MAPIFYIT_PLUGIN_FILE) . '?src=' . $src . '&amp;w=' . $w . '&amp;h=' . $h . '&amp;zc=' . $zc;
}

function get_file_real_path($path) {
	$dirs = wp_upload_dir();
	$url = $path;
	if (!stristr($path, home_url())) {
		$url = home_url() . $path;
	}
	$url = str_replace($dirs['baseurl'], $dirs['basedir'], $url);
	$url = str_replace('\\', DIRECTORY_SEPARATOR, $url);
	$url = str_replace('/', DIRECTORY_SEPARATOR, $url);
	return $url;
}

function cm_show_pro_notice($plugin_meta, $plugin_file, $plugin_data, $status) {
	if ($plugin_file == 'mapify_basic/map_marker.php') {
		$plugin_meta[] = '<a href="http://www.mapifypro.com/" target="_blank">Upgrade to MapifyPro for more features.</a>';
	}
	return $plugin_meta;
}
add_action('plugin_row_meta', 'cm_show_pro_notice', 10, 4);