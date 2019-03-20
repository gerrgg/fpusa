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

$()


$('.comment')

  .on('click', '.comment-helpful-btn', function(){
    let $btn = $(this);
    let comment_id = $(this).parent().parent().attr('id');
    let $badge = $(this).find('.badge');
    let increment = ( $(this).hasClass( 'btn-success' ) ) ? 1 : -1;
    let switch_class = ( $(this).hasClass( 'btn-success' ) ) ? 'danger' : 'success';
    let btn_txt = ( $(this).hasClass( 'btn-success' ) ) ? 'Not helpful' : 'Helpful';
    let data = {
      action: 'fpusa_comment_helpful',
      id: comment_id,
      increment: increment,
    }
    $.post( ajax_object.ajax_url, data, function( response ){
      console.log( response );
      $btn.removeClass( 'btn-danger btn-success' ).addClass( 'btn-' + switch_class );
      $badge.removeClass( 'badge-danger badge-success' ).addClass( 'badge-' + switch_class );
      $btn.find('span.btn-text').text( btn_txt );
      if( response.length ) $badge.text( response );
    });
  })

  .on('click', '.reply-karma i', function(){
    let comment = $(this).parent().parent().parent();
    let $parent = $(this).parent();
    let increment = $(this).attr('data-increment');
    let btn_pressed = $(this);
    let karma_score = $('#' + comment.attr('id') + '_comment_karma' );
    let data = {
      action: 'fpusa_comment_helpful',
      id: comment.attr('id'),
      increment: increment
    }

    $.post(ajax_object.ajax_url, data, function( response ){
      if( response.length ) karma_score.text( response );
      $parent.find('i').removeClass('karma-highlight');
      btn_pressed.addClass('karma-highlight');

    });
  })

  .on('click', '.comment-on-comment', function(){
    $comment = $(this).parent().parent();
    textarea_id = 'comment_on_' + $comment.attr('id');
    if( ! $( '#' + textarea_id ).length ) {
      $comment.append(
        $( '<div/>', { id: textarea_id, class: 'mt-3 coc-wrapper' } ).append(
          '<textarea class="form-control my-2"></textarea>',
          '<button type="button" class="btn btn-info" style="float: right">Submit</button>',
          '<span class="word-counter text-mute text-small"></span>'
        ));
    } else {
      $( '#' + textarea_id ).remove();
    }

    set_submit_comment_event( textarea_id, $comment.attr('id') );
  })

  .on( 'click', '.show-comments-thread', function(){
    let $thread = $(this).next();
    ( $thread.is( ':visible' ) ) ? $thread.hide() : $thread.show();
  });

  function set_submit_comment_event( textarea_id, comment_id ){
    textarea = $('#' + textarea_id).find('textarea');

    // $(textarea).keyup(function(){
    //   let count = textarea.val().length;
    //   let word_count = textarea.next().next();
    //   if( count < 15 ){
    //     word_count.text( 15 - count + ' more characters to go!' );
    //   } else {
    //     word_count.text( 'Your comment is long enough to post.' );
    //   }
    // });

    $('#' + textarea_id + ' button').click(function(){

      let data = {
        action: 'fpusa_comment_on_comment',
        parent_comment: comment_id,
        comment: textarea.val(),
      }

      $.post(ajax_object.ajax_url, data, function( response ){
        window.location.reload('');
      });
    });
  }

  $.fn.ignore = function(sel){
    return this.clone().find(sel||">*").remove().end();
  };

  $('#sort_comments_by').change(function(){
    let product_id = $(this).attr('data-product-id');
    let sortby = $(this).val();

    // console.log( product_id, sortby, $comments );

    let data = {
      action: 'fpusa_sort_product_reviews',
      p_id: product_id,
      sortby: sortby,
    }

    $.post(ajax_object.ajax_url, data, function( response ){
      console.log( response );
      reorder_comments( response )
    });

  });

  function reorder_comments( data ){
    // get all the comment ids
    let arr = $.map(data, function(comment) {
      return comment.comment_ID
    });

    // get children
    var wrapper = $('#comments-wrapper');
    var items = wrapper.children('div.comment');

    // map the index of items where comment id and item id match
    var newOrder = $.map(arr, function(comment_id) {
      for( var i = 0; i < items.length; i++ ){
        if( comment_id == items[i].id ){
          return $(items).clone(true,true).get(i);
        }
      }
    });

    wrapper.empty().html(newOrder);
  }

  $('div.woocommerce').on('change', 'input.qty', function(){
    $("[name='update_cart']").trigger('click');
  });

  $('form.checkout')
  .on( 'click', '.use-this-address', function(){
    let $selection = copy_to_inputs();
    get_time_in_transit( $selection );
    if( $selection.length > 0 ){
      $('#order-button').html( $('#use-payment').addClass('btn-block') );
      // $('#step-btn-1').attr('href', '#step-1');
      $('#step-2').collapse('toggle');
    }
  })
  .on( 'click', '.use-payment-method', function(){
    let card_num = $('#mes_cc-card-number').val();
    let expire = $('#mes_cc-card-expiry').val().split(' / ');
    let cvc = $('#mes_cc-card-cvc').val();
    if( card_num.length > 0 && valid_credit_card( card_num ) ){
      expire.splice(1, 0, '1');
      future = new Date(expire);
      now = new Date();
      if( future > now && cvc.length > 0 ){
        $('#step-3').collapse('toggle');
        $order_btn = $('#place-order').clone();

        $('#order-button').html( $order_btn );
        $('#order-instructions').html( $('#order-instructions-btm').text() );
      }
    }
  });


  $('input.shipping_method').change(function(){
    $('#selected_option').text( $(this).prev().text() );
  });

  function get_time_in_transit( selection ){
    let data = {
      action: 'get_time_in_transit',
      street: $('#shipping_address_1').val(),
      postal: $('#shipping_postcode').val(),
      country: $('#shipping_country').val(),
    }

    $.post( ajax_object.ajax_url, data, function( response ){
      display_time_in_transit_response( response, selection.val() );
      $('#selected_option').text( $('input.shipping_method:checked').prev().text() );
    } );
  }

  $('#selected_option').text( $('input.shipping_method:checked').prev().text() );

  function display_time_in_transit_response( data, selection ){
    // find what user has selected
    $shipping_methods = $('#shipping_method li > input');
    response = data.TransitResponse.ServiceSummary;

    let services = {
      'GND': 'ups:2:03',
      '3DS': 'ups:2:12',
      '2DA': 'ups:2:02',
      '1DP': 'ups:2:13',
      '1DA': 'ups:2:01'
    }

      // loop through the services array
    Object.keys(services).forEach(function (key) {
        // foreach shipping method
      $shipping_methods.each( function(){
          // check if the key matches the value
        if( this.value == services[key] ){
          // loop through api response
          for( let i = 0; i < response.length; i++ ){
            // compare it to $response
            if( response[i].Service.Code == key ){
              // format and display it to browser!
              let est_arrival = response[i].EstimatedArrival;
              let date = moment( est_arrival.Date ).format( 'dddd, MMMM Do' );
              $(this).prev().html( date );
            }
          }
        }
      });
    });

  }

  function copy_to_inputs(){
    selection = $('#step-1 input[type="radio"]:checked');
    let data = {
      action: 'fpusa_checkout_address',
      id: selection.val()
    };

    $.post(ajax_object.ajax_url, data, function( response ){
      let name = response.ship_to.split( ' ' );

      fields = {
        first_name: name[0],
        last_name: name[1],
        company: response.ship_to,
        country_field: response.country,
        address_1: response.address_1,
        address_2: response.address_2,
        city: response.city,
        state: response.state,
        postcode: response.postal,
        phone: response.phone,
        order_comments: response.notes,
      }

      for( let i = 0; i < 2; i++ ){
        let prefix = ( i == 0 ) ? 'shipping' : 'billing';

        Object.keys(fields).forEach(function (key) {
        	$('#' + prefix + '_' + key).val( fields[key] ).change();
        });
      }

    });

    return selection;
  }

  function valid_credit_card(value) {
  // accept only digits, dashes or spaces
	if (/[^0-9-\s]+/.test(value)) return false;

	// The Luhn Algorithm. It's so pretty.
	var nCheck = 0, nDigit = 0, bEven = false;
	value = value.replace(/\D/g, "");

	for (var n = value.length - 1; n >= 0; n--) {
		var cDigit = value.charAt(n),
			  nDigit = parseInt(cDigit, 10);

		if (bEven) {
			if ((nDigit *= 2) > 9) nDigit -= 9;
		}

		nCheck += nDigit;
		bEven = !bEven;
	}

	return (nCheck % 10) == 0;
}

});
