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
		add_theme_support('wp-block-styles');
		add_editor_style(array('assets/fonts/fonts.css', 'assets/css/editor-style.css'));

		// Add theme support for custom line height
		add_theme_support('custom-line-height');

		// Add theme support for custom spacing
		add_theme_support('custom-spacing');
	}

	// Register navigation menus
	if (function_exists('register_nav_menus')) {
		register_nav_menus(array(
			'primary' => esc_html__('Primary Menu', 'vibe-photo'),
			'footer' => esc_html__('Footer Menu', 'vibe-photo'),
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

	// Ship presentation dependencies locally; no CDN or Google Fonts requests.
	wp_enqueue_style('foundation-css', get_template_directory_uri() . '/assets/vendor/foundation/foundation.min.css', array(), '6.8.1');
	wp_enqueue_style('vibe-photo-fonts', get_template_directory_uri() . '/assets/fonts/fonts.css', array(), filemtime(get_template_directory() . '/assets/fonts/fonts.css'));
	wp_enqueue_style('vibe-photo-style', get_stylesheet_uri(), array('foundation-css', 'vibe-photo-fonts'), filemtime(get_stylesheet_directory() . '/style.css'));
	wp_enqueue_script('foundation-js', get_template_directory_uri() . '/assets/vendor/foundation/foundation.min.js', array('jquery'), '6.8.1', true);

	// Threaded replies only need core's script on open singular discussions.
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	// Add responsive navigation
	wp_enqueue_script('vibe-photo-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array('jquery', 'foundation-js'), filemtime(get_template_directory() . '/assets/js/navigation.js'), true);

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
