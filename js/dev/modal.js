jQuery( function ( $ ){
  var fpusa = {
    $modal: $('#fpusa_modal'),

    ['address']: function( action ){

      var $form = $('<form/>', {
        method: 'POST',
        action: ajax_object.ajax_url,
        class: ''
      });

      $form.append( $('<table/>'), { class: 'mx-2' } );

      var fields = [
        { label: 'Full name', type: 'text', id: 'ship_to', value: '' },
        { label: 'Address 1', type: 'text', id: 'address_1', value: '' },
        { label: 'Address 2', type: 'text', id: 'address_2', value: '' },
        { label: 'City', type: 'text', id: 'city', value: '' },
        { label: 'State', type: 'text', id: 'state', value: '' },
        { label: 'Postal Code', type: 'tel', id: 'postal', value: '' },
        { label: 'Phone', type: 'tel', id: 'phone', value: '' }
      ];

      for( var i = 0; i < fields.length; i++ ){
        $form.find('table').append( fpusa.build_field( fields[i] ) );
      }

      
      return $form
    },

    build_field: function( field ){
      let row = $('<tr>');
      row.append(`<td><label for="${field.id}">${field.label}</label></td>`);
      row.append( '<td>', $('<input/>', { type: field.type, id: field.id, name: field.id, value: field.value, class: 'form-control' } ), '</td>' );
      return row;
    },

    init: function(){
      fpusa.$title = this.$modal.find('.modal-title'),
      fpusa.$body = this.$modal.find('.modal-body'),

      this.$modal.on('show.bs.modal', function( e ){
        let $button = $(e.relatedTarget);

        let data = {
          title: $button.attr('data-title'),
          model: $button.attr('data-model'),
          action: $button.attr('data-action'),
        }

        fpusa.$title.text( data.title );
        fpusa.$body.html( fpusa[data.model](data.action));
      });
    },

    update_modal: function(){
      console.log( 'update!' );
    }
  }

  fpusa.init();
});
