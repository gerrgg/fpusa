<?php

class Address
{
  public $ID;
  public $ship_to;
  public $address_1;
  public $address_2;
  public $city;
  public $state;
  public $postal;
  public $country;
  public $phone;
  public $notes;
  public $type;
  public $user_id;

  public function __construct( $id ){
    $data = $this->get_data( $id );
    if( ! empty( $data ) ){
      $this->ID = $id;
      $this->ship_to = $data->address_shipto;
      $this->address_1 = $data->address_1;
      $this->address_2 = $data->address_2;
      $this->city = $data->address_city;
      $this->state = $data->address_state;
      $this->postal = $data->address_postal;
      $this->country = $data->address_country;
      $this->phone = $data->address_phone;
      $this->notes = $data->address_delivery_notes;
      $this->type = $data->address_type;
      $this->user_id = $data->address_user_id;
    }
  }

  public function convert_to_radio(){
    $str = sprintf(
      '<b>%s</b><span> %s %s, %s, %s, %s, %s</span>',
      $this->ship_to, $this->address_1, $this->address_2, $this->city, $this->state, $this->postal, $this->country
    );
    ?>
    <div class="form-check pl-4 checkout-address <?php if( $this->is_default() ) echo 'active'; ?>">
      <input class="form-check-input" type="radio" name="use_address" id="use_address_<?php echo $this->ID ?>" value="<?php echo $this->ID ?>" <?php if( $this->is_default() ) echo 'checked' ?>>
      <label class="form-check-label checkout-address-label" for="use_address_<?php echo $this->ID ?>">
        <?php echo $str; ?>
          <span>
            <?php make_modal_btn(  array(
                'text'   => 'EDIT ADDRESS',
                'title'  => 'Edit your shipping address',
                'model'  => 'address',
                'action' => 'edit'
              ) ); ?>
          </span>
      </label>

    </div>
    <?php
  }

  public function sync_customer( $user_id ){
    $name = $this->get_first_n_last_name();
    update_user_meta( $user_id, 'shipping_first_name', $name[0] );
    update_user_meta( $user_id, 'shipping_last_name', $name[1] );
    update_user_meta( $user_id, 'shipping_address_1', $this->address_1 );
    if( ! empty( $this->address_2 ) ) update_user_meta( $user_id, 'shipping_address_2', $this->address_2 );
    update_user_meta( $user_id, 'shipping_city', $this->city );
    update_user_meta( $user_id, 'shipping_state', $this->state );
    update_user_meta( $user_id, 'shipping_postcode', $this->postal );
    update_user_meta( $user_id, 'shipping_country', $this->country );
    update_user_meta( $user_id, 'shipping_phone', $this->phone );
  }

  public function get_first_n_last_name(){
    return explode( ' ', $this->ship_to );
  }

  public function is_default(){
    $default = get_metadata( 'user', get_current_user_id(), 'default_address', true );
    return ( $this->ID === $default );
  }

  public function formatted_address(){
    return sprintf("<ul class='list-unstyled m-0 p-0'>
  									<li><b>%s</b></li>
  									<li>%s <br>
  											%s
  									</li>
  									<li>%s, %s  %s</li>
  									<li>Phone number: +%s</li>
  									<ul>
  									",
  									$this->ship_to, $this->address_1, $this->address_2,
  									$this->city, $this->state, $this->postal,
  									$this->phone, $this->notes);
  }

  public function get_notes(){
    return '<b>Order Notes:</b> ' . $this->notes;
  }

  public function get_data( $id ){
    if( $id != 'new' ){
      global $wpdb;
      $table_name = $wpdb->prefix . 'address';

      $address = $wpdb->get_row(
        "SELECT *
        FROM $table_name
        WHERE address_id = $id"
      );

      return ( ! empty( $address ) ) ? $address : '';
    }
  }

  public function get_edit_link(){
    return "/edit-address/$this->ID";
  }

  public function get_delete_link(){
    return admin_url( "admin-post.php?action=fpusa_delete_address&id=$this->ID" );
  }

  public function get_address_as_default_link(){
    return admin_url( "admin-post.php?action=fpusa_make_address_default&id=$this->ID" );
  }
}
