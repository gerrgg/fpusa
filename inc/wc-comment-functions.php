<?php


function fpusa_create_comment_karma_table(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'karma';
	$sql = "CREATE TABLE $table_name (
		karma_id mediumint(9) NOT NULL AUTO_INCREMENT,
		karma_user_id mediumint(9) NOT NULL,
		karma_comment_id mediumint(9) NOT NULL,
		karma_value mediumint(9) NOT NULL,
		PRIMARY KEY  (karma_id)
	) $charset_collate;";

	fpusa_maybe_create_table( $table_name, $sql );
}

function fpusa_get_comments_template(){
	/*
		looks for a file in /yourtheme/woocommerce/single-product/customer_reviews.php
	*/
	wc_get_template('single-product/customer_reviews.php');
}

function fpusa_cr_get_stars(){
	wc_get_template( 'single-product/review/rating.php' );
}
add_action( 'fpusa_customer_review_left', 'fpusa_cr_get_stars', 10 );



function fpusa_get_rating_histogram( $ratings, $count ){
	?>
	<table class="product-rating-histogram">
	<?php for( $i = 5; $i > 0; $i-- ) :
		$now = ( isset( $ratings[$i] ) ) ? intval( ( $ratings[$i] / $count ) * 100 ) : 0;
	?>
		<tr>
			<td>
				<a href=""><?php echo $i ?> stars</a>
			</td>
			<td style="width: 80%">
				<a class="progress">
					<div class="progress-bar" role="progressbar" style="width: <?php echo $now; ?>%"aria-valuenow="<?php echo $now ?>%" aria-valuemin="0" aria-valuemax="100"></div>
				</a>
			</td>
			<td>
				<a href=""><?php echo $now ?>%</a>
			</td>
		</tr>
	<?php endfor; ?>
	</table> <?php
}

function fpusa_cr_get_review_btn(){
	/*
	* output html for review button
	*/
	wc_get_template('/single-product/review/button.php');
}
add_action( 'fpusa_customer_review_left', 'fpusa_cr_get_review_btn', 20 );

add_action( 'fpusa_customer_review_right', 'fpusa_get_user_product_images', 10 );
function fpusa_get_user_product_images(){
	global $product;
	$col = 4;
	$srcs = fpusa_get_user_content_src( null, $product->get_id() );
	if( ! empty( $srcs ) ) : ?>
		<h5><?php echo sizeof( $srcs ) ?> Customer Images</h5>
		<div id="user-product-img-overview">
			<?php foreach( $srcs as $src ) fpusa_img_link_to_full_show_thumb( $src[1][0], $src[0][0] ); ?>
		</div>
		<?php // TODO: Colorbox! http://www.jacklmoore.com/colorbox/  ?>
		<?php if( sizeof( $srcs ) > $col ) echo '<a href="#">See all product images</a>';
	endif;
}

add_action( 'fpusa_customer_review_right', 'fpusa_sort_comments_by', 15 );

function fpusa_sort_comments_by(){
	global $product;
	?>
	<div class="form-group float-right mt-3">
		<label><b>Sort by</b></label>
		<select class="" id="sort_comments_by" data-product-id="<?php echo $product->get_id(); ?>">
			<option value="meta_value">Top Reviews</option>
			<option value="comment_karma">Most Helpful</option>
			<option value="comment_date">Most Recent</option>
		</select>
	</div>
	<?php
}

add_action( 'fpusa_customer_review_right', 'fpusa_get_reviews', 20 );

function fpusa_get_reviews(){
	/**
	* uses the wp_list_comments() function to setup args and use a custom callback function
	*/
	global $product;

	$args = array(
		'post_type' => 'product',
		'post_id' => $product->get_id(),
		'meta_key' => 'rating',
		'orderby' => 'meta_value',
	 );
	$comments = get_comments( $args );
	// var_dump( $comments );
	?>
	<div id="comments-wrapper">
		<?php wp_list_comments( array( 'callback' => 'fpusa_comments', 'style' => 'div' ), $comments); ?>
	</div>
	<?php
}

add_action('wp_ajax_fpusa_sort_product_reviews', 'fpusa_sort_product_reviews_by');
add_action('wp_ajax_nopriv_fpusa_sort_product_reviews', 'fpusa_sort_product_reviews_by');

function fpusa_sort_product_reviews_by(){
	$args = array(
		'post_type' => 'product',
		'post_id'		=> $_POST['p_id'],
		'meta_key'  => 'rating',
		'orderby'		=> $_POST['sortby']
	);

	$comments = get_comments( $args );
	wp_send_json( $comments );

	wp_die();
}

function fpusa_comments( $comment ){
	/**
	* provides the html markup for each individual comment a product may have.
	* @param WP_Comment $comment
	*/
	$rating = get_metadata( 'comment', $comment->comment_ID, 'rating', true );
	$headline = get_metadata( 'comment', $comment->comment_ID, 'headline', true );
	$verified = get_metadata( 'comment', $comment->comment_ID, 'verified', true );
	$srcs = fpusa_get_user_content_src( $comment->user_id, get_the_id() );

	// var_dump( $srcs );
	?>

	<div id="<?php echo $comment->comment_ID; ?>" class="comment my-4">
		<div class="comment-top d-flex align-items-center">
			<span class="pr-2">
				<?php echo get_avatar($comment->comment_author_email, 50); ?>
			</span>
			<b><?php echo $comment->comment_author ?></b>
		</div>
		<div class="comment-meta">
			<div class="d-flex">
				<?php echo wc_get_rating_html($rating); ?>
				<?php if( ! empty( $headline ) ) echo '<b class="pl-2">' . $headline . '</b>'; ?>
			</div>
			<?php if( $verified ) echo '<span class="verified">Verified Purchase</span>'; ?>
			<div class="d-flex">
				<?php foreach( $srcs as $src ) fpusa_img_link_to_full_show_thumb( $src[1][0], $src[0][0], 'img-xs' );?>
			</div>
			<p class="pt-3"><?php echo $comment->comment_content ?></p>
			<p>
			</p>

		</div>
		<div class="comment-actions" role="group">
			<?php do_action('fpusa_comment_actions', $comment->comment_ID ); ?>
		</div>
		<a role="button" class="show-comments-thread">
		<?php fpusa_get_comments_thread( $comment->comment_ID );
}

function fpusa_get_product_review_link( $id, $action = 'post'){
	/**
	* returns the appropirate url based on the ID and action parameters
	* @param int $id - The id of the product trying to review
	* @param string $action - post is for creating new reviews, edit is for updating reviews
	*/
	$url = "/review?action=$action&p_id=$id";
	return $url;
}

function fpusa_get_comments_thread( $comment_id ){
	$args = array(
		'post_id' => 0,
		'parent' => $comment_id,
		'type' => 'response',
		'orderby' => 'comment_karma',
	);

	$comments = get_comments( $args );

	// var_dump( $comments );

	if( ! empty( $comments ) ){
		echo '<a href="javascript:void(0);" role="button" class="show-comments-thread">Show all ' . sizeof( $comments ) . ' comments</a>';
		echo '<div class="comment-thread">';
		wp_list_comments( array( 'callback' => 'fpusa_comments_thread_callback', 'style' => 'div' ), $comments);
		echo '</div>';
	}
}

function fpusa_comments_thread_callback( $comment ){
	$prev_vote = fpusa_get_previous_karma_vote( $comment->comment_ID );
	$value = ( isset($prev_vote->karma_value) ) ? $prev_vote->karma_value : 0;
	?>
	<div id="<?php echo $comment->comment_ID ?>" class="comment-reply">
		<div class="comment-top d-flex align-items-center">
			<div class="reply-karma d-flex flex-column justify-content-center align-items center text-center">
				<i class="fas fa-chevron-up <?php if( $value == 1 ) echo 'karma-highlight'; ?>" data-increment="1"></i>
				<span id="<?php echo $comment->comment_ID ?>_comment_karma" class="text-center"><?php echo $comment->comment_karma ?></span>
				<i class="fas fa-chevron-down <?php if( $value == -1 ) echo 'karma-highlight'; ?>" data-increment="-1"></i>
			</div>
			<span class="pr-2 border-right">
				<?php echo get_avatar($comment->comment_author_email, 50); ?>
			</span>
			<div class="pl-2">
				<p class="m-0"><b><?php echo $comment->comment_author ?></b></p>
				<p class="text-small"><?php echo $comment->comment_content; ?></p>
			</div>
		</div>

	<?php
}

add_action( 'fpusa_comment_actions', 'fpusa_comment_helpful_button', 10, 1 );
function fpusa_comment_helpful_button( $comment_id ){
	// if user has clicked, switch to unhelpful btn
	$comment = get_comment( $comment_id );
	fpusa_get_helpful_button_html( $comment_id, $comment->comment_karma );
}

function fpusa_get_helpful_button_html( $comment_id, $karma ){
	$args = array(
		'class' => 'success',
		'text' => 'Helpful'
	);

	$prev_vote = fpusa_get_previous_karma_vote( $comment_id );

	if( ! empty( $prev_vote ) ){
		$args['class'] = ( $prev_vote->karma_value != 1 ) ? 'success' : 'danger';
		$args['text'] = ( $prev_vote->karma_value != 1 ) ? 'Helpful' : 'Not Helpful';
	}

	?>
	<button type="button" class="btn comment-helpful-btn btn-<?php echo $args['class'] ?>">
		<span class="btn-text"><?php echo $args['text'] ?></span>
		<span class="badge badge-<?php echo $args['class'] ?>"><?php echo $karma ?></span>
	</button>
	<?php
}

add_action( 'wp_ajax_fpusa_comment_helpful', 'fpusa_add_to_comment_karma' );
function fpusa_add_to_comment_karma(){
	// get comment, increment karma, and update in DB.
	$comment = get_comment( $_POST['id'], ARRAY_A );
	$comment['comment_karma'] = intval($comment['comment_karma']) + intval( $_POST['increment'] );

	$prev_vote = fpusa_get_previous_karma_vote( $_POST['id'] );

	if( $prev_vote->karma_value != $_POST['increment'] ){
		wp_update_comment( $comment );
		echo $comment['comment_karma'];
	}

	fpusa_update_user_karma_link( $_POST['id'], $_POST['increment'] );
	// keep track of who has clicked helpful, keep json of users who clicked in comment_meta.
	wp_die();
}

function fpusa_update_user_karma_link( $comment_id, $karma ){
	global $wpdb;
	$table_name = $wpdb->prefix . 'karma';

	$args = array(
		'karma_user_id' => get_current_user_id(),
		'karma_comment_id' => $comment_id,
		'karma_value' => $karma
	);

	//returns id of prev vote;
	$prev_vote = fpusa_get_previous_karma_vote( $comment_id );

	if( empty( $prev_vote ) ){
		$wpdb->insert( $table_name, $args );
	} else {
		$wpdb->update( $table_name, $args, array( 'karma_id' => $prev_vote->karma_id ) );
	}

	return $prev_vote;

}

function fpusa_get_previous_karma_vote( $comment_id ){
	global $wpdb;
	$user_id = get_current_user_id();
	$table_name = $wpdb->prefix . 'karma';
	$result = $wpdb->get_row( "SELECT *
														 FROM $table_name
														 WHERE karma_user_id = $user_id
														 AND karma_comment_id = $comment_id" );

	// var_dump( $result );
	return $result;
}



add_action( 'fpusa_comment_actions', 'fpusa_comment_on_comment', 20, 1 );
function fpusa_comment_on_comment( $parent_id ){
	?>
	<button type="button" class="btn btn-outline-secondary comment-on-comment">
		Comment
		<i class="far fa-comment"></i>
	</button>
	<?php
}

add_action( 'wp_ajax_fpusa_comment_on_comment', 'fpusa_comment_on_comment_callback' );
function fpusa_comment_on_comment_callback(){
	$user = wp_get_current_user();
	$comment = get_comment( $_POST['parent_comment'] );

	$comment_id = wp_insert_comment( array(
    'comment_author' => $user->user_login,
    'comment_author_email' => $user->user_email,
    'comment_content' => $_POST['comment'],
    'comment_type' => 'response',
    'comment_parent' => $_POST['parent_comment'],
    'user_id' => $user->ID,
		'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
		'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
    'comment_date' => current_time('mysql'),
    'comment_approved' => 1,
	));

	echo $comment_id;

	wp_die();
}

// add_action( 'fpusa_comment_actions', 'fpusa_report_comment', 30, 1 );
function fpusa_report_comment( $parent_id ){
	?>
	<button type="button" class="btn btn-outline-danger comment-report">
		Report Abuse
		<i class="fas fa-bullhorn"></i>
	</button>
	<?php
}




add_shortcode( 'create_review', 'fpusa_create_review_template' );

function fpusa_create_review_template(){
	$action = $_GET['action'];

	switch( $action ){
		case 'post':
			wc_get_template('single-product/review/create.php');
			break;
		case 'review-more':
			fpusa_get_more_to_review();
			break;
		case 'edit':
			wc_get_template('single-product/review/create.php');
			break;
		case 'show':
			wc_get_template('single-product/review/show.php');
			break;
	}
}

function fpusa_get_more_to_review(){
	$items = fpusa_get_wc_customer_unique_item_purchases();
	echo '<h3>Review other purchases!</h3>';
	echo '<div class="row">';
	foreach( $items as $id ){
		$product = wc_get_product( $id );
		$comment = fpusa_get_user_product_review( $id );
		$rating = get_metadata('comment', $comment['comment_ID'], 'rating', true);
		if( $product ) : ?>
			<div class="col-6 col-sm-3 my-3">
				<?php if ( $rating > 0 ) : ?>
				<a href="<?php echo fpusa_get_product_review_link( $id, 'edit' ); ?>" class="text-center">
					<?php echo $product->get_image(); ?>
					<?php fpusa_get_stars_review_link( $id, $rating, 'edit', 1 ) ?>
				</a>
			<?php else : ?>
				<a href="<?php echo fpusa_get_product_review_link( $id ); ?>" class="text-center">
					<?php echo $product->get_image(); ?>
					<?php fpusa_get_stars_review_link( $id, $rating, 'post', 1 ) ?>
				</a>
			<?php endif; ?>
			</div>
		<?php endif;

	}
	echo '</div>';
}

function fpusa_get_overall_star_rating_form( $id, $rating, $action = 'post', $link ){
	?>
	<div class="create-review-star-rating form-group">
		<h5>Overall rating</h5>
		<?php fpusa_get_stars_review_link( $id, $rating, $action, $link ) ?>
		<input id="product-rating" name="rating" type="hidden" value="<?php echo $rating; ?>" />
	</div>
	<?php
}



function fpusa_get_stars_review_link( $id, $rating, $action = 'post', $link = 1){
	$max = 5;
	$empty = $max - $rating;
	?>
	<div class="d-flex my-1">

		<?php for( $i = 1; $i <= $rating; $i++ ) : ?>
			<?php if( $link != 0 ) : ?><a class="link-color-normal" href="<?php echo fpusa_get_product_review_link( $id, $action ) ?>"><?php endif; ?>
				<i id="star-<?php echo $i ?>" class="fas fa-star fa-2x click-star" data-rating="<?php echo $i; ?>"></i>
			<?php if( $link != 0  ) : ?></a><?php endif; ?>
		<?php endfor; ?>

		<?php for( $i = $rating + 1; $i <= $max; $i++ ) : ?>
			<?php if( $link != 0  ) : ?><a class="link-color-normal" href="<?php echo fpusa_get_product_review_link( $id, $action ) ?>"><?php endif; ?>
				<i id="star-<?php echo $i ?>" class="far fa-star fa-2x click-star" data-rating="<?php echo $i; ?>"></i>
			<?php if( $link != 0  ) : ?></a><?php endif; ?>
		<?php endfor; ?>

	</div><?php
}



function fpusa_get_wc_customer_unique_item_purchases( $user_id = NULL ){
	if( is_null( $user_id ) ) $user_id = get_current_user_id();
	$orders = wc_get_orders( array( 'customer_id' => $user_id ) );
	$unique_items = array();
	foreach( $orders as $order ){
		foreach( $order->get_items() as $item ){
			$product = $item->get_product();
			if( ! empty( $product ) && ! in_array( $product->get_id(), $unique_items ) ){
				array_push( $unique_items, $product->get_id() );
			}
		}
	}

	return $unique_items;
}

function fpusa_create_review_product_preview( $product ){
	$src = wp_get_attachment_image_src( $product->get_image_id() );
	?>
	<div class="product-preview d-flex align-items-center form-group">
    <img src="<?php echo $src[0]; ?>" class="img-xs" />
    <p class="m-0"><?php echo $product->get_name(); ?></p>
  </div>
	<?php
}

function fpusa_get_user_product_review( $p_id ){

	$comments = get_comments(array(
		'post_id' 						=> $p_id,
		'user_id' 						=> get_current_user_id(),
		'include_unapproved'  => false,
	));

	$comment = get_comment( $comments[0]->comment_ID , ARRAY_A );

	return $comment;
}

function fpusa_add_headline( $headline = null ){
	?>
	<div class="form-group">
		<h5>Add a headline</h5>
		<input type="text" class="form-control" name="headline" value="<?php if( ! is_null( $headline ) ) echo $headline; ?>">
	</div>
	<?php
}

function fpusa_add_comment( $content = '' ){
	?>
	<div class="form-group">
		<h5>Write your review</h5>
		<textarea rows="4" name="comment" class="form-control" placeholder="What did you like or dislike? What did you use this product for?" ><?php if( ! empty( $content ) ) echo $content; ?></textarea>
	</div>
	<?php
}

function fpusa_drop_zone(){
	//https://stackoverflow.com/questions/29411257/how-to-integrate-dropzonejs-with-wordpress-media-handler-in-frontend
	?>
		<div class="form-group">
			<h5>Add a photo</h5>
			<p>A picture speaks a thousand words.</p>
			<input type="file" name="files">
		</div>
	<?php
}


function fpusa_handle_dropped_media(){
		// Dropzone.js integration to handle media upload
		status_header(200);

		$num_files = count( $_FILES['files']['tmp_name'] );
		$attachments = array();
		$attachment_id = 0;

		if ( !empty($_FILES) ) {
        $files = $_FILES;
        foreach($files as $file) {
            $newfile = array (
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size']
            );

            $_FILES = array('upload' => $newfile);
            foreach($_FILES as $file => $array) {
							//https://codex.wordpress.org/Function_Reference/media_handle_upload
                $attachment_id = media_handle_upload( $file, $_POST['product_id'] );
								array_push( $attachments, $attachment_id );
            }
        }
    }

    return $attachments;
    die();
}

add_action( 'admin_post_fpusa_process_product_review', 'fpusa_process_review' );
// add_action( 'admin_post_nopriv_fpusa_process_product_review', 'fpusa_process_review' );

function fpusa_process_review(){
	// create the comment, connect it to the product
	$comment_id = fpusa_create_comment( $_POST );

	if( ! empty( $_FILES ) ){
		// upload the attachments, collect the attachment ids
		$attachment_ids = fpusa_handle_dropped_media();
		//foreach attachment, create a postmeta pointing to the comment the attachment belongs to.
		foreach( $attachment_ids as $id ){
			update_post_meta( $id, '_wp_attachment_comment', $comment_id );
		}
	}

	wp_redirect( fpusa_get_review_more_link() );
}

function fpusa_get_review_more_link(){
	return '/review?action=review-more';
}

function fpusa_create_comment( $data ){
	global $wpdb;
	$user = wp_get_current_user();

	$comment = fpusa_get_user_product_review( $data['product_id'] );

	$args = array(
		'comment_post_ID' => $data['product_id'],
		'comment_author'	=> $user->user_login,
		'comment_author_email'	=> $user->user_email,
		'comment_author_url'	=> $user->user_url,
		'comment_content' =>  $data['comment'],
		'comment_type'			=> 'review',
		'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
		'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
		'comment_date' => current_time( 'mysql', $gmt = 0 ),
		'user_id' => get_current_user_id(),
		'comment_approved' => 1,
	);

	if( ! empty( $comment ) ){
		// careful of character case!
		$comment_id = $comment['comment_ID'];
		$args['comment_ID'] = $comment_id;
		wp_update_comment($args);
	} else {
		$comment_id = wp_insert_comment($args);
	}



	update_comment_meta( $comment_id, 'rating', $data['rating'] );
	update_comment_meta( $comment_id, 'headline', $data['headline'] );

	// https://docs.woocommerce.com/wc-apidocs/function-wc_customer_bought_product.html
	update_comment_meta( $comment_id, 'verified',
		wc_customer_bought_product( $user->user_email, get_current_user_id(), $data['product_id'] )
	);

	return $comment_id;
}
