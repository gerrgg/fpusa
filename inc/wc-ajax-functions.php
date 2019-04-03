<?php


add_action( 'wp_ajax_fpusa_get_variation_data', 'fpusa_get_product_data_callback' );
add_action( 'wp_ajax_nopriv_fpusa_get_variation_data', 'fpusa_get_product_data_callback' );
add_action( 'wp_ajax_fpusa_get_buy_again_data', 'fpusa_get_product_data_callback' );
add_action( 'wp_ajax_fpusa_single_product_feedback', 'fpusa_single_product_feedback_callback' );
add_action( 'wp_ajax_nopriv_fpusa_single_product_feedback', 'fpusa_single_product_feedback_callback' );


function fpusa_get_product_data_callback(){
	$product = wc_get_product( $_POST['p_id'] );
	if( ! empty( $product ) ){
		$image_src = wp_get_attachment_image_src( $product->get_image_id(), 'shop_thumbnail' );
		$data = array(
			'name' => $product->get_name(),
			'image' => $image_src[0],
			'link' 	=> $product->get_permalink(),
			'price' => $product->get_price_html(),
			'stock' => $product->get_availability(),
		);
		wp_send_json( $data );
	}
	wp_die();
}


function fpusa_single_product_feedback_callback(){
	/**
   * Email user reported problem to admin.
   */
	// TODO: Maybe we could add this somewhere on the backend for the theme author to fix, todo list?
	$header = "Issue Report: " . $_POST['where'] . ' - ' . $_POST['issue'];

	$msg = 'URL: ' . $_POST['url'] . '<br>';
	$msg .= 'Location: ' . $_POST['where'] . '<br>';
	$msg .= 'Issue: ' . $_POST['issue'] . '<br>';
	$msg .= 'Comment: ' . $_POST['comments'] . '<br>';

	wp_mail( get_option('admin_email'), $header, $msg );
	wp_die();
}
