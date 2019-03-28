jQuery( function ( $ ){
  var fpusa = {
    $modal: $('#fpusa_modal'),
    init: function(){
      // find title and body
      fpusa.$title = this.$modal.find('.modal-title'),
      fpusa.$body = this.$modal.find('.modal-body'),
      fpusa.$form = this.$modal.find( 'form.modal-form' );

      // set event on show
      this.$modal.on('show.bs.modal', '', this.build);
      this.$modal.on( 'click', 'button.modal_ajax_submit', this.submit_form );
    },

    submit_form: function( e ){
      e.preventDefault();

      form_data = $('#modal-table input, #modal-table select, #modal-table textarea ').serialize();

      $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url,
        data: { action: 'fpusa_address_create', form_data },
        success: function( data ){
          console.log( data );
        }
      })
    },

    // get data from button clicked
    build: function( e ){
      let $button = $(e.relatedTarget);

      let data = {
        title: $button.attr('data-title'),
        model: $button.attr('data-model'),
        action: $button.attr('data-action'),
        id: $button.attr('data-id'),
      }

      fpusa.$title.text( data.title );
      // run function based on the data-model and data-action attributes of the button pressed.
      fpusa[ data.model ]( data.action, data.id );
    },

    ['address']: function( action, id = '' ){
      var fields = [
        { label: 'Full name', type: 'text', id: 'address_shipto', value: '' },
        { label: 'Address 1', type: 'text', id: 'address_1', value: '' },
        { label: 'Address 2', type: 'text', id: 'address_2', value: '' },
        { label: 'City', type: 'text', id: 'address_city', value: '' },
        { label: 'State', type: 'text', id: 'address_state', value: '' },
        { label: 'Postal Code', type: 'tel', id: 'address_postal', value: '' },
        { label: 'Country', type: 'tel', id: 'address_country', value: '' },
        { label: 'Phone', type: 'tel', id: 'address_phone', value: '' }
      ];

      if( action != 'create' && id != '' ){
        // console.log( 'get data first' );
        fpusa.get_field_values( fields, 'address', action, id );
      } else {
        // console.log( 'create the form' );
        fpusa.create_form( fields, 'address', action );
      }

    },

    get_field_values: function( fields, model, action, id ){
      // console.log( 'getting_field_values' );

      var $form;

      $.post( ajax_object.ajax_url, { action: `fpusa_get_${model}`, id: id  }, function( data ){
        Object.keys( fields ).forEach( function(fieldKey) {
          Object.keys( data ).forEach( function(dataKey) {
            if( fields[fieldKey].id == dataKey ){
              fields[fieldKey].value = data[dataKey];
            }
          });
        });

        fpusa.create_form( fields, model, action );
      });

    },

    create_form: function( fields, model, action ){
      // console.log( 'create_form', fields );

      var $form = $('<form/>', {
        method: 'POST',
        action: ajax_object.ajax_url,
        class: 'modal-form'
      });

      $form.append( $('<table/>', { id: 'modal-table', class: 'mx-2' } ) );

      for( var i = 0; i < fields.length; i++ ){
        $form.find('table').append( fpusa.build_field( fields[i] ) );
      }

      $form.append( $('<input/>', { type: 'hidden', name: 'action', value: `fpusa_${model}_${action}` } ) );

      fpusa.$body.html( $form );
    },

    build_field: function( field ){
      let row = $('<tr>');
      row.append(`<td><label for="${field.id}">${field.label}</label></td>`);
      row.append( '<td>', $('<input/>', { type: field.type, id: field.id, name: field.id, value: field.value, class: 'form-control' } ), '</td>' );
      return row;
    },

    update_modal: function(){
      console.log( 'update!' );
    }
  }

  fpusa.init();
});
