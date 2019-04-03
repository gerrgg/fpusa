<?php

function fpusa_choose_location_btn(){
	/*
	*
	* Allows the user to choose which address they would like to use.
	* TODO: Integrate with address class
	*
	*/
	$user = wp_get_current_user();
	$loc_str = fpusa_get_ship_address_str( $user );
	?>
	<a id="user-navigation" class="nav-link align-items-center" data-toggle="modal" data-target="#fpusa_choose_loc">
		<i class="fas fa-map-marker-alt fa-2x pr-2"></i>
		<div class="d-flex flex-column">
			<span id="deliver-name">
				Deliver to <?php echo $user->user_nicename; ?>
			</span>
			<span id="deliver-loc" class="highlight-text">
				<?php echo $loc_str; ?>
			</span>
		</div>
	</a>
	<?php
}

function fpusa_edit_location_modal(){
	/**
	*
	* Creates the html for the location modal
	*
	*/
	?>
	<div class="modal fade" id="fpusa_choose_loc" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="fpusa_choose_loc">Edit your location</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php ( is_user_logged_in() ) ? fpusa_get_user_loc_html() : fpusa_get_guest_loc_html(); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary text-white" data-dismiss="modal">Done</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function fpusa_get_guest_loc_html(){
	/**
	*
	* Displays html within the location modal for non-logged in users.
	*
	*/
	?>
	<p class='text-muted'>Delivery options and speeds may vary based on your location.</p>
	<a role="button" href="/my-account/" class='btn btn-primary btn-block text-white'>Sign in to edit your address</a>
	<?php
}

function fpusa_get_user_loc_html(){
	/**
	*
	* Displays html within the location modal for logged in users.
	*
	*/
	$address = fpusa_get_customer_location_details( 'shipping' );
	if( ! empty( $address ) ) : ?>
		<p class="text-muted">Delivery options and speeds can vary based differant locations</p>
		<label>Current Shipping Address:</label>
		<button role="button" class="text-left mb-2">
			<address>
				<b><?php echo $address['shipto']; ?></b>
				<span><?php echo $address['address_1']; ?>,</span><br>
				<span><?php echo $address['address_2']; ?></span>
				<span><?php echo $address['city']; ?> </span>
				<span><?php echo $address['state']; ?></span>
				<span><?php echo $address['postcode']; ?></span>
			</address>
		</button><br><br>
		<a href="/edit-address/">Edit Addresses</a>
	<?php else :
		 fpusa_get_guest_loc_html();
	endif;
}

function fpusa_buy_again_modal(){
	/**
	* grabs a php file and displays its html contents.
	*/
	wc_get_template('global/buy-again-modal.php');
}

add_shortcode( 'yourstore', 'fpusa_yourstore_buy_it_again' );

function fpusa_yourstore_buy_it_again(){
	$items = fpusa_get_wc_customer_unique_item_purchases(); ?>
	<div class="row">
		<?php
		foreach( $items as $id ) :
			$product = wc_get_product( $id );
			if( $product ) : ?>
				<div class="col-6 col-sm-2">
						<div class="p-2">
						<a class="product-image-link-sm" href="<?php echo $product->get_permalink(); ?>">
							<?php echo $product->get_image(); ?>
						</a>

						<a href="<?php echo $product->get_permalink(); ?>">
							<p class="m-0"><?php echo $product->get_name(); ?></p>
						</a>
						<?php echo wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ); ?>
						<p class="price"><?php echo $product->get_price_html() ?></p>
						<a class="btn btn-warning" href="<?php echo $product->add_to_cart_url() ?>">Add to cart</a>
					</div>
				</div>
				<?php
			endif;
		endforeach; ?>
	</div><?php
}

function fpusa_cat_sub_nav_cat_menu(){
	/**
	*
	*
	*/
		if( fpusa_is_cat() ) : // needed when on shop page
			$siblings = fpusa_get_cat_siblings( fpusa_get_department() );
			if( sizeof( $siblings ) > 1 ) : ?>
				<nav class="navbar navbar-expand-lg navbar-light bg-dark">
			    <div class="navbar-nav">
			      <?php foreach( $siblings as $term ) : ?>
							<li class="nav-item">
								<a class="nav-link" href="<?php echo get_term_link( (int)$term->term_id ) ?>"><?php echo $term->name; ?></a>
							</li>
						<?php endforeach; ?>
			    </div>
				</nav>
			<?php
			endif;
		endif;
}
