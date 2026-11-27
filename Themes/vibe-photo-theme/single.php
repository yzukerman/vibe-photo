<?php get_header(); ?>

<main id="main-content" tabindex="-1" class="site-main portfolio-post">
	<div class="grid-container">
		<div class="grid-x grid-padding-x">
			<div class="cell large-12">
				<?php while (have_posts()) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class('single-photo'); ?>>
						<header class="entry-header text-center">
							<h1 class="entry-title"><?php the_title(); ?></h1>
							<div class="entry-meta">
								<time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
								<?php if (get_the_category_list()) : ?>
									<span class="categories"><?php the_category(', '); ?></span>
								<?php endif; ?>
							</div>
						</header>

						<div class="entry-content">
							<div class="content-text">
								<?php the_content(); ?>
								<?php
								wp_link_pages(array(
									'before' => '<div class="page-links">' . __('Pages:', 'vibe-photo'),
									'after'  => '</div>',
								));
								?>
								<!-- Photo Metadata -->
								<div class="photo-metadata">
									<?php
									$camera = get_post_meta(get_the_ID(), '_photo_camera', true);
									$lens = get_post_meta(get_the_ID(), '_photo_lens', true);
									$aperture = get_post_meta(get_the_ID(), '_photo_aperture', true);
									$shutter_speed = get_post_meta(get_the_ID(), '_photo_shutter_speed', true);
									$iso = get_post_meta(get_the_ID(), '_photo_iso', true);
									$location = get_post_meta(get_the_ID(), '_photo_location', true);

									if ($camera || $lens || $aperture || $shutter_speed || $iso || $location) :
									?>
										<h3><?php _e('Photo Details', 'vibe-photo'); ?></h3>
										<dl class="metadata-list">
											<?php if ($camera) : ?>
												<dt><?php _e('Camera:', 'vibe-photo'); ?></dt>
												<dd><?php echo esc_html($camera); ?></dd>
											<?php endif; ?>

											<?php if ($lens) : ?>
												<dt><?php _e('Lens:', 'vibe-photo'); ?></dt>
												<dd><?php echo esc_html($lens); ?></dd>
											<?php endif; ?>

											<?php if ($aperture) : ?>
												<dt><?php _e('Aperture:', 'vibe-photo'); ?></dt>
												<dd><?php echo esc_html($aperture); ?></dd>
											<?php endif; ?>

											<?php if ($shutter_speed) : ?>
												<dt><?php _e('Shutter Speed:', 'vibe-photo'); ?></dt>
												<dd><?php echo esc_html($shutter_speed); ?></dd>
											<?php endif; ?>

											<?php if ($iso) : ?>
												<dt><?php _e('ISO:', 'vibe-photo'); ?></dt>
												<dd><?php echo esc_html($iso); ?></dd>
											<?php endif; ?>

											<?php if ($location) : ?>
												<dt><?php _e('Location:', 'vibe-photo'); ?></dt>
												<dd><?php echo esc_html($location); ?></dd>
											<?php endif; ?>
										</dl>
									<?php endif; ?>
								</div>
							</div>

							<footer class="entry-footer">
								<?php if (get_the_tags()) : ?>
									<div class="tags">
										<?php the_tags('<span class="tags-label">' . __('Tags:', 'vibe-photo') . '</span> ', ', '); ?>
									</div>
								<?php endif; ?>

							</footer>
					</article>

				<?php endwhile; ?>
			</div>
		</div>

        <?php the_post_navigation(array(
            'prev_text' => esc_html__('← Previous post', 'vibe-photo'),
            'next_text' => esc_html__('Next post →', 'vibe-photo'),
        )); ?>

		<div class="grid-x grid-padding-x">
			<div class="cell large-12">
				<div class="post-comments-spacing" style="margin-top: 3rem;"></div>

				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if (comments_open() || get_comments_number()) :
					comments_template();
				endif;
				?>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>