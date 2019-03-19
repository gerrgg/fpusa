<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
add_action( 'fpusa_after_payment', 'woocommerce_checkout_coupon_form', 10 );

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'fpusa_checkout_step_2', 'woocommerce_checkout_payment' );

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
// add_action( 'fpusa_checkout_step_3', function(){ echo '<div class="card border-bottom p-3">'; }, 5 );
add_action( 'fpusa_checkout_step_3', 'woocommerce_order_review', 10 );
add_action( 'fpusa_checkout_step_3', 'fpusa_place_order', 20 );

add_action( 'fpusa_order_summary', 'get_order_summary' );
function get_order_summary(){
	wc_get_template( 'cart/cart-totals.php' );
}

function fpusa_place_order(){
	?>
	<div class="card p-3 mt-3">
		<div class="d-flex align-items-center">
			<button id="place-order" type="submit" class="btn btn-warning btn-block">Place your order</button>
			<span class="mx-5">
				<div class="order-total">
					Order Total: <?php wc_cart_totals_order_total_html(); ?>
				</div>
				<p id="order-instructions-btm">By placing your order, you agree to Amazon.com's privacy notice and conditions of use.</p>
			</span>
		</div>
	</div>
	<?php
}
// add_action( 'fpusa_checkout_step_3', function(){ echo '</div>'; }, 15 );

add_action( 'woocommerce_before_checkout_form', 'fpusa_checkout_header', 1, 1 );
function fpusa_checkout_header( $checkout ){
	$items_count = sizeof( WC()->cart->get_cart() );
	?>
	<nav id="checkout-header" class="navbar navbar-dark bg-dark">
		<div class="container">
			<div class="d-flex justify-content-between align-items-center w-100">
				<?php the_custom_logo(); ?>
				<h1>Checkout
					(<a class="link-color-normal text-success" href=""><?php echo $items_count . ' ' . pluralize( $items_count, 'item' ) ?></a>)
				</h1>
				<a href="<?php echo get_privacy_policy_url() ?>"><i class="fas fa-lock fa-2x"></i></a>
			</div>
		</div>
	</nav>
	<?php
}

function fpusa_checkout_steps( $parent, $labels ){
	$max_steps = sizeof( $labels );
	for( $i = 1; $i <= $max_steps; $i++ ) : ?>
		<h4 class="mb-2">
			<a id="step-btn-<?php echo $i; ?>" href="#step-<?php echo $i; ?>" class="checkout-step" data-toggle="collapse" role="button" aria-expanded="<?php if( $i == 1 ) echo 'true'  ?>" aria-controls="step-<?php echo $i; ?>">
				<span class="pr-2"><?php echo $i; ?>: </span>
				<span><?php echo $labels[$i - 1] ?></span>
			</a>
		</h4>
		<div class="pl-5 ml-3 my-2 collapse <?php if( $i == 1 ) echo 'show'  ?>" id="step-<?php echo $i; ?>" data-parent="#<?php echo $parent; ?>">
			<?php do_action( 'fpusa_checkout_step_' . $i ); ?>
		</div>
	<?php endfor;
}

add_action( 'fpusa_checkout_step_1', 'fpusa_choose_shipping_address' );
function fpusa_choose_shipping_address(){
  // TODO: Sort addresses by default first, than date
  $addresses = fpusa_get_user_address_ids( get_current_user_id() );
  ?>
  <div class="card border-bottom">
    <div class="card-body">
      <h4 class="border-bottom">Your addresses</h4>
      <?php
      foreach( $addresses as $id ){
        $address = new Address( $id[0] );
        if( ! empty( $address ) ){
          $address->convert_to_radio();
        }
      }
      ?>
			<a href="/edit-address/new"><i class="fas fa-plus"></i> Add new address</a>
    </div>
    <div class="card-header">
      <button id="use-address" type="button" class="btn btn-warning use-this-address">Use this address</button>
    </div>
  </div>
  <?php
}

add_action( 'wp_ajax_fpusa_checkout_address', 'fpusa_get_checkout_address' );
function fpusa_get_checkout_address(){
	$address = new Address( $_POST['id'] );
	if( ! empty( $address ) ){
		wp_send_json( $address );
		wp_die();
	}
}

function iww_make_date( $dates ){
	// Eastern Time Zone
  date_default_timezone_set('EST');
  // Current Hour in numerical format 0-23
  $current_hour = date('G');
	// $current_hour = 13;
  // Current day in numerical format 1-7
  $current_day = date('N');
  // init string
	$date_str = '';
	foreach( $dates as $key => $date ){
    if( $current_day > 5 ){
      $date = ( $current_day == 6 ) ? $date + 1 : $date + 2;
    } else {
      // weekdays
      if( $current_hour >= 12 ) $date++;
    }

    // if this isn't the first date, add a hyphen to the string
		if( $key != 0 )$date_str .= ' - ';
    // create date based on leadtime + $date passed to function
		$future = date( 'l, M j', strtotime('+' . $date . 'days') );
    // if future lands on a sunday, add another day to it.
		if( preg_match( '/Saturday/', $future ) ) $future = date( 'l, F j', strtotime('+' . ($date + 2) . 'days') );
		if( preg_match( '/Sunday/', $future ) ) $future = date( 'l, F j', strtotime('+' . ($date + 1) . 'days') );

		$date_str .= $future;
	}
	return '<h6 class="m-0 p-0 text-success iww-date-estimate">'.$date_str.'</h6>';
}

function fpusa_get_default_est_delivery( $method ){
	switch( $method ){
		case '3 Day Select (UPS)':
		$date_str = iww_make_date( [3] );
		break;
		case 'Ground (UPS)':
		$date_str = iww_make_date( [2, 5] );
		break;
		case '2nd Day Air (UPS)':
		$date_str = iww_make_date( [2] );
		break;
		case 'Next Day Air (UPS)':
		$date_str = iww_make_date( [2] );
		break;
		case 'Next Day Air Saver (UPS)':
		$date_str = iww_make_date( [1] );
		break;
		case 'Next Day Air Early AM (UPS)':
		$date_str = iww_make_date( [1] );
		break;
		case 'Free shipping':
		$date_str = iww_make_date( [5, 10] );
		break;
		default :
		$date_str = iww_make_date( [5, 15] );
		break;
	}

	return $date_str;
}

add_action( 'wp_ajax_get_time_in_transit', 'fpusa_get_time_in_transit' );
add_action( 'wp_ajax_nopriv_get_time_in_transit', 'fpusa_get_time_in_transit' );

function fpusa_get_time_in_transit(){
	$ups = new UPS();
	$tit = $ups->time_in_transit($_POST);
	wp_send_json( $tit );
	wp_die();
}

function fpusa_cart_item_stock( $item ){
	$stock = $item['data']->get_stock_status();
	$class = ( $stock == 'instock' ) ? 'success' : 'warning';
	$text = ( $stock == 'instock' ) ? 'In stock' : 'On backorder';
	return "<span class='text-$class'>$text</span>";
}

function fpusa_get_order_totals_html( $arr ){
	?>
	<tr>
		<td><?php echo $arr[0] ?>:</td>
		<td <?php if( $arr[0] == 'Shipping & handling' ) echo 'id="shipping_fees"';?> >$<?php echo $arr[1] ?></td>
	</tr>
	<?php
}
