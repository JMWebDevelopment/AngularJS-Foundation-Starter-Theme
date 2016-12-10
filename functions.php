<?php
/**
 * functions.php
 *
 * @package **Theme Name**
 * @author Jacob Martella
 * @version 1.0
 */
/**
 * Table of Contents
 * I. General Functions
 * II. Header Functions
 * III. Home Functions
 * IV. Footer Functions
 * V. Single Post Functions
 * VI. Archive Functions
 * VII. Author Functions
 * VIII. Comments Functions
 * IX. Other Functions
 */
/**
 ******************** I. General Functions *********************************
 */
/**
 * Enqueue the necessary scripts
 */
function theme_slug_scripts() {
	global $wp_styles; // Call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

	// Load What-Input files in footer
	wp_enqueue_script( 'what-input', get_template_directory_uri() . '/vendor/what-input/what-input.min.js', array(), '', true );

	// Adding Foundation scripts file in the footer
	wp_enqueue_script( 'foundation-js', get_template_directory_uri() . '/assets/js/foundation.min.js', array( 'jquery' ), '6.0', true );

	// Add the AngularJS files
	wp_enqueue_script( 'angularjs', get_stylesheet_directory_uri() . '/bower_components/angular/angular.js' );
	wp_enqueue_script( 'angularjs-route', get_stylesheet_directory_uri() . '/bower_components/angular-route/angular-route.min.js' );
	wp_enqueue_script( 'angularjs-ui-route', get_stylesheet_directory_uri() . '/bower_components/angular-ui/angular-ui-router.min.js' );
    //wp_enqueue_script( 'angular-resource', '//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-resource.min.js', array('angular-route'), null, false);
	wp_enqueue_script( 'angularjs-ui-resource', get_stylesheet_directory_uri() . '/bower_components/angular/angular-resource.min.js' );

	// Adding scripts file in the footer
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
	} else {
		$user = '';
	}
	$args = array(
		'partials' 				=> trailingslashit( get_template_directory_uri() ) . 'partials/',
		'api_url' 				=> rest_get_url_prefix() . '/wp/v2/',
		'template_directory' 	=> get_stylesheet_directory_uri() . '/',
		'nonce' 				=> wp_create_nonce( 'wp_rest' ),
		'is_admin' 				=> current_user_can( 'administrator' ),
        'site_url'              => home_url( '/' ),
		'site_title' 			=> get_bloginfo( 'name' ),
		'site_description' 		=> get_bloginfo( 'description' ),
		'logged_in' 			=> is_user_logged_in(),
		'logged_in_user'		=> $user
	);
	wp_enqueue_script( 'site-js', get_template_directory_uri() . '/assets/js/scripts.js', array( 'jquery', 'angularjs', 'angularjs-route' ), '', true );
	wp_localize_script( 'site-js', 'myLocalized', $args );

	// Register main stylesheet
	wp_enqueue_style( 'site-css', get_template_directory_uri() . '/style.css', array(), '', 'all' );

	// Comment reply script for threaded comments
	if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action('wp_enqueue_scripts', 'theme_slug_scripts', 999);
/**
 * Add in theme supports
 */
function theme_slug_theme_support() {

	//* Add WP Thumbnail Support
	add_theme_support( 'post-thumbnails' );

	//* Default thumbnail size
	set_post_thumbnail_size(125, 125, true);

	//* Add RSS Support
	add_theme_support( 'automatic-feed-links' );

	//* Add Support for WP Controlled Title Tag
	add_theme_support( 'title-tag' );

	//* Add HTML5 Support
	add_theme_support( 'html5',
		array(
			'comment-list',
			'comment-form',
			'search-form',
		)
	);

	//* Add the Editor Stylesheet
	add_editor_style('assets/css/editor-styles.css');

	//* Add Support for Translation
	load_theme_textdomain( 'theme-slug', get_template_directory() .'/assets/translation' );

	//* Adding post format support
	/* add_theme_support( 'post-formats',
		array(
			'aside',             // title less blurb
			'gallery',           // gallery of images
			'link',              // quick link to other site
			'image',             // an image
			'quote',             // a quick quote
			'status',            // a Facebook like status update
			'video',             // video
			'audio',             // audio
			'chat'               // chat transcript
		)
	); */
}
add_action('after_setup_theme','theme_slug_theme_support', 16);
/**
 * Include theme options
 */
require('assets/functions/theme-options.php');
/**
 * Include custom functions
 */
require('assets/functions/menu-walkers.php');
/**
 * Register Sidebar
 */
function theme_slug_register_sidebars() {
	register_sidebar(array(
			'id' => 'sidebar1',
			'name' => __('Sidebar', 'theme-slug'),
			'description' => __('The first (primary) sidebar.', 'theme-slug'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
	));
}
add_action( 'widgets_init', 'theme_slug_register_sidebars' );
/**
 * Add support to query posts by year, month and day
 * @param $valid_vars
 * @return array
 */
function theme_slug_rest_query_vars( $valid_vars ) {
	$valid_vars = array_merge( $valid_vars, array( 'author_slug' ) );
	return $valid_vars;
}
add_filter( 'rest_query_vars', 'theme_slug_rest_query_vars' );

/**
 * Rewrite the search url so that we can grab it in the JS
 */
function search_url_rewrite () {
	if ( is_search() && !empty( $_GET['s'] ) ) {
		wp_redirect( home_url( '/search/' ) . urlencode( get_query_var( 's' ) ) );
		exit();
	}
}
add_action( 'template_redirect', 'search_url_rewrite' );

add_action( 'rest_api_init', 'theme_slug_register_rest_fields' );
function theme_slug_register_rest_fields() {

    register_rest_route( 'wp/v2', '/posts?year=(?P<year>[0-9 .\-]+)', array(
        'methods' => 'GET',
        'callback' => 'theme_slug_get_year_posts',
    ), true );

    register_rest_field( 'post',
        'featured_image',
        array(
            'get_callback'    => 'theme_slug_get_thumbnail_url',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field( 'post',
        'category_name',
        array(
            'get_callback'    => 'theme_slug_get_category_name_from_restapi',
            'update_callback' => null,
            'schema'          => null
        )
    );

    register_rest_field( 'post',
        'tag_name',
        array(
            'get_callback'    => 'theme_slug_get_tag_name_from_restapi',
            'update_callback' => null,
            'schema'          => null
        )
    );

    register_rest_field( 'post',
        'comments',
        array(
            'get_callback' 	  => 'theme_slug_get_comments',
            'update_callback' => null,
            'schema' 		  => null,
    ) );

	register_rest_field( 'post',
		'author_slug',
		array(
			'get_callback' 	  => 'theme_slug_get_author_id',
			'update_callback' => null,
			'schema' 		  => null,
	) );

}

function theme_slug_get_year_posts( $data ) {
    echo 'in here somewhere';
    $posts = get_posts( array ( 'year' => (int) $data[ 'year' ] ) );

    print_r($data);

    if ( empty( $posts ) ) {
        return null;
    }

    $response = new WP_REST_Response( $posts );

    return $response;
}

function theme_slug_get_thumbnail_url($post){
    if ( has_post_thumbnail( $post[ 'id' ] ) ) {
        $imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $post[ 'id' ] ), 'full' );
        $imgURL = $imgArray[ 0 ];
        return $imgURL;
    } else {
        return false;
    }
}

function theme_slug_get_category_name_from_restapi( $object, $field_name, $request ) {
    $cats = [];
    foreach ( $object[ 'categories' ] as $cat ) {
        array_push( $cats, get_cat_name( $cat ) );
    }
    return $cats;
}

function theme_slug_get_tag_name_from_restapi( $object, $field_name, $request ) {
    $tags = [];
    if ( isset( $object[ 'tags' ] ) ) {
        foreach ( $object['tags'] as $tag_id ) {
            $tag = get_tag( $tag_id );
            array_push( $tags, $tag->name );
        }
    }
    return $tags;
}

add_action( 'rest_post_collection_params', function( $params ) {
    $params['year'] = array(
        'type'        => 'integer',
        'description' => 'Restrict posts to ones published in a specific year.'
    );
    $params['monthnum'] = array(
        'type'        => 'integer',
        'description' => 'Restrict posts to ones published in a specific month.'
    );
    $params['day'] = array(
        'type'        => 'integer',
        'description' => 'Restrict posts to ones published in a specific day.'
    );
        return $params;
} );

add_filter( 'rest_post_query', function( $query_vars, $request ) {
    if ( $request['year'] ) {
        $query_vars['year'] = $request['year'];
    }
    if ( $request['monthnum'] ) {
        $query_vars['monthnum'] = $request['monthnum'];
    }
    if ( $request['day'] ) {
        $query_vars['day'] = $request['day'];
    }
        return $query_vars;
}, 10, 2 );

function theme_slug_get_comments( $object, $field_name, $request ) {

    return get_comments( array( 'post_id' => $object[ 'id' ] ) );

}

function theme_slug_get_post_by_slug( WP_REST_Request $request ) {

	$slug = $request['slug'];
	$return['slug'] = $slug;

	$return['post'] = get_page_by_path( $slug, ARRAY_A, 'post' );
	$return['post']['comments'] = get_comments( array( 'ID' => $return['post']['ID'] ) );

	$response = new WP_REST_Response( $return );
	return $response;

}

function theme_slug_get_author_id( $object, $field_name, $request ) {
	$id = $object[ 'author' ];
	$user = get_user_by( 'id', $id );
	return $user->user_login;
}

/**
 ******************** II. Header Functions *********************************
 */
/**
 * Register Menus
 */
register_nav_menus(
		array(
				'top-nav' 		=> __( 'Top Menu', 'theme-slug' ),   // Main nav in header
				'main-nav' 		=> __( 'Main Menu', 'theme-slug' ),   // Main nav in header
				'footer-links' 	=> __( 'Footer Links', 'theme-slug' ) // Secondary nav in footer
		)
);
/**
 * Add custom attributes to nav links
 */
function theme_slug_add_menu_atts( $atts, $item, $args ) {
	//$atts['ng-class'] = '{\'active-tab\': $route.current.activePage == \' ' . $item->slug . '\'}';
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'theme_slug_add_menu_atts', 10, 3 );
/**
 ******************** III. Home Functions *********************************
 */
/**
 ******************** IV. Footer Functions *********************************
 */
/**
 ******************** V. Single Post Functions *********************************
 */
/**
 ******************** VI. Archive Functions *********************************
 */
/**
 * Numeric Archive Page Navigation
 */
function theme_slug_page_navi($before = '', $after = '') {
	global $wpdb, $wp_query;
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	$numposts = $wp_query->found_posts;
	$max_page = $wp_query->max_num_pages;
	if ( $numposts <= $posts_per_page ) { return; }
	if(empty($paged) || $paged == 0) {
		$paged = 1;
	}
	$pages_to_show = 7;
	$pages_to_show_minus_1 = $pages_to_show-1;
	$half_page_start = floor($pages_to_show_minus_1/2);
	$half_page_end = ceil($pages_to_show_minus_1/2);
	$start_page = $paged - $half_page_start;
	if($start_page <= 0) {
		$start_page = 1;
	}
	$end_page = $paged + $half_page_end;
	if(($end_page - $start_page) != $pages_to_show_minus_1) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}
	if($end_page > $max_page) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	if($start_page <= 0) {
		$start_page = 1;
	}
	echo $before.'<nav class="page-navigation"><ul class="pagination">'."";
	if ($start_page >= 2 && $pages_to_show < $max_page) {
		$first_page_text = __( "First", 'theme-slug' );
		echo '<li><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
	}
	echo '<li>';
	previous_posts_link('Previous');
	echo '</li>';
	for($i = $start_page; $i  <= $end_page; $i++) {
		if($i == $paged) {
			echo '<li class="current"> '.$i.' </li>';
		} else {
			echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
		}
	}
	echo '<li>';
	next_posts_link('Next');
	echo '</li>';
	if ($end_page < $max_page) {
		$last_page_text = __( "Last", 'theme-slug' );
		echo '<li><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
	}
	echo '</ul></nav>'.$after."";
}
/**
 ******************** VII. Author Functions *********************************
 */
/**
 ******************** VIII. Comments Functions *********************************
 */
function theme_slug_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
<li <?php comment_class('panel'); ?>>
	<div class="media-object">
		<div class="media-object-section">
			<?php echo get_avatar( $comment, 75 ); ?>
		</div>
		<div class="media-object-section">
			<article id="comment-<?php comment_ID(); ?>" class="clearfix large-12 columns">
				<header class="comment-author">
					<?php
					// create variable
					$bgauthemail = get_comment_author_email();
					?>
					<?php printf(__('%s', 'theme-slug'), get_comment_author_link()) ?> on
					<time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__(' F jS, Y - g:ia', 'theme-slug')); ?> </a></time>
					<?php edit_comment_link(__('(Edit)', 'theme-slug'),'  ','') ?>
				</header>
				<?php if ($comment->comment_approved == '0') : ?>
					<div class="alert alert-info">
						<p><?php _e('Your comment is awaiting moderation.', 'theme-slug') ?></p>
					</div>
				<?php endif; ?>
				<section class="comment_content clearfix">
					<?php comment_text() ?>
				</section>
				<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
			</article>
		</div>
	</div>
	<!-- </li> is added by WordPress automatically -->
	<?php
}
/**
 ******************** IX. Other Functions *********************************
 */