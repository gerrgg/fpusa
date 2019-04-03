<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
// remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
remove_action( 'woocommerce_after_cart_table', 'woocommerce_cart_totals' );
add_action( 'woocommerce_after_cart_table', 'fpusa_get_checkout_button' );

function fpusa_get_checkout_button(){
	$subtotal = WC()->cart->get_subtotal();
	$item_cou
	?>
	<div class="cart_totals">
		<div class="card">
			<div class="card-body">
				<h5><?php fpusa_get_cart_subtotal(); ?></h5>
				<a href="<?php echo wc_get_checkout_url(); ?>" role="button" class="btn btn-block btn-warning">Proceed to checkout</a>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'fpusa_checkout_step_2', 'woocommerce_checkout_payment', 10 );
add_action( 'fpusa_after_payment', 'woocommerce_checkout_coupon_form' );

add_action( 'fpusa_checkout_step_3', 'fpusa_maybe_set_order_prefs_callback', 10 );
add_action( 'fpusa_checkout_step_3', 'woocommerce_order_review', 20 );
add_action( 'fpusa_checkout_step_3', 'fpusa_place_order', 30 );

function fpusa_checkout_payment(){
	wc_get_template( '/myaccount/payment-methods.php' );
}

add_action( 'wp_ajax_fpusa_get_cart_totals', 'fpusa_get_cart_totals' );
add_action( 'wp_ajax_nopriv_fpusa_get_cart_totals', 'fpusa_get_cart_totals' );

function fpusa_get_cart_totals(){
	WC()->cart->calculate_totals();

  ob_start();
  fpusa_get_order_summary();
  $fpusa_order_summary = ob_get_clean();

	wp_send_json( $fpusa_order_summary );
	wp_die();
}

add_action( 'fpusa_order_summary', 'fpusa_get_order_summary' );
function fpusa_get_order_summary(){
	$totals = WC()->cart->get_totals();
	// var_dump( $totals );
	?>
	<div id="cart-totals">
		<h5 class="my-3">Order Summary</h5>
		<table class="table">
			<?php
				$formatted_totals = array(
					array('Items', $totals['cart_contents_total']),
					array('Shipping & handling', $totals['shipping_total']),
					array('Your coupon savings', $totals['discount_total']),
					array('Estimated tax to be collected', $totals['fee_total']),
				);

				foreach( $formatted_totals as $total_arr ){
					echo fpusa_get_order_totals_html( $total_arr );
				}
			?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

			<tr class="fpusa-order-total">
				<th><?php _e( 'Order total:', 'woocommerce' ); ?></th>
				<td>$<?php echo $totals['total']; ?></td>
			</tr>

			<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
		</table>
	</div>
	<?php
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

function get_user_order_prefs(  ){
	$valid_order_prefs = array(
		'use_address' => '',
		'use_payment' => ''
	);

	$default_address = get_user_meta( get_current_user_id(), 'default_address', true );

	$address = new Address( $default_address );
	if( ! empty( $address->ID ) ){
		$valid_order_prefs['use_address'] =  $default_address;
	}


	$token = WC_Payment_Tokens::get_customer_default_token( get_current_user_id() );
	if( ! empty( $token->get_id() ) ){
		$valid_order_prefs['use_payment'] =  $token->get_id();
	}

	return $valid_order_prefs;
}

function get_user_order_prefs_ajax( ){
	$valid_order_prefs = array(
		'use_address' => '',
		'use_payment' => ''
	);

	$default_address = get_user_meta( get_current_user_id(), 'default_address', true );

	$address = new Address( $default_address );
	if( ! empty( $address->ID ) ){
		$valid_order_prefs['use_address'] =  $default_address;
	}


	$token = WC_Payment_Tokens::get_customer_default_token( get_current_user_id() );
	if( ! empty( $token->get_id() ) ){
		$valid_order_prefs['use_payment'] =  $token->get_id();
	}

	wp_send_json( $valid_order_prefs );
	wp_die();
}

add_action( 'wp_ajax_fpusa_update_user_order_prefs','fpusa_update_user_order_prefs' );
function fpusa_update_user_order_prefs(){
	$user_id = get_current_user_id();


	// Check for address validation?
	$address = new Address( $_POST['use_address'] );
	if( $address->is_valid() ){
		update_user_meta( $user_id, 'default_address', $_POST['use_address']  );
		$default_address = get_user_meta( $user_id, 'default_address', 'true' );
		$address->sync_customer();
	}

	$token = WC_Payment_Tokens::get( intval( $_POST['use_payment'] ) );
	$token->set_default( 1 );
	$token->save();

	wp_send_json( $arr );
	wp_die();
}

function fpusa_place_order(){
	?>
	<div class="card p-3 mt-3">
		<div class="d-flex align-items-center">
			<button id="place-order" type="submit" class="btn btn-warning btn-block">Place your order</button>
			<span class="mx-5">
				<div class="fpusa-order-total">
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
			make_modal_btn(array(
				'text' => '<i class="fas fa-plus"></i> Add new address',
				'title' => 'Add a new shipping address',
				'model' => 'address',
				'action' => 'create',
			));
      ?>
    </div>
    <div class="card-header">
      <button id="use-address" type="button" class="btn btn-warning use-this-address">Use this address</button>
    </div>
  </div>
  <?php
}

add_action( 'wp_ajax_fpusa_get_billing_address', 'fpusa_get_billing_address' );
function fpusa_get_billing_address( $id = '' ){
	// echo $_POST['id'];
	$doing_ajax = ( isset( $_POST['id'] ) );
	$id = ( $doing_ajax ) ? $_POST['id'] : $id;

	$billing = array(
		'billing_first_name' => get_metadata( 'payment_token', $id, 'billing_first_name', true ),
		'billing_last_name' => get_metadata( 'payment_token', $id, 'billing_last_name', true ),
		'billing_address_1' => get_metadata( 'payment_token', $id, 'billing_address_1', true ),
		'billing_address_2' => get_metadata( 'payment_token', $id, 'billing_address_2', true ),
		'billing_city' => get_metadata( 'payment_token', $id, 'billing_city', true ),
		'billing_state' => get_metadata( 'payment_token', $id, 'billing_state', true ),
		'billing_postcode' => get_metadata( 'payment_token', $id, 'billing_postcode', true ),
		'billing_country' => get_metadata( 'payment_token', $id, 'billing_country', true ),
	);

	if( $doing_ajax ){
		wp_send_json( $billing );
		wp_die();
	} else {
		return $billing;
	}

}

add_action( 'wp_ajax_fpusa_billing_address_edit', 'fpusa_billing_address_edit' );
function fpusa_billing_address_edit(){
		parse_str( $_POST['form_data'], $form_data );
		foreach( $form_data as $key => $value ){
			if( ! empty( $value ) ){
				update_metadata( 'payment_token', $_POST['id'], $key, $value);
				update_user_meta( get_current_user_id(), $key, $value );
			}
		}

		wp_send_json( fpusa_get_billing_address( $_POST['id'] ) );
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
		$future = date( 'l, F jS', strtotime('+' . $date . 'days') );
    // if future lands on a sunday, add another day to it.
		if( preg_match( '/Saturday/', $future ) ) $future = date( 'l, F jS', strtotime('+' . ($date + 2) . 'days') );
		if( preg_match( '/Sunday/', $future ) ) $future = date( 'l, F jS', strtotime('+' . ($date + 1) . 'days') );

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
		<td>$<?php echo number_format( $arr[1], 2 ) ?></td>
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



add_action( 'wp_ajax_fpusa_get_address', 'fpusa_ajax_get_address' );
// add_action( 'wp_ajax_nopriv_fpusa_address_create', 'fpusa_ajax_address_create' );
function fpusa_ajax_get_address(){
	global $wpdb;
	if( isset( $_POST['id'] ) ){
		$id = $_POST['id'];
		$table_name = $wpdb->prefix . 'address';
		$row = $wpdb->get_row( "SELECT * FROM $table_name WHERE address_id = $id" );
		wp_send_json( $row );
	}
	wp_die();
}

add_action( 'wp_ajax_fpusa_address_create', 'fpusa_ajax_address_create' );
// add_action( 'wp_ajax_nopriv_fpusa_address_create', 'fpusa_ajax_address_create' );
function fpusa_ajax_address_create(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'address';
	parse_str( $_POST['form_data'], $form_data );

	if( ! empty( $form_data ) ){
		$form_data['address_user_id'] = get_current_user_id();
		$id = $wpdb->insert( $table_name, $form_data );
	}

	echo $id;
	wp_die();
}

add_action( 'wp_ajax_fpusa_address_edit', 'fpusa_ajax_address_edit' );
// add_action( 'wp_ajax_nopriv_fpusa_address_create', 'fpusa_ajax_address_create' );
function fpusa_ajax_address_edit(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'address';
	parse_str( $_POST['form_data'], $form_data );


	echo $wpdb->update( $table_name, $form_data, array( 'address_id' => $_POST['id'] ) );


	wp_die();
}


function fpusa_get_applied_coupons( $echo = true ){
	$html = '';
	$applied_coupons = WC()->cart->get_coupon_discount_totals();
	if( ! empty( $applied_coupons ) ){
		foreach( $applied_coupons as $code => $coupon ){
			$html .= "<div class='d-flex coupon-line border-bottom pb-1'>";
			$html .= 	"<button id='$code' type='button' data-coupon='$code' class='woocommerce-remove-coupon close pr-3' aria-label='Close'>";
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
