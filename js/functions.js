jQuery(document).ready(function($){
  //userUpload
  Dropzone.autoDiscover = false;

  $("#media-uploader").dropzone({
    url: ajax_object.dropParam,
    paramName: 'file'
  });


  $('#cat-btn').click(function(){
    $('#hm-menu').css('width', '100%');
  });

  $('.closebtn').click(function(){
    $('#hm-menu').css('width', '0px');
  });

  $('#fpusa_buy_again').on('show.bs.modal', function (event) {
    // get id from button
    let button = $(event.relatedTarget) ;// Button that triggered the modal
    let p_id = button.data('id'); // Extract info from data-* attributes
    let form = $('#fpusa_ba_form');

    //get data from wc
    $.ajax({
      type: 'POST',
      url: ajax_object.ajax_url,
      data: {
        'p_id': p_id,
        'action': 'fpusa_get_buy_again_data'
      },
      success: function(data){
        $('#fpusa_ba_img_link').attr('href', data.link);
        $('#fpusa_ba_img_link').html(data.image);

        $('#fpusa_ba_title').attr('href', data.link);
        $('#fpusa_ba_title').html(data.name);

        $('#fpusa_ba_price').html(' ' + data.price);

        $('#fpusa_ba_stock').html(data.stock);

        form.attr('action', data.link);
        form.find('button').val(p_id);
      }
    });

  });



  $('.slick').slick({
     dots: true,
     arrows: true,
     infinite: false,
     speed: 300,
     slidesToShow: 4,
     slidesToScroll: 4,
     responsive: [
       {
         breakpoint: 1024,
         settings: {
           slidesToShow: 3,
           slidesToScroll: 3,
           infinite: true,
           dots: true
         }
       },
       {
         breakpoint: 600,
         settings: {
           slidesToShow: 2,
           slidesToScroll: 2
         }
       },
       {
         breakpoint: 480,
         settings: {
           slidesToShow: 1,
           slidesToScroll: 1
         }
       }
       // You can unslick at a given breakpoint now by adding:
       // settings: "unslick"
       // instead of a settings object
     ]
  });

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


  // function fpusa_change_stock( data ){
  //   console.log(data);
  //   let $stock = $('p.stock');
  //   let availability = data.availability;
  //   let stock_class = data.class
  //   if( ! $stock.hasClass( stock_class ) ){
  //     // remove the 3 possible classes if $stock doesnt have the new class.
  //     $stock.removeClass('in-stock', 'out-of-stock', 'available-on-backorder');
  //     $stock.addClass('stock ' + stock_class);
  //   }
  //   $stock.text(availability);
  // }


  function notZero(value){
    return value != 0;
  }

  // Credit David Walsh (https://davidwalsh.name/javascript-debounce-function)

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.
function debounce(func, wait, immediate) {
  var timeout;

  return function executedFunction() {
    var context = this;
    var args = arguments;

    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };

    var callNow = immediate && !timeout;

    clearTimeout(timeout);

    timeout = setTimeout(later, wait);

    if (callNow) func.apply(context, args);
  };
};

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function fpusa_find_attributes_without_value( options ){
  options.each(function(){
    if( ! this.value.length ){
      $('#single-what-to-select').text( ucfirst( this.id.substring(3) ) );
    }
  });
}


$('.create-review-star-rating i.click-star').click(function(){
  $('i.click-star').removeClass('fas').addClass('far');

  let rating = $(this).attr('data-rating');
  for( var i = 1; i <= rating; i++ ){
    $('#star-' + i).addClass('fas');
  }

  $('input#product-rating').val( rating );
});


$('.comment-helpful-btn').click(function(){
  let comment_id = $(this).attr('data-comment-id');
  let data = {
    action: 'fpusa_comment_helpful',
    id: comment_id
  }
  $.post( ajax_object.ajax_url, data, function( response ){
    console.log( response );
  });
});


});
