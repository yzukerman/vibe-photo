<?php get_header(); ?>
<main id="main-content" class="site-main portfolio-main">
    <div class="grid-container portfolio-container">
        <h1 class="show-for-sr"><?php esc_html_e('Photography collections', 'vibe-photo'); ?></h1>
        <?php
        $collections = new WP_Query(array(
            'post_type' => array('post', 'photo_gallery'),
            'posts_per_page' => 12,
            'paged' => max(1, get_query_var('paged'), get_query_var('page')),
            'meta_query' => array(array('key' => '_thumbnail_id', 'compare' => 'EXISTS')),
        ));
        if ($collections->have_posts()) : ?>
            <div class="collection-grid">
            <?php while ($collections->have_posts()) : $collections->the_post(); ?>
                <article <?php post_class('collection-card'); ?>>
                    <a class="collection-link" href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('large', array('class' => 'collection-cover', 'alt' => '')); ?>
                        <div class="collection-caption">
                            <span class="collection-label"><?php esc_html_e('View gallery', 'vibe-photo'); ?></span>
                            <h2 class="collection-title"><?php the_title(); ?></h2>
                        </div>
                    </a>
                </article>
            <?php endwhile; ?>
            </div>
            <nav class="pagination" aria-label="<?php esc_attr_e('Collections', 'vibe-photo'); ?>">
                <?php echo paginate_links(array(
                    'current' => max(1, get_query_var('paged'), get_query_var('page')),
                    'total' => $collections->max_num_pages,
                    'prev_text' => __('Previous', 'vibe-photo'),
                    'next_text' => __('Next', 'vibe-photo'),
                )); ?>
            </nav>
        <?php else : ?>
            <p><?php esc_html_e('Collections will appear here when posts have a featured image.', 'vibe-photo'); ?></p>
        <?php endif; wp_reset_postdata(); ?>
    </div>
</main>
<?php get_footer(); ?>
