<?php get_header(); ?>

<main id="main-content" tabindex="-1" class="site-main">
    <div class="grid-container portfolio-container">
        <?php if (is_404()) : ?>
            <h1 class="entry-title"><?php esc_html_e('Page not found', 'vibe-photo'); ?></h1>
            <p><?php esc_html_e('That page could not be found. Try searching for a photograph or story.', 'vibe-photo'); ?></p>
            <?php get_search_form(); ?>
        <?php else : ?>
            <h1 class="entry-title"><?php esc_html_e('Latest posts', 'vibe-photo'); ?></h1>
            <?php if (have_posts()) : ?>
                <div class="grid-x grid-padding-x">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="cell medium-6 large-4">
                            <article id="post-<?php the_ID(); ?>" <?php post_class('latest-post-item'); ?>>
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                        <?php the_post_thumbnail('large', array('class' => 'post-featured-image', 'alt' => '')); ?>
                                    </a>
                                <?php endif; ?>
                                <div class="post-content">
                                    <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                    <div class="post-excerpt"><?php the_excerpt(); ?></div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php the_posts_pagination(array(
                    'prev_text' => __('Previous', 'vibe-photo'),
                    'next_text' => __('Next', 'vibe-photo'),
                )); ?>
            <?php else : ?>
                <p><?php esc_html_e('No posts found. Try another search or return later for new photographs.', 'vibe-photo'); ?></p>
                <?php get_search_form(); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
