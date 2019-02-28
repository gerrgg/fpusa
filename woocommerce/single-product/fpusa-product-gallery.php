<?php

defined( 'ABSPATH' ) || exit;

global $product;
global $post;

$attachment_ids = $product->get_gallery_image_ids();
$video_urls = json_decode(get_post_meta( $post->ID, 'product_videos', true ));

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
