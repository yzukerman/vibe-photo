<?php
/**
 * Clear all cached location data
 * 
 * Run this from the command line:
 * php -d memory_limit=512M clear-location-cache.php
 * 
 * Or add ?clear_location_cache=1 to any page URL (admin only)
 */

// Load WordPress
if (php_sapi_name() === 'cli') {
    // Running from command line
    require_once('../../../Sites/vibe-photo/wp-load.php');
} else {
    // Running from web - check if admin
    require_once('../../../Sites/vibe-photo/wp-load.php');
    
    if (!isset($_GET['clear_location_cache']) || !current_user_can('manage_options')) {
        die('Access denied');
    }
}

// Get all attachments with cached location data
$args = array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'meta_key' => '_vibe_photo_location',
    'fields' => 'ids'
);

$attachments = get_posts($args);

$count = 0;
foreach ($attachments as $attachment_id) {
    delete_post_meta($attachment_id, '_vibe_photo_location');
    $count++;
}

if (php_sapi_name() === 'cli') {
    echo "Cleared location cache for {$count} images.\n";
} else {
    echo "Cleared location cache for {$count} images. <a href='/'>Go back</a>";
}
