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
			'header_top_right' => esc_html__( 'Header Top Right', 'fpusa' ),
			'header_bottom_mid' => esc_html__( 'Header Bottom Mid', 'fpusa' ),
			'header_bottom_right' => esc_html__( 'Header Bottom Right', 'fpusa' ),
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


		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 100,
			'width'       => 100,
			'flex-width'  => true,
			'flex-height' => true,
		) );

		require_once get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';
		require_once get_template_directory() . '/inc/class-wc-address.php';
		require_once get_template_directory() . '/inc/class-ups.php';
		require_once get_template_directory() . '/inc/wc-address-functions.php';

		/**
		 * Custom template tags for this theme.
		 */
		require_once get_template_directory() . '/inc/template-tags.php';

		/**
		 * Functions which enhance the theme by hooking into WordPress.
		 */
		require_once get_template_directory() . '/inc/template-functions.php';

		/**
		 * Admin Functions which enhance the theme by hooking into WordPress Backend.
		 */
		require_once get_template_directory() . '/inc/admin-template-functions.php';

		require_once get_template_directory() . '/inc/wc-checkout-functions.php';

		/**
		 * Functions which enhance the theme by hooking into WordPress.
		 */

		/**
		 * Customizer additions.
		 */
		require_once get_template_directory() . '/inc/customizer.php';

		/**
		 * Load WooCommerce compatibility file.
		 */
		if ( class_exists( 'WooCommerce' ) ) {
			require get_template_directory() . '/inc/woocommerce.php';
		}

		/**
		 * custom tables
		 */
		fpusa_create_comment_karma_table();
		fpusa_create_user_address_table();

	}
endif;
add_action( 'after_setup_theme', 'fpusa_setup' );
// Register the rest route here.






function fpusa_create_comment_karma_table(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'karma';
	$sql = "CREATE TABLE $table_name (
		karma_id mediumint(9) NOT NULL AUTO_INCREMENT,
		karma_user_id mediumint(9) NOT NULL,
		karma_comment_id mediumint(9) NOT NULL,
		karma_value mediumint(9) NOT NULL,
		PRIMARY KEY  (karma_id)
	) $charset_collate;";

	fpusa_maybe_create_table( $table_name, $sql );
}

function fpusa_maybe_create_table( $table_name, $sql ){
	// Add one library admin function for next function
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	maybe_create_table( $table_name, $sql );
}

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

	register_sidebar( array(
		'name'          => 'Header-top-right',
		'id'            => 'header_top_right',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="rounded">',
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

	wp_enqueue_style( 'dropzone-css', get_template_directory_uri() . '/css/dropzone.css' );

	wp_enqueue_script( 'bs4-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '4.2', true );


	wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), '4.9', true );

	wp_enqueue_script( 'zoom-js', get_template_directory_uri() . '/js/jquery.zoom.js', array('jquery'), '4.9', true );

	wp_enqueue_script( 'dropzone-js', get_template_directory_uri() . '/js/dropzone.js', array('jquery'));

	wp_enqueue_script( 'moment-js', 'http://momentjs.com/downloads/moment.min.js', array(), '4.9', true);

	wp_enqueue_script( 'js-functions', get_template_directory_uri() . '/js/dev/functions.js', array('jquery'), filemtime(get_template_directory() . '/js/dev/functions.js'), true );
	wp_localize_script( 'js-functions', 'ajax_object', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	));

	wp_enqueue_script( 'js-modal', get_template_directory_uri() . '/js/dev/modal.js', array('jquery'), filemtime(get_template_directory() . '/js/dev/modal.js'), true );
	wp_localize_script( 'js-modal', 'ajax_object', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	));

	if( is_checkout() ){
		wp_enqueue_script( 'js-checkout', get_template_directory_uri() . '/js/dev/checkout.js', array('jquery'), filemtime(get_template_directory() . '/js/dev/checkout.js'), true );
		wp_localize_script( 'js-checkout', 'ajax_object', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));
	}

	wp_enqueue_script( 'js-reviews', get_template_directory_uri() . '/js/dev/reviews.js', array('jquery'), filemtime(get_template_directory() . '/js/dev/reviews.js'), true );
	wp_localize_script( 'js-reviews', 'ajax_object', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	));

	wp_enqueue_script( 'js-single-product', get_template_directory_uri() . '/js/dev/single-product.js', array('jquery'), filemtime(get_template_directory() . '/js/dev/single-product.js'), true );
	wp_localize_script( 'js-single-product', 'ajax_object', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	));
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

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
add_action( 'woocommerce_after_cart_table', 'woocommerce_cart_totals', 10 );



function fpusa_choose_location_btn(){
	/*
	*
	* Allows the user to choose which address they would like to use.
	* TODO: Integrate with address class
	*
	*/
	$user = wp_get_current_user();
	$loc_str = fpusa_get_ship_address_str( $user );
	?>
	<a id="user-navigation" class="nav-link align-items-center" data-toggle="modal" data-target="#fpusa_choose_loc">
		<i class="fas fa-map-marker-alt fa-2x pr-2"></i>
		<div class="d-flex flex-column">
			<span id="deliver-name">
				Deliver to <?php echo $user->user_nicename; ?>
			</span>
			<span id="deliver-loc" class="highlight-text">
				<?php echo $loc_str; ?>
			</span>
		</div>
	</a>
	<?php
}

function fpusa_edit_location_modal(){
	/**
	*
	* Creates the html for the location modal
	*
	*/
	?>
	<div class="modal fade" id="fpusa_choose_loc" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="fpusa_choose_loc">Edit your location</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php ( is_user_logged_in() ) ? fpusa_get_user_loc_html() : fpusa_get_guest_loc_html(); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary text-white" data-dismiss="modal">Done</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function fpusa_get_guest_loc_html(){
	/**
	*
	* Displays html within the location modal for non-logged in users.
	*
	*/
	?>
	<p class='text-muted'>Delivery options and speeds may vary based on your location.</p>
	<a role="button" href="/my-account/" class='btn btn-primary btn-block text-white'>Sign in to edit your address</a>
	<?php
}

function fpusa_get_user_loc_html(){
	/**
	*
	* Displays html within the location modal for logged in users.
	*
	*/
	$address = fpusa_get_customer_location_details( 'shipping' );
	if( ! empty( $address ) ) : ?>
		<p class="text-muted">Delivery options and speeds can vary based differant locations</p>
		<label>Current Shipping Address:</label>
		<button role="button" class="text-left mb-2">
			<address>
				<b><?php echo $address['shipto']; ?></b>
				<span><?php echo $address['address_1']; ?>,</span><br>
				<span><?php echo $address['address_2']; ?></span>
				<span><?php echo $address['city']; ?> </span>
				<span><?php echo $address['state']; ?></span>
				<span><?php echo $address['postcode']; ?></span>
			</address>
		</button><br><br>
		<a href="/edit-address/">Edit Addresses</a>
	<?php else :
		 fpusa_get_guest_loc_html();
	endif;
}



function fpusa_get_myaccount_icons( $endpoint ){
	/**
	* Displays specified icons for each endpoint
	* @param string $endpoint - the woocommerce endpoint we are talking about
	* @return string html markup based on the value of $endpoint.
	*
	*/
	$icon = '';

	switch( $endpoint ){
		case 'dashboard':
			$icon =  'tachometer-alt';
		break;

		case 'orders':
			$icon =  'file-invoice-dollar';
		break;

		case 'payment-methods':
			$icon =  'credit-card';
		break;

		case 'downloads':
			$icon =  'file-download';
		break;

		case 'edit-address':
		 	$icon =  'address-book';
		break;

		case 'edit-account':
			$icon =  'user';
		break;

		case 'customer-logout':
			$icon =  'sign-out-alt';
		break;
	}

	return "<i class='fas fa-$icon fa-3x'></i>";
}

function fpusa_buy_again_modal(){
	/**
	* grabs a php file and displays its html contents.
	*/
	wc_get_template('global/buy-again-modal.php');
}

add_shortcode( 'yourstore', 'fpusa_yourstore_buy_it_again' );

function fpusa_yourstore_buy_it_again(){
	$items = fpusa_get_wc_customer_unique_item_purchases(); ?>
	<div class="row">
		<?php
		foreach( $items as $id ) :
			$product = wc_get_product( $id );
			if( $product ) : ?>
				<div class="col-6 col-sm-2">
						<div class="p-2">
						<a class="product-image-link-sm" href="<?php echo $product->get_permalink(); ?>">
							<?php echo $product->get_image(); ?>
						</a>

						<a href="<?php echo $product->get_permalink(); ?>">
							<p class="m-0"><?php echo $product->get_name(); ?></p>
						</a>
						<?php echo wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ); ?>
						<p class="price"><?php echo $product->get_price_html() ?></p>
						<a class="btn btn-warning" href="<?php echo $product->add_to_cart_url() ?>">Add to cart</a>
					</div>
				</div>
				<?php
			endif;
		endforeach; ?>
	</div><?php
}

function fpusa_slick_query($header, $args){
	/**
	 * Used to get the children of a product category
	 * @param string $header - The TITLE of the slider
	 * @param array $args - a query used to display differant information.
	 */
	$children  = fpusa_get_cat_children();
	if( $children ){
		$wc_query = new WP_Query( $args );
		?>
		<h3><?php echo $header; ?></h3>
		<div class="slick">
		<?php
		if( $wc_query->have_posts() ) :
			while( $wc_query->have_posts() ) :
				$wc_query->the_post();
				wc_get_template_part('content', 'product');
			endwhile;
		endif;
		wp_reset_postdata();
		?>
		</div>
		<?php
	}
}

function fpusa_shop_by_category( ){
	$cats = fpusa_get_cats_w_img();
	if( ! empty( $cats ) ){
		?>
		<h3>Shop by Category</h3>
		<div class="row">
			<?php foreach( $cats as $cat ) : ?>
				<div class="col-6 col-sm-2">
					<a href="<?php echo $cat['link']; ?>">
						<img src="<?php echo $cat['image_src']?>" />
						<p class="text-center"><?php echo $cat['name']; ?></p>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}

function fpusa_cat_sub_nav_cat_menu(){
	/**
	*
	*
	*/
		if( fpusa_is_cat() ) : // needed when on shop page
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

add_filter('woocommerce_edit_account_form_start', function(){
	?>
	<div class="row">
	<?php
});

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
	// TODO: MAKE AJAX
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
add_action( 'admin_post_handle_dropped_media', 'fpusa_handle_dropped_media' );

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
	/**
	 * outputs the opening tab for the div on the far right on the single product page.
	 */
	echo '<div class="fpusa-single-product-right mt-1">';
}

function fpusa_single_right_wrapper_end(){
	/**
	 * outputs the ending tab for the div on the far right on the single product page.
	 */
	echo '</div>';
}

// TODO: Include place on options page to setup links to social media.
// TODO: Social media integration - share buttons
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 1 );

add_action( 'fpusa_single_product_right', 'fpusa_single_product_price_right', 5 );
function fpusa_single_product_price_right(){
	/**
	 * Simply displays price above the add to cart button
	 */
	global $product;
	?>
	<div class="<?php echo $product->get_type(); ?> price"><?php echo $product->get_price_html(); ?></div>
	<?php
}

add_action( 'fpusa_single_product_right', 'fpusa_single_product_shipping_upsell', 10 );
/**
 * outputs html for shipping upsells, need it tommorow?
 * TODO: Need to integrate with cart, checkout and Shipping? Are we going to use Shippo?
 */
function fpusa_single_product_shipping_upsell(){
	?>
	<p style="overflow: hidden;">fpusa_single_product_shipping_upsell();</p>
	<?php
}

add_action( 'fpusa_single_product_right', 'fpusa_single_product_stock', 15 );

function fpusa_single_product_stock(){
	/**
	 * outputs product stock status on the far right column.
	 */
	global $product;
	echo wc_get_stock_html( $product );
}

add_action( 'fpusa_single_product_right', 'fpusa_add_to_cart_btn', 50 );
function fpusa_add_to_cart_btn(){
	/**
	 * Outputs the html for the add to cart button.
	 * Currently, this is rigged to simulate the add to cart button and qty, but the
	 * real elements are in the woocommerce_single_product_summary();
	 */
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
	/**
	 * Outputs the html for the add to cart button.
	 */
	$loc_str = fpusa_get_ship_address_str( );
	echo '<button class="btn" data-toggle="modal" data-target="#fpusa_choose_loc"><i class="fas fa-map-marker-alt pr-2 text-dark"></i>Deliver to ' . $loc_str . '</button>';
}

add_action( 'woocommerce_single_product_summary', 'fpusa_report_product_info_btn', 100 );
function fpusa_report_product_info_btn(){
	/**
   * Output the report problem link and modal
   */
	wc_get_template('single-product/report-problem.php');
}

function fpusa_single_product_feedback_callback(){
	/**
   * Email user reported problem to admin.
   */
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
	/**
   * Send emails in HTML format.
   */
    return 'text/html';
}
add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

function fpusa_get_image_html( $id, $active = false, $size = 'woocommerce_gallery_thumbnail' ){
	/**
		* outs puts an image and includes markup for the full-size version of the image.
    * @param int $id - The ID of the attachment
	  * @param bool $active - determines whether or now the returned html should include the class thumb-active
		* @param string $size - determines the size the image should return
   */
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
	/**
		* takes in a url, converts the url to work with youtube API and generates markup for the thumbnail.
		* @param string $url
   */

	 // remove the uneeded url parts
	$v_id = str_replace( 'https://www.youtube.com/watch?v=', '', $url );

	// alter url to properly embed the video - Must work with youtube API
	$embed = 'https://www.youtube.com/embed/' . $v_id . '?iv_load_policy=3&autoplay=1';

	// display html for with video thumbnail img and url to video at vid-url html attribute.
	if( ! empty( $v_id ) ) : ?>
		<p class="img-xs fpusa-woocommerce_gallery_thumbnail">
			<img class="fpusa_video_link" src="<?php echo 'https://img.youtube.com/vi/' . $v_id . '/default.jpg'; ?>" vid-url="<?php echo $embed; ?>"/>
		</p>
	<?php endif;
}

function fpusa_product_specifications(){
	/*
	* Gets the html for product specifications
	*/
	wc_get_template( 'single-product/tabs/product-specifications.php' );
}
add_action( 'before_woocommerce_product_additional_information', 'fpusa_product_specifications', 5 );


add_filter( 'woocommerce_product_tabs', 'fpusa_custom_review_tab', 98 );
function fpusa_custom_review_tab( $tabs ) {
	/*
	* sets the callback for the review tab to a custom function
	*/
	$tabs['reviews']['callback'] = 'fpusa_get_comments_template';
	return $tabs;
}

function fpusa_get_comments_template(){
	/*
		looks for a file in /yourtheme/woocommerce/single-product/customer_reviews.php
	*/
	wc_get_template('single-product/customer_reviews.php');
}

function fpusa_cr_get_stars(){
	wc_get_template( 'single-product/review/rating.php' );
}
add_action( 'fpusa_customer_review_left', 'fpusa_cr_get_stars', 10 );



function fpusa_get_rating_histogram( $ratings, $count ){
	?>
	<table class="product-rating-histogram">
	<?php for( $i = 5; $i > 0; $i-- ) :
		$now = ( isset( $ratings[$i] ) ) ? intval( ( $ratings[$i] / $count ) * 100 ) : 0;
	?>
		<tr>
			<td>
				<a href=""><?php echo $i ?> stars</a>
			</td>
			<td style="width: 80%">
				<a class="progress">
					<div class="progress-bar" role="progressbar" style="width: <?php echo $now; ?>%"aria-valuenow="<?php echo $now ?>%" aria-valuemin="0" aria-valuemax="100"></div>
				</a>
			</td>
			<td>
				<a href=""><?php echo $now ?>%</a>
			</td>
		</tr>
	<?php endfor; ?>
	</table> <?php
}

function fpusa_cr_get_review_btn(){
	/*
	* output html for review button
	*/
	wc_get_template('/single-product/review/button.php');
}
add_action( 'fpusa_customer_review_left', 'fpusa_cr_get_review_btn', 20 );

add_action( 'fpusa_customer_review_right', 'fpusa_get_user_product_images', 10 );
function fpusa_get_user_product_images(){
	global $product;
	$col = 4;
	$srcs = fpusa_get_user_content_src( null, $product->get_id() );
	if( ! empty( $srcs ) ) : ?>
		<h5><?php echo sizeof( $srcs ) ?> Customer Images</h5>
		<div id="user-product-img-overview">
			<?php foreach( $srcs as $src ) fpusa_img_link_to_full_show_thumb( $src[1][0], $src[0][0] ); ?>
		</div>
		<?php // TODO: Colorbox! http://www.jacklmoore.com/colorbox/  ?>
		<?php if( sizeof( $srcs ) > $col ) echo '<a href="#">See all product images</a>';
	endif;
}

add_action( 'fpusa_customer_review_right', 'fpusa_sort_comments_by', 15 );

function fpusa_sort_comments_by(){
	global $product;
	?>
	<div class="form-group float-right mt-3">
		<label><b>Sort by</b></label>
		<select class="" id="sort_comments_by" data-product-id="<?php echo $product->get_id(); ?>">
			<option value="meta_value">Top Reviews</option>
			<option value="comment_karma">Most Helpful</option>
			<option value="comment_date">Most Recent</option>
		</select>
	</div>
	<?php
}

add_action( 'fpusa_customer_review_right', 'fpusa_get_reviews', 20 );

function fpusa_get_reviews(){
	/**
	* uses the wp_list_comments() function to setup args and use a custom callback function
	*/
	global $product;

	$args = array(
		'post_type' => 'product',
		'post_id' => $product->get_id(),
		'meta_key' => 'rating',
		'orderby' => 'meta_value',
	 );
	$comments = get_comments( $args );
	// var_dump( $comments );
	?>
	<div id="comments-wrapper">
		<?php wp_list_comments( array( 'callback' => 'fpusa_comments', 'style' => 'div' ), $comments); ?>
	</div>
	<?php
}

add_action('wp_ajax_fpusa_sort_product_reviews', 'fpusa_sort_product_reviews_by');
add_action('wp_ajax_nopriv_fpusa_sort_product_reviews', 'fpusa_sort_product_reviews_by');

function fpusa_sort_product_reviews_by(){
	$args = array(
		'post_type' => 'product',
		'post_id'		=> $_POST['p_id'],
		'meta_key'  => 'rating',
		'orderby'		=> $_POST['sortby']
	);

	$comments = get_comments( $args );
	wp_send_json( $comments );

	wp_die();
}

function fpusa_comments( $comment ){
	/**
	* provides the html markup for each individual comment a product may have.
	* @param WP_Comment $comment
	*/
	$rating = get_metadata( 'comment', $comment->comment_ID, 'rating', true );
	$headline = get_metadata( 'comment', $comment->comment_ID, 'headline', true );
	$verified = get_metadata( 'comment', $comment->comment_ID, 'verified', true );
	$srcs = fpusa_get_user_content_src( $comment->user_id, get_the_id() );

	// var_dump( $srcs );
	?>

	<div id="<?php echo $comment->comment_ID; ?>" class="comment my-4">
		<div class="comment-top d-flex align-items-center">
			<span class="pr-2">
				<?php echo get_avatar($comment->comment_author_email, 50); ?>
			</span>
			<b><?php echo $comment->comment_author ?></b>
		</div>
		<div class="comment-meta">
			<div class="d-flex">
				<?php echo wc_get_rating_html($rating); ?>
				<?php if( ! empty( $headline ) ) echo '<b class="pl-2">' . $headline . '</b>'; ?>
			</div>
			<?php if( $verified ) echo '<span class="verified">Verified Purchase</span>'; ?>
			<div class="d-flex">
				<?php foreach( $srcs as $src ) fpusa_img_link_to_full_show_thumb( $src[1][0], $src[0][0], 'img-xs' );?>
			</div>
			<p class="pt-3"><?php echo $comment->comment_content ?></p>
			<p>
			</p>

		</div>
		<div class="comment-actions" role="group">
			<?php do_action('fpusa_comment_actions', $comment->comment_ID ); ?>
		</div>
		<a role="button" class="show-comments-thread">
		<?php fpusa_get_comments_thread( $comment->comment_ID );
}

function fpusa_get_product_review_link( $id, $action = 'post'){
	/**
	* returns the appropirate url based on the ID and action parameters
	* @param int $id - The id of the product trying to review
	* @param string $action - post is for creating new reviews, edit is for updating reviews
	*/
	$url = "/review?action=$action&p_id=$id";
	return $url;
}

function fpusa_get_comments_thread( $comment_id ){
	$args = array(
		'post_id' => 0,
		'parent' => $comment_id,
		'type' => 'response',
		'orderby' => 'comment_karma',
	);

	$comments = get_comments( $args );

	// var_dump( $comments );

	if( ! empty( $comments ) ){
		echo '<a href="javascript:void(0);" role="button" class="show-comments-thread">Show all ' . sizeof( $comments ) . ' comments</a>';
		echo '<div class="comment-thread">';
		wp_list_comments( array( 'callback' => 'fpusa_comments_thread_callback', 'style' => 'div' ), $comments);
		echo '</div>';
	}
}

function fpusa_comments_thread_callback( $comment ){
	$prev_vote = fpusa_get_previous_karma_vote( $comment->comment_ID );
	$value = ( isset($prev_vote->karma_value) ) ? $prev_vote->karma_value : 0;
	?>
	<div id="<?php echo $comment->comment_ID ?>" class="comment-reply">
		<div class="comment-top d-flex align-items-center">
			<div class="reply-karma d-flex flex-column justify-content-center align-items center text-center">
				<i class="fas fa-chevron-up <?php if( $value == 1 ) echo 'karma-highlight'; ?>" data-increment="1"></i>
				<span id="<?php echo $comment->comment_ID ?>_comment_karma" class="text-center"><?php echo $comment->comment_karma ?></span>
				<i class="fas fa-chevron-down <?php if( $value == -1 ) echo 'karma-highlight'; ?>" data-increment="-1"></i>
			</div>
			<span class="pr-2 border-right">
				<?php echo get_avatar($comment->comment_author_email, 50); ?>
			</span>
			<div class="pl-2">
				<p class="m-0"><b><?php echo $comment->comment_author ?></b></p>
				<p class="text-small"><?php echo $comment->comment_content; ?></p>
			</div>
		</div>

	<?php
}

add_action( 'fpusa_comment_actions', 'fpusa_comment_helpful_button', 10, 1 );
function fpusa_comment_helpful_button( $comment_id ){
	// if user has clicked, switch to unhelpful btn
	$comment = get_comment( $comment_id );
	fpusa_get_helpful_button_html( $comment_id, $comment->comment_karma );
}

function fpusa_get_helpful_button_html( $comment_id, $karma ){
	$args = array(
		'class' => 'success',
		'text' => 'Helpful'
	);

	$prev_vote = fpusa_get_previous_karma_vote( $comment_id );

	if( ! empty( $prev_vote ) ){
		$args['class'] = ( $prev_vote->karma_value != 1 ) ? 'success' : 'danger';
		$args['text'] = ( $prev_vote->karma_value != 1 ) ? 'Helpful' : 'Not Helpful';
	}

	?>
	<button type="button" class="btn comment-helpful-btn btn-<?php echo $args['class'] ?>">
		<span class="btn-text"><?php echo $args['text'] ?></span>
		<span class="badge badge-<?php echo $args['class'] ?>"><?php echo $karma ?></span>
	</button>
	<?php
}

add_action( 'wp_ajax_fpusa_comment_helpful', 'fpusa_add_to_comment_karma' );
function fpusa_add_to_comment_karma(){
	// get comment, increment karma, and update in DB.
	$comment = get_comment( $_POST['id'], ARRAY_A );
	$comment['comment_karma'] = intval($comment['comment_karma']) + intval( $_POST['increment'] );

	$prev_vote = fpusa_get_previous_karma_vote( $_POST['id'] );

	if( $prev_vote->karma_value != $_POST['increment'] ){
		wp_update_comment( $comment );
		echo $comment['comment_karma'];
	}

	fpusa_update_user_karma_link( $_POST['id'], $_POST['increment'] );
	// keep track of who has clicked helpful, keep json of users who clicked in comment_meta.
	wp_die();
}

function fpusa_update_user_karma_link( $comment_id, $karma ){
	global $wpdb;
	$table_name = $wpdb->prefix . 'karma';

	$args = array(
		'karma_user_id' => get_current_user_id(),
		'karma_comment_id' => $comment_id,
		'karma_value' => $karma
	);

	//returns id of prev vote;
	$prev_vote = fpusa_get_previous_karma_vote( $comment_id );

	if( empty( $prev_vote ) ){
		$wpdb->insert( $table_name, $args );
	} else {
		$wpdb->update( $table_name, $args, array( 'karma_id' => $prev_vote->karma_id ) );
	}

	return $prev_vote;

}

function fpusa_get_previous_karma_vote( $comment_id ){
	global $wpdb;
	$user_id = get_current_user_id();
	$table_name = $wpdb->prefix . 'karma';
	$result = $wpdb->get_row( "SELECT *
														 FROM $table_name
														 WHERE karma_user_id = $user_id
														 AND karma_comment_id = $comment_id" );

	// var_dump( $result );
	return $result;
}



add_action( 'fpusa_comment_actions', 'fpusa_comment_on_comment', 20, 1 );
function fpusa_comment_on_comment( $parent_id ){
	?>
	<button type="button" class="btn btn-outline-secondary comment-on-comment">
		Comment
		<i class="far fa-comment"></i>
	</button>
	<?php
}

add_action( 'wp_ajax_fpusa_comment_on_comment', 'fpusa_comment_on_comment_callback' );
function fpusa_comment_on_comment_callback(){
	$user = wp_get_current_user();
	$comment = get_comment( $_POST['parent_comment'] );

	$comment_id = wp_insert_comment( array(
    'comment_author' => $user->user_login,
    'comment_author_email' => $user->user_email,
    'comment_content' => $_POST['comment'],
    'comment_type' => 'response',
    'comment_parent' => $_POST['parent_comment'],
    'user_id' => $user->ID,
		'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
		'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
    'comment_date' => current_time('mysql'),
    'comment_approved' => 1,
	));

	echo $comment_id;

	wp_die();
}

// add_action( 'fpusa_comment_actions', 'fpusa_report_comment', 30, 1 );
function fpusa_report_comment( $parent_id ){
	?>
	<button type="button" class="btn btn-outline-danger comment-report">
		Report Abuse
		<i class="fas fa-bullhorn"></i>
	</button>
	<?php
}




add_shortcode( 'create_review', 'fpusa_create_review_template' );

function fpusa_create_review_template(){
	$action = $_GET['action'];

	switch( $action ){
		case 'post':
			wc_get_template('single-product/review/create.php');
			break;
		case 'review-more':
			fpusa_get_more_to_review();
			break;
		case 'edit':
			wc_get_template('single-product/review/create.php');
			break;
		case 'show':
			wc_get_template('single-product/review/show.php');
			break;
	}
}

function fpusa_get_more_to_review(){
	$items = fpusa_get_wc_customer_unique_item_purchases();
	echo '<h3>Review other purchases!</h3>';
	echo '<div class="row">';
	foreach( $items as $id ){
		$product = wc_get_product( $id );
		$comment = fpusa_get_user_product_review( $id );
		$rating = get_metadata('comment', $comment['comment_ID'], 'rating', true);
		if( $product ) : ?>
			<div class="col-6 col-sm-3 my-3">
				<?php if ( $rating > 0 ) : ?>
				<a href="<?php echo fpusa_get_product_review_link( $id, 'edit' ); ?>" class="text-center">
					<?php echo $product->get_image(); ?>
					<?php fpusa_get_stars_review_link( $id, $rating, 'edit', 1 ) ?>
				</a>
			<?php else : ?>
				<a href="<?php echo fpusa_get_product_review_link( $id ); ?>" class="text-center">
					<?php echo $product->get_image(); ?>
					<?php fpusa_get_stars_review_link( $id, $rating, 'post', 1 ) ?>
				</a>
			<?php endif; ?>
			</div>
		<?php endif;

	}
	echo '</div>';
}

function fpusa_get_overall_star_rating_form( $id, $rating, $action = 'post', $link ){
	?>
	<div class="create-review-star-rating form-group">
		<h5>Overall rating</h5>
		<?php fpusa_get_stars_review_link( $id, $rating, $action, $link ) ?>
		<input id="product-rating" name="rating" type="hidden" value="<?php echo $rating; ?>" />
	</div>
	<?php
}



function fpusa_get_stars_review_link( $id, $rating, $action = 'post', $link = 1){
	$max = 5;
	$empty = $max - $rating;
	?>
	<div class="d-flex my-1">

		<?php for( $i = 1; $i <= $rating; $i++ ) : ?>
			<?php if( $link != 0 ) : ?><a class="link-color-normal" href="<?php echo fpusa_get_product_review_link( $id, $action ) ?>"><?php endif; ?>
				<i id="star-<?php echo $i ?>" class="fas fa-star fa-2x click-star" data-rating="<?php echo $i; ?>"></i>
			<?php if( $link != 0  ) : ?></a><?php endif; ?>
		<?php endfor; ?>

		<?php for( $i = $rating + 1; $i <= $max; $i++ ) : ?>
			<?php if( $link != 0  ) : ?><a class="link-color-normal" href="<?php echo fpusa_get_product_review_link( $id, $action ) ?>"><?php endif; ?>
				<i id="star-<?php echo $i ?>" class="far fa-star fa-2x click-star" data-rating="<?php echo $i; ?>"></i>
			<?php if( $link != 0  ) : ?></a><?php endif; ?>
		<?php endfor; ?>

	</div><?php
}



function fpusa_get_wc_customer_unique_item_purchases( $user_id = NULL ){
	if( is_null( $user_id ) ) $user_id = get_current_user_id();
	$orders = wc_get_orders( array( 'customer_id' => $user_id ) );
	$unique_items = array();
	foreach( $orders as $order ){
		foreach( $order->get_items() as $item ){
			$product = $item->get_product();
			if( ! empty( $product ) && ! in_array( $product->get_id(), $unique_items ) ){
				array_push( $unique_items, $product->get_id() );
			}
		}
	}

	return $unique_items;
}

function fpusa_create_review_product_preview( $product ){
	$src = wp_get_attachment_image_src( $product->get_image_id() );
	?>
	<div class="product-preview d-flex align-items-center form-group">
    <img src="<?php echo $src[0]; ?>" class="img-xs" />
    <p class="m-0"><?php echo $product->get_name(); ?></p>
  </div>
	<?php
}

function fpusa_get_user_product_review( $p_id ){

	$comments = get_comments(array(
		'post_id' 						=> $p_id,
		'user_id' 						=> get_current_user_id(),
		'include_unapproved'  => false,
	));

	$comment = get_comment( $comments[0]->comment_ID , ARRAY_A );

	return $comment;
}

function fpusa_add_headline( $headline = null ){
	?>
	<div class="form-group">
		<h5>Add a headline</h5>
		<input type="text" class="form-control" name="headline" value="<?php if( ! is_null( $headline ) ) echo $headline; ?>">
	</div>
	<?php
}

function fpusa_add_comment( $content = '' ){
	?>
	<div class="form-group">
		<h5>Write your review</h5>
		<textarea rows="4" name="comment" class="form-control" placeholder="What did you like or dislike? What did you use this product for?" ><?php if( ! empty( $content ) ) echo $content; ?></textarea>
	</div>
	<?php
}

function fpusa_drop_zone(){
	//https://stackoverflow.com/questions/29411257/how-to-integrate-dropzonejs-with-wordpress-media-handler-in-frontend
	?>
		<div class="form-group">
			<h5>Add a photo</h5>
			<p>A picture speaks a thousand words.</p>
			<input type="file" name="files">
		</div>
	<?php
}


function fpusa_handle_dropped_media(){
		// Dropzone.js integration to handle media upload
		status_header(200);

		$num_files = count( $_FILES['files']['tmp_name'] );
		$attachments = array();
		$attachment_id = 0;

		if ( !empty($_FILES) ) {
        $files = $_FILES;
        foreach($files as $file) {
            $newfile = array (
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size']
            );

            $_FILES = array('upload' => $newfile);
            foreach($_FILES as $file => $array) {
							//https://codex.wordpress.org/Function_Reference/media_handle_upload
                $attachment_id = media_handle_upload( $file, $_POST['product_id'] );
								array_push( $attachments, $attachment_id );
            }
        }
    }

    return $attachments;
    die();
}

add_action( 'admin_post_fpusa_process_product_review', 'fpusa_process_review' );
// add_action( 'admin_post_nopriv_fpusa_process_product_review', 'fpusa_process_review' );

function fpusa_process_review(){
	// create the comment, connect it to the product
	$comment_id = fpusa_create_comment( $_POST );

	if( ! empty( $_FILES ) ){
		// upload the attachments, collect the attachment ids
		$attachment_ids = fpusa_handle_dropped_media();
		//foreach attachment, create a postmeta pointing to the comment the attachment belongs to.
		foreach( $attachment_ids as $id ){
			update_post_meta( $id, '_wp_attachment_comment', $comment_id );
		}
	}

	wp_redirect( fpusa_get_review_more_link() );
}

function fpusa_get_review_more_link(){
	return '/review?action=review-more';
}

function fpusa_create_comment( $data ){
	global $wpdb;
	$user = wp_get_current_user();

	$comment = fpusa_get_user_product_review( $data['product_id'] );

	$args = array(
		'comment_post_ID' => $data['product_id'],
		'comment_author'	=> $user->user_login,
		'comment_author_email'	=> $user->user_email,
		'comment_author_url'	=> $user->user_url,
		'comment_content' =>  $data['comment'],
		'comment_type'			=> 'review',
		'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
		'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
		'comment_date' => current_time( 'mysql', $gmt = 0 ),
		'user_id' => get_current_user_id(),
		'comment_approved' => 1,
	);

	if( ! empty( $comment ) ){
		// careful of character case!
		$comment_id = $comment['comment_ID'];
		$args['comment_ID'] = $comment_id;
		wp_update_comment($args);
	} else {
		$comment_id = wp_insert_comment($args);
	}



	update_comment_meta( $comment_id, 'rating', $data['rating'] );
	update_comment_meta( $comment_id, 'headline', $data['headline'] );

	// https://docs.woocommerce.com/wc-apidocs/function-wc_customer_bought_product.html
	update_comment_meta( $comment_id, 'verified',
		wc_customer_bought_product( $user->user_email, get_current_user_id(), $data['product_id'] )
	);

	return $comment_id;
}

function fpusa_img_link_to_full_show_thumb( $full, $thumb, $img_class = 'img-md', $echo = true ){
	if( $echo ) : ?>
		<a href="<?php echo $full ?>">
			<img src="<?php echo $thumb ?>" class="mx-1 <?php echo $img_class; ?>"/>
		</a>
	<?php
	else :
		$html = '';
		$html .= "<a href='$full'>";
		$html .= "<img src='$thumb' class='mx-1 $img_class'>";
		$html .= "</a>";
		return $html;
  endif;
}


function fpusa_get_user_content_src( $user = null, $p_id = '' ){
	global $wpdb;
	$good_srcs = array();

	// add another condition if the user ID is defined.
	$qry_str = "SELECT ID
							FROM $wpdb->posts
							WHERE post_type = 'attachment'
							AND post_parent = $p_id";
	if( ! is_null( $user ) ) $qry_str .= " AND post_author = $user";

	$results = $wpdb->get_results("$qry_str");

	foreach( $results as $attachment ){
		$comment_id = get_post_meta( $attachment->ID, '_wp_attachment_comment', true );
		$comment = get_comment( $comment_id, ARRAY_A ) ;
		if( ! empty( $comment_id ) && $comment['comment_approved'] == 1 ){
			array_push( $good_srcs, array(
					wp_get_attachment_image_src( $attachment->ID, 'thumbnail', false ),
					wp_get_attachment_image_src( $attachment->ID, 'full', false )
			));
		}
	}
	return $good_srcs;
}

function fpusa_get_cart_subtotal(){
	global $woocommerce;
	?>
		<label>Subtotal (<?php echo $woocommerce->cart->get_cart_contents_count() ?> items):</label>
		<span class="price">$<?php echo $woocommerce->cart->get_subtotal() ?></span>
	<?php

}

function pluralize( $count, $str ){
	if( $count > 1 ) $str .= 's';
	return $str;
}

function make_modal_btn( $args = array() ){
	$defaults = array(
		'text'   => 'text',
		'title'  => 'title',
		'model'  => '',
		'action' => '',
		'id'		 => '',
	);

	$args = wp_parse_args( $args, $defaults );

	echo sprintf( '<a href="#fpusa_modal" data-toggle="modal"
										data-title="%s" data-model="%s" data-action="%s"
										data-id="%d" class="">%s</a>',
		$args['title'],
		$args['model'],
		$args['action'],
		$args['id'],
		$args['text']
 	);
}
