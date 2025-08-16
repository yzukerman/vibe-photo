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
        add_theme_support('custom-logo');
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
            'primary' => esc_html__('Primary Menu', 'vibe-photo'),
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
 * Remove gallery shortcode processing to prevent conflicts
 */
function vibe_photo_disable_gallery_shortcode() {
    remove_shortcode('gallery');
}
add_action('init', 'vibe_photo_disable_gallery_shortcode');

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
        add_filter('the_content', function($content) {
            error_log('=== VIBE PHOTO DEBUG: the_content filter called ===');
            error_log('Original content length: ' . strlen($content));
            error_log('Content preview: ' . substr($content, 0, 200));
            return $content;
        }, 999);
        
        // Check for any gallery-related filters
        add_action('wp_footer', function() {
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
 * Custom post type for Photo Galleries
 */
function vibe_photo_register_gallery_post_type() {
    $labels = array(
        'name'                  => _x('Photo Galleries', 'Post type general name', 'vibe-photo'),
        'singular_name'         => _x('Photo Gallery', 'Post type singular name', 'vibe-photo'),
        'menu_name'             => _x('Photo Galleries', 'Admin Menu text', 'vibe-photo'),
        'name_admin_bar'        => _x('Photo Gallery', 'Add New on Toolbar', 'vibe-photo'),
        'add_new'               => __('Add New', 'vibe-photo'),
        'add_new_item'          => __('Add New Photo Gallery', 'vibe-photo'),
        'new_item'              => __('New Photo Gallery', 'vibe-photo'),
        'edit_item'             => __('Edit Photo Gallery', 'vibe-photo'),
        'view_item'             => __('View Photo Gallery', 'vibe-photo'),
        'all_items'             => __('All Photo Galleries', 'vibe-photo'),
        'search_items'          => __('Search Photo Galleries', 'vibe-photo'),
        'not_found'             => __('No photo galleries found.', 'vibe-photo'),
        'not_found_in_trash'    => __('No photo galleries found in Trash.', 'vibe-photo'),
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

/**
 * Flush rewrite rules when theme is activated
 */
function vibe_photo_flush_rewrite_rules() {
    // Make sure our post type is registered
    vibe_photo_register_gallery_post_type();
    
    // Flush the rewrite rules
    if (function_exists('flush_rewrite_rules')) {
        flush_rewrite_rules();
    }
}
add_action('after_switch_theme', 'vibe_photo_flush_rewrite_rules');

/**
 * Add custom meta boxes for photo metadata
 */
function vibe_photo_add_meta_boxes() {
    add_meta_box(
        'photo-metadata',
        __('Photo Metadata', 'vibe-photo'),
        'vibe_photo_metadata_callback',
        array('post', 'photo_gallery')
    );
}
add_action('add_meta_boxes', 'vibe_photo_add_meta_boxes');

/**
 * Meta box callback for photo metadata
 */
function vibe_photo_metadata_callback($post) {
    wp_nonce_field('vibe_photo_save_metadata', 'vibe_photo_metadata_nonce');
    
    $camera = get_post_meta($post->ID, '_photo_camera', true);
    $lens = get_post_meta($post->ID, '_photo_lens', true);
    $aperture = get_post_meta($post->ID, '_photo_aperture', true);
    $shutter_speed = get_post_meta($post->ID, '_photo_shutter_speed', true);
    $iso = get_post_meta($post->ID, '_photo_iso', true);
    $location = get_post_meta($post->ID, '_photo_location', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e('Camera', 'vibe-photo'); ?></th>
            <td><input type="text" name="photo_camera" value="<?php echo esc_attr($camera); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Lens', 'vibe-photo'); ?></th>
            <td><input type="text" name="photo_lens" value="<?php echo esc_attr($lens); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Aperture', 'vibe-photo'); ?></th>
            <td><input type="text" name="photo_aperture" value="<?php echo esc_attr($aperture); ?>" placeholder="f/2.8" /></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Shutter Speed', 'vibe-photo'); ?></th>
            <td><input type="text" name="photo_shutter_speed" value="<?php echo esc_attr($shutter_speed); ?>" placeholder="1/250s" /></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('ISO', 'vibe-photo'); ?></th>
            <td><input type="text" name="photo_iso" value="<?php echo esc_attr($iso); ?>" placeholder="100" /></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Location', 'vibe-photo'); ?></th>
            <td><input type="text" name="photo_location" value="<?php echo esc_attr($location); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Save photo metadata
 */
function vibe_photo_save_metadata($post_id) {
    if (!isset($_POST['vibe_photo_metadata_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['vibe_photo_metadata_nonce'], 'vibe_photo_save_metadata')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('camera', 'lens', 'aperture', 'shutter_speed', 'iso', 'location');
    
    foreach ($fields as $field) {
        if (isset($_POST['photo_' . $field])) {
            update_post_meta($post_id, '_photo_' . $field, sanitize_text_field($_POST['photo_' . $field]));
        }
    }
}
add_action('save_post', 'vibe_photo_save_metadata');

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
 * Add custom gallery shortcode using Foundation Grid
 */
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

/**
 * Add custom meta box for gallery images
 */
function vibe_photo_add_gallery_meta_box() {
    add_meta_box(
        'gallery-images',
        __('Gallery Images', 'vibe-photo'),
        'vibe_photo_gallery_images_callback',
        'photo_gallery'
    );
}
add_action('add_meta_boxes', 'vibe_photo_add_gallery_meta_box');

/**
 * Gallery images meta box callback
 */
function vibe_photo_gallery_images_callback($post) {
    wp_nonce_field('vibe_photo_save_gallery_images', 'vibe_photo_gallery_images_nonce');
    
    $gallery_images = get_post_meta($post->ID, '_gallery_images', true);
    ?>
    <div class="gallery-images-meta">
        <p>
            <label for="gallery_images"><?php _e('Gallery Images (comma-separated image IDs):', 'vibe-photo'); ?></label>
            <input type="text" 
                   name="gallery_images" 
                   id="gallery_images" 
                   value="<?php echo esc_attr($gallery_images); ?>" 
                   class="large-text" 
                   placeholder="<?php _e('e.g., 123,124,125', 'vibe-photo'); ?>" />
        </p>
        <p class="description">
            <?php _e('Enter image attachment IDs separated by commas. You can find image IDs in the Media Library.', 'vibe-photo'); ?>
        </p>
        <p>
            <button type="button" class="button" id="select-gallery-images">
                <?php _e('Select Images', 'vibe-photo'); ?>
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
                title: '<?php _e('Select Gallery Images', 'vibe-photo'); ?>',
                button: {
                    text: '<?php _e('Add to Gallery', 'vibe-photo'); ?>'
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

/**
 * Save gallery images meta data
 */
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