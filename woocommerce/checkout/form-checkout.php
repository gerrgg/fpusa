<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', esc_html__( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

$shipping_address = fpusa_get_customer_location_details('shipping');

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', wc_get_checkout_url() ); ?>

<div class="row">
	<div class="col-9">
		<div class="accordion" id="checkoutSteps">

			<h4>
				<a class="checkout-step d-flex" data-toggle="collapse" href="#step1" role="button" aria-expanded="false" aria-controls="step1">
	        <label class="pr-5">1</label>
					<p class="m-0">Choose a shipping address</p>
	      </a>
			</h4>

			<div id="step1" class="collapse" data-parent="#checkoutSteps">
	      <div class="card-body">
	        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
	      </div>
	    </div>

			<h4>
				<a class="checkout-step d-flex" data-toggle="collapse" href="#step2" role="button" aria-expanded="false" aria-controls="step2">
	        <label class="pr-5">2</label>
					<p class="m-0">Choose a payment method</p>
	      </a>
			</h4>

			<div id="step2" class="collapse" data-parent="#checkoutSteps">
	      <div class="card-body">
	        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
	      </div>
	    </div>

			<h4>
				<a class="checkout-step d-flex" data-toggle="collapse" href="#step3" role="button" aria-expanded="false" aria-controls="step3">
	          <label class="pr-5">3</label>
						<p class="m-0">Review items and shipping</p>
	      </a>
			</h4>

			<div id="step3" class="collapse" data-parent="#checkoutSteps">
	      <div class="card-body">
	        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
	      </div>
	    </div>
		</div>
	</div>
	<div class="col">
		<button>checkout</button>
	</div>
</div>
