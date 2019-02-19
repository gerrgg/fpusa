<?php
/**
 * FallProtectionUSA functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package FallProtectionUSA
 */

if ( ! function_exists( 'fpusa_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function fpusa_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on FallProtectionUSA, use a find and replace
		 * to change 'fpusa' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'fpusa', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		require_once get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'category_menu' => esc_html__( 'Categories', 'fpusa' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'fpusa_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'fpusa_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function fpusa_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'fpusa_content_width', 640 );
}
add_action( 'after_setup_theme', 'fpusa_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function fpusa_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'fpusa' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'fpusa' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'fpusa_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function fpusa_scripts() {
	wp_enqueue_style( 'fpusa-style', get_stylesheet_uri() );

	wp_enqueue_style( 'bs4', get_template_directory_uri() . '/css/bootstrap.min.css' );

	wp_enqueue_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.7.1/css/all.css' );

	wp_enqueue_script( 'bs4-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '4.2', true );

	wp_enqueue_script( 'js-functions', get_template_directory_uri() . '/js/functions.js', array('jquery'), filemtime(get_template_directory() . '/js/functions.js'), true );

	// wp_enqueue_script( 'fpusa-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151217', true );

	// wp_enqueue_script( 'fpusa-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	wp_localize_script( 'js-functions', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'fpusa_scripts' );
add_action( 'admin_post_nopriv_fpusa_update_zip', 'fpusa_update_zip' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
// require get_template_directory() . '/inc/fpusa-free-gifts.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function fpusa_get_location_from_ip(){
	$data = array();
	// look around for an ip
	$ip = get_client_ip();
	// get the long and lat from that IP
	$geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ip"));
	// ask geocoder.ca for info on that long and lat
	if( isset( $geo['geoplugin_latitude'], $geo['geoplugin_longitude'] ) ){
		$loc = new SimpleXMLElement(file_get_contents('http://geocoder.ca/?latt='. $geo['geoplugin_latitude'] .'&longt='. $geo['geoplugin_longitude'] .'&matchonly=1&geoit=xml'));
		// return the postal code if there is one.
		if( isset( $loc->postal ) ) array_push( $data, $loc->postal );
		if( isset( $loc->postal ) ) array_push( $data, $loc->city );
	}
	return $data;
}

function fpusa_update_zip(){
	$user = new WP_Customer();
	$user->set_shipping_postcode();
}

// Hook in
add_filter( 'woocommerce_default_address_fields' , 'fpusa_custom_address_fields' );

// Our hooked in function - $address_fields is passed via the filter!
function fpusa_custom_address_fields( $address_fields ) {
     $address_fields['first_name']['class'][] = 'col-6';
		 $address_fields['last_name']['class'][] = 'col-6';
		 $address_fields['company']['class'][] = 'col-6';
		 $address_fields['country']['class'][] = 'col-6';
		 $address_fields['address_1']['class'][] = 'col-12';
		 $address_fields['address_2']['class'][] = 'col-12';
		 $address_fields['city']['class'][] = 'col-4';
		 $address_fields['state']['class'][] = 'col-4';
		 $address_fields['postcode']['class'][] = 'col';

     return $address_fields;
}

add_filter( 'woocommerce_checkout_fields', 'fpusa_custom_checkout_fields' );

function fpusa_custom_checkout_fields( $fields ){
	$fields['billing']['billing_email']['priority'] = 10;
	$fields['billing']['billing_email']['class'][] = 'col-6';

	$fields['billing']['billing_phone']['priority'] = 10;
	$fields['billing']['billing_phone']['class'][] = 'col-6';

	return $fields;
}

add_filter('woocommerce_edit_account_form_start', function(){
	?>
	<div class="row"/>
	<?php
});

add_action( 'fpusa_order_actions', 'fpusa_track_package' );

function fpusa_track_package( $order ){
	?>
	<a href="#" class="btn btn-primary text-white btn-block">Track Package</a>
	<?php
}

add_action( 'wp_ajax_fpusa_get_buy_again_data', 'fpusa_get_buy_again_data_callback' );

function fpusa_get_buy_again_data_callback(){
	$product = wc_get_product( $_POST['p_id'] );
	if( ! empty( $product ) ){
		$data = array(
			'name' => $product->get_name(),
			'image' => $product->get_image(),
			'link' 	=> $product->get_permalink(),
			'price' => $product->get_price_html(),
			'stock' => $product->get_stock_status(),
		);
		wp_send_json( $data );
	}
	wp_die();
}

function fpusa_split_tags( $tags ){
	/**
	* Take in a list of tags, split them up and give them back in Key => Value
	*
	*
	**/
	$formatted = array();
	if( ! empty( $tags ) ){
		foreach( $tags as $tag ){
			$arr = explode( ': ', $tag->name );
			array_push( $formatted, $arr );
		}
	}

	return $formatted;
}

add_action( 'woocommerce_before_main_content', 'fpusa_cat_sub_nav_cat_menu', 1 );
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_action('woocommerce_before_shop_loop', 'woocommerce_breadcrumb', 15 );
function fpusa_cat_sub_nav_cat_menu(){
	/**
	*
	*
	*/
		if( fpusa_is_cat() ) :
			$siblings = fpusa_get_cat_siblings( fpusa_get_department() );
			if( sizeof( $siblings ) > 1 ) : ?>
			<nav class="navbar navbar-expand-lg navbar-light bg-dark">
		    <div class="navbar-nav">
		      <?php foreach( $siblings as $term ) : ?>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo get_term_link( (int)$term->term_id ) ?>"><?php echo $term->name; ?></a>
						</li>
					<?php endforeach; ?>
		    </div>
			</nav>
			<?php
			endif;
		endif;
}

function fpusa_is_cat(){
	/**
	* checks for /product-category/ in URI and returns bool
	* @return bool
	*
	*/
	return preg_match( '/product-category/', $_SERVER['REQUEST_URI'] );
}

function fpusa_get_cat_siblings( $department ){
	/**
	* @param WP_Term $department
	* @return array WP_Term $terms
	*/
	if( ! is_wp_error( $department ) ){
		$args = array( 'taxonomy' => 'product_cat', 'parent' => $department->term_id );
		$terms = get_terms( $args );
		return $terms;
	} else {
		return 0;
	}
}

function fpusa_get_department(){
	/**
	* Checks if we are in a category using the URI, if so, grab the slug of the next cat and return WP_Term
	* @return WP_Term $department
	*/
 global $wp_query;
 $categories = explode( '/', $_SERVER['REQUEST_URI'] );

 if( $categories[1] = 'product-category' ){
	 $department = get_terms( array(
		 'slug' => $categories[2],
		 'taxonomy' => 'product_cat',
	 ));
 }

 return $department[0];
}
