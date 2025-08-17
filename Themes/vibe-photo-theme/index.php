<?php get_header(); ?>

<main class="site-main">
		<div class="grid-container">
			<!-- Latest Posts Section -->
			<	</main>

<?php get_footer(); ?>s="latest-posts">
				<h2>Latest Posts</h2>
				<?php
				// Force a query for posts if the main query isn't working
				global $wp_query;

				// Check if this is the front page or home page
				if (is_home() || is_front_page()) {
					// If main query has no posts, create our own
					if (!have_posts()) {
						$wp_query = new WP_Query(array(
							'post_type' => 'post',
							'posts_per_page' => 10,
							'post_status' => 'publish'
						));
					}
				}

				if (have_posts()) : ?>
					<div class="grid-x grid-padding-x">
						<?php while (have_posts()) : the_post(); ?>
							<div class="cell medium-6 large-4">
								<article id="post-<?php the_ID(); ?>" <?php post_class('latest-post-item'); ?>>
									<?php if (has_post_thumbnail()) : ?>
										<div class="post-thumbnail">
											<a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail('large', array('class' => 'post-featured-image')); ?>
											</a>
										</div>
									<?php endif; ?>

									<div class="post-content">
										<h3 class="post-title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</h3>
										<div class="post-meta">
											<time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
										</div>
										<div class="post-excerpt">
											<?php the_excerpt(); ?>
										</div>
									</div>
								</article>
							</div>
						<?php endwhile; ?>
					</div>

					<!-- Pagination -->
					<div class="pagination">
						<?php
						the_posts_pagination(array(
							'prev_text' => __('Previous', 'vibe-photo'),
							'next_text' => __('Next', 'vibe-photo'),
							'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'vibe-photo') . ' </span>',
						));
						?>
					</div>

				<?php else : ?>
					<div class="no-content">
						<h3><?php _e('No posts found', 'vibe-photo'); ?></h3>
						<p><?php _e('Check your WordPress Reading Settings. You may need to set "Your homepage displays" to "Your latest posts".', 'vibe-photo'); ?></p>
					</div>
				<?php endif; ?>
			</section>

			<!-- Photo Galleries Section -->
			<?php
			// Check if photo galleries exist before showing the section
			$gallery_check_query = new WP_Query(array(
				'post_type' => 'photo_gallery',
				'posts_per_page' => 1,
				'post_status' => 'publish'
			));

			if ($gallery_check_query->have_posts()) : ?>
				<section class="photo-galleries">
					<h2>Photo Galleries</h2>
					<?php
					// Query for photo gallery posts
					$gallery_query = new WP_Query(array(
						'post_type' => 'photo_gallery',
						'posts_per_page' => 12,
						'post_status' => 'publish'
					));

					if ($gallery_query->have_posts()) : ?>
						<div class="grid-x grid-padding-x photo-grid">
							<?php while ($gallery_query->have_posts()) : $gallery_query->the_post(); ?>
								<div class="cell medium-4 large-3">
									<article id="gallery-<?php the_ID(); ?>" <?php post_class('photo-item'); ?>>
										<?php if (has_post_thumbnail()) : ?>
											<a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail('gallery-medium'); ?>
											</a>
										<?php endif; ?>

										<div class="photo-meta">
											<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
											<time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
										</div>
									</article>
								</div>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>

					<?php wp_reset_postdata(); ?>
				</section>
			<?php endif;
			wp_reset_postdata(); ?>
		</div>
	</main>

	<footer class="site-footer">
		<div class="container">
			<p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'vibe-photo'); ?></p>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>

</html>