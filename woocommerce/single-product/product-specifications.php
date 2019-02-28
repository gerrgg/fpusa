<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$spec_name = json_decode( get_post_meta( get_the_ID(), 'spec_name', true ) );
$spec_value = json_decode( get_post_meta( get_the_ID(), 'spec_value', true ) );

?>
<h2>Specifications Table</h2>
<table class="table table-hover border" style="width: 100%">
	<?php for($i = 0; $i < sizeof( $spec_name ); $i++ ) : ?>
		<?php if( isset( $spec_name[$i], $spec_value[$i] ) ) : ?>
			<tr>
				<th><?php echo $spec_name[$i] ?>:</th>
				<td><?php echo $spec_value[$i] ?></td>
			</tr>
		<?php endif; ?>
	<?php endfor; ?>
</table>
