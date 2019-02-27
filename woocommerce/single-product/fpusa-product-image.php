<?php

defined( 'ABSPATH' ) || exit;

global $product;
$post_src = wp_get_attachment_image_src( $product->get_image_id(), 'woocommerce_single' );
?>
<div class="main-image d-flex p-1">
  <div id="gallery" class="mr-2">
    <?php fpusa_get_image_html( $product->get_image_id(), true ); ?>
    <?php do_action( 'fpusa_template_product_gallery' ); ?>
  </div>
  <div id="main">
    <a href="javascript:void(0)" class="product-image-link">
      <img src="<?php if( ! empty( $post_src ) ) echo $post_src[0]; ?>" />
    </a>
  </div>
</div>
