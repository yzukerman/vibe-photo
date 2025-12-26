<?php get_header(); ?>

<main class="site-main">
	<div class="grid-container">
		<div class="grid-x grid-padding-x">
			<div class="cell large-10 large-offset-1">
				<?php if (have_posts()) : ?>
					<header class="archive-header">
						<?php
						the_archive_title('<h1 class="archive-title">', '</h1>');
						the_archive_description('<div class="archive-description">', '</div>');
						?>
					</header>

					<div class="grid-x grid-padding-x">
						<?php while (have_posts()) : the_post(); ?>
							<div class="cell medium-6 large-4">
								<article id="post-<?php the_ID(); ?>" <?php post_class('archive-post-item'); ?>>
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
											<?php if (get_the_category_list()) : ?>
												<span class="categories"><?php the_category(', '); ?></span>
											<?php endif; ?>
										</div>
										<div class="post-excerpt">
											<?php the_excerpt(); ?>
										</div>
										<a href="<?php the_permalink(); ?>" class="read-more"><?php _e('Read More', 'vibe-photo'); ?></a>
									</div>
								</article>
							</div>
						<?php endwhile; ?>
					</div>

					<!-- Pagination -->
					<div class="pagination">
						<?php
						the_posts_pagination(array(
							'mid_size' => 2,
							'prev_text' => __('&laquo; Previous', 'vibe-photo'),
							'next_text' => __('Next &raquo;', 'vibe-photo'),
						));
						?>
					</div>

				<?php else : ?>
					<div class="no-posts">
						<h2><?php _e('Nothing Found', 'vibe-photo'); ?></h2>
						<p><?php _e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'vibe-photo'); ?></p>
						<?php get_search_form(); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>