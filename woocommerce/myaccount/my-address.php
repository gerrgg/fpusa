<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
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
	exit; // Exit if accessed directly
}

	$customer_id = get_current_user_id();
	$get_addresses = fpusa_get_customer_addresses( get_current_user_id() );
?>


<h2 class="pb-3">Edit your address</h2>
<div class="row">
	<div class="col-6 col-sm-4">
		<div class="woocommerce-Address p-5 mx-3 d-flex flex-column justify-content-center align-items-center">
			<i class="fas fa-plus fa-3x"></i>
			<h3>Add Address</h3>
		</div>
	</div>
<?php foreach ( $get_addresses as $id => $address ) : ?>
	<div class="col-6 col-sm-4">
		<div id="<?php echo $address->address_id; ?>" class="woocommerce-Address p-5 mx-3">
			<header class="woocommerce-Address-title title d-flex justify-content-between">
			</header>
			<address>
				<?php
					$formatted_address = fpusa_get_formatted_address( $address );
					echo ( ! empty( $formatted_address ) ) ? $formatted_address : 'An address has not been setup yet!';
				?>
			</address>

				<a href="/edit-address/<?php echo $address->address_id ?>" class="edit link-color-normal"><?php _e( 'Edit', 'woocommerce' ); ?></a> |
				<a href="#" class="delete link-color-normal"><?php _e( 'Delete', 'woocommerce' ); ?></a> |
				<a href="#" class="set-as-default link-color-normal"><?php _e( 'Set as Default', 'woocommerce' ); ?></a>
		</div>
	</div>

<?php endforeach; ?>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	</div>
<?php endif;
