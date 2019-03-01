<?php

function my_enqueue($hook) {
    // Only add to the edit.php admin page.
    // See WP docs.
    if ('post.php' !== $hook) {
        return;
    }
		wp_enqueue_script('fpusa_admin_script', get_template_directory_uri() . '/js/admin.js', array('jquery'), filemtime(get_template_directory() . '/js/admin.js'), true);
		wp_enqueue_style( 'fpusa_admin_style', get_template_directory_uri() . '/css/admin.css', array(), filemtime(get_template_directory() . '/css/admin.css'), 'all' );
}

// https://codex.wordpress.org/Adding_Administration_Menus
/** Step 1. */
function fpusa_theme_options() {
	add_theme_page( 'Theme Options', 'Theme Options', 'manage_options', 'fpusa_options', 'fpusa_options_callback' );
  add_action( 'admin_init', 'fpusa_register_settings' );
}
add_action('admin_menu', 'fpusa_theme_options');
/**
* Registers a text field setting for Wordpress 4.7 and higher.
**/
function fpusa_register_settings() {
    // register_setting( 'fpusa_theme_options', 'my_option_name' );
}
function fpusa_options_callback(){
  ?>
  <div class="wrap">
    <h1><?php echo bloginfo('sitename'); ?> Theme Options</h1>
    <form method="post" action="options.php">
      <?php settings_fields( 'fpusa_theme_options' ); ?>
      <?php do_settings_sections( 'fpusa_theme_options' ); ?>
      <h2>Social Media Connections</h2>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">New Option Name</th>
          <td><input type="text" name="new_option_name" value="<?php echo esc_attr( get_option('new_option_name') ); ?>" /></td>
        </tr>
      </table>
    <?php submit_button(); ?>
    </form>
  </div>
  <?php
}
add_action('admin_enqueue_scripts', 'my_enqueue');
function fpusa_add_product_videos_meta_box(){
	add_meta_box(
		'fpusa-product-video',
		__('Product Videos', 'fpusa'),
		'fpusa_product_video_callback',
		'product',
		'side',
		'low'
	);
}

add_action( 'add_meta_boxes', 'fpusa_add_product_videos_meta_box' );
function fpusa_product_video_callback( $post ){
	/*
	 * needed for security reasons
	 */
	wp_nonce_field( basename( __FILE__ ), 'fpusa_product_video_callback' );
	$saved_urls = json_decode(get_post_meta( $post->ID, 'product_videos', true ));
	?>
	<div id="fpusa_product_video_input_table">
		<p>Video Url(s)</p>
		<table>
			<?php if( ! empty( $saved_urls ) ) : ?>
				<?php for( $i = 0; $i < sizeof( $saved_urls ); $i++ ) : ?>
	        <?php if( ! empty( $saved_urls[$i] ) ) : ?>
	  				<tr class="<?php echo $i; ?>">
	  					<td>
	  						<input id="fpusa_input_<?php echo $i; ?>" type="url" class="fpusa_product_video_input" name="product_videos[<?php echo $i; ?>]" placeholder="https://youtu.be/JfBoHBTxUus" value="<?php echo $saved_urls[$i]; ?>">
	  					</td>
							<td><button type="button" class="remove">&times;</button></td>
	  				</tr>
	        <?php endif; ?>
	  		<?php endfor; ?>
			<?php endif; ?>
		</table>
		<button type="button" class="add">Add</button>
	</div>
	<?php
	var_dump( $saved_urls );
}

function fpusa_save_multi_product_meta( $post_id, $post ){
	fpusa_save_product_meta( $post_id, array('product_videos') );
	fpusa_save_product_meta( $post_id, array('spec_name', 'spec_value') );
}
add_action( 'save_post', 'fpusa_save_multi_product_meta', 10, 2 );

function fpusa_save_product_meta( $post_id, $meta_key ){
	foreach( $meta_key as $key ){
		if( isset( $_POST[$key] ) ){
			$package = array();
			foreach( $_POST[$key] as $url ){
				( ! empty( $url ) ) ? array_push( $package, $url ) : delete_post_meta($post_id, $key);
		  }
			if( ! empty( $package ) ){
				update_post_meta( $post_id, $key, json_encode( $package ) );
			}
		}
	}
}

add_action( 'save_post', 'fpusa_save_product_specifications', 20, 2 );

add_action( 'woocommerce_product_options_advanced', 'fpusa_admin_product_specifications' );
function fpusa_admin_product_specifications(){
	global $product;
	$spec_name = json_decode( get_post_meta( get_the_ID(), 'spec_name', true ) );
	$spec_value = json_decode( get_post_meta( get_the_ID(), 'spec_value', true ) );
	// var_dump( get_post_meta( get_the_ID() ) );
	?>
	<div id="fpusa_admin_product_specifications">
		<p class="form-field comment_status_field">
			<label>Product Specifications</label>
			<table style="width: 100%">
					<?php for($i = 0; $i < sizeof( $spec_name ); $i++ ) : ?>
						<?php if( isset( $spec_name[$i], $spec_value[$i] ) ) : ?>
						<tr class="<?php echo $i; ?>">
							<td>
								<input type="text" name="spec_name[<?php echo $i; ?>]" value="<?php echo $spec_name[$i] ?>">
								<input type="text" name="spec_value[<?php echo $i; ?>]" value="<?php echo $spec_value[$i] ?>">
							</td>
							<td><button type="button" class="remove">&times;</button></td>
						</tr>
					<?php endif; ?>
				<?php endfor; ?>
			</table>
		</p>
		<button type="button" class="add">Add</button>
	</div>
	<?php
}
