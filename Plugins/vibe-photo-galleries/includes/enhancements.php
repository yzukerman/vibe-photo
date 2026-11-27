<?php
if (!defined('ABSPATH')) { exit; }

function vibe_photo_enhance_gallery_blocks($content) {
	// Only process if we have gallery blocks
	if (strpos($content, 'wp-block-gallery') === false) {
		return $content;
	}

	// Add lightbox attributes to gallery images
	$content = preg_replace_callback(
		'/<figure class="[^"]*wp-block-gallery[^"]*"[^>]*>(.*?)<\/figure>/s',
		function ($matches) {
			$gallery_content = $matches[0];

			// Add our lightbox class to the gallery container
			$gallery_content = str_replace(
				'wp-block-gallery',
				'wp-block-gallery vibe-lightbox-gallery',
				$gallery_content
			);

			// Enhance each image in the gallery - handle complex img tags with multiple attributes
			$gallery_content = preg_replace_callback(
				'/<img\s+[^>]*src=["\']([^"\']+)["\'][^>]*>/i',
				function ($img_matches) {
					$img_tag = $img_matches[0];
					$img_src = $img_matches[1];

					// Skip if already has lightbox attributes
					if (strpos($img_tag, 'lightbox-image') !== false) {
						return $img_tag;
					}

					// Clean up protocol-relative URLs
					if (strpos($img_src, '//') === 0) {
						$img_src = 'http:' . $img_src;
					}

					// Try to get the full size image URL by removing size suffix
					$full_size_src = $img_src;
					if (preg_match('/-(\d+)x(\d+)\.(jpg|jpeg|png|gif|webp)$/i', $img_src, $size_matches)) {
						$full_size_src = preg_replace('/-\d+x\d+\.(' . $size_matches[3] . ')$/i', '.$1', $img_src);
					}

					// Add lightbox attributes before the closing >
					$lightbox_attrs = sprintf(
						' data-lightbox="gallery" data-full-src="%s" data-src="%s" class="lightbox-image"',
						htmlspecialchars($full_size_src, ENT_QUOTES),
						htmlspecialchars($img_src, ENT_QUOTES)
					);

					// Insert lightbox attributes before the closing >
					$enhanced_img = str_replace('>', $lightbox_attrs . '>', $img_tag);

					return $enhanced_img;
				},
				$gallery_content
			);

			return $gallery_content;
		},
		$content
	);

	return $content;
}

/**
 * Also enhance classic gallery shortcodes
 */
function vibe_photo_enhance_gallery_shortcode($content) {
	// Look for gallery shortcode output
	if (strpos($content, 'gallery-') === false) {
		return $content;
	}

	// Add lightbox attributes to gallery images in shortcode galleries
	$content = preg_replace_callback(
		'/<div[^>]*class="[^"]*gallery[^"]*"[^>]*>(.*?)<\/div>/s',
		function ($matches) {
			$gallery_content = $matches[0];

			// Add our lightbox class
			$gallery_content = str_replace('class="', 'class="vibe-lightbox-gallery ', $gallery_content);

			// Enhance images within gallery items
			$gallery_content = preg_replace_callback(
				'/<a[^>]*href="([^"]+)"[^>]*><img([^>]+)src="([^"]+)"([^>]*)><\/a>/i',
				function ($link_matches) {
					$full_src = $link_matches[1];
					$img_attrs = $link_matches[2] . $link_matches[4];
					$thumb_src = $link_matches[3];

					// Create lightbox-enabled image (remove the link wrapper)
					return sprintf(
						'<img %s src="%s" data-lightbox="gallery" data-full-src="%s" data-src="%s" class="lightbox-image" %s>',
						$link_matches[2],
						$thumb_src,
						esc_attr($full_src),
						esc_attr($thumb_src),
						$link_matches[4]
					);
				},
				$gallery_content
			);

			return $gallery_content;
		},
		$content
	);

	return $content;
}

if (function_exists('add_filter')) {
	// Apply to all post content
	add_filter('the_content', 'vibe_photo_enhance_gallery_blocks', 20);
	add_filter('the_content', 'vibe_photo_enhance_gallery_shortcode', 21);
}

