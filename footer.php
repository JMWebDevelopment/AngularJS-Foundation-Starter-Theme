<?php
/**
 * Footer.php
 *
 * @package ***Theme Name***
 * @author  Jacob Martella
 * @version  1.0
 */
?>
					<footer class="footer" role="contentinfo">
						<div id="inner-footer" class="row">
							<div class="large-12 medium-12 columns">
								<nav role="navigation">
		    						<?php wp_nav_menu(array(
										    'container' => 'false',                              // Remove nav container
										    'menu' => __( 'Footer Links', 'theme-slug' ),   	// Nav name
										    'menu_class' => 'menu',      					// Adding custom nav class
										    'theme_location' => 'footer-links',             // Where it's located in the theme
										    'depth' => 0,                                   // Limit the depth of the nav
										    'fallback_cb' => ''  							// Fallback function
								    )); ?>
		    					</nav>
		    				</div>
							<div class="large-12 medium-12 columns">
								<p class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.</p>
							</div>
						</div> <!-- end #inner-footer -->
					</footer> <!-- end .footer -->
				</div>  <!-- end .main-content -->
			</div> <!-- end .off-canvas-wrapper-inner -->
		</div> <!-- end .off-canvas-wrapper -->
		<?php wp_footer(); ?>
	</body>
</html> <!-- end page -->