<?php get_header(); ?>
<main class="site-main">
    <div class="grid-container portfolio-container">
        <header class="search-heading">
            <h1 class="entry-title"><?php printf(esc_html__('Search results for: %s', 'vibe-photo'), esc_html(get_search_query())); ?></h1>
            <?php get_search_form(); ?>
        </header>
        <?php if (have_posts()) : ?>
            <div class="search-results-list">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('search-result'); ?>>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php the_excerpt(); ?>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e('No results found. Try another word or phrase.', 'vibe-photo'); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
