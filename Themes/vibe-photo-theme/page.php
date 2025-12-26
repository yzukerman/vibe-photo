<?php get_header(); ?>

<main class="site-main">
	<div class="grid-container">
		<div class="grid-x grid-padding-x">
			<div class="cell large-10 large-offset-1">
				<?php while (have_posts()) : the_post(); ?>
					<article id="page-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header>

						<div class="entry-content">
							<?php the_content(); ?>
							<?php
							wp_link_pages(array(
								'before' => '<div class="page-links">' . __('Pages:', 'vibe-photo'),
								'after'  => '</div>',
							));
							?>
							<?php
							// If comments are open or we have at least one comment, load up the comment template.
							if (comments_open() || get_comments_number()) :
								comments_template();
							endif;
							?>
					</article>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>