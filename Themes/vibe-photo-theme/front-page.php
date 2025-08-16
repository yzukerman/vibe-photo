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
            <!-- Hero Section -->
            <div class="grid-x grid-padding-x">
                <div class="cell text-center">
                    <section class="hero-section">
                        <h1 class="hero-title"><?php bloginfo('name'); ?></h1>
                        <p class="hero-description"><?php bloginfo('description'); ?></p>
                    </section>
                </div>
            </div>

            <!-- Recent Galleries Section -->
            <div class="grid-x grid-padding-x">
                <div class="cell">
                    <section class="recent-galleries">
                        <h2 class="section-title text-center">Latest Photo Galleries</h2>
                        
                        <?php
                        // Query for the 3 most recent photo galleries
                        $recent_galleries = new WP_Query(array(
                            'post_type' => 'photo_gallery',
                            'posts_per_page' => 3,
                            'post_status' => 'publish',
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ));

                        if ($recent_galleries->have_posts()) : ?>
                            <div class="grid-x grid-padding-x gallery-grid" data-equalizer data-equalize-on="medium">
                                <?php while ($recent_galleries->have_posts()) : $recent_galleries->the_post(); ?>
                                    <div class="cell medium-4">
                                        <article class="gallery-card" data-equalizer-watch>
                                            <div class="gallery-card-image">
                                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <?php the_post_thumbnail('gallery-medium', array('alt' => get_the_title())); ?>
                                                    <?php else : ?>
                                                        <div class="placeholder-image">
                                                            <span class="placeholder-text">No Image</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </a>
                                            </div>
                                            
                                            <div class="gallery-card-content">
                                                <h3 class="gallery-card-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                
                                                <?php if (get_the_excerpt()) : ?>
                                                    <p class="gallery-card-excerpt"><?php echo get_the_excerpt(); ?></p>
                                                <?php endif; ?>
                                                
                                                <div class="gallery-card-meta">
                                                    <time datetime="<?php echo get_the_date('c'); ?>" class="gallery-date">
                                                        <?php echo get_the_date(); ?>
                                                    </time>
                                                    
                                                    <?php
                                                    // Count images in gallery
                                                    $gallery_images = get_post_meta(get_the_ID(), '_gallery_images', true);
                                                    if ($gallery_images) {
                                                        $image_count = count(explode(',', $gallery_images));
                                                        echo '<span class="image-count">' . $image_count . ' ' . _n('photo', 'photos', $image_count, 'vibe-photo') . '</span>';
                                                    }
                                                    ?>
                                                </div>
                                                
                                                <a href="<?php the_permalink(); ?>" class="button photo-button">
                                                    <?php _e('View Gallery', 'vibe-photo'); ?>
                                                </a>
                                            </div>
                                        </article>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            
                            <div class="grid-x grid-padding-x">
                                <div class="cell text-center">
                                    <a href="<?php echo get_post_type_archive_link('photo_gallery'); ?>" class="button large photo-button">
                                        <?php _e('View All Galleries', 'vibe-photo'); ?>
                                    </a>
                                </div>
                            </div>
                            
                        <?php else : ?>
                            <div class="grid-x grid-padding-x">
                                <div class="cell text-center">
                                    <div class="callout secondary">
                                        <h3><?php _e('No Galleries Yet', 'vibe-photo'); ?></h3>
                                        <p><?php _e('Check back soon for new photo galleries.', 'vibe-photo'); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif;
                        
                        // Reset post data
                        wp_reset_postdata();
                        ?>
                    </section>
                </div>
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
