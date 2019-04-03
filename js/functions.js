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

});
