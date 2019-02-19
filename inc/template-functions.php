<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package FallProtectionUSA
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function fpusa_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'fpusa_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function fpusa_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'fpusa_pingback_header' );

function fpusa_get_header_right(){
	$user = wp_get_current_user();
	$login_msg = ( ! empty( $user->display_name ) ) ? "Hello, $user->display_name" : "Hello, Sign in";
	?>
	<ul class="navbar-nav d-flex align-items-end">
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<?php echo $login_msg; ?>
			</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
				<?php if( ( empty( $user->ID ) ) ) : ?>
				<a class="dropdown-item" href="/my-account/">Login/Register</a>
				<?php endif; ?>
				<a class="dropdown-item" href="/my-account/orders/">My Orders</a>
				<a class="dropdown-item" href="/my-account/edit-account/">My Account</a>
				<a class="dropdown-item" href="/my-account/edit-address/">Edit Address</a>
				<?php if( ( ! empty( $user->ID ) ) ) : ?>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="/my-account/customer-logout">Logout</a>
				<?php endif; ?>
			</div>
		</li>
		<li class="nav-item">
      <a class="nav-link" href="/my-account/orders/">Orders</a>
    </li>
		<li class="nav-item">
      <a class="nav-link" href="/cart/"><i class="fas fa-shopping-cart fa-2x"></i></a>
    </li>
	</ul>
	<?php
}

function fpusa_choose_location_btn(){
	$user = wp_get_current_user();
	if( ! empty($user) ){
		$customer = new WC_Customer( $user->ID );
		if( ! empty( $customer->get_shipping_city() ) && $customer->get_shipping_postcode() ){
			$loc_str = $customer->get_shipping_city() . ' ' . $customer->get_shipping_postcode();
		} else {
			$loc_str = "Your Location";
		}
	} else {
		$loc_str = "Your Location";
	}
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


function fpusa_make_modal( $args = '' ){
	 $defaults = array(
		 'id' 					=> 'defaultModal',
		 'labelledby' 	=> '',
		 'inner_class' 	=> 'modal-dialog',
		 'header' 			=> 'Modal Title',
		 'body' 				=> 'Body',
		 'footer' 			=> '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        								<button type="button" class="btn btn-primary">Save changes</button>',
	 );

	 $args = wp_parse_args( $args, $defaults );
	?>
	<!-- Modal -->
	<div class="modal fade" id="<?php echo $args['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $args['labelledby'] ?>" aria-hidden="true">
	  <div class="modal-dialog <?php echo $args['inner_class'] ?>" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo $args['header'] ?></h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <?php echo $args['body'] ?>
	      </div>
	      <div class="modal-footer">
	        <?php echo $args['footer'] ?>
	      </div>
	    </div>
	  </div>
	</div>
	<?php
}

function fpusa_edit_location_modal(){
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
	?>
	<p class='text-muted'>Delivery options and speeds may vary based on your location.</p>
	<a role="button" href="/my-account/" class='btn btn-primary btn-block text-white'>Sign in to edit your address</a>
	<?php
}

function fpusa_get_user_loc_html(){
	?>
	<?php
}

function fpusa_get_myaccount_icons( $endpoint ){
	$icon = '';

	switch( $endpoint ){
		case 'dashboard':
			$icon =  'tachometer-alt';
		break;

		case 'orders':
			$icon =  'file-invoice-dollar';
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
	?>
	<div class="modal fade" id="fpusa_buy_again" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Buy Again</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
				<div class="row">
					<div class="col-12 col-sm-4">
						<a id="fpusa_ba_img_link" href="#" class="product-image-link product-image-link-sm"></a>
					</div>
					<div class="col">
						<a id="fpusa_ba_title" href=""></a>
						<div class="d-flex">
							<span class="pr-1">Current Price:</span>
							<span id="fpusa_ba_price" class="price"></span>
						</div>
						<div id="fpusa_ba_stock"></div>
						<div id="fpusa_ba_qty"></div>
						<form id="fpusa_ba_form" class="cart" action="" method="post" enctype="multipart/form-data">
							<input type="number" id="<?php echo uniqid( 'quantity_' ) ?>" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
							<button type="submit" name="add-to-cart" value="" class="single_add_to_cart_button button alt">Add to cart</button>
						</form>
					</div>
				</div>
			</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
	<?php
}

function fpusa_tags_to_specs(){
	global $product;
	$tags = fpusa_split_tags( get_the_terms( $product->get_id(), 'product_tag' ) );

	if( empty( $tags ) ) return 0;

	foreach( $tags as $tag ) : ?>
	<tr>
		<th><?php echo $tag[0] ?></th>
		<td><p><?php echo $tag[1] ?></p></td>
	</tr>
	<?php endforeach;
}

add_shortcode( 'yourstore', 'fpusa_your_store_callback' );

function fpusa_your_store_callback(){
	if( isset( $_GET['fpusa_action'] ) ){
		$action = 'fpusa_yourstore_' . $_GET['fpusa_action'];
		$action();
	}
}

function fpusa_yourstore_buy_it_again(){

	$args = array(
	    'customer_id' => get_current_user_id(),
	);
	$already_shown = array();
	$orders = wc_get_orders( $args );
	wc_print_notices();
	if( ! empty( $orders ) ) : ?>
	<h2>Buy it again</h2>
	<div class="row">
	<?php
	foreach( $orders as $order ){
		foreach( $order->get_items() as $item ){
			$product = $item->get_product();
			if( ! empty( $product ) && ! in_array( $product->get_id(), $already_shown ) ) :
				array_push( $already_shown, $product->get_id() );
			?>
				<div class="col-6 col-sm-2">
						<div class="p-2">
						<a class="product-image-link-sm" href="<?php echo $product->get_permalink(); ?>">
							<?php echo $product->get_image(); ?>
						</a>
						<a href="<?php echo $product->get_permalink(); ?>">
							<?php echo $product->get_name(); ?>
						</a>
						<?php echo wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ); ?>
						<p class="price"><?php echo $product->get_price_html() ?></p>
						<a class="btn btn-warning" href="<?php echo $product->add_to_cart_url() ?>">Add to cart</a>
					</div>
				</div>
			<?php endif;
		}
	}
	else :
		woocommerce_login_form( array('message' => 'Whoops, we couldn\'t find any previous orders. Login or register.') );
	endif;
}
