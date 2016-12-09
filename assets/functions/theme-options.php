<?php
/**
 * Theme-options.php
 *
 * Theme options file, using the Customizer, for Fotographia
 *
 * @author Jacob Martella
 * @package Fotographia
 * @version 1.1
 */

//* Create the general settings section
function theme_slug_general_customizer( $wp_customize ) {
	$wp_customize->add_section(
		'general',
		array(
			'title' => __('Theme Settings', 'theme-slug'),
			'description' => __('These are the theme options.', 'theme-slug'),
			'priority' => 35,
		)
	);

	//* Get the categories for the home page options
	$cats = get_categories();
	$cat_args['none'] = __('None', 'fotographia');
	foreach($cats as $cat) {
		$cat_args[$cat->term_id] = $cat->name;
	}

	//* Home Slider Category
	$wp_customize->add_setting(
		'theme-slug-home-slider-cat',
		array(
			'default' => 'None',
			'sanitize_callback' => 'theme_slug_sanitize_category',
		)
	);

	$wp_customize->add_control(
		'theme-slug-home-slider-cat',
		array(
			'label' => __('Home Slider Category', 'theme-slug'),
			'section' => 'general',
			'type' => 'select',
			'choices' => $cat_args
		)
	);

}
add_action( 'customize_register', 'theme_slug_general_customizer' );


//* Sanitize Links
function theme_slug_sanitize_link($input) {
	return esc_url_raw( $input );
}

//* Sanitize Layout Option
function theme_slug_sanitize_select( $input, $setting ) {
	$input = sanitize_key( $input );
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

//* Sanitize Checkboxes
function theme_slug_sanitize_checkbox( $input ) {
	return ( ( isset( $input ) && true == $input ) ? 1 : 0 );
}

//* Sanitize Category Options
function theme_slug_sanitize_category( $input, $setting ) {
	$input = sanitize_key( $input );
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

//* Sanitize Numbers
function theme_slug_sanitize_num($input, $setting) {
	$number = absint( $input );
	return ( $input ? $input : $setting->default );
}

//* Sanitize Text
function theme_slug_sanitize_text($input) {
	return wp_filter_nohtml_kses( $input );
}
?>