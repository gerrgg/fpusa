jQuery( function( $ ) {

    $('form.checkout')
    .on( 'click', '.use-this-address', function(){
      let $selection = copy_to_inputs();
      let $preview = $('#step-1').prev().find('span.preview');
      get_time_in_transit( $selection );
      if( $selection.length > 0 ){
        $('#order-button').html( $('#use-payment').addClass('btn-block') );
        // $('#step-btn-1').attr('href', '#step-1');
        $('#step-2').collapse('toggle');
        $preview.html( get_checkout_preview_address() );
      }
    })
    .on( 'click', '.use-payment-method', function(){
      let $preview = $('#step-2').prev().find('span.preview');
      let selection = $('input[name="payment_method"]:checked').val();
      if( selection.length > 0 ){
        $('#use_card').val( selection );
        $('#step-3').collapse('toggle');
        $order_btn = $('#place-order').clone();
        $preview.html( get_payment_preview( selection ) );
        $('#order-button').html( $order_btn );
      }
    })
    .on( 'click', 'button[name="apply_coupon"]', function(){
        let coupon_code = $('input[name="coupon_code"]').val();
        $('input[name="coupon_code"]').val('');
        if( coupon_code.length ){
          $.post( ajax_object.ajax_url, { action: 'fpusa_apply_coupon', code: coupon_code }, function( response ){
            console.log( response );
            if( response == 1 ){
              $('#applied_coupons').append( get_coupon_form_html() );
            } else {
              $('.woocommerce-notices-wrapper').last().html( response );
            }
          } );
        }
    })
    .on( 'click', '.coupon-line button.close', function(){
      $coupon = $(this).parent();
      $.post( ajax_object.ajax_url, { action: 'fpusa_remove_coupon', code: this.id }, function( response ){
        if( response ){
          $coupon.remove();
        }
      } );
    } )
    .on( 'update_checkout', function(){
      console.log( 'update_checkout' );
    } );

    $('div.woocommerce').on('change', 'input.qty', function(){
      $("[name='update_cart']").trigger('click');
    });

    function get_checkout_preview_address(){
      data = {
        first: $('#shipping_first_name').val(),
        last: $('#shipping_last_name').val(),
        address_1: $('#shipping_address_1').val(),
        address_2: $('#shipping_address_2').val(),
        city: $('#shipping_city').val(),
        state: $('#shipping_state').val(),
        zip: $('#shipping_postcode').val(),
        notes: $('#order_comments').val(),
      }

      $address = $('<ul/>', { class: 'list-unstyled m-0 p-0' });
      $address.append( `<li>${data.first} ${data.last}</li>` )
      $address.append( `<li>${data.address_1}</li>` );
      if( data.address_2.length ){
        $address.append( `<li>${data.address_2}</li>` );
      }
      $address.append( `<li>${data.city}, ${data.state}  ${data.zip}</li>` );
      $address.append( `<li>${data.notes}</li>` );

      return $address;
    }

    function get_payment_preview( token_id ){
      if( token_id != 'paypal' ){
        let wrapper = $('<div/>');
        wrapper.append( '<p>' + $('#payment_method_' + token_id).parent().find('label').text() + '</p>');
        wrapper.append( '<p><a href="#">Billing Address: </a> Same as shipping address</p>');
        wrapper.append( get_checkout_coupon_form() );
        // console.log( card_text );
        return wrapper;
      }
    };

    function get_checkout_coupon_form(){
      let wrapper = $('<div/>', { class: 'form-group' } );
      wrapper.append( $('<label>Add a gift card or promotion code</label>') );
      wrapper.append( $('<input/>', {  }) )
    }




    $('input.shipping_method').change(function(){
      $('#selected_option').text( $(this).prev().text() );
    });

    function get_coupon_form_html(){
      $.post( ajax_object.ajax_url, { action: 'fpusa_get_coupon_html' }, function( response ){
        console.log( response );
        $('#applied_coupons').html( response );
      } );
    }

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
        }

        for( let i = 0; i < 2; i++ ){
          let prefix = ( i == 0 ) ? 'shipping' : 'billing';

          Object.keys(fields).forEach(function (key) {
            $('#' + prefix + '_' + key).val( fields[key] ).change();
          });
        }

        $('#order_comments').text( response.notes );

      });

      return selection;
    }

    $('#payment_method_paypal').show();


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
