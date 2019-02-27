<?php

defined( 'ABSPATH' ) || exit;

global $product;
global $post;

$attachment_ids = $product->get_gallery_image_ids();
$video_urls = fpusa_get_product_video_urls( $post );

if( !empty( $attachment_ids ) ){
  foreach( $attachment_ids as $id ) :
    fpusa_get_image_html( $id );
  endforeach;
}

if( !empty( $video_urls ) ){
  foreach( $video_urls as $url ) :
    fpusa_get_video_html( $url );
  endforeach;
}
