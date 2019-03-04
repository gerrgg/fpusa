<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( isset( $_GET['p_id'] ) ){
  $product = wc_get_product( $_GET['p_id'] );
}

if( $product ) : ?>

<form id="create_review" class="mx-auto" method="POST" action="<?php echo admin_url('admin-post.php') ?>" enctype="multipart/form-data">
  <h2>Create Review</h2>
  <?php fpusa_create_review_product_preview( $product ); ?>
  <?php fpusa_get_overall_star_rating_form(); ?>
	<?php fpusa_drop_zone(); ?>
	<?php fpusa_add_headline(); ?>
	<?php fpusa_add_comment(); ?>
	<hr>
	<input type="hidden" name="action" value="fpusa_process_product_review">
	<input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
	<button type="submit" class="btn btn-danger">Submit</button>
  <!-- comment -->
</form>

<?php endif;
