<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

add_action( 'fpusa_checkout_step_2', 'woocommerce_checkout_payment', 10 );
add_action( 'fpusa_after_payment', 'woocommerce_checkout_coupon_form' );

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );

add_action( 'fpusa_checkout_step_3', 'fpusa_maybe_set_order_prefs_callback', 10 );
add_action( 'fpusa_checkout_step_3', 'woocommerce_order_review', 20 );
add_action( 'fpusa_checkout_step_3', 'fpusa_place_order', 30 );

add_action( 'fpusa_order_summary', 'get_order_summary' );

function get_order_summary(){
	wc_get_template( 'cart/cart-totals.php' );
}

function fpusa_checkout_payment(){
	wc_get_template( '/myaccount/payment-methods.php' );
}

function compare_user_prefs(){
	$prefs = get_user_order_prefs();
	$user_id = get_current_user_id();

	$match = true;

	$default_address = get_user_meta( $user_id, 'default_address', true );
	if( $default_address != $prefs['use_address'] ){
		$match = false;
	}

	$default_token = WC_Payment_Tokens::get_customer_default_token( get_current_user_id() );
	if( $default_token->get_id() != $prefs['use_payment'] ){
		$match = false;
	}

	return $match;
}

function fpusa_maybe_set_order_prefs_callback(){
	$prefs_match = compare_user_prefs();
	if( ! $prefs_match ) : ?>
	<div class="alert alert-info d-flex" role="alert">
		<i class="fas fa-info-circle fa-2x pr-3"></i>
		<div>
			<h6 class="mb-1">Want to save time on your next order and go directly to this step when checking out?</h6>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" id="set_user_order_prefs" name="set_user_order_prefs">
				<label class="form-check-label" for="set_user_order_prefs">
					Check this box to default to these delivery and payment options in the future.
				</label>
			</div>
		</div>
	</div>
	<?php endif;
}

add_action( 'wp_ajax_fpusa_get_user_order_prefs', 'get_user_order_prefs_ajax' );
add_action( 'wp_ajax_nopriv_fpusa_get_user_order_prefs', 'get_user_order_prefs_ajax' );
function get_user_order_prefs( ){
	$arr = maybe_unserialize(get_user_meta( get_current_user_id(), 'order_prefs', true ));

	return array(
		'use_address' => $arr[0],
		'use_payment' => $arr[1]
	);
}

function get_user_order_prefs_ajax( ){
	$arr = maybe_unserialize(get_user_meta( get_current_user_id(), 'order_prefs', true ));

	$address = new Address( $arr[0] );
	if( empty( $address ) ){
		$arr[0] = '';
	}

	$payment = WC_Payment_Tokens::get( $arr[1] );
	// var_dump( $payment );
	if( empty( $payment ) ){
		$arr[1] = '';
	}
	wp_send_json( $arr );
	wp_die();
}

add_action( 'wp_ajax_fpusa_update_user_order_prefs','fpusa_update_user_order_prefs' );
function fpusa_update_user_order_prefs(){
	$user_id = get_current_user_id();

	update_user_meta( $user_id, 'default_address', $_POST['use_address']  );
	$default_address = get_user_meta( $user_id, 'default_address', 'true' );

	$token = WC_Payment_Tokens::get( intval( $_POST['use_payment'] ) );
	$token->set_default( 1 );
	$token->save();

	$arr = [ $default_address, $token->get_id() ];
	update_user_meta( $user_id, 'order_prefs', maybe_serialize( $arr ) );
	wp_send_json( $arr );
	wp_die();
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
				<p id="order-instructions-btm"><?php echo wc_get_privacy_policy_text( 'checkout' ); ?></p>
			</span>
		</div>
	</div>
	<?php
}

add_action( 'fpusa_after_header', 'fpusa_checkout_header', 1, 1 );
function fpusa_checkout_header( $checkout ){
	if( ! is_checkout() ) return '';

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
	<div class="border-bottom py-3">
		<h4 class="mb-2 d-flex">
			<a id="step-btn-<?php echo $i; ?>" href="#step-<?php echo $i; ?>" class="checkout-step" data-toggle="collapse" role="button" aria-expanded="<?php if( $i == 1 ) echo 'true'  ?>" aria-controls="step-<?php echo $i; ?>">
				<span class="pr-2"><?php echo $i; ?>: </span>
				<span><?php echo $labels[$i - 1] ?></span>
			</a>
			<span id="preview-<?php echo $i; ?>" class="pl-5 preview"></span>
		</h4>
		<div class="mt-4 mb-2 collapse <?php if( $i == 1 ) echo 'show'  ?>" id="step-<?php echo $i; ?>" data-parent="#<?php echo $parent; ?>">
			<?php do_action( 'fpusa_checkout_step_' . $i ); ?>
		</div>
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

add_action( 'wp_ajax_fpusa_get_billing_info', 'fpusa_get_billing_info' );
function fpusa_get_billing_info(){
	// echo $_POST['id'];

		$billing = array(
			'first_name' => get_metadata( 'payment_token', $_POST['id'], 'billing_first_name', true ),
			'last_name' => get_metadata( 'payment_token', $_POST['id'], 'billing_last_name', true ),
			'address_1' => get_metadata( 'payment_token', $_POST['id'], 'billing_address_1', true ),
			'address_2' => get_metadata( 'payment_token', $_POST['id'], 'billing_address_2', true ),
			'city' => get_metadata( 'payment_token', $_POST['id'], 'billing_city', true ),
			'state' => get_metadata( 'payment_token', $_POST['id'], 'billing_state', true ),
			'postcode' => get_metadata( 'payment_token', $_POST['id'], 'billing_postcode', true ),
			'country' => get_metadata( 'payment_token', $_POST['id'], 'billing_country', true ),
		);

		wp_send_json( $billing );

		wp_die();

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
		<!--  TODO: integrate with js  -->
		<td <?php if( $arr[0] == 'Shipping & handling' ) echo 'id="shipping_fees"';?> >$<?php echo $arr[1] ?></td>
	</tr>
	<?php
}

function fpusa_get_customer_saved_methods_list( $user_id ){
	global $wpdb;
	$token_arr = array();

	$table_name = $wpdb->prefix . 'woocommerce_payment_tokens';
	$results = $wpdb->get_results( "SELECT token_id
																 FROM $table_name
																 WHERE gateway_id = 'mes_cc'
																 AND user_id = $user_id" );


	foreach( $results as $result ){
		$token = WC_Payment_Tokens::get( $result->token_id );
		array_push( $token_arr, $token );
	}

	return $token_arr;
}

add_action( 'wp_ajax_fpusa_get_coupon_html', 'fpusa_get_applied_coupons' );
add_action( 'wp_ajax_nopriv_fpusa_get_coupon_html', 'fpusa_get_applied_coupons' );

function fpusa_get_applied_coupons( $echo = true ){
	$html = '';
	$applied_coupons = WC()->cart->get_coupon_discount_totals();
	if( ! empty( $applied_coupons ) ){
		foreach( $applied_coupons as $code => $coupon ){
			$html .= "<div class='d-flex coupon-line'>";
			$html .= 	"<button id='$code' type='button' class='close pr-3' aria-label='Close'>";
			$html .= 		"&times";
			$html .= 	"</button>";
			$html .= 	"<span class='p-0 m-0 font-weight-bold pr-3'>$code</span>";
			$html .= 	"<span class='price'>$-". number_format( $coupon, 2 ) ."</span>";
			$html .= "</div>";
		}
		if( $echo ){
			echo $html;
			// wp_die();
		} else {
			return $html;
		}
	}
}

add_action( 'wp_ajax_fpusa_apply_coupon', 'fpusa_apply_coupon' );
add_action( 'wp_ajax_nopriv_fpusa_apply_coupon', 'fpusa_apply_coupon' );

function fpusa_apply_coupon(){
	if( WC()->cart->add_discount( sanitize_text_field( $_POST['code'] ) ) ){
		echo 1;
	} else {
		wc_print_notices();
	}
  wp_die();
}

add_action( 'wp_ajax_fpusa_remove_coupon', 'fpusa_remove_coupon' );
add_action( 'wp_ajax_nopriv_fpusa_remove_coupon', 'fpusa_remove_coupon' );
function fpusa_remove_coupon(){
	echo ( WC()->cart->remove_coupon( sanitize_text_field( $_POST['code'] ) ) );
	wc_print_notices();
	wp_die();
}
