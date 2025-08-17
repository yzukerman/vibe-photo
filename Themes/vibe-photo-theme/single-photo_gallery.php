<?php get_header(); ?>

<main class="site-main">
		<div class="grid-container">
			<?php while (have_posts()) : the_post(); ?>
				<div class="grid-x grid-padding-x">
					<div class="cell">
						<article id="post-<?php the_ID(); ?>" <?php post_class('single-gallery'); ?>>
							<!-- Gallery Header -->
							<header class="gallery-header text-center">
								<h1 class="gallery-title"><?php the_title(); ?></h1>

								<div class="gallery-meta">
									<time datetime="<?php echo get_the_date('c'); ?>" class="gallery-date">
										<?php echo get_the_date(); ?>
									</time>

									<?php if (get_the_category_list()) : ?>
										<span class="gallery-categories">
											<?php the_category(', '); ?>
										</span>
									<?php endif; ?>
								</div>

								<?php
								// Check for excerpt first, then content, then fallback
								$gallery_description = '';

								// Try to get excerpt first
								if (has_excerpt()) {
									$gallery_description = get_the_excerpt();
								} else if (get_the_content()) {
									// Get content but remove gallery shortcodes to prevent duplicate display
									$content = get_the_content();

									// Remove ALL gallery shortcodes more aggressively
									$content = preg_replace('/\[gallery[^\]]*\]/', '', $content);
									$content = preg_replace('/\[\/gallery\]/', '', $content);

									// Only use content if there's something meaningful left after removing gallery shortcodes
									$cleaned_content = trim(strip_tags($content));
									if (!empty($cleaned_content) && strlen($cleaned_content) > 10) {
										$gallery_description = apply_filters('the_content', $content);
									}
								}

								// Show description if we have one
								if (!empty($gallery_description)) : ?>
									<div class="gallery-description">
										<?php echo $gallery_description; ?>
									</div>
								<?php endif; ?>
							</header>

							<!-- Masonry Gallery -->
							<div class="gallery-container">
								<?php
								// Get gallery images from post meta first
								$gallery_images = get_post_meta(get_the_ID(), '_gallery_images', true);

								// If no custom gallery images, try to get from post content
								if (empty($gallery_images)) {
									$content = get_the_content();

									// Look for gallery shortcode in content
									if (preg_match('/\[gallery[^\]]*ids=["\']([^"\']+)["\'][^\]]*\]/', $content, $matches)) {
										$gallery_images = $matches[1];
									} else if (preg_match('/\[gallery[^\]]*\]/', $content)) {
										// If gallery shortcode exists but no IDs, get attached images
										$attachments = get_children(array(
											'post_parent' => get_the_ID(),
											'post_type' => 'attachment',
											'post_mime_type' => 'image',
											'orderby' => 'menu_order',
											'order' => 'ASC'
										));

										if (!empty($attachments)) {
											$image_ids = array_keys($attachments);
											$gallery_images = implode(',', $image_ids);
										}
									}
								}

								// If still no images, get all attached images
								if (empty($gallery_images)) {
									$attachments = get_children(array(
										'post_parent' => get_the_ID(),
										'post_type' => 'attachment',
										'post_mime_type' => 'image',
										'orderby' => 'menu_order',
										'order' => 'ASC'
									));

									if (!empty($attachments)) {
										$image_ids = array_keys($attachments);
										$gallery_images = implode(',', $image_ids);
									}
								}

								if (!empty($gallery_images)) :
									$image_ids = explode(',', $gallery_images);
									$image_ids = array_filter(array_map('trim', $image_ids)); // Remove empty values and trim

									if (!empty($image_ids)) : ?>
										<div class="masonry-gallery custom-grid-gallery"
											id="vibe-gallery-container"
											style="
                                                 display: grid !important; 
                                                 grid-template-columns: repeat(3, 1fr) !important; 
                                                 gap: 20px !important; 
                                                 margin: 20px 0 !important;
                                                 width: 100% !important;
                                             ">
											<?php foreach ($image_ids as $image_id) :
												// Validate that this is a numeric ID and the attachment exists
												if (!is_numeric($image_id) || !wp_attachment_is_image($image_id)) {
													continue;
												}

												$image_url = wp_get_attachment_url($image_id);
												$image_large = wp_get_attachment_image_src($image_id, 'gallery-large');
												$image_medium = wp_get_attachment_image_src($image_id, 'gallery-medium');
												$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
												$image_caption = wp_get_attachment_caption($image_id);
												$image_title = get_the_title($image_id);

												if (!$image_url || !$image_medium) continue;
											?>
												<div class="masonry-item gallery-image-item photo-item">
													<div class="gallery-image-wrapper">
														<a href="<?php echo esc_url($image_url); ?>"
															class="gallery-link"
															data-caption="<?php echo esc_attr($image_caption ? $image_caption : $image_title); ?>"
															title="<?php echo esc_attr($image_title); ?>">

															<img src="<?php echo esc_url($image_medium[0]); ?>"
																alt="<?php echo esc_attr($image_alt ? $image_alt : $image_title); ?>"
																width="<?php echo esc_attr($image_medium[1]); ?>"
																height="<?php echo esc_attr($image_medium[2]); ?>"
																loading="lazy" />

															<div class="image-overlay">
																<span class="zoom-icon">🔍</span>
															</div>
														</a>

														<?php if ($image_caption) : ?>
															<div class="image-caption">
																<p><?php echo esc_html($image_caption); ?></p>
															</div>
														<?php endif; ?>
													</div>
												</div>
											<?php endforeach; ?>
										</div>

									<?php else : ?>
										<div class="callout warning text-center">
											<h3><?php _e('No Valid Images Found', 'vibe-photo'); ?></h3>
											<p><?php _e('The gallery images could not be loaded. Please check that the image IDs are correct.', 'vibe-photo'); ?></p>
											<?php if (current_user_can('edit_post', get_the_ID())) : ?>
												<p><a href="<?php echo get_edit_post_link(); ?>" class="button"><?php _e('Edit Gallery', 'vibe-photo'); ?></a></p>
											<?php endif; ?>
										</div>
									<?php endif; ?>

								<?php else : ?>
									<div class="callout secondary text-center">
										<h3><?php _e('Gallery Setup Required', 'vibe-photo'); ?></h3>
										<p><?php _e('This gallery doesn\'t have any images assigned yet.', 'vibe-photo'); ?></p>
										<?php if (current_user_can('edit_post', get_the_ID())) : ?>
											<p><?php _e('To add images to this gallery:', 'vibe-photo'); ?></p>
											<ol style="text-align: left; display: inline-block;">
												<li><?php _e('Edit this gallery', 'vibe-photo'); ?></li>
												<li><?php _e('Scroll down to the "Gallery Images" section', 'vibe-photo'); ?></li>
												<li><?php _e('Click "Select Images" to choose photos', 'vibe-photo'); ?></li>
												<li><?php _e('Save the gallery', 'vibe-photo'); ?></li>
											</ol>
											<p><a href="<?php echo get_edit_post_link(); ?>" class="button photo-button"><?php _e('Edit Gallery', 'vibe-photo'); ?></a></p>
										<?php else : ?>
											<p><?php _e('Please contact the site administrator to add images to this gallery.', 'vibe-photo'); ?></p>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>

							<!-- Navigation to other galleries -->
							<nav class="gallery-navigation">
								<div class="grid-x grid-padding-x">
									<div class="cell medium-6">
										<?php
										$prev_post = get_previous_post();
										if ($prev_post) : ?>
											<a href="<?php echo get_permalink($prev_post->ID); ?>" class="button secondary">
												← <?php echo esc_html($prev_post->post_title); ?>
											</a>
										<?php endif; ?>
									</div>

									<div class="cell medium-6 text-right">
										<?php
										$next_post = get_next_post();
										if ($next_post) : ?>
											<a href="<?php echo get_permalink($next_post->ID); ?>" class="button secondary">
												<?php echo esc_html($next_post->post_title); ?> →
											</a>
										<?php endif; ?>
									</div>
								</div>
							</nav>
						</article>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
	</main>

<?php get_footer(); ?>