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
        function($matches) {
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
                function($img_matches) {
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
        function($matches) {
            $gallery_content = $matches[0];
            
            // Add our lightbox class
            $gallery_content = str_replace('class="', 'class="vibe-lightbox-gallery ', $gallery_content);
            
            // Enhance images within gallery items
            $gallery_content = preg_replace_callback(
                '/<a[^>]*href="([^"]+)"[^>]*><img([^>]+)src="([^"]+)"([^>]*)><\/a>/i',
                function($link_matches) {
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
 * AJAX handler for getting image EXIF data
 */
function vibe_photo_get_image_exif() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'vibe_photo_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    $image_url = sanitize_url($_POST['image_url']);
    
    // Fix protocol-relative URLs
    if (strpos($image_url, '//') === 0) {
        $image_url = 'http:' . $image_url;
    }
    
    // Try to get attachment ID from URL
    $attachment_id = attachment_url_to_postid($image_url);
    
    $exif_data = array();
    
    if (!$attachment_id) {
        // If no attachment ID, try to work with the URL directly
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
        
        // Get basic file info
        if ($metadata) {
            $exif_data['size'] = $metadata['width'] . ' × ' . $metadata['height'] . ' pixels';
            
            // Get file size
            if (file_exists($file_path)) {
                $file_size = filesize($file_path);
                $exif_data['file_size'] = size_format($file_size);
            }
        }
    }
    
    // Try to get EXIF data if available
    if (function_exists('exif_read_data') && file_exists($file_path)) {
        $exif = @exif_read_data($file_path);
        
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
                        $exif_data['shutter'] = '1/' . round(1/$speed) . 's';
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
                
                // Convert DMS to decimal
                if (is_array($lat) && count($lat) >= 3) {
                    $lat_decimal = $lat[0] + ($lat[1]/60) + ($lat[2]/3600);
                    if ($lat_ref == 'S') $lat_decimal = -$lat_decimal;
                    
                    $lon_decimal = $lon[0] + ($lon[1]/60) + ($lon[2]/3600);
                    if ($lon_ref == 'W') $lon_decimal = -$lon_decimal;
                    
                    $exif_data['gps_coordinates'] = round($lat_decimal, 6) . ', ' . round($lon_decimal, 6);
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