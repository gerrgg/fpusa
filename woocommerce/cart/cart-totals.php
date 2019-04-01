<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$totals = WC()->cart->get_totals();
?>
<h5 class="my-3">Order Summary</h5>
<table class="table">

	<?php
		$formatted_totals = array(
			array('Items', $totals['cart_contents_total']),
			array('Shipping & handling', $totals['shipping_total']),
			array('Your Coupon Savings', $totals['discount_total']),
			array('Total before tax', $totals['cart_contents_total']),
			array('Estimated tax to be collected', $totals['fee_total']),
		);

		foreach( $formatted_totals as $total_arr ){
			echo fpusa_get_order_totals_html( $total_arr );
		}
	?>

<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

	<tr class="order-total">
		<th><?php _e( 'Order total:', 'woocommerce' ); ?></th>
		<td><?php wc_cart_totals_order_total_html(); ?></td>
	</tr>

	<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
</table>
