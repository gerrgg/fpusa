<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="reviews" class="">
  <?php
    /**
     * Hook: fpusa_customer_review_left.
     *
     * @hooked fpusa_cr_get_stars - 10
     * @hooked fpusa_show_product_images - 20
     */
     do_action('fpusa_customer_review_left');
  ?>
</div>
<div id="after-reviews">
  <?php
    /**
     * Hook: fpusa_customer_review_right.
     *
     * @hooked woocommerce_show_product_sale_flash - 10
     * @hooked fpusa_show_product_images - 20
     */
    do_action('fpusa_customer_review_right');
  ?>
</div>
