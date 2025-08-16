<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="grid-container">
            <div class="grid-x grid-padding-x align-justify align-middle">
                <div class="cell auto">
                    <a href="<?php echo home_url(); ?>" class="site-logo">
                        <?php bloginfo('name'); ?>
                    </a>
                </div>
                
                <div class="cell shrink">
                    <button class="menu-toggle hide-for-medium" data-toggle="responsive-menu">
                        <span class="fa fa-bars"></span> Menu
                    </button>
                    
                    <nav class="main-navigation show-for-medium">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_id' => 'primary-menu',
                            'container' => false,
                            'menu_class' => 'menu horizontal',
                        ));
                        ?>
                    </nav>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div class="grid-x">
                <div class="cell">
                    <nav class="main-navigation hide-for-medium" id="responsive-menu" data-toggler="is-active">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_id' => 'mobile-menu',
                            'container' => false,
                            'menu_class' => 'menu vertical',
                        ));
                        ?>
                    </nav>
                </div>
            </div>
        </div>
    </header>

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

                                <?php if (get_the_content()) : ?>
                                    <div class="gallery-description">
                                        <?php 
                                        // Get content but remove gallery shortcodes to prevent duplicate display
                                        $content = get_the_content();
                                        echo '<!-- DEBUG: Original content: ' . esc_html($content) . ' -->';
                                        
                                        // Remove ALL gallery shortcodes more aggressively
                                        $content = preg_replace('/\[gallery[^\]]*\]/', '', $content);
                                        $content = preg_replace('/\[\/gallery\]/', '', $content);
                                        
                                        echo '<!-- DEBUG: Content after removing gallery shortcodes: ' . esc_html($content) . ' -->';
                                        
                                        // Only show content if there's something meaningful left after removing gallery shortcodes
                                        $cleaned_content = trim(strip_tags($content));
                                        if (!empty($cleaned_content) && strlen($cleaned_content) > 10) {
                                            // Apply content filters but skip gallery shortcode processing
                                            echo apply_filters('the_content', $content);
                                        } else {
                                            echo '<p>Gallery content processed - shortcodes removed to prevent conflicts.</p>';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </header>

                            <!-- Masonry Gallery -->
                            <div class="gallery-container">
                                <?php
                                // Get gallery images from post meta first
                                $gallery_images = get_post_meta(get_the_ID(), '_gallery_images', true);
                                
                                // Debug: Show what we found
                                echo '<!-- DEBUG: Gallery ID: ' . get_the_ID() . ' -->';
                                echo '<!-- DEBUG: Gallery images from meta: "' . $gallery_images . '" -->';
                                echo '<!-- DEBUG: Meta empty? ' . (empty($gallery_images) ? 'YES' : 'NO') . ' -->';
                                
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
                                        <!-- DEBUGGING: Image count and CSS check -->
                                        <div style="background: yellow; padding: 10px; margin: 10px;">
                                            <strong>DEBUG:</strong> Found <?php echo count($image_ids); ?> images<br>
                                            <strong>Container CSS Test:</strong> This should be in a yellow box with proper styling.
                                        </div>
                                        
                                        <div class="masonry-gallery custom-grid-gallery" 
                                             id="vibe-gallery-container"
                                             data-masonry='{"percentPosition": true, "itemSelector": ".masonry-item"}' 
                                             style="
                                                 display: grid !important; 
                                                 grid-template-columns: repeat(3, 1fr) !important; 
                                                 gap: 20px !important; 
                                                 margin: 20px 0 !important;
                                                 background: rgba(0,255,0,0.1) !important;
                                                 border: 2px solid green !important;
                                                 padding: 20px !important;
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
                                                <div class="masonry-item gallery-image-item" style="
                                                    width: 100% !important; 
                                                    margin: 0 !important; 
                                                    padding: 0 !important;
                                                    border: 1px solid red !important;
                                                    background: rgba(255,0,0,0.1) !important;
                                                ">
                                                    <div class="gallery-image-wrapper" style="width: 100%; display: block;">
                                                        <a href="<?php echo esc_url($image_url); ?>" 
                                                           class="gallery-lightbox" 
                                                           data-caption="<?php echo esc_attr($image_caption ? $image_caption : $image_title); ?>"
                                                           title="<?php echo esc_attr($image_title); ?>"
                                                           style="display: block; width: 100%;">
                                                            
                                                            <img src="<?php echo esc_url($image_medium[0]); ?>" 
                                                                 alt="<?php echo esc_attr($image_alt ? $image_alt : $image_title); ?>"
                                                                 width="<?php echo esc_attr($image_medium[1]); ?>"
                                                                 height="<?php echo esc_attr($image_medium[2]); ?>"
                                                                 loading="lazy" 
                                                                 style="width: 100%; height: auto; display: block;" />
                                                            
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
                                        
                                        <!-- Gallery Info -->
                                        <div class="gallery-info callout photo-info">
                                            <div class="grid-x grid-padding-x">
                                                <div class="cell medium-6">
                                                    <h4><?php _e('Gallery Information', 'vibe-photo'); ?></h4>
                                                    <p><strong><?php _e('Total Images:', 'vibe-photo'); ?></strong> <?php echo count($image_ids); ?></p>
                                                    <p><strong><?php _e('Date Created:', 'vibe-photo'); ?></strong> <?php echo get_the_date(); ?></p>
                                                </div>
                                                
                                                <div class="cell medium-6">
                                                    <div class="gallery-actions">
                                                        <a href="<?php echo get_post_type_archive_link('photo_gallery'); ?>" class="button secondary">
                                                            <?php _e('← Back to Galleries', 'vibe-photo'); ?>
                                                        </a>
                                                        
                                                        <button class="button photo-button" id="slideshow-toggle">
                                                            <?php _e('Start Slideshow', 'vibe-photo'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
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

    <footer class="site-footer">
        <div class="grid-container">
            <div class="grid-x grid-padding-x">
                <div class="cell text-center">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'vibe-photo'); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
    
    <!-- DEBUG: Check what's happening to our gallery after page load -->
    <script>
    // Use jQuery instead of $ to avoid conflicts
    jQuery(document).ready(function($) {
        console.log('=== GALLERY DEBUG: Page loaded with jQuery ===');
        
        const gallery = document.querySelector('#vibe-gallery-container');
        if (gallery) {
            console.log('Gallery found:', gallery);
            console.log('Gallery innerHTML length:', gallery.innerHTML.length);
            console.log('Gallery computed styles:', {
                display: getComputedStyle(gallery).display,
                gridTemplateColumns: getComputedStyle(gallery).gridTemplateColumns,
                gap: getComputedStyle(gallery).gap
            });
            
            // Store original state
            const originalHTML = gallery.innerHTML;
            const originalStyle = gallery.getAttribute('style');
            const originalClassName = gallery.className;
            
            console.log('Stored original state - HTML length:', originalHTML.length);
            
            // Create a protective function
            function protectGallery() {
                if (gallery.innerHTML !== originalHTML) {
                    console.log('DETECTED: Gallery HTML was modified! Restoring...');
                    gallery.innerHTML = originalHTML;
                }
                
                if (gallery.getAttribute('style') !== originalStyle) {
                    console.log('DETECTED: Gallery style was modified! Restoring...');
                    gallery.setAttribute('style', originalStyle);
                }
                
                if (gallery.className !== originalClassName) {
                    console.log('DETECTED: Gallery classes were modified! Restoring...');
                    gallery.className = originalClassName;
                }
            }
            
            // Monitor for changes to the gallery
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    console.log('=== GALLERY CHANGED ===');
                    console.log('Type:', mutation.type);
                    console.log('Target:', mutation.target);
                    
                    if (mutation.type === 'childList') {
                        console.log('Gallery content changed!');
                        console.log('New innerHTML length:', gallery.innerHTML.length);
                        console.log('Restoring original content...');
                        // Restore immediately
                        setTimeout(protectGallery, 0);
                    }
                    
                    if (mutation.type === 'attributes') {
                        console.log('Gallery attributes changed!');
                        console.log('Changed attribute:', mutation.attributeName);
                        console.log('Restoring original attributes...');
                        // Restore immediately
                        setTimeout(protectGallery, 0);
                    }
                });
            });
            
            observer.observe(gallery, {
                childList: true,
                attributes: true,
                subtree: true,
                attributeFilter: ['style', 'class']
            });
            
            // Also run protection every 100ms for the first 5 seconds
            let protectionCount = 0;
            const protectionInterval = setInterval(function() {
                protectGallery();
                protectionCount++;
                if (protectionCount > 50) { // 5 seconds
                    clearInterval(protectionInterval);
                    console.log('Gallery protection monitoring complete');
                }
            }, 100);
            
            // Also check for any scripts that might be modifying the gallery
            const scripts = document.querySelectorAll('script');
            console.log('Scripts on page:', scripts.length);
            scripts.forEach((script, index) => {
                if (script.src) {
                    console.log(`Script ${index}: ${script.src}`);
                } else if (script.innerHTML.includes('gallery') || script.innerHTML.includes('masonry')) {
                    console.log(`Inline script ${index} contains gallery/masonry:`, script.innerHTML.substring(0, 200));
                }
            });
        } else {
            console.log('Gallery not found!');
        }
        
        // Also check after a delay to see final state
        setTimeout(function() {
            console.log('=== GALLERY DEBUG: 5 seconds after load ===');
            const gallery = document.querySelector('#vibe-gallery-container');
            if (gallery) {
                console.log('Final gallery innerHTML length:', gallery.innerHTML.length);
                console.log('Final gallery styles:', {
                    display: getComputedStyle(gallery).display,
                    gridTemplateColumns: getComputedStyle(gallery).gridTemplateColumns,
                    gap: getComputedStyle(gallery).gap
                });
                console.log('Gallery children count:', gallery.children.length);
                Array.from(gallery.children).forEach((child, index) => {
                    console.log(`Child ${index}:`, child.tagName, child.className, child.style.cssText);
                });
            }
        }, 5000);
    });
    </script>
</body>
</html>
