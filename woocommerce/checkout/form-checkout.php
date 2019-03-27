<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
do_action( 'woocommerce_before_checkout_form', $checkout );
// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>
<div class="container">
<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	<!-- layout bug fix -->
	<div class="row">
	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col-12 col-lg-8" id="customer_details">

			<div class="accordion" id="checkout-process">
				<?php
					fpusa_checkout_steps(
						'checkout-process',
						array( 'Shipping address', 'Payment method', 'Items & Shipping' )
					);
				?>

			</div>
			<div class="d-none">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>
		</div>


		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
  <div class="col">
  	<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

  	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
		<div id="order_review" class="woocommerce-checkout-review-order card">
			<div class="card-body">
				<div id="order-button">
					<button type="button" class="btn use-this-address btn-warning btn-block">Use this address</button>
				</div>
				<div class="text-center">
					<small id="order-instructions" class="text-center">Continue to step 3 to finish checking out. You'll have a chance to review and edit your order before it's final.</small>
				</div>
				<?php do_action( 'fpusa_order_summary' ); ?>
			</div>
		</div>
		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
  </div>
	<?php wp_nonce_field( 'woocommerce-process_checkout' ); ?>
</form>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div>
