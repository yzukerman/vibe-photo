	<footer class="site-footer">
		<div class="grid-container">
			<div class="grid-x grid-padding-x">
				<div class="cell text-center">
					<?php
					if (has_nav_menu('footer')) {
						wp_nav_menu(array(
							'theme_location' => 'footer',
							'menu_id' => 'footer-menu',
							'container' => 'nav',
							'container_class' => 'footer-navigation',
							'menu_class' => 'menu horizontal',
						));
					}
					?>
					<p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'vibe-photo'); ?></p>
				</div>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>
	</body>

	</html>