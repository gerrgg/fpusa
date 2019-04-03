<?php
/**
 * FallProtectionUSA functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package FallProtectionUSA
 */

if ( ! function_exists( 'fpusa_setup' ) ) :
	function fpusa_setup() {
		load_theme_textdomain( 'fpusa', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );

		register_nav_menus( array(
			'category_menu' => esc_html__( 'Categories', 'fpusa' ),
			'header_top_right' => esc_html__( 'Header Top Right', 'fpusa' ),
			'header_bottom_mid' => esc_html__( 'Header Bottom Mid', 'fpusa' ),
			'header_bottom_right' => esc_html__( 'Header Bottom Right', 'fpusa' ),
		) );


		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );


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

		// function incs
		require_once get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';
		require_once get_template_directory() . '/inc/class-wc-address.php';
		require_once get_template_directory() . '/inc/class-ups.php';
		require_once get_template_directory() . '/inc/wc-address-functions.php';
		require_once get_template_directory() . '/inc/wc-ajax-functions.php';
		require_once get_template_directory() . '/inc/wc-checkout-functions.php';
		require_once get_template_directory() . '/inc/wc-comment-functions.php';
		require_once get_template_directory() . '/inc/wc-header-functions.php';
		require_once get_template_directory() . '/inc/wc-shop-functions.php';
		require_once get_template_directory() . '/inc/wc-single-product-functions.php';



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


if ( ! function_exists( 'fpusa_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function fpusa_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( 'post-thumbnail', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
			?>
		</a>

		<?php
		endif; // End is_singular().
	}
endif;


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
	$GLOBALS['content_width'] = apply_filters( 'fpusa_content_width', 900 );
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

	wp_enqueue_style( 'owl-carousel', get_template_directory_uri() . '/css/owl.carousel.min.css' );

	wp_enqueue_style( 'owl-carousel-theme', get_template_directory_uri() . '/css/owl.theme.default.min.css' );

	wp_enqueue_style( 'dropzone-css', get_template_directory_uri() . '/css/dropzone.css' );

	wp_enqueue_script( 'bs4-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '4.2', true );

	wp_enqueue_script( 'owl-carousel-js', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '4.9', true );

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
		<div class="owl-carousel">
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



add_filter('woocommerce_edit_account_form_start', function(){
	?>
	<div class="row">
	<?php
});



add_action( 'fpusa_order_actions', 'fpusa_track_package' );

function fpusa_track_package( $order ){
	?>
	<a href="#" class="btn btn-primary text-white btn-block">Track Package</a>
	<?php
}



add_action( 'admin_post_handle_dropped_media', 'fpusa_handle_dropped_media' );



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

function fpusa_is_cat(){
	/**
	* checks for /product-category/ in URI and returns bool
	* @return bool
	*
	*/
	return preg_match( '/product-category/', $_SERVER['REQUEST_URI'] );
}

function wpdocs_set_html_mail_content_type() {
	/**
   * Send emails in HTML format.
   */
    return 'text/html';
}
add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

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
