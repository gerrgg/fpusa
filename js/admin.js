jQuery(document).ready(function(){

  fpusa_dynamic_input_table(
    $('#fpusa_product_video_input_table'),
    [
      { type: 'url', name: 'product_videos', class: 'fpusa_product_video_input', },
    ]
   );

  fpusa_dynamic_input_table(
    $('#fpusa_admin_product_specifications'),
    [
      { type: 'text', name: 'spec_name'},
      { type: 'text', name: 'spec_value'}
    ]
  );

  function fpusa_dynamic_input_table( $target, $row ){
    $target
    .on('click', 'button.add', function(){
      $next_row = ( +$target.find('tr').last().attr('class') + 1 ) || 0;
      // console.log( $next_row );
      $target.find('table').append(
        $('<tr/>', { class: $next_row }).append(
          $('<td/>').append( function(){
            let str = '';
            for(let i = 0; i < $row.length; i++){
              str += '<input type="'+ $row[i].type +'" name="'+ $row[i].name +'['+ $next_row +']" />';
            }
            return str;
          }),
          $('<td/>').append('<button type="button" class="remove">&times;</button>'),
        ),
      );
    })
    .on('click', 'button.remove', function(){
      $(this).parent().parent().remove();
    });
  }


});
