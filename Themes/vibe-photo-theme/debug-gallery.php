<?php

/**
 * Debug template to help identify gallery issues
 * Temporarily replace single-photo_gallery.php with this to debug
 */

get_header(); ?>

<div style="padding: 20px; background: #f9f9f9; margin: 20px;">
	<h2>Gallery Debug Information</h2>

	<?php while (have_posts()) : the_post(); ?>
		<h3>Post Information:</h3>
		<ul>
			<li><strong>Post ID:</strong> <?php echo get_the_ID(); ?></li>
			<li><strong>Post Type:</strong> <?php echo get_post_type(); ?></li>
			<li><strong>Post Title:</strong> <?php the_title(); ?></li>
			<li><strong>Post Status:</strong> <?php echo get_post_status(); ?></li>
		</ul>

		<h3>Gallery Images Meta:</h3>
		<?php
		$gallery_images = get_post_meta(get_the_ID(), '_gallery_images', true);
		echo '<p><strong>_gallery_images meta:</strong> ';
		if (empty($gallery_images)) {
			echo '<em>Empty or not set</em>';
		} else {
			echo '"' . esc_html($gallery_images) . '"';
		}
		echo '</p>';
		?>

		<h3>All Post Meta:</h3>
		<?php
		$all_meta = get_post_meta(get_the_ID());
		echo '<pre>';
		foreach ($all_meta as $key => $value) {
			echo esc_html($key) . ': ' . esc_html(print_r($value, true)) . "\n";
		}
		echo '</pre>';
		?>

		<h3>Post Content:</h3>
		<div style="background: white; padding: 10px; border: 1px solid #ddd;">
			<?php
			$content = get_the_content();
			echo '<p><strong>Raw content:</strong></p>';
			echo '<pre>' . esc_html($content) . '</pre>';

			// Check for gallery shortcode
			if (preg_match_all('/\[gallery[^\]]*\]/', $content, $matches)) {
				echo '<p><strong>Gallery shortcodes found:</strong></p>';
				foreach ($matches[0] as $shortcode) {
					echo '<pre>' . esc_html($shortcode) . '</pre>';
				}
			} else {
				echo '<p><strong>No gallery shortcodes found in content</strong></p>';
			}
			?>
		</div>

		<h3>Attached Images:</h3>
		<?php
		$attachments = get_children(array(
			'post_parent' => get_the_ID(),
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'orderby' => 'menu_order',
			'order' => 'ASC'
		));

		if (!empty($attachments)) {
			echo '<p><strong>Found ' . count($attachments) . ' attached images:</strong></p>';
			echo '<ul>';
			foreach ($attachments as $attachment) {
				echo '<li>ID: ' . $attachment->ID . ' - ' . esc_html($attachment->post_title) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p><strong>No attached images found</strong></p>';
		}
		?>

		<h3>Featured Image:</h3>
		<?php if (has_post_thumbnail()) : ?>
			<p><strong>Featured image ID:</strong> <?php echo get_post_thumbnail_id(); ?></p>
			<?php the_post_thumbnail('medium'); ?>
		<?php else : ?>
			<p><strong>No featured image set</strong></p>
		<?php endif; ?>

		<hr>
		<p><strong>Edit this gallery:</strong> <a href="<?php echo get_edit_post_link(); ?>" target="_blank">Edit in WordPress Admin</a></p>

	<?php endwhile; ?>
</div>

<?php get_footer(); ?>