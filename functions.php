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

	wp_enqueue_style( 'slick', get_template_directory_uri() . '/css/slick.css' );

	wp_enqueue_style( 'slick-theme', get_template_directory_uri() . '/css/slick-theme.css' );

	wp_enqueue_script( 'bs4-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '4.2', true );

	wp_enqueue_script( 'js-functions', get_template_directory_uri() . '/js/functions.js', array('jquery'), filemtime(get_template_directory() . '/js/functions.js'), true );

	wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), '4.9', true );

	wp_enqueue_script( 'zoom-js', get_template_directory_uri() . '/js/jquery.zoom.js', array('jquery'), '4.9', true );
	// wp_enqueue_script( 'fpusa-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151217', true );

	// wp_enqueue_script( 'fpusa-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	wp_localize_script( 'js-functions', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}


add_action( 'wp_enqueue_scripts', 'fpusa_scripts' );
add_action( 'admin_post_nopriv_fpusa_update_zip', 'fpusa_update_zip' );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 10 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

add_action( 'woocommerce_single_product_summary', 'fpusa_template_single_brand', 3 );
add_action( 'woocommerce_single_product_summary', 'fpusa_template_single_divider', 11 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 15 );

// move short description below variations
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 35 );

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_before_single_product_summary', 'fpusa_show_product_images', 20 );

add_action( 'fpusa_template_product_gallery', 'fpusa_get_product_gallery' );

function fpusa_get_product_gallery(){
	wc_get_template('single-product/fpusa-product-gallery.php');
}

function fpusa_show_product_images(){
	wc_get_template('single-product/fpusa-product-image.php');
}
// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

function fpusa_template_single_divider(){
	echo '<hr>';
}

function fpusa_template_single_brand(){
	wc_get_template( 'single-product/brand.php' );
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Admin Functions which enhance the theme by hooking into WordPress Backend.
 */
require get_template_directory() . '/inc/admin-template-functions.php';

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


add_action( 'fpusa_order_actions', 'fpusa_track_package' );

function fpusa_track_package( $order ){
	?>
	<a href="#" class="btn btn-primary text-white btn-block">Track Package</a>
	<?php
}

/**
*
* AJAX
*
*/
add_action( 'wp_ajax_fpusa_get_variation_data', 'fpusa_get_product_data_callback' );
add_action( 'wp_ajax_nopriv_fpusa_get_variation_data', 'fpusa_get_product_data_callback' );
add_action( 'wp_ajax_fpusa_get_buy_again_data', 'fpusa_get_product_data_callback' );
add_action( 'wp_ajax_fpusa_single_product_feedback', 'fpusa_single_product_feedback_callback' );
add_action( 'wp_ajax_nopriv_fpusa_single_product_feedback', 'fpusa_single_product_feedback_callback' );
function fpusa_get_product_data_callback(){
	$product = wc_get_product( $_POST['p_id'] );
	if( ! empty( $product ) ){
		$image_src = wp_get_attachment_image_src( $product->get_image_id(), 'shop_thumbnail' );
		$data = array(
			'name' => $product->get_name(),
			'image' => $image_src[0],
			'link' 	=> $product->get_permalink(),
			'price' => $product->get_price_html(),
			'stock' => $product->get_availability(),
		);
		wp_send_json( $data );
	}
	wp_die();
}
/**
*
* END AJAX
*
*/
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
add_action('woocommerce_before_shop_loop', 'woocommerce_breadcrumb', 21 );
add_action('woocommerce_before_single_product', 'woocommerce_breadcrumb', 21 );

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

add_action( 'woocommerce_archive_description', 'fpusa_shop_by_category', 15 );
function fpusa_get_cats_w_img(){
	/**
	* Only displays categories with an image assigned to it.
	* @return array $categories_w_img - Collection of image src, link and name of a category
	*/
	$categories_w_img = array();
	$children = fpusa_get_cat_children();
	if( $children ) {
		foreach( $children as $child ){
			$thumbnail_id = get_woocommerce_term_meta( $child->term_id, 'thumbnail_id', true );
			if( $thumbnail_id ){
				$image_src = wp_get_attachment_url( $thumbnail_id );

				array_push( $categories_w_img, array(
					'image_src' => $image_src,
					'link' => get_term_link( (int)$child->term_id ),
					'name' => $child->name,
				));

			}
		}
	}
	return $categories_w_img;
}

add_action('woocommerce_archive_description', 'fpusa_category_best_sellers', 16);
function fpusa_category_best_sellers(){
	if( is_shop() ) {
		return;
	}
	$args = array(
		'posts_per_page' => 8,
		'post_type' => 'product',
		'orderby' => 'title',
		'meta_key' => 'total_sales',
		'orderby' => 'meta_value_num',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'		 => get_queried_object()->term_id,
			),
		),
	);

	fpusa_slick_query('Best Sellers', $args);
}

add_action('woocommerce_archive_description', 'fpusa_category_newest_products', 17);
function fpusa_category_newest_products(){
	if( is_shop() ) {
		return;
	}
	$args = array(
		'posts_per_page' => 8,
		'post_type' => 'product',
		'orderby' => 'date',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'		 => get_queried_object()->term_id,
			),
		),
	);

	fpusa_slick_query('Hot new releases', $args);
}

add_action('woocommerce_archive_description', 'fpusa_category_top_rated', 18);
function fpusa_category_top_rated(){
	if( is_shop() ) {
		return;
	}
	$args = array(
		'posts_per_page' => 8,
		'post_type' => 'product',
		'orderby' => 'meta_value_num',
		'meta_key' => '_wc_average_rating',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'		 => get_queried_object()->term_id,
			),
		),
	);

	fpusa_slick_query('Top Rated', $args);
}

add_action('woocommerce_archive_description', 'fpusa_category_cheapest', 18);
function fpusa_category_cheapest(){
	if( is_shop() ) {
		return;
	}
	$args = array(
		'posts_per_page' => 8,
		'post_type' => 'product',
		'order' => 'ASC',
		'orderby' => 'meta_value_num',
		'meta_key' => '_price',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'		 => get_queried_object()->term_id,
			),
		),
	);

	fpusa_slick_query('Cheapest', $args);
}


function fpusa_get_cat_children(){
	/**
	 * Used to get the children of a product category
	 * @return WP_Term $children - The children taxonomys of a product category
	 */
	 if( ! is_shop() ) {
		 $term = get_queried_object();

		 // var_dump( $term );

		 $children = get_terms( $term->taxonomy, array(
			 'parent'    => $term->term_id,
			 'hide_empty' => false
		 ) );

		 return $children;
	 }
}


add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );
function new_loop_shop_per_page( $cols ) {
	/**
	 * Change number of products that are displayed per page (shop page)
	 * @param $cols - How many products per page
	 */
  $cols = 36;
  return $cols;
}

add_action( 'fpusa_single_product_right', 'fpusa_single_right_wrapper_start', 5 );
add_action( 'fpusa_single_product_right', 'fpusa_single_right_wrapper_end', 100 );

function fpusa_single_right_wrapper_start(){
	echo '<div class="fpusa-single-product-right mt-1">';
}

function fpusa_single_right_wrapper_end(){
	echo '</div>';
}

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 1 );

add_action( 'fpusa_single_product_right', 'fpusa_single_product_price_right', 5 );
function fpusa_single_product_price_right(){
	global $product;
	?>
	<div class="<?php echo $product->get_type(); ?> price"><?php echo $product->get_price_html(); ?></div>
	<?php
}

add_action( 'fpusa_single_product_right', 'fpusa_single_product_shipping_upsell', 10 );
function fpusa_single_product_shipping_upsell(){
	?>
	<p style="overflow: hidden;">fpusa_single_product_shipping_upsell();</p>
	<?php
}

add_action( 'fpusa_single_product_right', 'fpusa_single_product_stock', 15 );
function fpusa_single_product_stock(){
	global $product;
	echo wc_get_stock_html( $product );
}

add_action( 'fpusa_single_product_right', 'fpusa_add_to_cart_btn', 50 );
function fpusa_add_to_cart_btn(){
	global $product;
	?> <label>QTY:</label> <?php
	woocommerce_quantity_input( array(
		'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
		'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
		'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
	) );
	?>
	<label for="fpusa_add_to_cart" class="btn btn-block mt-1 btn-danger">Add to cart</label>
	<?php
}

add_action( 'fpusa_single_product_right', 'fpusa_single_product_address', 70 );
function fpusa_single_product_address(){
	$loc_str = fpusa_get_ship_address_str( );
	echo '<button class="btn" data-toggle="modal" data-target="#fpusa_choose_loc"><i class="fas fa-map-marker-alt pr-2 text-dark"></i>Deliver to ' . $loc_str . '</button>';
}

function fpusa_product_attribute_button( $options, $attribute_name, $product ){
	$possible_variations = fpusa_get_variations( $product );
	if( ! empty( $options ) ) : ?>
		<div id="var_btn" class="d-flex">
			<?php foreach( $options as $option ): ?>
				<button type="button" class="d-flex btn justify-content-center align-items-center border mx-1" data-key="<?php echo $attribute_name ?>" data-value="<? echo $option ?>">
					<?php fpusa_get_pa_btn_data( $possible_variations, $attribute_name, $option ) ?>
				</button>
			<?php endforeach; ?>
		</div> <?php
	endif;
}

function fpusa_get_pa_btn_data( $possible_variations, $attribute_name, $option ){
	foreach( $possible_variations as $id => $options ){
		foreach( $options as $attr => $value ){
			if( $attr == 'attribute_' . $attribute_name && $value == $option ){
				$product = wc_get_product( $id );
				$image_src = wp_get_attachment_image_src( $product->get_image_id() );
				?>
				<div class="text-center">
					<?php if( ! empty( $image_src[0] ) ) : ?>
						<img class="img-xs" src="<?php echo $image_src[0] ?>" />
					<?php endif; ?>
					<p class="price text-small">$<?php echo $product->get_price(); ?></p>
				</div>
				<?php
			}
		}
	}
}

function fpusa_get_variations( $product ){
	$children = $product->get_children();
	$var_arr = array();
	foreach( $children as $child ){
		$product = wc_get_product( $child );
		$var_arr[$child] = $product->get_variation_attributes();
	}
	return $var_arr;
}

add_action( 'woocommerce_single_product_summary', 'fpusa_report_product_info_btn', 100 );
function fpusa_report_product_info_btn(){
	?>
	<a href="javascript:void(0)"data-toggle="modal" data-target="#fpusa_product_feedback"><i class="far fa-flag pr-1"></i> Report incorrect product information.</a>
	<div class="modal fade" id="fpusa_product_feedback" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Report an issue</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="report-problem-form" class="modal-body">
					<div class="form-group">
						<label>Please tell us about an issue.</label>
						<select id="report_where" class="form-control report-problem-option mb-2" name="report_where">
							<option value="0">Which part of the page?</option>
							<option value="images">Images</option>
							<option value="name">Product Name</option>
							<option value="bullet_points">Bullet Points</option>
							<option value="other">Other Product Details</option>
						</select>
						<select id="report_issue" class="form-control report-problem-option" name="report_issue" disabled>
							<option value="0">What is the issue?</option>
						</select>
					</div>

					<div class="form-group">
						<label>Comments (optional)</label>
						<textarea id="report-comments" name="comments"></textarea>
					</div>
					<small class="text-muted d-flex align-items-center">
						<i class="fas fa-exclamation pr-3"></i>
						<span>
							Please do not enter personal information. For questions about an order, go to <a href="/my-account">Your Account</a>.
						</span>
					</small>
      </div>
      <div class="modal-footer">
        <button id="report-submit" type="button" class="btn btn-primary" disabled>Submit</button>
				<button id="report-done" type="button" class="btn btn-primary" data-dismiss="modal" style="display: none;">Done</button>
      </div>
    </div>
  </div>
</div>
	<?php
}

function fpusa_single_product_feedback_callback(){
	// var_dump( $_POST );
	// TODO: Maybe we could add this somewhere on the backend for the theme author to fix, todo list?
	$header = "Issue Report: " . $_POST['where'] . ' - ' . $_POST['issue'];

	$msg = 'URL: ' . $_POST['url'] . '<br>';
	$msg .= 'Location: ' . $_POST['where'] . '<br>';
	$msg .= 'Issue: ' . $_POST['issue'] . '<br>';
	$msg .= 'Comment: ' . $_POST['comments'] . '<br>';

	wp_mail( get_option('admin_email'), $header, $msg );
	wp_die();
}

function wpdocs_set_html_mail_content_type() {
    return 'text/html';
}
add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

// function fpusa_get_image_srcset( $id, $sizes = NULL ){
// 	$srcset = array();
//
// 	if( is_null( $sizes ) ){
// 		$sizes = array('gallery_thumbnail', 'thumbnail', 'large', 'full');
// 	}
//
// 	foreach( $sizes as $size ){
// 		$src_arr = wp_get_attachment_image_src($id, $size);
// 		$srcset[$size] = $src_arr[0];
// 		// if( ! in_array( $src_arr[0], $srcset ) ) $srcset[$size] = $src_arr[0];
// 	}
//
// 	return $srcset;
// }

function fpusa_get_image_html( $id, $active = false, $size = 'woocommerce_gallery_thumbnail' ){
	// $srcset = fpusa_get_image_srcset( $id );
	// $src_str = implode(', ', $srcset);
	$src = array(
		$size => wp_get_attachment_image_src($id, $size),
		'full' => wp_get_attachment_image_src($id, 'full'),
	);
	?>
	<p class="img-xs fpusa-<?php echo $size ?>">
		<img src="<?php echo $src[$size][0]; ?>" src-full="<?php echo $src['full'][0]; ?>" <?php if( $active ) echo 'class="thumb-active"' ?> />
	</p>
	<?php
}

function fpusa_get_video_html( $url ){
	$v_id = str_replace( 'https://www.youtube.com/watch?v=', '', $url );
	$embed = 'https://www.youtube.com/embed/' . $v_id . '?iv_load_policy=3&autoplay=1';
	if( ! empty( $v_id ) ) : ?>
		<p class="img-xs fpusa-woocommerce_gallery_thumbnail">
			<img class="fpusa_video_link" src="<?php echo 'https://img.youtube.com/vi/' . $v_id . '/default.jpg'; ?>" vid-url="<?php echo $embed; ?>"/>
		</p>
	<?php endif;
}
