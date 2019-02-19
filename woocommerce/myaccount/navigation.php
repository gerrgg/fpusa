<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>


<div class="row woocommerce-MyAccount-navigation">
	<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		<div class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?> col-12 col-sm-4">
			<a role="button" class="card text-dark" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
				<div class="p-3 d-flex justify-content-center align-items-center">
					<span class="mr-3"><?php echo fpusa_get_myaccount_icons( $endpoint ); ?></span>
					<span>
						<?php echo esc_html( $label ); ?>
					</span>
				</div>
			</a>
		</div>
	<?php endforeach; ?>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
