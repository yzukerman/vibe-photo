<?php get_header(); ?>

<main class="site-main">
		<div class="grid-container">
			<div class="grid-x grid-padding-x">
				<div class="cell large-8 large-offset-2">
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
								<?php if (has_post_thumbnail()) : ?>
									<div class="featured-image text-center">
										<?php the_post_thumbnail('gallery-large'); ?>
									</div>
								<?php endif; ?>

								<div class="content-text">
									<?php the_content(); ?>
								</div>

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

			<!-- Post Navigation Outside Centered Column -->
			<div class="grid-x grid-padding-x" style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #eee;">
				<div class="cell large-8 large-offset-2">
					<div class="post-navigation" style="display: flex; justify-content: space-between; align-items: center;">
						<div class="nav-previous">
							<?php 
							// Try built-in WordPress function first
							$prev_link = get_previous_post_link('%link', '← Previous Post');
							if ($prev_link) {
								$styled_prev_link = str_replace('<a ', '<a style="display: inline-block; padding: 0.75rem 1.5rem; background: #f8f8f8; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; font-weight: 500;" ', $prev_link);
								echo $styled_prev_link;
							} else {
								// Fallback: try manual approach
								$prev_post = get_previous_post();
								if ($prev_post) {
									echo '<a href="' . get_permalink($prev_post->ID) . '" style="display: inline-block; padding: 0.75rem 1.5rem; background: #f8f8f8; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; font-weight: 500;">← Previous Post</a>';
								}
							}
							?>
						</div>

						<div class="nav-next">
							<?php 
							$next_post = get_next_post();
							if ($next_post) {
								$next_url = get_permalink($next_post->ID);
								echo '<button onclick="window.location.href=\'' . esc_url($next_url) . '\'" style="display: inline-block; padding: 0.75rem 1.5rem; background: #f8f8f8; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; font-weight: 500; cursor: pointer; position: relative; z-index: 100;">Next Post →</button>';
							}
							?>
						</div>
					</div>

					<?php 
					// Debug info - remove this after testing
					$total_posts = wp_count_posts()->publish;
					$current_post_date = get_the_date('Y-m-d H:i:s');
					echo "<!-- Debug: Total published posts: $total_posts, Current post date: $current_post_date -->";
					?>
				</div>
			</div>

			<div class="grid-x grid-padding-x">
				<div class="cell large-8 large-offset-2">
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