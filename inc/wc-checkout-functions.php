<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'fpusa_checkout_step_2', 'woocommerce_checkout_payment' );

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
add_action( 'fpusa_checkout_step_3', 'woocommerce_order_review' );

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
			<a class="checkout-step" data-toggle="collapse" href="#step-<?php echo $i; ?>" role="button" aria-expanded="<?php if( $i == 1 ) echo 'true'  ?>" aria-controls="step-<?php echo $i; ?>">
				<span class="pr-5"><?php echo $i; ?></span>
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
    </div>
    <div class="card-header">
      <button type="button" class="btn btn-warning">Use this address</button>
    </div>
  </div>
  <?php
}
