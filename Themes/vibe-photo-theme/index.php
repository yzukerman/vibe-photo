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
            <?php if (have_posts()) : ?>
                <div class="grid-x grid-padding-x photo-grid" data-equalizer data-equalize-on="medium">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="cell medium-4 large-3">
                            <article id="post-<?php the_ID(); ?>" <?php post_class('photo-item'); ?> data-equalizer-watch>
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('gallery-medium'); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <div class="photo-meta">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="pagination">
                    <?php
                    the_posts_pagination(array(
                        'prev_text' => __('Previous', 'vibe-photo'),
                        'next_text' => __('Next', 'vibe-photo'),
                    ));
                    ?>
                </div>

            <?php else : ?>
                <div class="no-content">
                    <h2><?php _e('No photos found', 'vibe-photo'); ?></h2>
                    <p><?php _e('Sorry, no photos were found. Please check back later.', 'vibe-photo'); ?></p>
                </div>
            <?php endif; ?>
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