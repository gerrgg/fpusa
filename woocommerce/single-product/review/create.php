<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! is_user_logged_in() ) :
	woocommerce_login_form( array(
		'message' => 'You must login before posting a review!',
		'redirect' => '/my-account',
		'hidden' => false,
	) );
else :

$product = ( isset( $_GET['p_id'] ) ) ? wc_get_product( $_GET['p_id'] ) : '';
$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : '';
$comment = ( $action == 'edit' ) ? fpusa_get_user_product_review( $product->get_id() ) : '';
$rating = ( $comment ) ? get_metadata('comment', $comment['comment_ID'], 'rating', true) : '';
$headline = ( $comment ) ? get_metadata('comment', $comment['comment_ID'], 'headline', true) : '';
$content = ( $comment ) ? $comment['comment_content'] : '';

echo $content;

if( isset( $product ) && $product ) : ?>

<form id="create_review" class="mx-auto" method="POST" action="<?php echo admin_url('admin-post.php') ?>" enctype="multipart/form-data">
  <h2><?php echo ucfirst( $action ) ?> Review</h2>
  <?php fpusa_create_review_product_preview( $product ); ?>
  <?php fpusa_get_overall_star_rating_form( $product->get_id(), $rating, $action, 0 ); ?>
	<?php if( $action != 'edit' ) fpusa_drop_zone(); ?>
	<?php fpusa_add_headline( $headline ); ?>
	<?php fpusa_add_comment( $content ); ?>
	<hr>
	<input type="hidden" name="action" value="fpusa_process_product_review">
	<input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
	<button type="submit" class="btn btn-danger">Submit</button>
  <!-- comment -->
</form>

<?php endif;
endif;
