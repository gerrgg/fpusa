<?php

global $product;

$brand = $product->get_attribute( 'pa_brand' );
$url = get_term_link( $brand, 'pa_brand' );

if( ! empty( $brand ) ){
  echo ( ! is_wp_error( $url ) ) ? "<a href='$url'>$brand</a>" : $brand;
}
