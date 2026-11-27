<?php
/**
 * Plugin Name: Vibe Photo Galleries
 * Description: Photo galleries, an enhanced viewer, EXIF metadata, and optional Google geocoding. Works with Vibe Photo and other themes.
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: Yuval Zukerman
 * License: GPL-2.0-or-later
 * Text Domain: vibe-photo-galleries
 */
if (!defined('ABSPATH')) { exit; }
foreach (array('galleries', 'settings', 'metadata', 'enhancements') as $module) {
    require_once __DIR__ . '/includes/' . $module . '.php';
}
function vibe_photo_galleries_assets() {
    wp_enqueue_style('vibe-photo-galleries', plugins_url('assets/css/gallery.css', __FILE__), array(), filemtime(__DIR__ . '/assets/css/gallery.css'));
    wp_enqueue_script('vibe-photo-lightbox', plugins_url('assets/js/lightbox.js', __FILE__), array('jquery'), filemtime(__DIR__ . '/assets/js/lightbox.js'), true);
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
