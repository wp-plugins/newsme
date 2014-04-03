(function($) {
  $(document).on('click', '#newsatme_tags_sort_count', function(ev) {
    var new_list = _.sortBy($('#newsatme_tags_list li'), function(el) {
      return (- parseInt($(el).find('.subs_count').data('count')));
    });
    $('#newsatme_tags_list').html(new_list);
    $(ev.target).addClass('selected');
    return false;
  });

  $(document).on('click', '#newsatme_tags_sort_name', function(ev) {
    var new_list = _.sortBy($('#newsatme_tags_list li'), function(el) {
      return $(el).find('.tag_name').text().trim();
    });

    $('#newsatme_tags_list').html(new_list);

    $(ev.target).addClass('selected');
    return false;
  });

  $(function() {
    var $tagit = $('#newsatme_postmeta_tags_field');

    var available_tags = new Array();

    $('.newsatme_available_tag_row').each(function(index, item) {
      var tag = $(item).find('.tag_name').text();
      available_tags.push(tag);
    });

    $tagit.tagit({"availableTags": available_tags, removeConfirmation: true, allowSpaces: true});

    $(document).on('click', '.newsatme_available_tag_row', function(ev) {
      var $target = $(ev.target);
      var tag = $target.closest('a').find('.tag_name').text();
      $tagit.tagit("createTag", tag);
    });

    $('#newsatme_tags_sort_name').trigger('click');

  });
})(jQuery);
