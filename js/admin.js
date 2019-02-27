jQuery(document).ready(function(){
  $('#fpusa_product_video_input_table tbody').on('blur', 'input.fpusa_product_video_input', function(){
    $table = $('#fpusa_product_video_input_table tbody');
    let id = this.id.replace('fpusa_input_', '');
    let next_id = +id + 1;
    let $next_selector = 'fpusa_input_' + next_id;
    if( $('#' + $next_selector).length === 0 ){
      console.log($next_selector + ' doesnt exist');
      $table.append(
        $('<tr/>')
        .append('<td>#' + next_id + '</td>')
        .append('<td><input id="'+ $next_selector +'" class="fpusa_product_video_input" name="product_video_url[' + next_id + ']"/></td>')
      );
    }
  });
});
