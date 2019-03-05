<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="modal fade" id="fpusa_buy_again" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Buy Again</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 col-sm-4">
            <a id="fpusa_ba_img_link" href="#" class="product-image-link product-image-link-sm"></a>
          </div>
          <div class="col">
            <a id="fpusa_ba_title" href=""></a>
            <div class="d-flex">
              <span class="pr-1">Current Price:</span>
              <span id="fpusa_ba_price" class="price"></span>
            </div>
            <div id="fpusa_ba_stock"></div>
            <div id="fpusa_ba_qty"></div>
            <form id="fpusa_ba_form" class="cart" action="" method="post" enctype="multipart/form-data">
              <input type="number" id="<?php echo uniqid( 'quantity_' ) ?>" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
              <button type="submit" name="add-to-cart" value="" class="single_add_to_cart_button button alt">Add to cart</button>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
