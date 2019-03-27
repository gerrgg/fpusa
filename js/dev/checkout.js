jQuery( function( $ ) {
    // TODO: so much better than the other ugly way!
    var wc_checkout_form = {
      $checkout_form: $('form.checkout'),
      steps: { 'use_address': '', 'use_payment': '' },
      init: function(){
        $(document.body).bind( 'update_steps', this.update_steps );
        $(document.body).bind( 'init_checkout', this.init_checkout );

        this.init_checkout();

        //UI
        this.$checkout_form.on( 'click', 'input[type="radio"]', this.radio_activate );

        this.$checkout_form.on( 'click', 'input[name="set_user_order_prefs"]', this.update_user_order_prefs );

        //shipping_city
        this.$checkout_form.on( 'change', 'input[name="shipping_method[0]"]', this.update_expected_delivery );
        //address
        this.$checkout_form.on( 'click', 'button.use-this-address', this.submit_address );

        //payment methods
        this.$checkout_form.on( 'click', 'input[name="payment_method"]', this.payment_changed );
        this.$checkout_form.on( 'click', 'input[name="wc-simplify_commerce-payment-token"]', this.payment_changed );
        this.$checkout_form.on( 'click', 'button.use-payment-method', this.submit_payment );
      },

      init_checkout: function(){
        this.get_time_in_transit();
        this.check_user_prefs();
      },

      update_expected_delivery: function(){
        $('#selected_option').text( wc_checkout_form.get_transit_display_time );
      },

      payment_changed: function( e ){
        let name = e.target.name;
        if( name == 'wc-simplify_commerce-payment-token' ){
          $('#payment_method_mes_cc').prop('checked', true);
        } else {
          // payment method
          if( e.target.value != 'mes_cc' ){
            $('input[name="wc-simplify_commerce-payment-token"]').each(function(){
              $(this).prop('checked', false);
            });
          }
        }
      },

      check_user_prefs: function(){
        $.post( ajax_object.ajax_url, { action: 'fpusa_get_user_order_prefs' }, function( response ){
          // console.log(response);
          wc_checkout_form.steps.use_address = response[0];
          wc_checkout_form.steps.use_payment = response[1];
          $(document.body).trigger('update_steps');
        });
      },

      radio_activate: function( e ){
        let name = e.target.name;
        // console.log( e.target.value );
        if( name != 'shipping_method[0]' &&  e.target.value != 'mes_cc' ){
          let input_wrapper = ( name == 'use_address' ) ? $('input[name="'+ name +'"]').parent() : $('ul.wc_payment_methods li');
          input_wrapper.removeClass('active');
          $(e.target).parent().addClass('active');
          $(e.target).prop('checked', true);
        }

      },

      get_address_selected: function(){
        return wc_checkout_form.$checkout_form.find('input[name="use_address"]:checked').val();
      },

      get_saved_card: function(){
        return wc_checkout_form.$checkout_form.find( 'input[name="wc-simplify_commerce-payment-token"]:checked' ).val();
      },

      get_payment_method: function(){
        return wc_checkout_form.$checkout_form.find( 'input[name="payment_method"]:checked' ).val();
      },

      copy_to_inputs: function(){
        // TODO: fix fields!
        selection = wc_checkout_form.steps.use_address;
        console.log( selection );

        let data = {
          action: 'fpusa_checkout_address',
          id: selection
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
              $('#' + prefix + '_' + key).val( fields[key] );
            });
          }

          $('#order_comments').text( response.notes );

          wc_checkout_form.get_preview( 1 );

        });

      },

      get_preview: function( step ){
        let $preview = $('#preview-' + step);
        if( step == 1 ){
          console.log( 'getting address preview' );
          $preview.html( wc_checkout_form.get_address_preview() );
        } else {
          $preview.html( wc_checkout_form.get_payment_preview() );
        }
      },

      get_payment_preview: function(){
        let payment = $('ul.wc_payment_methods li input:checked');
        let preview = $('<div/>');

        payment.each(function(){
          if( $(this).attr('id') != 'payment_method_mes_cc' ){
            preview.append( '<p>' + $(this).next().text() + '</p>' );
            return false;
          }
        });

        preview.append('<a href="#">Billing address</a>: same as shipping.');

        return preview;
      },

      get_address_preview: function(){

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

          console.log( data );

          $address = $('<ul/>', { class: 'list-unstyled m-0 p-0' });
          $address.append( `<li>${data.first} ${data.last}</li>` )
          $address.append( `<li>${data.address_1}</li>` );
          if( data.address_2.length ){
            $address.append( `<li>${data.address_2}</li>` );
          }
          $address.append( `<li>${data.city}, ${data.state}  ${data.zip}</li>` );
          $address.append( `<li>Notes: ${data.notes}</li>` );

          return $address;
      },

      open: function( step ){
        $('#step-' + step).collapse('show');
      },

      update_user_order_prefs: function(){
        let steps = wc_checkout_form.steps;
        if( $(this).prop('checked') && steps.use_address && steps.use_payment ) {
          steps.action = 'fpusa_update_user_order_prefs';
          $.post( ajax_object.ajax_url, wc_checkout_form.steps);
        }
      },

      submit_address: function(){
        wc_checkout_form.steps.use_address = wc_checkout_form.get_address_selected();
        wc_checkout_form.copy_to_inputs();
        $(document.body).trigger('update_steps');
      },

      get_time_in_transit: function(){
        let data = {
          action: 'get_time_in_transit',
          street: $('#shipping_address_1').val(),
          postal: $('#shipping_postcode').val(),
          country: $('#shipping_country').val(),
        }

        $.post( ajax_object.ajax_url, data, function( response ){
          wc_checkout_form.display_time_in_transit_response( response );
          $('#selected_option').text( wc_checkout_form.get_transit_display_time );
        });
      },

      get_transit_display_time: function(){
        return wc_checkout_form.$checkout_form.find('input[name="shipping_method[0]"]:checked').prev().text();
      },

      display_time_in_transit_response: function( data ){
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
      },

      submit_payment: function(){
        $(document.body).trigger('update_steps');
        wc_checkout_form.steps.use_payment = wc_checkout_form.which_payment_method();
      },

      which_payment_method: function(){
        return ( wc_checkout_form.get_payment_method() == 'mes_cc' ) ? wc_checkout_form.get_saved_card() : wc_checkout_form.get_payment_method();
      },

      update_steps: function(){
        console.log( wc_checkout_form.steps );

        wc_checkout_form.get_time_in_transit();

        let do_step = 1;
        Object.keys( wc_checkout_form.steps ).forEach(function (key) {
          if( wc_checkout_form.steps[key] ){
            // wc_checkout_form.get_preview( do_step );
            do_step++;
          }
        });
        wc_checkout_form.open( do_step );
      },
    }

    // $('form.checkout')
    // .on( 'click', '.use-this-address', function(){
    //   let $selection = copy_to_inputs();
    //   let $preview = $('#step-1').prev().find('span.preview');
    //   // get_time_in_transit( $selection );
    //   if( $selection.length > 0 ){
    //     $('#order-button').html( $('#use-payment').addClass('btn-block') );
    //     // $('#step-btn-1').attr('href', '#step-1');
    //     $('#step-2').collapse('toggle');
    //     $preview.html( get_checkout_preview_address() );
    //   }
    // })
    // .on( 'click', '.use-payment-method', function(){
    //   let $preview = $('#step-2').prev().find('span.preview');
    //   let selection = $('input[name="payment_method"]:checked').val();
    //   if( selection.length > 0 ){
    //     $('#use_card').val( selection );
    //     $('#step-3').collapse('toggle');
    //     $order_btn = $('#place-order').clone();
    //     $preview.html( get_payment_preview( selection ) );
    //     $('#order-button').html( $order_btn );
    //   }
    // })
    // .on( 'click', 'button[name="apply_coupon"]', function(){
    //     let coupon_code = $('input[name="coupon_code"]').val();
    //     $('input[name="coupon_code"]').val('');
    //     if( coupon_code.length ){
    //       $.post( ajax_object.ajax_url, { action: 'fpusa_apply_coupon', code: coupon_code }, function( response ){
    //         console.log( response );
    //         if( response == 1 ){
    //           $('#applied_coupons').append( get_coupon_form_html() );
    //         } else {
    //           $('.woocommerce-notices-wrapper').last().html( response );
    //         }
    //       } );
    //     }
    // })
    // .on( 'click', '.coupon-line button.close', function(){
    //   $coupon = $(this).parent();
    //   $.post( ajax_object.ajax_url, { action: 'fpusa_remove_coupon', code: this.id }, function( response ){
    //     if( response ){
    //       $coupon.remove();
    //     }
    //   } );
    // } )
    // .on( 'update_steps', function(){
    //   console.log( 'update_steps' );
    // } );
    //
    // $('div.woocommerce').on('change', 'input.qty', function(){
    //   $("[name='update_cart']").trigger('click');
    // });
    //
    // function get_checkout_preview_address(){
    //   data = {
    //     first: $('#shipping_first_name').val(),
    //     last: $('#shipping_last_name').val(),
    //     address_1: $('#shipping_address_1').val(),
    //     address_2: $('#shipping_address_2').val(),
    //     city: $('#shipping_city').val(),
    //     state: $('#shipping_state').val(),
    //     zip: $('#shipping_postcode').val(),
    //     notes: $('#order_comments').val(),
    //   }
    //
    //   $address = $('<ul/>', { class: 'list-unstyled m-0 p-0' });
    //   $address.append( `<li>${data.first} ${data.last}</li>` )
    //   $address.append( `<li>${data.address_1}</li>` );
    //   if( data.address_2.length ){
    //     $address.append( `<li>${data.address_2}</li>` );
    //   }
    //   $address.append( `<li>${data.city}, ${data.state}  ${data.zip}</li>` );
    //   $address.append( `<li>${data.notes}</li>` );
    //
    //   return $address;
    // }
    //
    // function get_payment_preview( token_id ){
    //   if( token_id != 'paypal' ){
    //     let wrapper = $('<div/>');
    //     wrapper.append( '<p>' + $('#payment_method_' + token_id).parent().find('label').text() + '</p>');
    //     wrapper.append( '<p><a href="#">Billing Address: </a> Same as shipping address</p>');
    //     wrapper.append( get_checkout_coupon_form() );
    //     // console.log( card_text );
    //     return wrapper;
    //   }
    // };
    //
    // function get_checkout_coupon_form(){
    //   let wrapper = $('<div/>', { class: 'form-group' } );
    //   wrapper.append( $('<label>Add a gift card or promotion code</label>') );
    //   wrapper.append( $('<input/>', {  }) )
    // }
    //
    //
    //
    //
    // $('input.shipping_method').change(function(){
    //   $('#selected_option').text( $(this).prev().text() );
    // });
    //
    // function get_coupon_form_html(){
    //   $.post( ajax_object.ajax_url, { action: 'fpusa_get_coupon_html' }, function( response ){
    //     console.log( response );
    //     $('#applied_coupons').html( response );
    //   } );
    // }
    //
    // function get_time_in_transit( selection ){
    //   let data = {
    //     action: 'get_time_in_transit',
    //     street: $('#shipping_address_1').val(),
    //     postal: $('#shipping_postcode').val(),
    //     country: $('#shipping_country').val(),
    //   }
    //
    //   $.post( ajax_object.ajax_url, data, function( response ){
    //     display_time_in_transit_response( response, selection.val() );
    //     $('#selected_option').text( $('input.shipping_method:checked').prev().text() );
    //   } );
    // }
    //
    // $('#selected_option').text( $('input.shipping_method:checked').prev().text() );
    //
    // function display_time_in_transit_response( data, selection ){
    //   // find what user has selected
    //   $shipping_methods = $('#shipping_method li > input');
    //   response = data.TransitResponse.ServiceSummary;
    //
    //   let services = {
    //     'GND': 'ups:2:03',
    //     '3DS': 'ups:2:12',
    //     '2DA': 'ups:2:02',
    //     '1DP': 'ups:2:13',
    //     '1DA': 'ups:2:01'
    //   }
    //
    //     // loop through the services array
    //   Object.keys(services).forEach(function (key) {
    //       // foreach shipping method
    //     $shipping_methods.each( function(){
    //         // check if the key matches the value
    //       if( this.value == services[key] ){
    //         // loop through api response
    //         for( let i = 0; i < response.length; i++ ){
    //           // compare it to $response
    //           if( response[i].Service.Code == key ){
    //             // format and display it to browser!
    //             let est_arrival = response[i].EstimatedArrival;
    //             let date = moment( est_arrival.Date ).format( 'dddd, MMMM Do' );
    //             $(this).prev().html( date );
    //           }
    //         }
    //       }
    //     });
    //   });
    //
    // }
    //

    //
    // $('#payment_method_paypal').show();
    //
    //
    // function valid_credit_card(value) {
    //   // accept only digits, dashes or spaces
    //   if (/[^0-9-\s]+/.test(value)) return false;
    //
    //   // The Luhn Algorithm. It's so pretty.
    //   var nCheck = 0, nDigit = 0, bEven = false;
    //   value = value.replace(/\D/g, "");
    //
    //   for (var n = value.length - 1; n >= 0; n--) {
    //     var cDigit = value.charAt(n),
    //         nDigit = parseInt(cDigit, 10);
    //
    //     if (bEven) {
    //       if ((nDigit *= 2) > 9) nDigit -= 9;
    //     }
    //
    //     nCheck += nDigit;
    //     bEven = !bEven;
    //   }
    //
    //   return (nCheck % 10) == 0;
    // }

    wc_checkout_form.init();
  });
