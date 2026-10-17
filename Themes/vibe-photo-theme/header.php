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
	<header class="site-header">
		<div class="grid-container">
			<div class="grid-x grid-padding-x align-justify align-middle">
				<div class="cell auto">
					<a href="<?php echo esc_url(home_url()); ?>" class="site-logo">
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
							'fallback_cb' => false,
						));
						?>
					</nav>
				</div>
			</div>

            <details class="site-search">
                <summary aria-label="<?php esc_attr_e('Search', 'vibe-photo'); ?>">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                    <span class="show-for-sr"><?php esc_html_e('Search', 'vibe-photo'); ?></span>
                </summary>
                <?php get_search_form(); ?>
            </details>

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
							'fallback_cb' => false,
						));
						?>
					</nav>
				</div>
			</div>
		</div>
	</header>