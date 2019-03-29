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
        this.$checkout_form.on( 'click', 'a.set_as_default', this.update_address );

        //payment methods
        this.$checkout_form.on( 'click', 'input[name="payment_method"]', this.payment_changed );
        this.$checkout_form.on( 'click', 'input[name="wc-simplify_commerce-payment-token"]', this.payment_changed );
        this.$checkout_form.on( 'click', 'button.use-payment-method', this.submit_payment );
      },

      init_checkout: function(){
        // first get prefs!
        this.get_user_prefs();
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

      get_user_prefs: function(){
        $.post( ajax_object.ajax_url, { action: 'fpusa_get_user_order_prefs' }, function( response ){
          console.log( response );
          wc_checkout_form.steps.use_address = response[0];
          wc_checkout_form.steps.use_payment = response[1];
          //2nd trigger
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

      copy_billing_address: function( $preview ){
        $.post(ajax_object.ajax_url, {action: 'fpusa_get_billing_info', id: wc_checkout_form.steps.use_payment }, function( response ){
          wc_checkout_form.copy_to( response, 'billing' );
          $preview.html( wc_checkout_form.get_payment_preview() );
        });

      },

      copy_shipping_address: function( $preview ){
        selection = wc_checkout_form.steps.use_address;
        let data = {
          action: 'fpusa_checkout_address',
          id: selection
        };

        $.post(ajax_object.ajax_url, data, function( response ){
          if( response ){
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

            // copies to fields
            wc_checkout_form.copy_to( fields, 'shipping' );
            $('#order_comments').text( response.notes );

            $preview.html( wc_checkout_form.get_address_preview() );
            wc_checkout_form.get_time_in_transit();
          }

        });

      },

      copy_to: function( obj, where = 'both' ){
        // console.log( obj, where );

        if( where == 'both' ){
          for( var i = 0; i < 2; i ++ ){
            let prefix = ( i == 0 ) ? 'shipping' : 'billing';
            Object.keys(obj).forEach(function (key) {
              $('#' + prefix + '_' + key).val( obj[key] );
            });
          }

        } else {
          Object.keys(obj).forEach(function (key) {
            $('#' + where + '_'+ key).val( obj[key] );
          });
        }
      },

      get_payment_preview: function( ){
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

      get_address_preview: function( ){
        let data = {
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

        if( data && data.length ){
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
      },

      submit_payment: function(){
        wc_checkout_form.steps.use_payment = wc_checkout_form.which_payment_method();
        // wc_checkout_form.copy_billing_address();
        $(document.body).trigger('update_steps');
      },

      which_payment_method: function(){
        return ( wc_checkout_form.get_payment_method() == 'mes_cc' ) ? wc_checkout_form.get_saved_card() : wc_checkout_form.get_payment_method();
      },

      update_address: function( e ){
        let address_id = $( e.target ).parent().parent().prev().val();
        $.post(ajax_object.ajax_url, { action: 'fpusa_make_address_default', id: address_id }, function( data ){
          window.location.reload();
        });
      },

      update_steps: function(){
        // start at step 1
        let do_step = 1;

        // we need the keys because we HAVE to use a for loop to break out of.
        // https://stackoverflow.com/questions/39882842/how-to-break-out-from-foreach-loop-in-javascript
        let keys = Object.keys( wc_checkout_form.steps );

        for( let i = 0; i < keys.length; i++ ){
          if( wc_checkout_form.steps[keys[i]] ){
            var $preview = $('#preview-' + do_step);

            if( do_step == 1 ){
              wc_checkout_form.copy_shipping_address( $preview );
            } else {
              wc_checkout_form.copy_billing_address( $preview );
            }

            do_step++;
          } else {
            break;
          }
        }

        wc_checkout_form.open( do_step );
      },
    }

    wc_checkout_form.init();
  });
