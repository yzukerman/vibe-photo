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
		esc_html__('Vibe Photo - Google Cloud Settings', 'vibe-photo-galleries'),
		'vibe_photo_google_api_section_callback',
		'media'
	);

	// Add settings field
	add_settings_field(
		'vibe_photo_google_api_key',
		esc_html__('Google Cloud API Key', 'vibe-photo-galleries'),
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
	echo '<p>' . esc_html__('Configure Google Cloud API for reverse geocoding (converting GPS coordinates to location names).', 'vibe-photo-galleries') . '</p>';
	echo '<p>' . esc_html__('To get an API key:', 'vibe-photo-galleries') . '</p>';
	echo '<ol>';
	echo '<li><a href="' . esc_url('https://console.cloud.google.com/') . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Open Google Cloud Console', 'vibe-photo-galleries') . '</a></li>';
	echo '<li>' . esc_html__('Create a project or select an existing one', 'vibe-photo-galleries') . '</li>';
	echo '<li>' . esc_html__('Enable the "Geocoding API"', 'vibe-photo-galleries') . '</li>';
	echo '<li>' . esc_html__('Go to Credentials and create an API key', 'vibe-photo-galleries') . '</li>';
	echo '<li>' . esc_html__('Copy the API key and paste it below', 'vibe-photo-galleries') . '</li>';
	echo '</ol>';
}

/**
 * API key field callback
 */
function vibe_photo_google_api_key_callback() {
	$api_key = get_option('vibe_photo_google_api_key', '');
	echo '<input autocomplete="off" type="password" name="vibe_photo_google_api_key" value="' . esc_attr($api_key) . '" class="regular-text" />';
	echo '<p class="description">' . esc_html__('Your Google Cloud API key for Geocoding API. Leave empty to disable reverse geocoding.', 'vibe-photo-galleries') . '</p>';
}


/** Settings API may receive malformed array inputs from a crafted form. */
function vibe_photo_galleries_sanitize_api_key($value) {
    return is_string($value) ? sanitize_text_field($value) : '';
}
