jQuery(document).ready( function($) {
  newsatme_open_pointer(0);
  function newsatme_open_pointer(i) {
    pointer = newsatmePointer.pointers[i];
    options = $.extend( pointer.options, {
      close: function() {
        $.post( ajaxurl, {
          pointer: pointer.pointer_id,
          action: 'dismiss-wp-pointer'
        }, function() {
          var next = 1 + i ;
          if (newsatmePointer.pointers[next]) {
            newsatme_open_pointer(next);
          }
        });
      }
    });

    $(pointer.target).pointer( options ).pointer('open');
  }
});
