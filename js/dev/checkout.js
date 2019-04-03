jQuery( function( $ ) {
    // TODO: so much better than the other ugly way!
    var wc_checkout_form = {
      $checkout_form: $('form.checkout'),
      steps: { 'use_address': '', 'use_payment': '' },
      init: function(){
        $(document.body).bind( 'update_steps', this.update_steps );
        $(document.body).bind( 'init_checkout', this.init_checkout );
        // $(document.body).bind( 'update_checkout', this.update_checkout );

        this.init_checkout();

        //UI
        this.$checkout_form.on( 'click', 'input[type="radio"]', this.radio_activate );

        this.$checkout_form.on( 'click', 'input[name="set_user_order_prefs"]', this.update_user_order_prefs );

        //shipping_city
        this.$checkout_form.on( 'change', 'input[name="shipping_method[0]"]', this.get_time_in_transit );

        //coupon
        // this.$checkout_form.on( 'click', 'input[name="apply_coupon"]', function(){ $(document.body).trigger('update_checkout') } );
        this.$checkout_form.on( 'click', '.woocommerce-remove-coupon', this.get_time_in_transit );

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

          if( response ){
            wc_checkout_form.steps.use_address = response['use_address'];
            wc_checkout_form.steps.use_payment = response['use_payment'];
          }

          //2nd trigger
          $(document.body).trigger('update_steps');
        });
      },

      update_steps: function(){
        // console.log( wc_checkout_form.steps );

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

      copy_billing_address: function( $preview ){
        $.post(ajax_object.ajax_url, {action: 'fpusa_get_billing_address', id: wc_checkout_form.steps.use_payment }, function( response ){
          wc_checkout_form.copy_to( response, 'billing' );
          $preview.html( wc_checkout_form.get_payment_preview( response ) );
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
            $(document.body).trigger('update_checkout');

          }

        });

      },

      update_totals: function(){
        var $cart_totals = $('#cart-totals');
        $cart_totals.html( wc_checkout_form.spinner );
        $.post( ajax_object.ajax_url, {action: 'fpusa_get_cart_totals'}, function( data ){
          // console.log( 'hi', data );
          if( data ){
            $cart_totals.html( data );
            let new_total = $('tr.fpusa-order-total > td').text();
            $('div.fpusa-order-total').text( 'Order Total: ' + new_total );
          }
        });
      },

      spinner: function(){
        return `<div class="d-flex justify-content-center">
                  <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </div>`;
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

      get_payment_preview: function( data ){

        let payment = $('ul.wc_payment_methods li input:checked');
        let preview = $('<div/>');

        payment.each(function(){
          if( $(this).attr('id') != 'payment_method_mes_cc' ){
            preview.append( '<p>' + $(this).next().text() + '</p>' );
            return false;
          }
        });

        preview.append(
          wc_checkout_form.get_billing_link( data ),
          // wc_checkout_form.get_coupon_link(),
          // wc_checkout_form.get_inline_coupon_form()
        );

        return preview;
      },

      get_coupon_link: function(){
        $wrapper = $('</p>');
        $wrapper.append( '<a href="#step-2" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="step-2">Click to apply coupon.</a>' );
        return $wrapper;
      },

      get_inline_coupon_form: function(){
        $form = $( '<form/>', { class: 'checkout_coupon woocommerce-form-coupon d-flex align-items-center', method: 'POST' } );
        $form.append(
          $('<input/>', {
            type: 'text',
            name: 'coupon_code',
            class: 'input-text mr-3',
            placeholder: 'Enter promo code'
          }),
          $('<button/>', { type: 'submit', class: 'btn btn-outline-secondary btn-sm', name: 'apply_coupon' }).text('Apply')
        );
        return $form;
      },

      get_billing_link: function( data ){
        var $wrapper = $('<p/>', { class: 'd-flex' });
        var $btn = $('<a/>', {
          href: '#fpusa_modal',
          'data-toggle': 'modal',
          'data-title': 'Edit your billing address',
          'data-model': 'billing_address',
          'data-action': 'edit',
          'data-id': wc_checkout_form.get_saved_card,
          class: 'pr-2',
        }).text( 'Billing address: ' );

        $btn_txt = wc_checkout_form.get_billing_address( data );
        $wrapper.append( $btn, $btn_txt );

        return $wrapper;
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

          return wc_checkout_form.format_address( data );
      },

      get_billing_address: function( response ){
        // console.log( response );
        let data = {
            first: response.billing_first_name,
            last: response.billing_last_name,
            address_1: response.billing_address_1,
            address_2: response.billing_address_2,
            city: response.billing_city,
            state: response.billing_state,
            zip: response.billing_postcode,
          }

          return wc_checkout_form.format_address( data );
      },

      format_address: function( data ){
        $address = $('<ul/>', { class: 'list-unstyled m-0 p-0' });
        $address.append( `<li>${data.first} ${data.last}</li>` )
        $address.append( `<li>${data.address_1}</li>` );

        if( data.address_2 ){
          $address.append( `<li>${data.address_2}</li>` );
        }

        $address.append( `<li>${data.city}, ${data.state}  ${data.zip}</li>` );

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
          // console.log( response );
          wc_checkout_form.display_time_in_transit_response( response );
          $('#selected_option').text( wc_checkout_form.get_transit_display_time );
          wc_checkout_form.update_totals();
        });
      },

      get_transit_display_time: function(){
        return wc_checkout_form.$checkout_form.find('input[name="shipping_method[0]"]:checked').prev().text();
      },

      display_time_in_transit_response: function( data ){
        // find what user has selected
        $shipping_methods = $('#shipping_method li > input');

        if( data.TransitResponse.ServiceSummary ){
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
                    // console.log( response[i].Service.Description, est_arrival, date );
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


    }

    wc_checkout_form.init();
  });
