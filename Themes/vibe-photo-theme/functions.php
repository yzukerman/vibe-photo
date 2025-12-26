<?php

/**
 * Vibe Photo Theme Functions and Definitions
 * 
 * @package VibePhoto
 * @version 1.0.0
 * @author Yuval Zukerman
 * 
 * This file contains WordPress-specific functions. The lint warnings shown
 * in this file are expected when viewed outside of WordPress environment
 * and will not affect the theme's functionality.
 * 
 * All WordPress functions (add_theme_support, wp_enqueue_style, etc.) are
 * available when this theme runs within WordPress.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

// Early return if WordPress core is not loaded
if (!function_exists('add_action')) {
	return;
}

/**
 * Define theme constants
 */
if (!defined('VIBE_PHOTO_VERSION')) {
	define('VIBE_PHOTO_VERSION', '1.0.0');
}

// Only define template directory constants if WordPress functions exist
if (function_exists('get_template_directory')) {
	if (!defined('VIBE_PHOTO_THEME_DIR')) {
		define('VIBE_PHOTO_THEME_DIR', get_template_directory());
	}
}

if (function_exists('get_template_directory_uri')) {
	if (!defined('VIBE_PHOTO_THEME_URL')) {
		define('VIBE_PHOTO_THEME_URL', get_template_directory_uri());
	}
}

/**
 * Theme setup
 * 
 * Note: The following WordPress functions will show lint warnings when viewed
 * outside WordPress environment. This is normal and expected:
 * - add_theme_support() - WordPress theme feature support
 * - esc_html__() - WordPress translation function
 * - register_nav_menus() - WordPress menu registration
 * - add_image_size() - WordPress image size registration
 * - wp_enqueue_style/script() - WordPress asset enqueuing
 * - _x(), __() - WordPress internationalization functions
 * - All other wp_* functions are WordPress core functions
 */
function vibe_photo_setup() {
	// Add theme support for various features
	if (function_exists('add_theme_support')) {
		add_theme_support('post-thumbnails');
		add_theme_support('html5', array(
			'comment-list',
			'comment-form',
			'search-form',
			'gallery',
			'caption',
		));
		add_theme_support('title-tag');
		add_theme_support('automatic-feed-links');
		add_theme_support('responsive-embeds');

		// Add theme support for wide and full align images
		add_theme_support('align-wide');

		// Add theme support for editor styles
		add_theme_support('editor-styles');

		// Add theme support for custom line height
		add_theme_support('custom-line-height');

		// Add theme support for custom spacing
		add_theme_support('custom-spacing');
	}

	// Register navigation menus
	if (function_exists('register_nav_menus')) {
		register_nav_menus(array(
			'primary' => esc_html__('Primary Menu', 'vibe-photo-theme'),
			'footer' => esc_html__('Footer Menu', 'vibe-photo-theme'),
		));
	}

	// Add custom image sizes for photography
	if (function_exists('add_image_size')) {
		add_image_size('gallery-thumb', 300, 300, true);
		add_image_size('gallery-medium', 600, 400, true);
		add_image_size('gallery-large', 1200, 800, false);
		add_image_size('gallery-full', 1920, 1280, false);
	}
}
if (function_exists('add_action')) {
	add_action('after_setup_theme', 'vibe_photo_setup');
}

/**
 * Enqueue scripts and styles
 */
function vibe_photo_scripts() {
	// Only enqueue if WordPress functions are available
	if (!function_exists('wp_enqueue_style') || !function_exists('wp_enqueue_script')) {
		return;
	}

	// Enqueue Foundation CSS from CDN
	wp_enqueue_style('foundation-css', 'https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css', array(), '6.8.1');

	// Enqueue custom theme styles (after Foundation)
	wp_enqueue_style('vibe-photo-style', get_stylesheet_uri(), array('foundation-css'), '1.0.0');

	// Enqueue Foundation JavaScript from CDN
	wp_enqueue_script('foundation-js', 'https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/js/foundation.min.js', array('jquery'), '6.8.1', true);

	// Enqueue Masonry library from CDN
	// TEMPORARILY DISABLED - wp_enqueue_script('masonry-js', 'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js', array('jquery'), '4.2.2', true);

	// Enqueue imagesLoaded library from CDN
	// TEMPORARILY DISABLED - wp_enqueue_script('imagesloaded-js', 'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js', array('jquery'), '5.0.0', true);

	// Add custom JavaScript for photo gallery
	// TEMPORARILY DISABLED - wp_enqueue_script('vibe-photo-gallery', get_template_directory_uri() . '/assets/js/gallery-masonry.js', array('jquery', 'foundation-js', 'masonry-js', 'imagesloaded-js'), '1.0.0', true);

	// Add lightbox functionality
	wp_enqueue_script('vibe-photo-lightbox', get_template_directory_uri() . '/assets/js/lightbox.js', array('jquery', 'foundation-js'), '1.0.0', true);

	// Localize script for AJAX
	wp_localize_script('vibe-photo-lightbox', 'vibePhotoAjax', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('vibe_photo_nonce')
	));

	// Add responsive navigation
	wp_enqueue_script('vibe-photo-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array('jquery', 'foundation-js'), '1.0.0', true);

	// Initialize Foundation
	if (function_exists('wp_add_inline_script')) {
		wp_add_inline_script('foundation-js', '
            jQuery(document).ready(function($) {
                $(document).foundation();
            });
        ');
	}
}
if (function_exists('add_action')) {
	add_action('wp_enqueue_scripts', 'vibe_photo_scripts');
}

/**
 * Enhance WordPress Gallery blocks to use our lightbox
 */
function vibe_photo_enhance_gallery_blocks($content) {
	// Only process if we have gallery blocks
	if (strpos($content, 'wp-block-gallery') === false) {
		return $content;
	}

	// Add lightbox attributes to gallery images
	$content = preg_replace_callback(
		'/<figure class="[^"]*wp-block-gallery[^"]*"[^>]*>(.*?)<\/figure>/s',
		function ($matches) {
			$gallery_content = $matches[0];

			// Add our lightbox class to the gallery container
			$gallery_content = str_replace(
				'wp-block-gallery',
				'wp-block-gallery vibe-lightbox-gallery',
				$gallery_content
			);

			// Enhance each image in the gallery - handle complex img tags with multiple attributes
			$gallery_content = preg_replace_callback(
				'/<img\s+[^>]*src=["\']([^"\']+)["\'][^>]*>/i',
				function ($img_matches) {
					$img_tag = $img_matches[0];
					$img_src = $img_matches[1];

					// Skip if already has lightbox attributes
					if (strpos($img_tag, 'lightbox-image') !== false) {
						return $img_tag;
					}

					// Clean up protocol-relative URLs
					if (strpos($img_src, '//') === 0) {
						$img_src = 'http:' . $img_src;
					}

					// Try to get the full size image URL by removing size suffix
					$full_size_src = $img_src;
					if (preg_match('/-(\d+)x(\d+)\.(jpg|jpeg|png|gif|webp)$/i', $img_src, $size_matches)) {
						$full_size_src = preg_replace('/-\d+x\d+\.(' . $size_matches[3] . ')$/i', '.$1', $img_src);
					}

					// Add lightbox attributes before the closing >
					$lightbox_attrs = sprintf(
						' data-lightbox="gallery" data-full-src="%s" data-src="%s" class="lightbox-image"',
						htmlspecialchars($full_size_src, ENT_QUOTES),
						htmlspecialchars($img_src, ENT_QUOTES)
					);

					// Insert lightbox attributes before the closing >
					$enhanced_img = str_replace('>', $lightbox_attrs . '>', $img_tag);

					return $enhanced_img;
				},
				$gallery_content
			);

			return $gallery_content;
		},
		$content
	);

	return $content;
}

/**
 * Also enhance classic gallery shortcodes
 */
function vibe_photo_enhance_gallery_shortcode($content) {
	// Look for gallery shortcode output
	if (strpos($content, 'gallery-') === false) {
		return $content;
	}

	// Add lightbox attributes to gallery images in shortcode galleries
	$content = preg_replace_callback(
		'/<div[^>]*class="[^"]*gallery[^"]*"[^>]*>(.*?)<\/div>/s',
		function ($matches) {
			$gallery_content = $matches[0];

			// Add our lightbox class
			$gallery_content = str_replace('class="', 'class="vibe-lightbox-gallery ', $gallery_content);

			// Enhance images within gallery items
			$gallery_content = preg_replace_callback(
				'/<a[^>]*href="([^"]+)"[^>]*><img([^>]+)src="([^"]+)"([^>]*)><\/a>/i',
				function ($link_matches) {
					$full_src = $link_matches[1];
					$img_attrs = $link_matches[2] . $link_matches[4];
					$thumb_src = $link_matches[3];

					// Create lightbox-enabled image (remove the link wrapper)
					return sprintf(
						'<img %s src="%s" data-lightbox="gallery" data-full-src="%s" data-src="%s" class="lightbox-image" %s>',
						$link_matches[2],
						$thumb_src,
						esc_attr($full_src),
						esc_attr($thumb_src),
						$link_matches[4]
					);
				},
				$gallery_content
			);

			return $gallery_content;
		},
		$content
	);

	return $content;
}

if (function_exists('add_filter')) {
	// Apply to all post content
	add_filter('the_content', 'vibe_photo_enhance_gallery_blocks', 20);
	add_filter('the_content', 'vibe_photo_enhance_gallery_shortcode', 21);
}

/**
 * Remove default "Sample Page" from navigation menus
 */
function vibe_photo_filter_menu_items($items, $args) {
	if (!is_array($items)) {
		return $items;
	}

	foreach ($items as $key => $item) {
		// Remove "Sample Page" from menus - check multiple properties
		if (
			stripos($item->title, 'sample page') !== false ||
			(isset($item->post_title) && stripos($item->post_title, 'sample page') !== false) ||
			(isset($item->url) && stripos($item->url, 'sample-page') !== false) ||
			(isset($item->object) && $item->object === 'page' && stripos($item->title, 'sample') !== false)
		) {
			unset($items[$key]);
		}

		// Also remove "Hello world!" default post link if it appears
		if (stripos($item->title, 'hello world') !== false) {
			unset($items[$key]);
		}

		// Remove Privacy Policy from primary menu (header)
		if (isset($args->theme_location) && $args->theme_location === 'primary') {
			if (
				stripos($item->title, 'privacy policy') !== false ||
				stripos($item->title, 'privacy') !== false ||
				(isset($item->url) && stripos($item->url, 'privacy-policy') !== false) ||
				(isset($item->type) && $item->type === 'privacy-policy')
			) {
				unset($items[$key]);
			}
		}
	}
	return $items;
}

// Add multiple filters to catch different menu contexts
if (function_exists('add_filter')) {
	add_filter('wp_nav_menu_objects', 'vibe_photo_filter_menu_items', 10, 2);
	add_filter('wp_get_nav_menu_items', 'vibe_photo_filter_menu_items', 10, 2);
	add_filter('wp_page_menu', 'vibe_photo_filter_page_menu', 10, 2);
	add_filter('wp_list_pages', 'vibe_photo_filter_list_pages', 10, 2);
}

/**
 * Filter wp_list_pages to remove privacy policy
 */
function vibe_photo_filter_list_pages($output, $args) {
	// Remove privacy policy from wp_list_pages output
	$output = preg_replace('/<li[^>]*page-item-[0-9]+[^>]*>\s*<a[^>]*privacy-policy[^>]*>.*?<\/a>\s*<\/li>/i', '', $output);
	$output = preg_replace('/<li[^>]*>\s*<a[^>]*>Privacy Policy<\/a>\s*<\/li>/i', '', $output);
	return $output;
}

/**
 * Filter wp_page_menu fallback to remove sample page and privacy policy
 */
function vibe_photo_filter_page_menu($menu, $args) {
	// Remove sample page from fallback page menu
	$menu = preg_replace('/<li[^>]*>\s*<a[^>]*sample-page[^>]*>.*?<\/a>\s*<\/li>/i', '', $menu);
	$menu = preg_replace('/<li[^>]*>\s*<a[^>]*>.*?sample.*?page.*?<\/a>\s*<\/li>/i', '', $menu);
	// Remove privacy policy from fallback page menu
	$menu = preg_replace('/<li[^>]*>\s*<a[^>]*privacy-policy[^>]*>.*?<\/a>\s*<\/li>/i', '', $menu);
	$menu = preg_replace('/<li[^>]*>\s*<a[^>]*>.*?Privacy Policy.*?<\/a>\s*<\/li>/i', '', $menu);
	return $menu;
}

/**
 * Remove gallery shortcode processing to prevent conflicts
 */
function vibe_photo_disable_gallery_shortcode() {
	remove_shortcode('gallery');
}
add_action('init', 'vibe_photo_disable_gallery_shortcode');

/**
 * Modify the main query to ensure home page shows regular posts
 */
function vibe_photo_modify_main_query($query) {
	if (!is_admin() && $query->is_main_query()) {
		if (is_home() || is_front_page()) {
			// Ensure we're showing regular posts on the home/front page
			$query->set('post_type', array('post'));
			$query->set('posts_per_page', 10);
			$query->set('post_status', 'publish');
		}
	}
}
add_action('pre_get_posts', 'vibe_photo_modify_main_query');

/**
 * Create sample posts if none exist (for testing)
 */
function vibe_photo_create_sample_posts() {
	// Check if we have any published posts
	$posts = get_posts(array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'numberposts' => 1
	));

	// If no posts exist, create some sample ones
	if (empty($posts)) {
		$sample_posts = array(
			array(
				'title' => 'Welcome to Your Photography Site',
				'content' => 'This is your first post. You can edit or delete it, then start creating your own content!'
			),
			array(
				'title' => 'Photography Tips for Beginners',
				'content' => 'Here are some essential photography tips to help you get started with your photography journey.'
			),
			array(
				'title' => 'Latest Photo Gallery Updates',
				'content' => 'Check out our latest photo galleries featuring stunning landscapes and portraits.'
			)
		);

		foreach ($sample_posts as $sample_post) {
			wp_insert_post(array(
				'post_title' => $sample_post['title'],
				'post_content' => $sample_post['content'],
				'post_status' => 'publish',
				'post_type' => 'post',
				'post_author' => 1
			));
		}
	}
}
add_action('after_switch_theme', 'vibe_photo_create_sample_posts');

/**
 * Completely prevent gallery shortcode processing on gallery pages
 */
function vibe_photo_filter_content_on_gallery_pages($content) {
	if (is_singular('photo_gallery')) {
		// Remove all gallery shortcodes from content to prevent duplication
		$content = preg_replace('/\[gallery[^\]]*\]/', '', $content);
		$content = preg_replace('/\[\/gallery\]/', '', $content);
	}
	return $content;
}
add_filter('the_content', 'vibe_photo_filter_content_on_gallery_pages', 1); // Run early

/**
 * Debug function to track what's modifying gallery content
 */
function vibe_photo_debug_content_filters() {
	if (is_singular('photo_gallery')) {
		// Add debugging to track content modifications
		add_filter('the_content', function ($content) {
			error_log('=== VIBE PHOTO DEBUG: the_content filter called ===');
			error_log('Original content length: ' . strlen($content));
			error_log('Content preview: ' . substr($content, 0, 200));
			return $content;
		}, 999);

		// Check for any gallery-related filters
		add_action('wp_footer', function () {
			echo '<!-- DEBUG: Checking for gallery filters -->';
			global $wp_filter;
			if (isset($wp_filter['the_content'])) {
				echo '<!-- the_content filters: ';
				foreach ($wp_filter['the_content']->callbacks as $priority => $callbacks) {
					foreach ($callbacks as $callback) {
						if (is_array($callback['function'])) {
							echo $priority . ': ' . get_class($callback['function'][0]) . '::' . $callback['function'][1] . ', ';
						} else if (is_string($callback['function'])) {
							echo $priority . ': ' . $callback['function'] . ', ';
						}
					}
				}
				echo '-->';
			}
		});
	}
}
add_action('template_redirect', 'vibe_photo_debug_content_filters');

/**
 * Register Google Cloud API settings in Media Settings
 */
function vibe_photo_register_google_api_settings() {
	// Register the setting
	register_setting('media', 'vibe_photo_google_api_key', array(
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => ''
	));

	// Add settings section
	add_settings_section(
		'vibe_photo_google_api_section',
		__('Vibe Photo - Google Cloud Settings', 'vibe-photo-theme'),
		'vibe_photo_google_api_section_callback',
		'media'
	);

	// Add settings field
	add_settings_field(
		'vibe_photo_google_api_key',
		__('Google Cloud API Key', 'vibe-photo-theme'),
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
	echo '<p>' . __('Configure Google Cloud API for reverse geocoding (converting GPS coordinates to location names).', 'vibe-photo-theme') . '</p>';
	echo '<p>' . __('To get an API key:', 'vibe-photo-theme') . '</p>';
	echo '<ol>';
	echo '<li>' . __('Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>', 'vibe-photo-theme') . '</li>';
	echo '<li>' . __('Create a project or select an existing one', 'vibe-photo-theme') . '</li>';
	echo '<li>' . __('Enable the "Geocoding API"', 'vibe-photo-theme') . '</li>';
	echo '<li>' . __('Go to Credentials and create an API key', 'vibe-photo-theme') . '</li>';
	echo '<li>' . __('Copy the API key and paste it below', 'vibe-photo-theme') . '</li>';
	echo '</ol>';
}

/**
 * API key field callback
 */
function vibe_photo_google_api_key_callback() {
	$api_key = get_option('vibe_photo_google_api_key', '');
	echo '<input type="text" name="vibe_photo_google_api_key" value="' . esc_attr($api_key) . '" class="regular-text" />';
	echo '<p class="description">' . __('Your Google Cloud API key for Geocoding API. Leave empty to disable reverse geocoding.', 'vibe-photo-theme') . '</p>';
}

/**
 * Reverse geocode coordinates to location name using Google Geocoding API
 */
function vibe_photo_reverse_geocode($lat, $lon, $attachment_id = null) {
	// Get API key from settings
	$api_key = get_option('vibe_photo_google_api_key', '');

	// If no API key, return null
	if (empty($api_key)) {
		return null;
	}

	// Check if we have cached location for this attachment
	if ($attachment_id) {
		$cached_location = get_post_meta($attachment_id, '_vibe_photo_location', true);
		if (!empty($cached_location)) {
			return $cached_location;
		}
	}

	// Build API request URL
	$url = sprintf(
		'https://maps.googleapis.com/maps/api/geocode/json?latlng=%s,%s&key=%s',
		$lat,
		$lon,
		$api_key
	);

	// Make API request
	$response = wp_remote_get($url, array(
		'timeout' => 10,
		'sslverify' => true
	));

	// Check for errors
	if (is_wp_error($response)) {
		error_log('VIBE PHOTO: Geocoding API error: ' . $response->get_error_message());
		return null;
	}

	// Parse response
	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);

	// Check if we got results
	if (empty($data['results']) || $data['status'] !== 'OK') {
		error_log('VIBE PHOTO: Geocoding API returned no results. Status: ' . ($data['status'] ?? 'unknown'));
		return null;
	}

	// Extract location name
	// Try to get city, state/province, country format
	$location_parts = array();
	$address_components = $data['results'][0]['address_components'];

	$locality = null;
	$sublocality = null;
	$admin_area = null;
	$country = null;

	foreach ($address_components as $component) {
		// Skip Plus Code components
		if (in_array('plus_code', $component['types'])) {
			continue;
		}

		if (in_array('locality', $component['types'])) {
			$locality = $component['long_name'];
		}
		if (in_array('sublocality', $component['types']) || in_array('sublocality_level_1', $component['types'])) {
			$sublocality = $component['long_name'];
		}
		if (in_array('administrative_area_level_1', $component['types'])) {
			$admin_area = $component['short_name'];
		}
		if (in_array('administrative_area_level_2', $component['types']) && !$locality) {
			// Use level 2 admin area if no locality found
			if (!$locality) {
				$locality = $component['long_name'];
			}
		}
		if (in_array('country', $component['types'])) {
			$country = $component['long_name'];
		}
	}

	// Build location string
	if ($locality) {
		$location_parts[] = $locality;
	} elseif ($sublocality) {
		$location_parts[] = $sublocality;
	}
	if ($admin_area) {
		$location_parts[] = $admin_area;
	}
	if ($country) {
		$location_parts[] = $country;
	}

	// If we still have no parts, try to extract from formatted_address, but skip Plus Codes
	if (empty($location_parts)) {
		$formatted = $data['results'][0]['formatted_address'];
		// Check if it's a Plus Code (format: XXXX+XX)
		if (!preg_match('/^[A-Z0-9]{4,8}\+[A-Z0-9]{2,3}/', $formatted)) {
			$location_name = $formatted;
		} else {
			// If only Plus Code, try next result
			if (isset($data['results'][1])) {
				$formatted = $data['results'][1]['formatted_address'];
				if (!preg_match('/^[A-Z0-9]{4,8}\+[A-Z0-9]{2,3}/', $formatted)) {
					$location_name = $formatted;
				}
			}
		}
	} else {
		$location_name = implode(', ', $location_parts);
	}

	// If still empty, return null
	if (empty($location_name)) {
		error_log('VIBE PHOTO: Could not extract meaningful location from geocoding response');
		return null;
	}

	// Cache the result
	if ($attachment_id && !empty($location_name)) {
		update_post_meta($attachment_id, '_vibe_photo_location', $location_name);
	}

	return $location_name;
}

/**
 * AJAX handler for getting image EXIF data
 */
function vibe_photo_get_image_exif() {
	// Verify nonce
	if (!wp_verify_nonce($_POST['nonce'], 'vibe_photo_nonce')) {
		wp_send_json_error('Invalid nonce');
		return;
	}

	$image_url = esc_url_raw($_POST['image_url']);

	// Fix protocol-relative URLs
	if (strpos($image_url, '//') === 0) {
		$image_url = 'http:' . $image_url;
	}

	// Convert resized image URL to original URL for attachment lookup
	$original_url = $image_url;
	// Remove size dimensions (e.g., -683x1024.jpeg -> .jpeg)
	$original_url = preg_replace('/-\d+x\d+(\.[^.]+)$/', '$1', $original_url);

	// Try to get attachment ID from original URL
	$attachment_id = attachment_url_to_postid($original_url);

	$exif_data = array();
	if (!$attachment_id) {
		// If attachment_url_to_postid failed, try alternative methods to find the attachment
		global $wpdb;

		// Try multiple variations of the filename including -scaled versions
		$filename = basename($original_url);
		$filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
		$filename_ext = pathinfo($filename, PATHINFO_EXTENSION);

		// Try multiple variations of the filename
		$search_patterns = array(
			'%' . $filename,                                    // exact: DSCF7743.jpeg
			'%' . $filename_no_ext . '-scaled.' . $filename_ext, // scaled: DSCF7743-scaled.jpeg
			'%' . $filename_no_ext . '%.' . $filename_ext        // any variation: DSCF7743-*.jpeg
		);

		foreach ($search_patterns as $pattern) {
			$attachments_with_filename = $wpdb->get_results($wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} p 
				 LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
				 WHERE p.post_type = 'attachment' AND pm.meta_key = '_wp_attached_file' 
				 AND pm.meta_value LIKE %s 
				 LIMIT 1",
				$pattern
			));

			if ($attachments_with_filename) {
				$attachment_id = $attachments_with_filename[0]->ID;
				break; // Found one, stop searching
			}
		}
	}

	if (!$attachment_id) {
		// Still no attachment ID, work with file directly
		// If no attachment ID, try to work with the URL directly
		error_log('VIBE PHOTO: No attachment ID found for: ' . $image_url);
		$upload_dir = wp_get_upload_dir();

		// Try multiple methods to convert URL to file path
		$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image_url);

		// If that didn't work, try extracting just the relative path
		if (!file_exists($file_path)) {
			// Extract the path after /wp-content/uploads/
			if (preg_match('/\/wp-content\/uploads\/(.+)$/', $image_url, $matches)) {
				$relative_path = $matches[1];
				$file_path = $upload_dir['basedir'] . '/' . $relative_path;
			}
		}

		if (file_exists($file_path)) {
			// Get basic file info without WordPress metadata
			$image_size = getimagesize($file_path);
			if ($image_size) {
				$exif_data['size'] = $image_size[0] . ' × ' . $image_size[1] . ' pixels';
			}

			$file_size = filesize($file_path);
			if ($file_size) {
				$exif_data['file_size'] = size_format($file_size);
			}
		} else {
			wp_send_json_error(array(
				'message' => 'Image file not found'
			));
			return;
		}
	} else {
		// Get image metadata from WordPress
		$metadata = wp_get_attachment_metadata($attachment_id);
		$file_path = get_attached_file($attachment_id);

		// Debug logging
		error_log('VIBE PHOTO: Attachment ID: ' . $attachment_id);
		error_log('VIBE PHOTO: File path: ' . $file_path);
		error_log('VIBE PHOTO: File exists: ' . (file_exists($file_path) ? 'yes' : 'no'));

		// Check if file exists
		if (!file_exists($file_path)) {
			wp_send_json_error(array(
				'message' => 'Image file not found on server',
				'debug' => array(
					'attachment_id' => $attachment_id,
					'file_path' => $file_path
				)
			));
			return;
		}

		// Get WordPress image title and caption
		$post = get_post($attachment_id);
		if ($post) {
			// Get title (post_title)
			if (!empty($post->post_title)) {
				$exif_data['title'] = $post->post_title;
			}

			// Get caption (post_excerpt)  
			if (!empty($post->post_excerpt)) {
				$exif_data['caption'] = $post->post_excerpt;
			}

			// Get alt text (stored in meta)
			$alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
			if (!empty($alt_text)) {
				$exif_data['alt_text'] = $alt_text;
			}
		}

		// Get basic file info
		if ($metadata) {
			$exif_data['size'] = $metadata['width'] . ' × ' . $metadata['height'] . ' pixels';

			// Get file size
			$file_size = filesize($file_path);
			$exif_data['file_size'] = size_format($file_size);
		}
	}

	// Try to get EXIF data if available
	// Note: exif_read_data() only works with JPEG and TIFF formats, not WebP
	$file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
	$supported_formats = array('jpg', 'jpeg', 'tif', 'tiff');

	// Add debug info to response
	$exif_data['debug'] = array(
		'file_extension' => $file_extension,
		'exif_function_exists' => function_exists('exif_read_data'),
		'file_path' => $file_path,
		'is_supported_format' => in_array($file_extension, $supported_formats)
	);

	if (function_exists('exif_read_data') && file_exists($file_path) && in_array($file_extension, $supported_formats)) {
		// WordPress may have created a scaled version - try original first
		$original_path = $file_path;
		if (strpos($file_path, '-scaled.') !== false) {
			$original_path = str_replace('-scaled.', '.', $file_path);
			if (file_exists($original_path)) {
				$exif_data['debug']['trying_original'] = true;
				$file_path = $original_path;
			}
		}

		$exif = @exif_read_data($file_path);

		// Add to debug info
		$exif_data['debug']['exif_read_success'] = ($exif !== false);
		$exif_data['debug']['exif_keys_found'] = $exif ? count($exif) : 0;
		if ($exif) {
			$exif_data['debug']['exif_keys'] = array_keys($exif);
		}

		// Debug logging for troubleshooting
		if (!$exif) {
			error_log('VIBE PHOTO: Failed to read EXIF from: ' . $file_path);
			error_log('VIBE PHOTO: File exists: ' . (file_exists($file_path) ? 'yes' : 'no'));
			error_log('VIBE PHOTO: File readable: ' . (is_readable($file_path) ? 'yes' : 'no'));
			error_log('VIBE PHOTO: File size: ' . (file_exists($file_path) ? filesize($file_path) : 'N/A'));
		}

		if ($exif) {
			// Camera info
			if (isset($exif['Make']) && isset($exif['Model'])) {
				$exif_data['camera'] = trim($exif['Make'] . ' ' . $exif['Model']);
			}

			// Lens info - check multiple possible fields
			if (isset($exif['UndefinedTag:0xA434'])) {
				$exif_data['lens'] = $exif['UndefinedTag:0xA434'];
			} elseif (isset($exif['LensModel'])) {
				$exif_data['lens'] = $exif['LensModel'];
			} elseif (isset($exif['LensInfo'])) {
				$exif_data['lens'] = $exif['LensInfo'];
			} elseif (isset($exif['LensMake'])) {
				$lens_make = $exif['LensMake'];
				$lens_model = isset($exif['LensModel']) ? $exif['LensModel'] : '';
				$exif_data['lens'] = trim($lens_make . ' ' . $lens_model);
			}

			// Aperture
			if (isset($exif['COMPUTED']['ApertureFNumber'])) {
				$exif_data['aperture'] = $exif['COMPUTED']['ApertureFNumber'];
			} elseif (isset($exif['FNumber'])) {
				$aperture = explode('/', $exif['FNumber']);
				if (count($aperture) == 2 && $aperture[1] != 0) {
					$exif_data['aperture'] = 'f/' . round($aperture[0] / $aperture[1], 1);
				}
			} elseif (isset($exif['MaxApertureValue'])) {
				$aperture = explode('/', $exif['MaxApertureValue']);
				if (count($aperture) == 2 && $aperture[1] != 0) {
					$f_stop = pow(2, ($aperture[0] / $aperture[1]) / 2);
					$exif_data['aperture'] = 'f/' . round($f_stop, 1);
				}
			}

			// Shutter speed
			if (isset($exif['ExposureTime'])) {
				$shutter = $exif['ExposureTime'];
				if (strpos($shutter, '/') !== false) {
					$parts = explode('/', $shutter);
					if (count($parts) == 2 && $parts[1] != 0) {
						$decimal = $parts[0] / $parts[1];
						if ($decimal >= 1) {
							$exif_data['shutter'] = round($decimal, 1) . 's';
						} else {
							$exif_data['shutter'] = $shutter . 's';
						}
					}
				} else {
					$exif_data['shutter'] = $shutter . 's';
				}
			} elseif (isset($exif['ShutterSpeedValue'])) {
				$shutter_speed = explode('/', $exif['ShutterSpeedValue']);
				if (count($shutter_speed) == 2 && $shutter_speed[1] != 0) {
					$speed = pow(2, $shutter_speed[0] / $shutter_speed[1]);
					if ($speed >= 1) {
						$exif_data['shutter'] = '1/' . round(1 / $speed) . 's';
					} else {
						$exif_data['shutter'] = round($speed, 1) . 's';
					}
				}
			}

			// ISO
			if (isset($exif['ISOSpeedRatings'])) {
				$exif_data['iso'] = 'ISO ' . $exif['ISOSpeedRatings'];
			} elseif (isset($exif['PhotographicSensitivity'])) {
				$exif_data['iso'] = 'ISO ' . $exif['PhotographicSensitivity'];
			}

			// Focal length
			if (isset($exif['FocalLength'])) {
				$focal = $exif['FocalLength'];
				if (strpos($focal, '/') !== false) {
					$parts = explode('/', $focal);
					if (count($parts) == 2 && $parts[1] != 0) {
						$exif_data['focal_length'] = round($parts[0] / $parts[1]) . 'mm';
					}
				} else {
					$exif_data['focal_length'] = $focal . 'mm';
				}
			}

			// 35mm equivalent focal length
			if (isset($exif['FocalLengthIn35mmFilm'])) {
				$exif_data['focal_35mm'] = $exif['FocalLengthIn35mmFilm'] . 'mm (35mm equiv.)';
			}

			// Date taken - try multiple date fields
			if (isset($exif['DateTimeOriginal'])) {
				$date = DateTime::createFromFormat('Y:m:d H:i:s', $exif['DateTimeOriginal']);
				if ($date) {
					$exif_data['date_taken'] = $date->format('F j, Y g:i A');
				}
			} elseif (isset($exif['DateTime'])) {
				$date = DateTime::createFromFormat('Y:m:d H:i:s', $exif['DateTime']);
				if ($date) {
					$exif_data['date_taken'] = $date->format('F j, Y g:i A');
				}
			} elseif (isset($exif['DateTimeDigitized'])) {
				$date = DateTime::createFromFormat('Y:m:d H:i:s', $exif['DateTimeDigitized']);
				if ($date) {
					$exif_data['date_taken'] = $date->format('F j, Y g:i A');
				}
			}

			// Flash information
			if (isset($exif['Flash'])) {
				$flash_value = intval($exif['Flash']);
				$flash_descriptions = array(
					0 => 'No Flash',
					1 => 'Flash Fired',
					5 => 'Flash Fired, Return not detected',
					7 => 'Flash Fired, Return detected',
					8 => 'On, Did not fire',
					9 => 'On, Fired',
					13 => 'On, Return not detected',
					15 => 'On, Return detected',
					16 => 'Off, Did not fire',
					24 => 'Auto, Did not fire',
					25 => 'Auto, Fired',
					29 => 'Auto, Fired, Return not detected',
					31 => 'Auto, Fired, Return detected',
					32 => 'No flash function',
					65 => 'Red-eye reduction, Fired',
					69 => 'Red-eye reduction, Fired, Return not detected',
					71 => 'Red-eye reduction, Fired, Return detected',
					73 => 'Red-eye reduction, On, Fired',
					77 => 'Red-eye reduction, On, Fired, Return not detected',
					79 => 'Red-eye reduction, On, Fired, Return detected',
					89 => 'Red-eye reduction, Auto, Fired',
					93 => 'Red-eye reduction, Auto, Fired, Return not detected',
					95 => 'Red-eye reduction, Auto, Fired, Return detected'
				);
				$exif_data['flash'] = isset($flash_descriptions[$flash_value]) ? $flash_descriptions[$flash_value] : 'Unknown';
			}

			// White balance
			if (isset($exif['WhiteBalance'])) {
				$wb_values = array(0 => 'Auto', 1 => 'Manual');
				$exif_data['white_balance'] = isset($wb_values[$exif['WhiteBalance']]) ? $wb_values[$exif['WhiteBalance']] : 'Unknown';
			}

			// Exposure mode
			if (isset($exif['ExposureMode'])) {
				$exposure_modes = array(0 => 'Auto', 1 => 'Manual', 2 => 'Auto bracket');
				$exif_data['exposure_mode'] = isset($exposure_modes[$exif['ExposureMode']]) ? $exposure_modes[$exif['ExposureMode']] : 'Unknown';
			}

			// Metering mode
			if (isset($exif['MeteringMode'])) {
				$metering_modes = array(
					0 => 'Unknown',
					1 => 'Average',
					2 => 'Center-weighted average',
					3 => 'Spot',
					4 => 'Multi-spot',
					5 => 'Pattern',
					6 => 'Partial'
				);
				$exif_data['metering_mode'] = isset($metering_modes[$exif['MeteringMode']]) ? $metering_modes[$exif['MeteringMode']] : 'Unknown';
			}

			// GPS coordinates if available
			if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
				$lat = $exif['GPSLatitude'];
				$lon = $exif['GPSLongitude'];
				$lat_ref = isset($exif['GPSLatitudeRef']) ? $exif['GPSLatitudeRef'] : '';
				$lon_ref = isset($exif['GPSLongitudeRef']) ? $exif['GPSLongitudeRef'] : '';

				// Helper function to convert GPS rational number to decimal
				$gps_to_decimal = function ($coordinate) {
					if (is_array($coordinate) && count($coordinate) >= 3) {
						// Each component might be a rational number like "25/1"
						$degrees = $coordinate[0];
						$minutes = $coordinate[1];
						$seconds = $coordinate[2];

						// Evaluate fractions
						if (is_string($degrees) && strpos($degrees, '/') !== false) {
							$parts = explode('/', $degrees);
							$degrees = $parts[1] != 0 ? $parts[0] / $parts[1] : 0;
						}
						if (is_string($minutes) && strpos($minutes, '/') !== false) {
							$parts = explode('/', $minutes);
							$minutes = $parts[1] != 0 ? $parts[0] / $parts[1] : 0;
						}
						if (is_string($seconds) && strpos($seconds, '/') !== false) {
							$parts = explode('/', $seconds);
							$seconds = $parts[1] != 0 ? $parts[0] / $parts[1] : 0;
						}

						return $degrees + ($minutes / 60) + ($seconds / 3600);
					}
					return 0;
				};

				// Convert DMS to decimal
				if (is_array($lat) && count($lat) >= 3) {
					$lat_decimal = $gps_to_decimal($lat);
					if ($lat_ref == 'S') $lat_decimal = -$lat_decimal;

					$lon_decimal = $gps_to_decimal($lon);
					if ($lon_ref == 'W') $lon_decimal = -$lon_decimal;

					$exif_data['gps_coordinates'] = round($lat_decimal, 6) . ', ' . round($lon_decimal, 6);

					// Add reverse geocoding
					$location_name = vibe_photo_reverse_geocode($lat_decimal, $lon_decimal, $attachment_id);
					if ($location_name) {
						$exif_data['location'] = $location_name;
					}
				}
			}

			// Color space
			if (isset($exif['ColorSpace'])) {
				$color_spaces = array(1 => 'sRGB', 65535 => 'Uncalibrated');
				$exif_data['color_space'] = isset($color_spaces[$exif['ColorSpace']]) ? $color_spaces[$exif['ColorSpace']] : 'Unknown';
			}

			// Software/Camera firmware
			if (isset($exif['Software'])) {
				$exif_data['software'] = $exif['Software'];
			}
		}
	}

	wp_send_json_success($exif_data);
}

// Register AJAX handlers for both logged-in and non-logged-in users
add_action('wp_ajax_vibe_photo_get_image_exif', 'vibe_photo_get_image_exif');
add_action('wp_ajax_nopriv_vibe_photo_get_image_exif', 'vibe_photo_get_image_exif');

/**
 * Check PHP EXIF extension availability
 * Call via: http://localhost:3002/?check_exif=1
 */
function vibe_photo_check_exif() {
	if (isset($_GET['check_exif']) && $_GET['check_exif'] === '1') {
		echo '<div style="background: #f0f0f0; padding: 20px; margin: 20px; border: 1px solid #ccc; font-family: monospace;">';
		echo '<h3>PHP EXIF Extension Status</h3>';
		echo '<p><strong>EXIF Extension Available:</strong> ' . (function_exists('exif_read_data') ? 'YES' : 'NO') . '</p>';
		echo '<p><strong>PHP Version:</strong> ' . phpversion() . '</p>';

		if (function_exists('exif_read_data')) {
			echo '<p style="color: green;">✓ EXIF extension is available and ready to use!</p>';
		} else {
			echo '<p style="color: red;">✗ EXIF extension is NOT available. You may need to enable it in your PHP configuration.</p>';
			echo '<p>To enable EXIF extension:</p>';
			echo '<ul>';
			echo '<li>Edit your php.ini file</li>';
			echo '<li>Uncomment or add: extension=exif</li>';
			echo '<li>Restart your web server</li>';
			echo '</ul>';
		}

		// Test with a sample image if EXIF is available
		if (function_exists('exif_read_data')) {
			$upload_dir = wp_upload_dir();
			echo '<p><strong>Upload Directory:</strong> ' . $upload_dir['basedir'] . '</p>';
		}

		// Test AJAX endpoint
		echo '<h3>AJAX Endpoint Test</h3>';
		echo '<button onclick="testAjax()" style="padding: 10px; background: #0073aa; color: white; border: none; cursor: pointer;">Test AJAX</button>';
		echo '<div id="ajax-result" style="margin-top: 10px; padding: 10px; background: #fff; border: 1px solid #ddd;"></div>';

		echo '<script>
        function testAjax() {
            var resultDiv = document.getElementById("ajax-result");
            resultDiv.innerHTML = "Testing AJAX...";
            
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "' . admin_url('admin-ajax.php') . '", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        resultDiv.innerHTML = "<strong>AJAX Response:</strong><pre>" + xhr.responseText + "</pre>";
                    } else {
                        resultDiv.innerHTML = "<strong>AJAX Error:</strong> " + xhr.status + " - " + xhr.statusText;
                    }
                }
            };
            
            xhr.send("action=vibe_photo_test_ajax&test_data=hello");
        }
        </script>';

		echo '</div>';
		exit;
	}
}
add_action('init', 'vibe_photo_check_exif');

/**
 * Simple AJAX test endpoint
 */
function vibe_photo_test_ajax() {
	wp_send_json_success(array(
		'message' => 'AJAX is working!',
		'test_data' => $_POST['test_data'] ?? 'no data',
		'timestamp' => current_time('mysql')
	));
}
add_action('wp_ajax_vibe_photo_test_ajax', 'vibe_photo_test_ajax');
add_action('wp_ajax_nopriv_vibe_photo_test_ajax', 'vibe_photo_test_ajax');

/**
 * Create sample gallery for testing
 * This function can be called once to create a test gallery
 */
function vibe_photo_create_sample_gallery() {
	// Only run if no galleries exist yet
	$existing_galleries = get_posts(array(
		'post_type' => 'photo_gallery',
		'post_status' => 'publish',
		'numberposts' => 1
	));

	if (!empty($existing_galleries)) {
		return; // Sample gallery already exists
	}

	// Create a sample gallery post
	$gallery_id = wp_insert_post(array(
		'post_title' => 'Sample Photography Gallery',
		'post_content' => 'This is a sample gallery to test the photography theme. You can edit this gallery and add your own images through the WordPress admin panel.',
		'post_status' => 'publish',
		'post_type' => 'photo_gallery',
		'post_excerpt' => 'A beautiful collection of sample photographs to showcase the gallery functionality.'
	));

	if ($gallery_id && !is_wp_error($gallery_id)) {
		// For now, we'll leave the gallery empty so users can add their own images
		// To add images, edit the gallery in WordPress admin and use the "Gallery Images" meta box
		return $gallery_id;
	}
}

// Uncomment the line below to create a sample gallery when the theme is activated
// add_action('after_switch_theme', 'vibe_photo_create_sample_gallery');

/**
 * Quick test function - call this once to create a gallery with placeholder images
 * Visit: http://localhost:3002/?test_gallery=create
 * Visit: http://localhost:3002/?debug_galleries=list
 */
function vibe_photo_test_gallery_creation() {
	// Debug: List all galleries
	if (isset($_GET['debug_galleries']) && $_GET['debug_galleries'] === 'list') {
		$galleries = get_posts(array(
			'post_type' => 'photo_gallery',
			'post_status' => 'publish',
			'numberposts' => -1
		));

		echo '<div style="background: #e8f5e8; padding: 20px; margin: 20px; border: 1px solid #4caf50;">';
		echo '<h3>All Photo Galleries (' . count($galleries) . ' found)</h3>';

		if (empty($galleries)) {
			echo '<p><strong>No galleries found!</strong></p>';
		} else {
			foreach ($galleries as $gallery) {
				$permalink = get_permalink($gallery->ID);
				echo '<div style="margin: 10px 0; padding: 10px; background: white; border: 1px solid #ddd;">';
				echo '<strong>Title:</strong> ' . $gallery->post_title . '<br>';
				echo '<strong>Slug:</strong> ' . $gallery->post_name . '<br>';
				echo '<strong>ID:</strong> ' . $gallery->ID . '<br>';
				echo '<strong>URL:</strong> <a href="' . $permalink . '" target="_blank">' . $permalink . '</a><br>';
				echo '<strong>Edit:</strong> <a href="' . get_edit_post_link($gallery->ID) . '" target="_blank">Edit in Admin</a>';
				echo '</div>';
			}
		}
		echo '</div>';
		return;
	}

	if (isset($_GET['test_gallery']) && $_GET['test_gallery'] === 'create' && current_user_can('edit_posts')) {
		// Create or update gallery-1
		$gallery_post = get_page_by_path('gallery-1', OBJECT, 'photo_gallery');

		if (!$gallery_post) {
			// Create new gallery
			$gallery_id = wp_insert_post(array(
				'post_title' => 'Test Gallery 1',
				'post_name' => 'gallery-1',
				'post_content' => 'This is a test gallery created automatically. [gallery ids="1,2,3,4,5"] You can edit this gallery to add your own images.',
				'post_status' => 'publish',
				'post_type' => 'photo_gallery',
				'post_excerpt' => 'A test photography gallery with placeholder content.'
			));
		} else {
			$gallery_id = $gallery_post->ID;
			// Update existing gallery with gallery shortcode
			wp_update_post(array(
				'ID' => $gallery_id,
				'post_content' => 'This is a test gallery. [gallery ids="1,2,3,4,5"] Please replace with your actual image IDs or upload images directly to this gallery.'
			));
		}

		echo '<div style="background: #dff0d8; padding: 20px; margin: 20px; border: 1px solid #d6e9c6;">';
		echo '<h3>Test Gallery Created/Updated!</h3>';
		echo '<p>Gallery ID: ' . $gallery_id . '</p>';
		echo '<p><a href="' . get_permalink($gallery_id) . '">View Gallery</a> | ';
		echo '<a href="' . get_edit_post_link($gallery_id) . '">Edit Gallery</a></p>';
		echo '<p><strong>Next steps:</strong></p>';
		echo '<ol>';
		echo '<li>Go to Media Library and note some image IDs</li>';
		echo '<li>Replace "1,2,3,4,5" in the gallery shortcode with real image IDs</li>';
		echo '<li>Or upload images directly to the gallery</li>';
		echo '</ol>';
		echo '</div>';
	}
}
add_action('wp', 'vibe_photo_test_gallery_creation');

/**
 * REMOVED FOR WORDPRESS.ORG COMPLIANCE
 * 
 * WordPress.org does not allow custom post types in themes.
 * If you need the Photo Gallery custom post type, it should be moved
 * to a separate plugin. The code has been commented out below for reference.
 * 
 * Note: The single-photo_gallery.php and archive-photo_gallery.php templates
 * remain in the theme but will not be used without the custom post type.
 */

/*
 * Custom post type for Photo Galleries
 *
function vibe_photo_register_gallery_post_type() {
	$labels = array(
		'name'                  => _x('Photo Galleries', 'Post type general name', 'vibe-photo-theme'),
		'singular_name'         => _x('Photo Gallery', 'Post type singular name', 'vibe-photo-theme'),
		'menu_name'             => _x('Photo Galleries', 'Admin Menu text', 'vibe-photo-theme'),
		'name_admin_bar'        => _x('Photo Gallery', 'Add New on Toolbar', 'vibe-photo-theme'),
		'add_new'               => __('Add New', 'vibe-photo-theme'),
		'add_new_item'          => __('Add New Photo Gallery', 'vibe-photo-theme'),
		'new_item'              => __('New Photo Gallery', 'vibe-photo-theme'),
		'edit_item'             => __('Edit Photo Gallery', 'vibe-photo-theme'),
		'view_item'             => __('View Photo Gallery', 'vibe-photo-theme'),
		'all_items'             => __('All Photo Galleries', 'vibe-photo-theme'),
		'search_items'          => __('Search Photo Galleries', 'vibe-photo-theme'),
		'not_found'             => __('No photo galleries found.', 'vibe-photo-theme'),
		'not_found_in_trash'    => __('No photo galleries found in Trash.', 'vibe-photo-theme'),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array('slug' => 'gallery'),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'          => 'dashicons-camera',
		'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
	);

	register_post_type('photo_gallery', $args);
}
add_action('init', 'vibe_photo_register_gallery_post_type');
*/

/**
 * REMOVED FOR WORDPRESS.ORG COMPLIANCE
 * 
 * Flush rewrite rules function was tied to custom post type (not allowed in themes)
 *
/*
 * Flush rewrite rules when theme is activated
 *
function vibe_photo_flush_rewrite_rules() {
	// Make sure our post type is registered
	vibe_photo_register_gallery_post_type();

	// Flush the rewrite rules
	if (function_exists('flush_rewrite_rules')) {
		flush_rewrite_rules();
	}
}
add_action('after_switch_theme', 'vibe_photo_flush_rewrite_rules');
 */

/**
 * Custom excerpt length for photography posts
 */
function vibe_photo_excerpt_length($length) {
	return 20;
}
add_filter('excerpt_length', 'vibe_photo_excerpt_length');

/**
 * Customize excerpt more text
 */
function vibe_photo_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'vibe_photo_excerpt_more');

/**
 * REMOVED FOR WORDPRESS.ORG COMPLIANCE
 * 
 * WordPress.org does not allow shortcodes in themes.
 * If you need the gallery shortcode, it should be moved to a separate plugin.
 * The code has been commented out below for reference.
 */

/*
 * Add custom gallery shortcode using Foundation Grid
 *
function vibe_photo_gallery_shortcode($atts) {
	$atts = shortcode_atts(array(
		'ids' => '',
		'columns' => 3,
		'size' => 'gallery-medium',
	), $atts);

	if (empty($atts['ids'])) {
		return '';
	}

	$ids = explode(',', $atts['ids']);
	$columns = intval($atts['columns']);

	// Foundation grid classes based on columns
	$cell_class = 'cell';
	switch ($columns) {
		case 2:
			$cell_class .= ' medium-6';
			break;
		case 3:
			$cell_class .= ' medium-4';
			break;
		case 4:
			$cell_class .= ' medium-3 large-3';
			break;
		case 6:
			$cell_class .= ' medium-2 large-2';
			break;
		default:
			$cell_class .= ' medium-4';
	}

	$output = '<div class="grid-x grid-padding-x photo-gallery-grid" data-equalizer data-equalize-on="medium">';

	foreach ($ids as $id) {
		$image = wp_get_attachment_image($id, $atts['size'], false, array('class' => 'gallery-image'));
		$link = wp_get_attachment_url($id);
		$title = get_the_title($id);

		$output .= '<div class="' . $cell_class . '">';
		$output .= '<div class="photo-card" data-equalizer-watch>';
		$output .= '<div class="photo-card-image">';
		$output .= '<a href="' . esc_url($link) . '" class="gallery-link" title="' . esc_attr($title) . '">' . $image . '</a>';
		$output .= '</div>';
		if ($title) {
			$output .= '<div class="photo-card-content">';
			$output .= '<h4 class="photo-card-title">' . esc_html($title) . '</h4>';
			$output .= '</div>';
		}
		$output .= '</div></div>';
	}

	$output .= '</div>';
	return $output;
}
add_shortcode('vibe_gallery', 'vibe_photo_gallery_shortcode');
*/

/**
 * REMOVED FOR WORDPRESS.ORG COMPLIANCE
 * 
 * Meta box functionality was tied to custom post type (not allowed in themes)
 *
/*
 * Add custom meta box for gallery images
 *
function vibe_photo_add_gallery_meta_box() {
	add_meta_box(
		'gallery-images',
		__('Gallery Images', 'vibe-photo-theme'),
		'vibe_photo_gallery_images_callback',
		'photo_gallery'
	);
}
add_action('add_meta_boxes', 'vibe_photo_add_gallery_meta_box');
 */

/**
 * REMOVED FOR WORDPRESS.ORG COMPLIANCE
 * 
 * Gallery images meta box callback was tied to custom post type (not allowed in themes)
 *
/*
 * Gallery images meta box callback
 *
function vibe_photo_gallery_images_callback($post) {
	wp_nonce_field('vibe_photo_save_gallery_images', 'vibe_photo_gallery_images_nonce');

	$gallery_images = get_post_meta($post->ID, '_gallery_images', true);
?>
	<div class="gallery-images-meta">
		<p>
			<label for="gallery_images"><?php _e('Gallery Images (comma-separated image IDs):', 'vibe-photo-theme'); ?></label>
			<input type="text"
				name="gallery_images"
				id="gallery_images"
				value="<?php echo esc_attr($gallery_images); ?>"
				class="large-text"
				placeholder="<?php _e('e.g., 123,124,125', 'vibe-photo-theme'); ?>" />
		</p>
		<p class="description">
			<?php _e('Enter image attachment IDs separated by commas. You can find image IDs in the Media Library.', 'vibe-photo-theme'); ?>
		</p>
		<p>
			<button type="button" class="button" id="select-gallery-images">
				<?php _e('Select Images', 'vibe-photo-theme'); ?>
			</button>
		</p>
		<div id="gallery-preview" style="margin-top: 15px;">
			<?php if ($gallery_images) {
				$image_ids = explode(',', $gallery_images);
				foreach ($image_ids as $image_id) {
					$image_id = trim($image_id);
					if (!empty($image_id)) {
						$image = wp_get_attachment_image($image_id, 'thumbnail', false, array('style' => 'margin: 5px; max-width: 80px;'));
						echo $image;
					}
				}
			} ?>
		</div>
	</div>

	<script>
		jQuery(document).ready(function($) {
			$('#select-gallery-images').on('click', function(e) {
				e.preventDefault();

				var mediaUploader = wp.media({
					title: '<?php _e('Select Gallery Images', 'vibe-photo-theme'); ?>',
					button: {
						text: '<?php _e('Add to Gallery', 'vibe-photo-theme'); ?>'
					},
					multiple: true
				});

				mediaUploader.on('select', function() {
					var attachments = mediaUploader.state().get('selection').toJSON();
					var imageIds = [];
					var previewHtml = '';

					attachments.forEach(function(attachment) {
						imageIds.push(attachment.id);
						previewHtml += '<img src="' + attachment.sizes.thumbnail.url + '" style="margin: 5px; max-width: 80px;" />';
					});

					$('#gallery_images').val(imageIds.join(','));
					$('#gallery-preview').html(previewHtml);
				});

				mediaUploader.open();
			});
		});
	</script>
<?php
}
 */

/**
 * REMOVED FOR WORDPRESS.ORG COMPLIANCE
 * 
 * Save gallery images function was tied to custom post type (not allowed in themes)
 *
/*
 * Save gallery images meta data
 *
function vibe_photo_save_gallery_images($post_id) {
	if (!isset($_POST['vibe_photo_gallery_images_nonce'])) {
		return;
	}

	if (!wp_verify_nonce($_POST['vibe_photo_gallery_images_nonce'], 'vibe_photo_save_gallery_images')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (isset($_POST['gallery_images'])) {
		update_post_meta($post_id, '_gallery_images', sanitize_text_field($_POST['gallery_images']));
	}
}
add_action('save_post', 'vibe_photo_save_gallery_images');
 */
