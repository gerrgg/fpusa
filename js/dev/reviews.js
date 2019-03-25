jQuery(document).ready(function( $ ){

  $('.create-review-star-rating i.click-star').click(function(){
    $('i.click-star').removeClass('fas').addClass('far');

    let rating = $(this).attr('data-rating');
    for( var i = 1; i <= rating; i++ ){
      $('#star-' + i).addClass('fas');
    }

    $('input#product-rating').val( rating );
  });


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

      // map the index of items where comment id and item id matcha
      var newOrder = $.map(arr, function(comment_id) {
        for( var i = 0; i < items.length; i++ ){
          if( comment_id == items[i].id ){
            return $(items).clone(true,true).get(i);
          }
        }
      });

      wrapper.empty().html(newOrder);
    }


});
