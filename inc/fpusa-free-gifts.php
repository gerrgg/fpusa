<?php

add_action( 'init', 'fpusa_create_tables' );
function fpusa_create_tables(){
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  // connection details
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  // Create the features table
  $table_name = $wpdb->prefix . 'fpusa_free_gifts';
  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          gift_givers text NOT NULL,
          gifts text NOT NULL,
          brand text NOT NULL,
          amt mediumint(9) NOT NULL,
          created timestamp DEFAULT CURRENT_TIMESTAMP NULL,
          PRIMARY KEY  (id)
  ) $charset_collate;";
  dbDelta( $sql );
}

add_action('admin_menu', 'fpusa_free_gifts');

function fpusa_free_gifts(){
  add_submenu_page( 'edit.php?post_type=product', __('Free Gifts', 'fpusa'), __('Free Gifts', 'iww'), 'administrator', 'fpusa_free_gifts', 'fpusa_free_gifts_menu');
}

function fpusa_free_gifts_menu(){
  ?>
  <form class="form" method="POST" action="admin-post.php">
    <label>Givers</label>
    <textarea id="gift_givers" name="gift_givers"></textarea><br>
    <label>Gifts</label>
    <textarea id="gifts" name="gifts"></textarea><br>
    <label>Brand</label>
    <input type="text" name="brand" /><br>
    <label>Minimum Spend</label>
    <input type="number" name="amt" min="0"/><br>

    <input type="hidden" name="action" value="fpusa_create_gift_rule">
    <input type="submit" />
  </form>

  <?php
  fpusa_get_gift_categories();
}

add_action('admin_post_nopriv_fpusa_create_gift_rule', 'fpusa_create_gift_rule_callback' );
add_action('admin_post_fpusa_create_gift_rule', 'fpusa_create_gift_rule_callback' );

function fpusa_create_gift_rule_callback(){
  global $wpdb;
  $table_name = $wpdb->prefix . 'fpusa_free_gifts';
  if( isset( $_POST['gift_givers'], $_POST['gifts'], $_POST['brand'], $_POST['amt'] ) ){
    $wpdb->insert(
      $table_name,
      array(
        'gift_givers' => $_POST['gift_givers'],
        'gifts' => $_POST['gifts'],
        'brand' => $_POST['brand'],
        'amt' => $_POST['amt'],
    ));
  }
  wp_redirect('/wp-admin/edit.php?post_type=product&page=fpusa_free_gifts');
}

function fpusa_get_gift_categories(){
  global $wpdb;
  $rows = $wpdb->get_results( "SELECT * FROM $wpdb->prefix" . 'fpusa_free_gifts');
  if( ! empty( $rows ) ) : ?>
    <table>
      <th>ID</th><th>Givers</th><th>Gift</th><th>Brand</th><th>Amount</th><th>Created</th>
      <?php foreach( $rows as $row ) : ?>
        <tr>
          <?php foreach( $row as $col ) : ?>
            <td>
              <?php echo $col; ?>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif;
}

add_action( 'woocommerce_check_cart_items', 'fpusa_check_gift_criteria' );

function fpusa_check_gift_criteria( ){
  global $wpdb;

  $rules = $wpdb->get_results("SELECT * FROM $wpdb->prefix" . 'fpusa_free_gifts');
  $brands_in_cart = fpusa_find_brands_in_cart();
  $cart_value = WC()->cart->get_subtotal();
  $free_gifts = array();

  // loop through each rule
  foreach( $rules as $rule ){
    // if brand is within a rules brand criteria
    if( in_array( $rule->brand, $brands_in_cart ) ){
      // grab all the skus in the gift column
      $arr = explode(',', $rule->gifts );
      // loop through each sku, find a an id and build the product
      foreach( $arr as $sku ){
        $id = iconic_get_product_id_by_sku( $sku );
        if( ! empty( $id ) ){
          $product = wc_get_product( $id );
          // if everything is cool, push to final array for display.
          if( ! empty( $product ) ) array_push( $free_gifts, $product );
        }
      }
    }
  }

  // var_dump( $free_gifts );
  fpusa_show_free_gifts( $free_gifts );
}

function fpusa_find_brands_in_cart(){
  $brands_in_cart = array();
  $cart_items = WC()->cart->get_cart_contents();
  foreach( $cart_items as $item ){
    $brand = $item['data']->get_attribute('pa_brand');
    if( ! empty( $brand ) && ! in_array( $brand, $brands_in_cart ) ) array_push($brands_in_cart, $brand);
  }
  return $brands_in_cart;
}

function fpusa_show_free_gifts( $gifts ){
  if( ! empty( $gifts ) ){
    ?>
    <div id="free-gift-div">
      <button id="free-gift-btn">Dont forget your free gift!</button>
      <div class="free-gift-display">
        <div class="row">
          <?php foreach( $gifts as $product ) : ?>
            <div class="col-2">
              <div class="free-gift-item">
                <?php echo $product->get_image(); ?>
                <a id="<?php echo $product->get_id(); ?>" href="<?php echo $product->add_to_cart_url() ?>" role="button" class="btn btn-secondary btn-block text-white">ADD</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php
  }
}

function iconic_get_product_id_by_sku( $sku = false ) {
    global $wpdb;
    if( !$sku ) return null;
    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
    if ( $product_id ) return $product_id;
    return null;
}
