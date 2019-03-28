<?php

function fpusa_create_user_address_table(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'address';
	$forign_table_name = $wpdb->prefix . 'address';
	$sql = "CREATE TABLE $table_name (
		address_id mediumint(9) NOT NULL AUTO_INCREMENT,
		address_shipto varchar(255) NOT NULL,
		address_1 varchar(255) NOT NULL,
		address_2 varchar(255) NULL,
		address_city varchar(255) NULL,
		address_state varchar(100) NOT NULL,
		address_postal varchar(25) NOT NULL,
		address_country varchar(100) NOT NULL,
		address_phone varchar(100) NOT NULL,
		address_delivery_notes text NOT NULL,
		address_user_id mediumint(9) NOT NULL,
		PRIMARY KEY (address_id),
		FOREIGN KEY ( address_user_id ) REFERENCES $wpdb->users (ID)
	) $charset_collate;";

	fpusa_maybe_create_table( $table_name, $sql );
}

function fpusa_get_user_address_ids( $id ){
	global $wpdb;
	$table_name = $wpdb->prefix . 'address';
	$sql = "SELECT address_id FROM $table_name WHERE address_user_id = $id";
	$addresses = $wpdb->get_results($sql, ARRAY_N );

	// sort addresses by default
  $default = get_user_meta( get_current_user_id(), 'default_address', true );
	if( address_exists( $default ) ){
		$addresses = fpusa_sort_addresses_by_default( $addresses, $default );
	}


	return $addresses;
}

function address_exists( $id ){
	$exists = false;
	$address = new Address( $id );
	if( ! empty( $address->ID ) ) $exists = true;
	return $exists;
}

function fpusa_sort_addresses_by_default( $addresses, $default ){
  // var_dump( $addresses );
  $arr = array();
  foreach( $addresses as $address ) array_push( $arr, $address[0] );

  //https://stackoverflow.com/a/18852178/3247137
  $sorted_arr = array_diff( $arr, array( $default ) );
  array_unshift( $sorted_arr, $default );

  return $sorted_arr;
}

function fpusa_get_ship_address_str( $user = '' ){
	/**
	*
	* Gets the shipping address of a user and converts to string.
	* @param WP_User
	* @return string $loc_star
	* TODO: Currently lacks the functionality for multiple addresses - Needs an address table?
	*
	*/
	if( empty( $user ) ) $user = wp_get_current_user();

	if( ! empty($user) ){
		$customer = new WC_Customer( $user->ID );
		if( ! empty( $customer->get_shipping_city() ) && $customer->get_shipping_postcode() ){
			$loc_str = $customer->get_shipping_city() . ' ' . $customer->get_shipping_postcode();
		} else {
			$loc_str = "Login to select location";
		}
	} else {
		$loc_str = "Login to select location";
	}
	return $loc_str;
}

function fpusa_get_customer_location_details( $type = 'shipping' ){
	/**
	*
	* Displays html within the location modal for non-logged in users.
	* @param string $type - specifies which address to return
	* @return array $address - collection of user address data
	*/
	$user = wp_get_current_user();
	$address = array(
		'shipto' => get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true ),
		'address_1' => get_user_meta( $user->ID, $type.'_address_1', true ),
		'address_2' => get_user_meta( $user->ID, $type.'_address_2', true ),
		'city' => get_user_meta( $user->ID, $type.'_city', true ),
		'state' => get_user_meta( $user->ID, $type.'_state', true ),
		'postcode' => get_user_meta( $user->ID, $type.'_postcode', true ),
	);
	return $address;
}

// Hook in
add_filter( 'woocommerce_default_address_fields' , 'fpusa_custom_address_fields', 100 );

// Our hooked in function - $address_fields is passed via the filter!
function fpusa_custom_address_fields( $address_fields ) {
     $address_fields['first_name']['class'][] = 'form-row-wide';
		 $address_fields['last_name']['class'][] = 'form-row-wide';

		 // var_dump( $address_fields['last_name'] );

		 $address_fields['phone'] = array(
			 'label' => 'Phone',
			 'required' => false,
			 'class' => array(
				 'form-row-last',
				 'form-row-wide'
			 ),
			 'autocomplete' => false,
			 'priority' => 100,
		 );

     return $address_fields;
}

function fpusa_get_delivery_notes_form( $value ){
	?>
	<p class="form-row form-row-last form-row-wide">
	<h5>Add delivery instructions</h5>
	<p>Do we need additional information to help us find this address?</p>
	<textarea id="delivery_instructions" name="delivery_instructions" class="m-0" placeholder="Provide details such as building description, nearby landmarks or other instructions."><?php echo $value ?></textarea>
	<p>
	<?php
}

add_filter( 'woocommerce_checkout_fields', 'fpusa_custom_checkout_fields' );

function fpusa_custom_checkout_fields( $fields ){
	$fields['billing']['billing_email']['priority'] = 10;
	$fields['billing']['billing_email']['class'][] = 'col-6';

	$fields['billing']['billing_phone']['priority'] = 10;
	$fields['billing']['billing_phone']['class'][] = 'col-6';

	return $fields;
}

function fpusa_update_zip(){
	$user = new WP_Customer();
	$user->set_shipping_postcode();
}

add_action( 'admin_post_edit_address', 'fpusa_edit_address' );

function fpusa_edit_address(){
	if ( ! isset( $_POST['woocommerce-edit-address-nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce-edit-address-nonce'], 'woocommerce-edit_address' ) ) {
   print 'Sorry, your nonce did not verify.';
   exit;
	} else {
		global $wpdb;
		$table_name = $wpdb->prefix . 'address';

		var_dump( $_POST );

		$action = ( $_POST['address_id'] === 'new' ) ? 'insert' : 'update';

		$args = array(
			'address_shipto' => $_POST['ship_to'],
			'address_1' => $_POST['address_1'],
			'address_2' => $_POST['address_2'],
			'address_city' => $_POST['city'],
			'address_state' => $_POST['state'],
			'address_postal' => $_POST['postal'],
			'address_country' => $_POST['country'],
			'address_phone' => $_POST['phone'],
			'address_delivery_notes' => $_POST['delivery_instructions'],
			'address_user_id' => get_current_user_id(),
		);


		if( $action === 'insert' ){
			$wpdb->insert(
				$table_name,
				$args
			);

			$default_address = get_metadata( 'user', get_current_user_id(), 'default_address', true );

			if( empty( $default_address ) ){
				update_metadata( 'user', get_current_user_id(), 'default_address', $wpdb->insert_id );
			}

		} else {
			$wpdb->update(
					$table_name,
					$args,
					array( 'address_id' => $_POST['address_id'] )
			);
		}



		wp_redirect( '/edit-address/' );
	}
}

function fpusa_get_address( $id ){
	$address = new Address( $id );
	return ( empty( $address ) );
}

function fpusa_form_field( $key, $value, $type = 'text' ){
	// echo $key . ' ' . $value . ' ' . $type . '<br>';
	if( $key != 'state' && $key != 'country'  ) : ?>
	<div class="form-group">
		<label for="<?php echo $key ?>"><?php echo str_replace( '_', ' ', ucfirst( $key ) ) ?></label>
		<input id="<?php echo $key ?>" class="form-control" type="<?php echo $type ?>" name="<?php echo $key ?>" value="<?php echo $value ?>" />
	</div>
<?php
	else : ?>
		<label for="<?php echo $key ?>"><?php echo str_replace( '_', ' ', ucfirst( $key ) ) ?></label>
		<?php woocommerce_form_field( $key, array('type' => $key), wc_get_post_data_by_key( $key, $value ) );
	endif;
}

add_action( 'admin_post_fpusa_delete_address', 'fpusa_delete_address' );
add_action( 'admin_post_fpusa_make_address_default', 'fpusa_make_address_default' );

function fpusa_delete_address(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'address';

	$wpdb->delete($table_name, array( 'address_id' => $_GET['id'] ));

	wp_redirect('/edit-address/');
}

function fpusa_make_address_default(){
	global $wpdb;
	$wpdb->update(
		$wpdb->usermeta,
		array( 'meta_value' => $_GET['id'] ),
		array( 'user_id' => get_current_user_id(), 'meta_key' => 'default_address' )
	);

	fpusa_customer_address_sync( $_GET['id'] );
	exit;
}

function fpusa_customer_address_sync( $address_id ){
	$address = new Address( $address_id );
	if( ! empty( $address ) ){
		var_dump( $address );
		$address->sync_customer( get_current_user_id() );
	}
}
