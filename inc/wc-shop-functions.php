<?php

add_action( 'woocommerce_before_main_content', 'fpusa_cat_sub_nav_cat_menu', 1 );
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_action('woocommerce_before_shop_loop', 'woocommerce_breadcrumb', 21 );
add_action('woocommerce_before_single_product', 'woocommerce_breadcrumb', 21 );
add_action( 'woocommerce_archive_description', 'fpusa_shop_by_category', 15 );

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 10 );

function fpusa_shop_by_category( ){
	$cats = fpusa_get_cats_w_img();
	if( ! empty( $cats ) ){
		?>
		<h3>Shop by Category</h3>
		<div class="row">
			<?php foreach( $cats as $cat ) : ?>
				<div class="col-6 col-sm-2">
					<a href="<?php echo $cat['link']; ?>">
						<img src="<?php echo $cat['image_src']?>" />
						<p class="text-center"><?php echo $cat['name']; ?></p>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}

function fpusa_get_cat_siblings( $department ){
	/**
	* @param WP_Term $department
	* @return array WP_Term $terms
	*/
	if( ! is_wp_error( $department ) ){
		$args = array( 'taxonomy' => 'product_cat', 'parent' => $department->term_id );
		$terms = get_terms( $args );
		return $terms;
	} else {
		return 0;
	}
}

function fpusa_get_department(){
	/**
	* Checks if we are in a category using the URI, if so, grab the slug of the next cat and return WP_Term
	* @return WP_Term $department
	*/
 global $wp_query;
 $categories = explode( '/', $_SERVER['REQUEST_URI'] );

 if( $categories[1] = 'product-category' ){
	 $department = get_terms( array(
		 'slug' => $categories[2],
		 'taxonomy' => 'product_cat',
	 ));
 }

 return $department[0];
}

add_action('woocommerce_archive_description', 'fpusa_category_best_sellers', 16);
function fpusa_category_best_sellers(){
	if( is_shop() ) {
		return;
	}
	$args = array(
		'posts_per_page' => 8,
		'post_type' => 'product',
		'orderby' => 'title',
		'meta_key' => 'total_sales',
		'orderby' => 'meta_value_num',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'		 => get_queried_object()->term_id,
			),
		),
	);

	fpusa_slick_query('Best Sellers', $args);
}

add_action('woocommerce_archive_description', 'fpusa_category_newest_products', 17);
function fpusa_category_newest_products(){
	if( is_shop() ) {
		return;
	}
	$args = array(
		'posts_per_page' => 8,
		'post_type' => 'product',
		'orderby' => 'date',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'		 => get_queried_object()->term_id,
			),
		),
	);

	fpusa_slick_query('Hot new releases', $args);
}

add_action('woocommerce_archive_description', 'fpusa_category_top_rated', 18);
function fpusa_category_top_rated(){
	if( is_shop() ) {
		return;
	}
	$args = array(
		'posts_per_page' => 8,
		'post_type' => 'product',
		'orderby' => 'meta_value_num',
		'meta_key' => '_wc_average_rating',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'		 => get_queried_object()->term_id,
			),
		),
	);

	fpusa_slick_query('Top Rated', $args);
}

add_action('woocommerce_archive_description', 'fpusa_category_cheapest', 18);
function fpusa_category_cheapest(){
	if( is_shop() ) {
		return;
	}
	$args = array(
		'posts_per_page' => 8,
		'post_type' => 'product',
		'order' => 'ASC',
		'orderby' => 'meta_value_num',
		'meta_key' => '_price',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'		 => get_queried_object()->term_id,
			),
		),
	);

	fpusa_slick_query('Cheapest', $args);
}

function fpusa_get_cat_children(){
	/**
	 * Used to get the children of a product category
	 * @return WP_Term $children - The children taxonomys of a product category
	 */
	 if( ! is_shop() ) {
		 $term = get_queried_object();

		 // var_dump( $term );

		 $children = get_terms( $term->taxonomy, array(
			 'parent'    => $term->term_id,
			 'hide_empty' => false
		 ) );

		 return $children;
	 }
}


add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );
function new_loop_shop_per_page( $cols ) {
	/**
	 * Change number of products that are displayed per page (shop page)
	 * @param $cols - How many products per page
	 */
  $cols = 36;
  return $cols;
}

function fpusa_get_cats_w_img(){
	/**
	* Only displays categories with an image assigned to it.
	* @return array $categories_w_img - Collection of image src, link and name of a category
	*/
	$categories_w_img = array();
	$children = fpusa_get_cat_children();
	if( $children ) {
		foreach( $children as $child ){
			$thumbnail_id = get_woocommerce_term_meta( $child->term_id, 'thumbnail_id', true );
			if( $thumbnail_id ){
				$image_src = wp_get_attachment_url( $thumbnail_id );

				array_push( $categories_w_img, array(
					'image_src' => $image_src,
					'link' => get_term_link( (int)$child->term_id ),
					'name' => $child->name,
				));

			}
		}
	}
	return $categories_w_img;
}
