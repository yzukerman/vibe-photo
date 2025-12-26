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