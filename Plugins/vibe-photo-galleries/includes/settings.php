<?php
if (!defined('ABSPATH')) { exit; }

function vibe_photo_register_google_api_settings() {
	// Register the setting
	register_setting('media', 'vibe_photo_google_api_key', array(
		'type' => 'string',
		'sanitize_callback' => 'vibe_photo_galleries_sanitize_api_key',
		'default' => ''
	));

	// Add settings section
	add_settings_section(
		'vibe_photo_google_api_section',
		__('Vibe Photo - Google Cloud Settings', 'vibe-photo-galleries'),
		'vibe_photo_google_api_section_callback',
		'media'
	);

	// Add settings field
	add_settings_field(
		'vibe_photo_google_api_key',
		__('Google Cloud API Key', 'vibe-photo-galleries'),
		'vibe_photo_google_api_key_callback',
		'media',
		'vibe_photo_google_api_section'
	);
}
add_action('admin_init', 'vibe_photo_register_google_api_settings');

/**
 * Settings section callback
 */
function vibe_photo_google_api_section_callback() {
	echo '<p>' . __('Configure Google Cloud API for reverse geocoding (converting GPS coordinates to location names).', 'vibe-photo-galleries') . '</p>';
	echo '<p>' . __('To get an API key:', 'vibe-photo-galleries') . '</p>';
	echo '<ol>';
	echo '<li>' . __('Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>', 'vibe-photo-galleries') . '</li>';
	echo '<li>' . __('Create a project or select an existing one', 'vibe-photo-galleries') . '</li>';
	echo '<li>' . __('Enable the "Geocoding API"', 'vibe-photo-galleries') . '</li>';
	echo '<li>' . __('Go to Credentials and create an API key', 'vibe-photo-galleries') . '</li>';
	echo '<li>' . __('Copy the API key and paste it below', 'vibe-photo-galleries') . '</li>';
	echo '</ol>';
}

/**
 * API key field callback
 */
function vibe_photo_google_api_key_callback() {
	$api_key = get_option('vibe_photo_google_api_key', '');
	echo '<input autocomplete="off" type="password" name="vibe_photo_google_api_key" value="' . esc_attr($api_key) . '" class="regular-text" />';
	echo '<p class="description">' . __('Your Google Cloud API key for Geocoding API. Leave empty to disable reverse geocoding.', 'vibe-photo-galleries') . '</p>';
}


/** Settings API may receive malformed array inputs from a crafted form. */
function vibe_photo_galleries_sanitize_api_key($value) {
    return is_string($value) ? sanitize_text_field($value) : '';
}
