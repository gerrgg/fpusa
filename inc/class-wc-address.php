<?php

class Address
{
  public $ship_to;
  public $address_1;
  public $address_2;
  public $city;
  public $state;
  public $postal;
  public $country;
  public $phone;
  public $notes;
  public $user_id;

  public function __construct( $id ){
    global $wpdb;
    $data = $this->get_data( $id );
    $this->ship_to = $data->address_shipto;
    $this->address_1 = $data->address_1;
    $this->address_2 = $data->address_2;
    $this->city = $data->address_city;
    $this->state = $data->address_state;
    $this->postal = $data->address_postal;
    $this->country = $data->address_country;
    $this->phone = $data->address_phone;
    $this->notes = $data->address_delivery_notes;
    $this->user_id = $data->address_user_id;
  }

  public function get_data( $id ){
    global $wpdb;
  	$table_name = $wpdb->prefix . 'address';

  	$address = $wpdb->get_row(
  			"SELECT *
  			 FROM $table_name
  			 WHERE address_id = $id"
  	);

    return $address;
  }

  public function get_edit_link(){
    return '';
  }

  public function get_delete_link(){
    return '';
  }

  public function get_address_as_default_link(){
    return '';
  }
}
