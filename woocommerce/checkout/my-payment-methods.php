<?php

defined( 'ABSPATH' ) || exit;

global $wpdb;
$saved_methods = fpusa_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();

do_action( 'woocommerce_before_account_payment_methods', $has_methods ); ?>

<?php if ( $has_methods ) : ?>

<table class="table mb-5">
  <thead>
    <tr>
      <th class="px-0 mx-0"><h4 class="m-0 p-0">Your credit and debit cards</h4></th>
      <th>Name on card</th>
      <th>Expires on</th>
    </tr>
  </thead>
  <tbody>
		<?php foreach ( $saved_methods as $token ) : ?>
        <tr>
          <td>
            <div class="form-check">
              <input class="form-check-input use_card" type="radio" name="wc-simplify_commerce-payment-token" id="payment_method_<?php echo $token->get_id(); ?>" value="<?php echo $token->get_id(); ?>" <?php checked( $token->is_default(), 1 ); ?>>
              <label class="form-check-label" for="payment_method_<?php echo $token->get_id(); ?>">
                <?php echo sprintf( '<b>%s</b> ending in %s', ucfirst( $token->get_card_type() ), $token->get_last4() ) ?>
              </label>
            </div>
        </td>
        <td><?php echo get_metadata( 'payment_token', $token->get_id(), 'name_on_card', true )?></td>
        <td><?php echo $token->get_expiry_month() . '/' . $token->get_expiry_year() ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php endif; ?>
