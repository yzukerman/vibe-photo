<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<a class="skip-link" href="#main-content"><?php esc_html_e('Skip to content', 'vibe-photo'); ?></a>
	<header class="site-header">
		<div class="grid-container">
			<div class="grid-x grid-padding-x align-justify align-middle">
				<div class="cell auto">
					<a href="<?php echo esc_url(home_url()); ?>" class="site-logo">
						<?php bloginfo('name'); ?>
					</a>
				</div>

				<div class="cell shrink">

					<?php if (has_nav_menu('primary')) : ?>
                    <nav class="main-navigation show-for-medium" aria-label="<?php esc_attr_e('Primary navigation', 'vibe-photo'); ?>">
						<?php
						wp_nav_menu(array(
							'theme_location' => 'primary',
							'menu_id' => 'primary-menu',
							'container' => false,
							'menu_class' => 'menu horizontal',
							'fallback_cb' => false,
						));
						?>
					</nav>
                    <?php endif; ?>
				</div>
			</div>

            <details class="site-search">
                <summary aria-label="<?php esc_attr_e('Search', 'vibe-photo'); ?>">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                    <span class="show-for-sr"><?php esc_html_e('Search', 'vibe-photo'); ?></span>
                </summary>
                <?php get_search_form(); ?>
            </details>

            <?php if (has_nav_menu('primary')) : ?>
            <details class="mobile-navigation hide-for-medium">
                <summary class="mobile-menu-toggle"><?php esc_html_e('Menu', 'vibe-photo'); ?></summary>
                <nav class="main-navigation" aria-label="<?php esc_attr_e('Mobile navigation', 'vibe-photo'); ?>">
                    <?php wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id' => 'mobile-menu',
                        'container' => false,
                        'menu_class' => 'menu vertical',
                        'fallback_cb' => false,
                    )); ?>
                </nav>
            </details>
            <?php endif; ?>
		</div>
	</header>