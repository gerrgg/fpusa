<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

		<?php foreach ( $customer_orders->orders as $customer_order ) :
			$order      = wc_get_order( $customer_order );
			$order_items = $order->get_items();
			$actions = wc_get_account_orders_actions( $order );
		?>
		<div id="my-orders" class="card mb-2">
			<!-- header -->
			<div class="card-header text-muted d-flex">
		    <div class="mr-auto">
					<span>ORDER PLACED</span><br>
					<span><?php echo $order->get_date_created()->format('F, jS Y') ?></span>
				</div>
				<div class="ml-auto">
					<span>ORDER # <?php echo $order->get_id(); ?></span><br>
					<ul class="m-0 p-0 a-list-vertical">
						<?php foreach( $actions as $key => $action ) : ?>
							<li>
								<a href="<?php echo $action['url'] ?>"><?php echo ucfirst($key); ?></a>
							<li>
						<?php endforeach; ?>
					</ul>
				</div>
		  </div>
			<!-- body -->
			<div class="card-body">
				<h3 class="text-success"><?php echo $order->get_status(); ?></h3>
				<ul class="list-group list-group-flush m-0">
					<li class="d-flex mb-3">
						<div class="w-75">
					    <?php foreach( $order_items as $key => $item ) :
								$id = ( ! empty( $item->get_variation_id() ) ) ? $item->get_variation_id() : $item->get_product_id();
								$product = wc_get_product( $id );
							?>
							<div class="pb-3 d-flex">
								<a class="product-image-link product-image-link-sm" href="<?php echo $product->get_permalink() ?>"><?php echo $product->get_image();?></a>
								<span class="pl-3">
									<a href="<?php echo $product->get_permalink() ?>"><?php echo $product->get_name();?></a>
									<div class="d-flex align-items-center">
										<p class="price">$<?php echo $item->get_subtotal(); ?></p>
										<small class="text-muted pl-1"> x <?php echo $item->get_quantity() ?></small>
									</div>
									<button class="btn btn-warning" data-toggle="modal" data-target="#fpusa_buy_again" data-id="<?php echo $id ?>">Buy it again</button><br>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="w-25">
						<?php do_action('fpusa_order_actions', $order); ?>
					</div>
				</li>
		   </ul>
		</div>
	</div>
<?php endforeach; ?>

<?php fpusa_buy_again_modal() ?>

<?php else : ?>
	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php _e( 'Go shop', 'woocommerce' ) ?>
		</a>
		<?php _e( 'No order has been made yet.', 'woocommerce' ); ?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
