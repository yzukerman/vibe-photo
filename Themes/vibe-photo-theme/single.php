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

                        <div class="post-navigation">
                            <?php
                            $prev_post = get_previous_post();
                            $next_post = get_next_post();
                            ?>
                            
                            <?php if ($prev_post) : ?>
                                <a href="<?php echo get_permalink($prev_post->ID); ?>" class="nav-previous">
                                    <span class="nav-label"><?php _e('Previous Photo', 'vibe-photo'); ?></span>
                                    <span class="nav-title"><?php echo get_the_title($prev_post->ID); ?></span>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($next_post) : ?>
                                <a href="<?php echo get_permalink($next_post->ID); ?>" class="nav-next">
                                    <span class="nav-label"><?php _e('Next Photo', 'vibe-photo'); ?></span>
                                    <span class="nav-title"><?php echo get_the_title($next_post->ID); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </footer>
                </article>

                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>

            <?php endwhile; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'vibe-photo'); ?></p>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
