<?php

/**
 * The template for displaying photo gallery archive
 *
 * @package Vibe Photo Theme
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="site-main">
	<div class="grid-container">
		<!-- Galleries Grid -->
		<div class="grid-x grid-padding-x gallery-archive">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post();
                    $image_count = absint(apply_filters('vibe_photo_collection_image_count', 0, get_the_ID()));
                    $cover_id = vibe_photo_collection_cover_id(get_the_ID());
                    $featured_image = $cover_id ? wp_get_attachment_image_src($cover_id, 'large') : false;
				?>
					<div class="cell medium-6 large-4">
						<article class="gallery-card">
							<div class="gallery-card-image">
								<a href="<?php the_permalink(); ?>">
									<?php if (!empty($featured_image)) : ?>
										<img src="<?php echo esc_url($featured_image[0]); ?>"
											alt="<?php the_title_attribute(); ?>"
											loading="lazy" />
									<?php else : ?>
										<div class="placeholder-image">
											<span class="icon">📷</span>
											<p><?php _e('No Images', 'vibe-photo'); ?></p>
										</div>
									<?php endif; ?>

									<div class="gallery-overlay">
										<div class="gallery-info">
											<h3><?php the_title(); ?></h3>
											<?php if ($image_count > 0) : ?>
												<p class="image-count"><?php /* translators: %d: number of photos. */ echo esc_html(sprintf(_n('%d Photo', '%d Photos', $image_count, 'vibe-photo'), $image_count)); ?></p>
											<?php endif; ?>
											<span class="view-gallery"><?php _e('View Gallery →', 'vibe-photo'); ?></span>
										</div>
									</div>
								</a>
							</div>

							<div class="gallery-card-content">
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

								<?php if (has_excerpt()) : ?>
									<p class="gallery-excerpt"><?php the_excerpt(); ?></p>
								<?php endif; ?>

								<div class="gallery-meta">
									<time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
									<?php if ($image_count > 0) : ?>
										<span class="separator">•</span>
										<span class="image-count"><?php /* translators: %d: number of photos. */ echo esc_html(sprintf(_n('%d photo', '%d photos', $image_count, 'vibe-photo'), $image_count)); ?></span>
									<?php endif; ?>
								</div>
							</div>
						</article>
					</div>
				<?php endwhile; ?>

				<!-- Pagination -->
				<div class="cell">
					<div class="pagination-wrapper text-center">
						<?php
						the_posts_pagination(array(
							'prev_text' => __('← Previous', 'vibe-photo'),
							'next_text' => __('Next →', 'vibe-photo'),
							'before_page_number' => '<span class="screen-reader-text">' . __('Page', 'vibe-photo') . ' </span>',
						));
						?>
					</div>
				</div>

			<?php else : ?>
				<div class="cell">
					<div class="callout secondary text-center">
						<h3><?php _e('No Galleries Found', 'vibe-photo'); ?></h3>
						<p><?php _e('No photo galleries have been created yet.', 'vibe-photo'); ?></p>
						<?php if (current_user_can('edit_posts')) : ?>
							<p><a href="<?php echo esc_url(admin_url('post-new.php?post_type=photo_gallery')); ?>" class="button photo-button"><?php _e('Create First Gallery', 'vibe-photo'); ?></a></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>