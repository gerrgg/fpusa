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

	$get_addresses = fpusa_get_user_address_ids( get_current_user_id() );
?>


<h2 class="pb-3">Edit your address</h2>
<div class="row">
	<div class="col-6 col-sm-4">
		<a href="/edit-address/new" class="woocommerce-Address p-5 d-flex flex-column justify-content-center align-items-center text-dark a-link-normal">
			<i class="fas fa-plus fa-3x"></i>
			<h3>Add Address</h3>
		</a>
	</div>
	<?php
	if( ! empty( $get_addresses ) ) :
		foreach ( $get_addresses as $id ) :
			$address = new Address( $id->address_id );
			if( ! empty( $address ) ) :
	?>
			<div class="col-12 col-sm-4">
				<div id="<?php echo $address->address_id; ?>" class="woocommerce-Address p-5">
					<?php if( $address->is_default() ) : ?>
						<span class="badge badge-success">Default</span>
					<?php endif; ?>
					<header class="woocommerce-Address-title title d-flex justify-content-between">
					</header>
					<address>
						<?php echo $address->formatted_address(); ?>
					</address>
					<?php echo $address->get_notes(); ?>
					<br>
						<a href="<?php echo $address->get_edit_link(); ?>" class="edit link-color-normal"><?php _e( 'Edit', 'woocommerce' ); ?></a> |
						<a href="<?php echo $address->get_delete_link(); ?>" class="delete link-color-normal"><?php _e( 'Delete', 'woocommerce' ); ?></a> |
						<a href="<?php echo $address->get_address_as_default_link(); ?>" class="set-as-default link-color-normal"><?php _e( 'Set as Default', 'woocommerce' ); ?></a>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>
<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	</div>
<?php endif; ?>
