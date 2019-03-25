jQuery( document ).ready(function( $ ){

  fpusa_find_attributes_without_value( $('table.variations select') );
  $('#for_img').zoom();

  $('#fpusa_form').change(function(){
    let options = $('table.variations select');
    let selections = [];

    options.each( function() {
      selections.push( this.value.length );
    });

    if( selections.every( notZero ) ){
      // ready
      $('#single-additional-info').hide();
      let variation_id = $('input[name="variation_id"]').val();
      var data = {
        'action' : 'fpusa_get_variation_data',
        'p_id' : variation_id
      }
      $.post(ajax_object.ajax_url, data, function( response ){
        // console.log( response );
        $('.product_title').html(response.name);
        $('.variable.price').html(response.price);
        // TODO: Doesnt work.
        $('p.stock').html(response.stock);
      });
    } else {
      $('#single-additional-info').show();
      fpusa_find_attributes_without_value( options );
    }

  });

  // Single product images - start

  $('.fpusa-woocommerce_gallery_thumbnail img').mouseenter(function(){
    // remove all .thumb-active from thumbnails
    if( ! $(this).hasClass('thumb-active') ){
      $('.fpusa-woocommerce_gallery_thumbnail img').each(function(){
        $(this).removeClass('thumb-active');
      });

      $(this).addClass('thumb-active');
      fpusa_change_main_img( $(this) );
    }

  });

  function fpusa_change_main_img( thumb ){
    console.log( thumb );
    $image = $('#for_img');
    $video = $('#for_vid');
    if( ! thumb.hasClass('fpusa_video_link') ){
      $video.hide();
      $video.find('iframe').attr('src', '' );
      $image.find('img').attr('src', thumb.attr('src-full') );
      $image.show();
    } else {
      $image.hide();
      $video.find('iframe').attr('src', thumb.attr('vid-url') );
      $video.show();
    }
  }

  // Single product images - end

  $('#for_img img').hover(function(){
    $('#for_img').zoom({
      url: $(this).attr('src')
    });
  });

  // This is a mess

  $('#report_where').change(function(){
    let awnser = this.value;
    let $issue = $('#report_issue');
    if( awnser ) $issue.prop('disabled', false);
    $issue.html( fpusa_get_report_options( awnser ) );
  });

  $('#report_issue').change(function(){
    let awnser = this.value;
    if( awnser != 0 ){
       $('#report-submit').prop('disabled', false);
     } else {
       $('#report-submit').prop('disabled', true);
     }
  });

  $('#report-submit').click(function(){
    var data = {
      'action' : 'fpusa_single_product_feedback',
      'url' : window.location.href,
      'where' : $('#report_where').val(),
      'issue' : $('#report_issue').val(),
      'comments' : $('#report-comments').val()
    };

    $.post(ajax_object.ajax_url, data, function( response ){
      $('#report-problem-form').html('<h1 class="mb-5">Thank you!</h1><p>Your feedback helps us improve our website and make shopping better for thousands of customers.</p>');
      $('#report-submit').hide();
      $('#report-done').show();
    });
  });

  $('.fpusa-single-product-right .quantity input.qty').change(function(){
    console.log( this.value );
    $('.cart input.qty').val( this.value );
  });

  // This is a mess

  function fpusa_get_report_options( awnser ){
    let options = [];
    let options_html = '<option value="0">What is the issue?</option>';

    switch( awnser ){
      case 'images':
        options = [
          'Doesnt match product',
          'Offensive images',
          'Shows additional items',
          'Is not clear',
          'Other'
        ];
        break;
      case 'name':
        options = [
          'Differant from product',
          'Missing key information',
          'Unimportant information',
          'Incorrect information',
          'Other'
        ];
        break;
      case 'bullet_points':
        options = [
          'Differant from product',
          'Missing key information',
          'Unimportant information',
          'Other'
        ];
        break;
      case 'other':
        options = [
          'Price issue',
          'Missing information',
          'Shipping/Availability Issue',
          'Size chart issue',
          'Conflicting information',
          'Product quality issue',
          'Incorrect information',
          'Other'
        ];
        break;
    }

    for( i = 0; i < options.length; i++ ){
      options_html += '<option value="'+ options[i] +'">'+ options[i] +'</option>'
    }
    return options_html;
  }

  function fpusa_find_attributes_without_value( options ){
    options.each(function(){
      if( ! this.value.length ){
        $('#single-what-to-select').text( ucfirst( this.id.substring(3) ) );
      }
    });
  }

});
