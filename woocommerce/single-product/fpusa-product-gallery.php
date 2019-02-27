<?php

defined( 'ABSPATH' ) || exit;

global $product;

$attachment_ids = $product->get_gallery_image_ids();

if( !empty( $attachment_ids ) ){
  foreach( $attachment_ids as $id ) :
    fpusa_get_image_html( $id );
  endforeach;
}
