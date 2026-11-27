<?php
/** Run with wp eval-file /path/to/repo/tests/cover-smoke.php on a test site with galleries. */
$posts = get_posts(array('post_type' => array('post', 'photo_gallery'), 'post_status' => 'publish', 'numberposts' => 20));
foreach ($posts as $post) {
    $featured = get_post_thumbnail_id($post->ID);
    $cover = vibe_photo_collection_cover_id($post->ID);
    if ($featured && wp_attachment_is_image($featured) && $cover !== $featured) throw new RuntimeException('Featured image precedence failed');
    if ($cover && !wp_attachment_is_image($cover)) throw new RuntimeException('Cover is not an image attachment');
}
$query = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1, 'vibe_photo_cover_grid' => true));
if (strpos($query->request, 'cover_meta') === false) throw new RuntimeException('Cover query eligibility missing');
$ordinary = new WP_Query(array('post_type' => 'post', 'posts_per_page' => 1));
if (strpos($ordinary->request, 'cover_meta') !== false) throw new RuntimeException('Ordinary query unexpectedly filtered');
echo "Cover precedence and scoped pagination query checks passed.\n";
