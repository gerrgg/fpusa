jQuery(document).ready(function($){

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

    //present data


  });



  $('.slick').slick({
    dots: true,
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



});
