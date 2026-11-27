<?php
if (!defined('ABSPATH')) { exit; }

function vibe_photo_register_gallery_post_type() {
	$labels = array(
		'name'                  => _x('Photo Galleries', 'Post type general name', 'vibe-photo-galleries'),
		'singular_name'         => _x('Photo Gallery', 'Post type singular name', 'vibe-photo-galleries'),
		'menu_name'             => _x('Photo Galleries', 'Admin Menu text', 'vibe-photo-galleries'),
		'name_admin_bar'        => _x('Photo Gallery', 'Add New on Toolbar', 'vibe-photo-galleries'),
		'add_new'               => __('Add New', 'vibe-photo-galleries'),
		'add_new_item'          => __('Add New Photo Gallery', 'vibe-photo-galleries'),
		'new_item'              => __('New Photo Gallery', 'vibe-photo-galleries'),
		'edit_item'             => __('Edit Photo Gallery', 'vibe-photo-galleries'),
		'view_item'             => __('View Photo Gallery', 'vibe-photo-galleries'),
		'all_items'             => __('All Photo Galleries', 'vibe-photo-galleries'),
		'search_items'          => __('Search Photo Galleries', 'vibe-photo-galleries'),
		'not_found'             => __('No photo galleries found.', 'vibe-photo-galleries'),
		'not_found_in_trash'    => __('No photo galleries found in Trash.', 'vibe-photo-galleries'),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
        'show_in_rest'       => true,
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

function vibe_photo_gallery_shortcode($atts) {
    $atts = shortcode_atts(array('ids' => '', 'columns' => 3, 'size' => 'large'), $atts, 'vibe_gallery');
    $ids = array_filter(array_map('absint', explode(',', $atts['ids'])));
    if (!$ids) { return ''; }
    return gallery_shortcode(array('ids' => implode(',', $ids), 'columns' => max(1, min(9, absint($atts['columns']))), 'size' => sanitize_key($atts['size']), 'link' => 'file'));
}
add_shortcode('vibe_gallery', 'vibe_photo_gallery_shortcode');

// Display legacy saved image selections when another theme has no gallery template.
function vibe_photo_galleries_content($content) {
    if (!is_singular('photo_gallery') || !in_the_loop() || !is_main_query()) { return $content; }
    if (has_block('core/gallery') || has_shortcode($content, 'gallery') || has_shortcode($content, 'vibe_gallery')) { return $content; }
    $ids = get_post_meta(get_the_ID(), '_gallery_images', true);
    return $content . ($ids ? vibe_photo_gallery_shortcode(array('ids' => $ids)) : '');
}
add_filter('the_content', 'vibe_photo_galleries_content', 9);

function vibe_photo_galleries_meta_box() {
    add_meta_box('vibe-photo-gallery-images', __('Gallery images', 'vibe-photo-galleries'), 'vibe_photo_galleries_meta_box_content', 'photo_gallery');
}
add_action('add_meta_boxes', 'vibe_photo_galleries_meta_box');
function vibe_photo_galleries_meta_box_content($post) {
    wp_nonce_field('vibe_photo_save_gallery_images', 'vibe_photo_gallery_images_nonce');
    echo '<label for="vibe-photo-image-ids">' . esc_html__('Image attachment IDs, separated by commas. You can also use a Gallery block in the editor.', 'vibe-photo-galleries') . '</label>';
    echo '<input class="widefat" id="vibe-photo-image-ids" name="gallery_images" value="' . esc_attr(get_post_meta($post->ID, '_gallery_images', true)) . '">';
}
function vibe_photo_save_gallery_images($post_id) {
    if (get_post_type($post_id) !== 'photo_gallery' || wp_is_post_revision($post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) { return; }
    if (!isset($_POST['vibe_photo_gallery_images_nonce']) || !is_string($_POST['vibe_photo_gallery_images_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['vibe_photo_gallery_images_nonce'])), 'vibe_photo_save_gallery_images') || !current_user_can('edit_post', $post_id)) { return; }
    if (isset($_POST['gallery_images']) && is_string($_POST['gallery_images'])) {
        $ids = array_filter(array_map('absint', explode(',', sanitize_text_field(wp_unslash($_POST['gallery_images'])))));
        update_post_meta($post_id, '_gallery_images', implode(',', $ids));
    }
}
add_action('save_post_photo_gallery', 'vibe_photo_save_gallery_images');
