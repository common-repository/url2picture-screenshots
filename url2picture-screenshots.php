<?php
/*
Plugin Name: URL2Picture Screenshots
Plugin URI: http://www.url2picture.com/Home/Plugins
Description: This plugins allows to easily embed thumbnails or full-page screenshots taken in all major browsers of any website into your WordPress site. 
Version: 2.0
Author: Daniel Herken
Author URI: http://www.url2picture.com
License: GPLv2
*/
?>

<?php
/*  Copyright 2014  Daniel Herken  (email : info@url2picture.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?php 

define('CONTENT_DIR', dirname(dirname(dirname(__FILE__))));
define('SCREENSHOT_DIR', WP_CONTENT_DIR . '/url2picture');
define('SCREENSHOT_URL', '/wp-content/url2picture');

// *** Settings Section *** //

// Add settings menu
add_action('admin_menu', 'url2picture_plugin_menu');

function url2picture_plugin_menu() {
	add_options_page('URL2Picture Plugin Options', 'URL2Picture', 'manage_options', 'url2picture-unique-identifier', 'url2picture_plugin_options' );
	
	if ( is_admin() ){ // admin actions
		add_action( 'admin_init', 'register_url2picture_settings' );
	} 	
}

function register_url2picture_settings() { // whitelist options
	register_setting( 'url2picture-settings', 'url2picture-settings', 'validate_settings' );
	add_settings_section('url2picture_plugin_main', 'URL2Picture Plugin Settings', 'url2picture_settings_section', 'url2picture-unique-identifier');
	add_settings_field('url2picture_plugin_apikey', 'API Key', 'url2picture_apikey_settingsfield', 'url2picture-unique-identifier', 'url2picture_plugin_main');
	add_settings_field('url2picture_plugin_secret', 'Secret', 'url2picture_secret_settingsfield', 'url2picture-unique-identifier', 'url2picture_plugin_main');
	add_settings_field('url2picture_plugin_cache', 'Cache', 'url2picture_cache_settingsfield', 'url2picture-unique-identifier', 'url2picture_plugin_main');
}

function url2picture_apikey_settingsfield() {
	$options = get_option('url2picture-settings');
	echo "<input id='apikey' name='url2picture-settings[apikey]' size='50' type='text' value='{$options['apikey']}' />";
	echo "<p class='description'>You can find your API-Key on <a href='http://www.url2picture.com'>URL2Picture.com</a>. You need to <a href='http://url2picture.com/Account/LogOn'>login</a> or <a href='http://url2picture.com/Account/Register'>signup</a> first.</p>";
}

function url2picture_secret_settingsfield() {
	$options = get_option('url2picture-settings');
	echo "<input id='secret' name='url2picture-settings[secret]' size='50' type='text' value='{$options['secret']}' />";
	echo "<p class='description'>You can find your Secret on <a href='http://www.url2picture.com'>URL2Picture.com</a>. You need to <a href='http://url2picture.com/Account/LogOn'>login</a> or <a href='http://url2picture.com/Account/Register'>signup</a> first.</p>";
}

function url2picture_cache_settingsfield() {
	$options = get_option('url2picture-settings');
	if ($options['cache'] == true)
	{
		echo "<input id='cache' name='url2picture-settings[cache]' type='checkbox' checked='checked' />";
	} 
	else 
	{
		echo "<input id='cache' name='url2picture-settings[cache]' type='checkbox'/>";
	}
	echo "<p class='description'>Should Wordpress cache the screenshots on the local server?</p>";
}

function url2picture_settings_section() {
	echo ""; // No second headline here
}

function validate_settings($input) {
	$newinput['apikey'] = trim($input['apikey']);
	$newinput['secret'] = trim($input['secret']);
	$newinput['cache'] = trim($input['cache']);
	return $newinput;
}

function url2picture_plugin_options() {
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page'));		
	}
	echo '<div class="wrap">';
	echo screen_icon();
	echo "<h2>URL2Picture Plugin Settings</h2>";
	echo '<form method="post" action="options.php">';
	settings_fields( 'url2picture-settings' );
	do_settings_sections( 'url2picture-unique-identifier' );
	submit_button();
	echo "</form></div>	";
}

/*** Get Screenshot section ***/

function getScreenshot($atts, $content = null) {
	$Url = urlencode(trim($content));

	extract(shortcode_atts(array(
		"width" => '1024',
		"height" => '768',
		"thumbnail_width" => '0',
		"thumbnail_height" => '0',
		"crop_width" => '0',
		"crop_height" => '0',
		"delay" => '0',
		"browser" => 'CHROME32'
	), $atts));
	
	$path = SCREENSHOT_DIR . '/' . md5($Url . '+' . $width . '+' . $height . '+' . $thumbnail_width . '+' . $thumbnail_height . '+' . $crop_width . '+' . $crop_height . '+' . $delay . '+' . $browser) . '.png';
	$image_url = get_bloginfo('url') . SCREENSHOT_URL . '/' . md5($Url . '+' . $width . '+' . $height . '+' . $thumbnail_width . '+' . $thumbnail_height . '+' . $crop_width . '+' . $crop_height . '+' . $delay . '+' . $browser) . '.png';
	
	$settings = get_option('url2picture-settings');
	$Cache = $settings['cache'];
	
	$ApiKey = $settings['apikey'];
	$Secret = $settings['secret'];		
	$Url = "apikey=" . $ApiKey . "&url=" . $Url . "&width=" . esc_attr($width) . "&height=" . esc_attr($height) . "&thumbnail_width=" . esc_attr($thumbnail_width) . "&thumbnail_height=" . esc_attr($thumbnail_height) . "&crop_width=" . esc_attr($crop_width) . "&crop_height=" . esc_attr($crop_height) . "&delay=" . esc_attr($delay) . "&browser=" . esc_attr($browser);	
	$Token = md5($Url.$Secret);	
	$Src = "http://www.url2picture.com/Picture/Png?".$Url."&token=".$Token;

	// If file not exists and not force download and save file to disk
	if (!file_exists($path) || $Cache == 'on') {		
		if (!is_dir(SCREENSHOT_DIR)) {
			mkdir(SCREENSHOT_DIR);
		}		
		
		// Download Image and save to disk
		$ch = curl_init($Src);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$rawdata = curl_exec($ch);
		curl_close ($ch);
		if (file_exists($path)) {
			unlink($path);
		}
		$fp = fopen($path,'w+');
		fwrite($fp, $rawdata);
		fclose($fp);		
	} 
	else 
	{
		$image_url = $Src;
	}
	
	return "<img src='" . $image_url ."'/>";	
}

add_shortcode("url2picture", "getScreenshot");
?>