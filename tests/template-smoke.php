<?php
/** Run with wp eval-file /path/to/repo/tests/template-smoke.php populated (or paginated / empty) in separate processes on a populated test site. */
$_SERVER['SERVER_NAME'] = wp_parse_url(home_url(), PHP_URL_HOST);
$requested_case = isset($args[0]) ? $args[0] : 'populated';
$original_query = $GLOBALS['wp_query'];
$original_post = $GLOBALS['post'] ?? null;
try {
    foreach (array(
        'populated' => array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1),
        'paginated' => array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1, 'paged' => 2),
        'empty' => array('post_type' => 'post', 'post__in' => array(0)),
    ) as $case => $args) {
        if ($case !== $requested_case) { continue; }
        $query = new WP_Query($args);
        $GLOBALS['wp_query'] = $query;
        $expected_ids = wp_list_pluck($query->posts, 'ID');
        ob_start();
        include get_template_directory() . '/index.php';
        $html = ob_get_clean();
        foreach (array('<main ' => 1, '</main>' => 1, 'class="site-footer"' => 1, '<title>' => 1, '</html>' => 1) as $fragment => $expected) {
            if (substr_count($html, $fragment) !== $expected) { throw new RuntimeException($case . ': invalid structure: ' . $fragment); }
        }
        foreach ($expected_ids as $id) {
            if (strpos($html, 'id="post-' . $id . '"') === false) { throw new RuntimeException($case . ': main query post missing'); }
        }
        if ($case === 'empty' && strpos($html, 'No posts found') === false) { throw new RuntimeException('Empty state missing'); }
        if ($case === 'populated' && $query->max_num_pages > 1 && strpos($html, 'nav-links') === false) { throw new RuntimeException('Pagination missing'); }
        echo $case . " fallback: PASS\n";
    }
} finally {
    $GLOBALS['wp_query'] = $original_query;
    $GLOBALS['post'] = $original_post;
    wp_reset_postdata();
}
