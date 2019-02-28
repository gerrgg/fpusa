<?php
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




function my_enqueue($hook) {
    // Only add to the edit.php admin page.
    // See WP docs.
    if ('post.php' !== $hook) {
        return;
    }
    wp_enqueue_script('fpusa_admin_script', get_template_directory_uri() . '/js/admin.js', array('jquery'), filemtime(get_template_directory() . '/js/admin.js'), true);
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
	$saved_urls = fpusa_get_product_video_urls( $post );
	if( ! empty( $saved_urls ) ) : ?>
		<p>Video Url(s)</p>
		<table id="fpusa_product_video_input_table">
			<?php for( $i = 0; $i < sizeof( $saved_urls ); $i++ ) : ?>
        <?php if( ! empty( $saved_urls[$i] ) ) : ?>
  				<tr>
  					<td><label>#<?php echo $i ?></label></td>
  					<td>
  						<input id="fpusa_input_<?php echo $i; ?>" type="url" class="fpusa_product_video_input" name="product_video_url[<?php echo $i; ?>]" placeholder="https://youtu.be/JfBoHBTxUus" value="<?php echo $saved_urls[$i]; ?>">
  					</td>
  				</tr>
        <?php endif; ?>
  		<?php endfor; ?>
		</table>
	<?php endif;
	// var_dump( get_post_meta($post->ID) );
}

function fpusa_get_product_video_urls( $post ){
	$count = 0;
	$saved_urls = array();
	while( metadata_exists( 'post', $post->ID, 'product_video_url_' . $count ) ){
		$saved_urls[$count] = get_post_meta( $post->ID, 'product_video_url_' . $count, true );
		$count++;
	}
	return $saved_urls;
}

function fpusa_save_post_meta( $post_id, $post ){
	if( isset( $_POST['product_video_url'] ) ){
		$count = 0;
		foreach( $_POST['product_video_url'] as $url ){
      if( ! empty( $url ) ){
  			update_post_meta( $post_id, 'product_video_url_' . $count, $url );
  			$count++;
      }
	  }
	}
}
add_action( 'save_post', 'fpusa_save_post_meta', 10, 2 );
