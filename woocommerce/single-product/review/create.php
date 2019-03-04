<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( isset( $_GET['p_id'] ) ){
  $product = wc_get_product( $_GET['p_id'] );
}

if( $product ) : ?>

<form id="create_review" class="mx-auto">
  <h2>Create Review</h2>
  <?php fpusa_create_review_product_preview( $product ); ?>
  <?php fpusa_get_overall_star_rating_form(); ?>
  <!-- headline  -->
  <!-- comment -->
</form>

<?php endif;
