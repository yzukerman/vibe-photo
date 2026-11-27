<?php
/**
 * Plugin Name: Vibe Photo Galleries
 * Description: Photo galleries, an enhanced viewer, EXIF metadata, and optional Google geocoding. Works with Vibe Photo and other themes.
 * Version: 1.0.1
 * Requires at least: 6.7.1
 * Requires PHP: 8.2
 * Author: Yuval Zukerman
 * License: GPL-2.0-or-later
 * Text Domain: vibe-photo-galleries
 * Domain Path: /languages
 */
if (!defined('ABSPATH')) { exit; }
foreach (array('galleries', 'settings', 'metadata', 'enhancements', 'strings') as $vibe_photo_galleries_module) {
    require_once __DIR__ . '/includes/' . $vibe_photo_galleries_module . '.php';
}
function vibe_photo_galleries_assets() {
    wp_enqueue_style('vibe-photo-galleries', plugins_url('assets/css/gallery.css', __FILE__), array(), filemtime(__DIR__ . '/assets/css/gallery.css'));
    wp_enqueue_script('vibe-photo-lightbox', plugins_url('assets/js/lightbox.js', __FILE__), array('jquery'), filemtime(__DIR__ . '/assets/js/lightbox.js'), true);
    wp_localize_script('vibe-photo-lightbox', 'vibePhotoStrings', vibe_photo_galleries_viewer_strings());
    wp_localize_script('vibe-photo-lightbox', 'vibePhotoAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('vibe_photo_nonce')));
}
add_action('wp_enqueue_scripts', 'vibe_photo_galleries_assets');
function vibe_photo_galleries_activate() {
    vibe_photo_register_gallery_post_type();
    flush_rewrite_rules();
}
function vibe_photo_galleries_deactivate() {
    unregister_post_type('photo_gallery');
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'vibe_photo_galleries_activate');
register_deactivation_hook(__FILE__, 'vibe_photo_galleries_deactivate');

// WordPress loads directory language packs and bundled catalogs on demand.
