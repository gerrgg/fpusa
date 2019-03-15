<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<?php
	$address = new Address( $load_address );
	$action = ( empty( $address->data ) ) ? 'insert' : 'update';
	$header = ( $action == 'insert' ) ? 'Add an address' : 'Edit Address' . $load_address;
	?>

	<form id="fpusa_edit_address" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>" class="form center-small">

		<h3><?php echo $header ?></h3><?php // @codingStandardsIgnoreLine ?>

		<div class="woocommerce-address-fields">

			<div class="woocommerce-address-fields__field-wrapper">
				<?php
				foreach ( $address as $key => $value ) :
					if( $key != 'user_id' && $key != 'notes' && $key != 'ID' ) fpusa_form_field( $key, $value );
				endforeach;

				fpusa_get_delivery_notes_form( $address->notes );
				?>
			</div>

			<button type="submit" class="btn btn-warning btn-block" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
			<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
			<input type="hidden" name="address_id" value="<?php echo $load_address; ?>" />
			<input type="hidden" name="action" value="edit_address" />
		</div>

	</form>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
