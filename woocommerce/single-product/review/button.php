<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
$action = ( empty( fpusa_get_user_product_review( $product->get_id() ) ) ) ? 'post' : 'edit';
?>
<div id="fpusa-review-btn">
  <h4>Write a review</h4>
  <p>Share your thoughts with other customers.</p>
  <a type="button" href="<?php echo fpusa_get_product_review_link( $product->get_id(), $action ) ?>" class="btn btn-default btn-block">Write a customer review</a>
</div>
<?php
