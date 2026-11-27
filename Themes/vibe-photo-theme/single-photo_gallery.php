<?php get_header(); ?>
<main id="main-content" tabindex="-1" class="site-main portfolio-post">
    <div class="grid-container">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <header class="entry-header text-center"><h1 class="entry-title"><?php the_title(); ?></h1></header>
                <div class="entry-content content-text"><?php the_content(); ?></div>
            </article>
        <?php if (comments_open() || get_comments_number()) { comments_template(); } ?>
        <?php endwhile; the_post_navigation(); ?>
    </div>
</main>
<?php get_footer(); ?>
