<?php
/**
 * The template for displaying photo gallery archive
 *
 * @package Vibe Photo Theme
 */

get_header(); ?>

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
            <!-- Page Header -->
            <div class="grid-x grid-padding-x">
                <div class="cell">
                    <header class="page-header text-center">
                        <h1 class="page-title"><?php _e('Photo Galleries', 'vibe-photo'); ?></h1>
                        <p class="page-description"><?php _e('Browse through our collection of photography galleries', 'vibe-photo'); ?></p>
                    </header>
                </div>
            </div>

            <!-- Galleries Grid -->
            <div class="grid-x grid-padding-x gallery-archive">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); 
                        $gallery_images = get_post_meta(get_the_ID(), '_gallery_images', true);
                        $image_count = 0;
                        $featured_image = '';
                        
                        // Get image count and featured image
                        if (!empty($gallery_images)) {
                            $image_ids = explode(',', $gallery_images);
                            $image_ids = array_filter(array_map('trim', $image_ids));
                            $image_count = count($image_ids);
                            
                            if (!empty($image_ids[0]) && is_numeric($image_ids[0])) {
                                $featured_image = wp_get_attachment_image_src($image_ids[0], 'gallery-medium');
                            }
                        }
                        
                        // Fallback to post thumbnail
                        if (empty($featured_image) && has_post_thumbnail()) {
                            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'gallery-medium');
                        }
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
                                                    <p class="image-count"><?php echo sprintf(_n('%d Photo', '%d Photos', $image_count, 'vibe-photo'), $image_count); ?></p>
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
                                            <span class="image-count"><?php echo sprintf(_n('%d photo', '%d photos', $image_count, 'vibe-photo'), $image_count); ?></span>
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
                                <p><a href="<?php echo admin_url('post-new.php?post_type=photo_gallery'); ?>" class="button photo-button"><?php _e('Create First Gallery', 'vibe-photo'); ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
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
</body>
</html>
