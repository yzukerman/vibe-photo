<?php
/** Run with: wp eval-file tests/gallery-smoke.php (plugin must be active). */
foreach (array('photo_gallery' => post_type_exists('photo_gallery'), 'vibe_gallery' => shortcode_exists('vibe_gallery'), 'native gallery' => shortcode_exists('gallery')) as $feature => $available) {
    if (!$available) { throw new RuntimeException('Missing: ' . $feature); }
}
$ids = get_posts(array('post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => 1, 'fields' => 'ids'));
if ($ids && strpos(do_shortcode('[vibe_gallery ids="' . absint($ids[0]) . '"]'), '<img') === false) {
    throw new RuntimeException('Gallery shortcode did not render an image');
}
echo "Gallery registration and shortcode checks passed.\n";
