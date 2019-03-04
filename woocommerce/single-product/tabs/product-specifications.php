<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$spec_name = json_decode( get_post_meta( get_the_ID(), 'spec_name', true ) );
$spec_value = json_decode( get_post_meta( get_the_ID(), 'spec_value', true ) );
?>

<?php if( isset( $spec_name, $spec_value ) ) : ?>
<div class="col-12 col-sm-6">
	<h2>Specifications Table</h2>
	<table id="specifications-table" class="table table-hover table-half border ml-5">
		<?php for($i = 0; $i < sizeof( $spec_name ); $i++ ) : ?>
				<tr>
					<th><?php echo $spec_name[$i] ?>:</th>
					<td><?php echo $spec_value[$i] ?></td>
				</tr>
		<?php endfor; ?>
	</table>
</div>
<?php endif; ?>
