<?php
/**
 * Header.php
 *
 * @package ***Theme Name***
 * @author  Jacob Martella
 * @version  1.0
 */
?>
<!doctype html>

  <html class="no-js" <?php language_attributes(); ?>>

	<head>
		<base href="/">
		<meta charset="utf-8">
		
		<!-- Force IE to use the latest rendering engine available -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta class="foundation-mq">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php wp_head(); ?>

		<!-- Drop Google Analytics here -->
		<!-- end analytics -->

	</head>
	
	<!-- Uncomment this line if using the Off-Canvas Menu --> 
		
	<body <?php body_class(); ?>>

		<div class="off-canvas-wrapper" ng-app="myapp">
			
			<div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
				
				<?php get_template_part( 'parts/content', 'offcanvas' ); ?>
				
				<div class="off-canvas-content" data-off-canvas-content>
					
					<header class="header" role="banner">
							
						 <!-- This navs will be applied to the topbar, above all content 
							  To see additional nav styles, visit the /parts directory -->
						<div class="top-bar show-for-large" id="main-menu">
							<div class="top-bar-right">
								<nav id="theme-slug-top-menu" class="theme-slug-menu">
									<?php wp_nav_menu(array(
											'container' => false,                           // Remove nav container
											'menu_class' => 'medium-horizontal menu',       // Adding custom nav class
											'items_wrap' => '<ul id="%1$s" class="%2$s" data-responsive-menu="accordion medium-dropdown">%3$s</ul>',
											'theme_location' => 'top-nav',        			// Where it's located in the theme
											'depth' => 5,                                   // Limit the depth of the nav
											'fallback_cb' => false,                         // Fallback function (see below)
											'walker' => new Topbar_Menu_Walker()
									)); ?>
								</nav>
							</div>
						</div>
						<div class="top-bar hide-for-large show-for-medium-down" id="top-bar-menu">
							<div class="float-right">
								<ul class="menu">
									<li><button class="menu-icon" type="button" data-toggle="off-canvas"></button></li>
									<!--<li><a data-toggle="off-canvas">Menu</a></li>-->
								</ul>
							</div>
						</div>

						<div class="row">
							<div class="masthead large-12 medium-12 columns">
								<?php if (get_header_image()) { ?>
							 			<img src="<?php echo get_header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>'" alt="<?php echo get_bloginfo('name' ); ?> Header Image" /></a>'
							 		<?php } else { ?>
							 			<h1 class="site-title"><a href="<?php echo esc_url(get_home_url()); ?>"><?php echo get_bloginfo('name'); ?></a></h1>
										<h2 class="site-description"><a href="<?php echo esc_url(get_home_url()); ?>"><?php echo get_bloginfo('description'); ?></a></h2>
							 		<?php } ?>
							</div>
						</div>
						<div class="top-bar show-for-large" id="main-menu">
							<div class="top-bar-right">
								<nav id="theme-slug-main-menu" class="theme-slug-menu">
									<?php wp_nav_menu(array(
											'container' => false,                           // Remove nav container
											'menu_class' => 'medium-horizontal menu',       // Adding custom nav class
											'items_wrap' => '<ul id="%1$s" class="%2$s" data-responsive-menu="accordion medium-dropdown">%3$s</ul>',
											'theme_location' => 'main-nav',        			// Where it's located in the theme
											'depth' => 5,                                   // Limit the depth of the nav
											'fallback_cb' => false,                         // Fallback function (see below)
											'walker' => new Topbar_Menu_Walker()
									)); ?>
								</nav>
							</div>
						</div>
		 	
					</header> <!-- end .header -->