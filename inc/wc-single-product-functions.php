<?php
add_action( 'fpusa_single_product_right', 'fpusa_single_right_wrapper_start', 5 );
add_action( 'fpusa_single_product_right', 'fpusa_single_right_wrapper_end', 100 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

add_action( 'woocommerce_single_product_summary', 'fpusa_template_single_brand', 3 );
add_action( 'woocommerce_single_product_summary', 'fpusa_template_single_divider', 11 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 15 );

// move short description below variations
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 35 );

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_before_single_product_summary', 'fpusa_show_product_images', 20 );

add_action( 'fpusa_template_product_gallery', 'fpusa_get_product_gallery' );

function fpusa_get_product_gallery(){
	wc_get_template('single-product/fpusa-product-gallery.php');
}

function fpusa_show_product_images(){
	wc_get_template('single-product/fpusa-product-image.php');
}
// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

function fpusa_template_single_divider(){
	echo '<hr>';
}

function fpusa_template_single_brand(){
	wc_get_template( 'single-product/brand.php' );
}

function fpusa_single_right_wrapper_start(){
	/**
	 * outputs the opening tab for the div on the far right on the single product page.
	 */
	echo '<div class="fpusa-single-product-right mt-1">';
}

function fpusa_single_right_wrapper_end(){
	/**
	 * outputs the ending tab for the div on the far right on the single product page.
	 */
	echo '</div>';
}

// TODO: Include place on options page to setup links to social media.
// TODO: Social media integration - share buttons
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 1 );

add_action( 'fpusa_single_product_right', 'fpusa_single_product_price_right', 5 );
function fpusa_single_product_price_right(){
	/**
	 * Simply displays price above the add to cart button
	 */
	global $product;
	?>
	<div class="<?php echo $product->get_type(); ?> price"><?php echo $product->get_price_html(); ?></div>
	<?php
}

add_action( 'fpusa_single_product_right', 'fpusa_single_product_shipping_upsell', 10 );
/**
 * outputs html for shipping upsells, need it tommorow?
 * TODO: Need to integrate with cart, checkout and Shipping? Are we going to use Shippo?
 */
function fpusa_single_product_shipping_upsell(){
	?>
	<p style="overflow: hidden;">fpusa_single_product_shipping_upsell();</p>
	<?php
}

add_action( 'fpusa_single_product_right', 'fpusa_single_product_stock', 15 );

function fpusa_single_product_stock(){
	/**
	 * outputs product stock status on the far right column.
	 */
	global $product;
	echo wc_get_stock_html( $product );
}

add_action( 'fpusa_single_product_right', 'fpusa_add_to_cart_btn', 50 );
function fpusa_add_to_cart_btn(){
	/**
	 * Outputs the html for the add to cart button.
	 * Currently, this is rigged to simulate the add to cart button and qty, but the
	 * real elements are in the woocommerce_single_product_summary();
	 */
	global $product;
	?> <label>QTY:</label> <?php
	woocommerce_quantity_input( array(
		'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
		'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
		'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
	) );
	?>
	<label for="fpusa_add_to_cart" class="btn btn-block mt-1 btn-danger">Add to cart</label>
	<?php
}

add_action( 'fpusa_single_product_right', 'fpusa_single_product_address', 70 );
function fpusa_single_product_address(){
	/**
	 * Outputs the html for the add to cart button.
	 */
	$loc_str = fpusa_get_ship_address_str( );
	echo '<button class="btn" data-toggle="modal" data-target="#fpusa_choose_loc"><i class="fas fa-map-marker-alt pr-2 text-dark"></i>Deliver to ' . $loc_str . '</button>';
}

add_action( 'woocommerce_single_product_summary', 'fpusa_report_product_info_btn', 100 );
function fpusa_report_product_info_btn(){
	/**
   * Output the report problem link and modal
   */
	wc_get_template('single-product/report-problem.php');
}

function fpusa_get_image_html( $id, $active = false, $size = 'woocommerce_gallery_thumbnail' ){
	/**
		* outs puts an image and includes markup for the full-size version of the image.
    * @param int $id - The ID of the attachment
	  * @param bool $active - determines whether or now the returned html should include the class thumb-active
		* @param string $size - determines the size the image should return
   */
	$src = array(
		$size => wp_get_attachment_image_src($id, $size),
		'full' => wp_get_attachment_image_src($id, 'full'),
	);
	?>
	<p class="img-xs fpusa-<?php echo $size ?>">
		<img src="<?php echo $src[$size][0]; ?>" src-full="<?php echo $src['full'][0]; ?>" <?php if( $active ) echo 'class="thumb-active"' ?> />
	</p>
	<?php
}

function fpusa_get_video_html( $url ){
	/**
		* takes in a url, converts the url to work with youtube API and generates markup for the thumbnail.
		* @param string $url
   */

	 // remove the uneeded url parts
	$v_id = str_replace( 'https://www.youtube.com/watch?v=', '', $url );

	// alter url to properly embed the video - Must work with youtube API
	$embed = 'https://www.youtube.com/embed/' . $v_id . '?iv_load_policy=3&autoplay=1';

	// display html for with video thumbnail img and url to video at vid-url html attribute.
	if( ! empty( $v_id ) ) : ?>
		<p class="img-xs fpusa-woocommerce_gallery_thumbnail">
			<img class="fpusa_video_link" src="<?php echo 'https://img.youtube.com/vi/' . $v_id . '/default.jpg'; ?>" vid-url="<?php echo $embed; ?>"/>
		</p>
	<?php endif;
}

function fpusa_product_specifications(){
	/*
	* Gets the html for product specifications
	*/
	wc_get_template( 'single-product/tabs/product-specifications.php' );
}
add_action( 'before_woocommerce_product_additional_information', 'fpusa_product_specifications', 5 );


add_filter( 'woocommerce_product_tabs', 'fpusa_custom_review_tab', 98 );
function fpusa_custom_review_tab( $tabs ) {
	/*
	* sets the callback for the review tab to a custom function
	*/
	$tabs['reviews']['callback'] = 'fpusa_get_comments_template';
	return $tabs;
}
