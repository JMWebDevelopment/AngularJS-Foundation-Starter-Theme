<div class="off-canvas position-right" id="off-canvas" data-off-canvas data-position="right">
	<?php wp_nav_menu(array(
			'container' => false,                           // Remove nav container
			'menu_class' => 'vertical menu',       // Adding custom nav class
			'items_wrap' => '<ul id="%1$s" class="%2$s" data-accordion-menu>%3$s</ul>',
			'theme_location' => 'top-nav',        			// Where it's located in the theme
			'depth' => 5,                                   // Limit the depth of the nav
			'fallback_cb' => false,                         // Fallback function (see below)
			'walker' => new Off_Canvas_Menu_Walker()
	)); ?>
	<?php wp_nav_menu(array(
			'container' => false,                           // Remove nav container
			'menu_class' => 'vertical menu',       // Adding custom nav class
			'items_wrap' => '<ul id="%1$s" class="%2$s" data-accordion-menu>%3$s</ul>',
			'theme_location' => 'main-nav',        			// Where it's located in the theme
			'depth' => 5,                                   // Limit the depth of the nav
			'fallback_cb' => false,                         // Fallback function (see below)
			'walker' => new Off_Canvas_Menu_Walker()
	)); ?>
</div>