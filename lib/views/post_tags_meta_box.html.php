<input type="hidden" name="postmeta_noncename" id="newsatme_noncename" value="<?php echo $nonce; ?>" /> 
<input type="text" id="newsatme_postmeta_tags_field" name="<?php echo wpNewsAtMe::TAGS_INPUT_NAME; ?>" value="<?php echo $tags; ?>" class="widefat" />

<?php if ($error) { ?>
  <div class="updated">
    <p><?php _e('News@me is not able to retrieve your topics at the moment. Please try again later.', 'wpnewsatme'); ?></p>
  </div>
<?php } ?>

<?php if ($available_tags) { ?>
<div class="newsatme_tags_sorting">
<?php _e('Sort by:', 'wpnewsatme'); ?>
  <a href="javascript:;" id="newsatme_tags_sort_name"><?php _e('A - Z', 'wpnewsatme'); ?></a> |
  <a href="javascript:;" id="newsatme_tags_sort_count"><?php _e('Subscribers', 'wpnewsatme'); ?></a>
</div>

<div class="wrap_tag_list">
  <ul id="newsatme_tags_list">
  <?php foreach($available_tags as $key => $value) { ?>
    <li class="newsatme_available_tag_row test">
      <a href="javascript:void(0);">
      <span class="tag_name"><?php echo htmlspecialchars($value['tag']) ?></span>
      <span class="subs_count" data-count="<?php echo $value['subs'] ;?>">(<?php echo $value['subs'] ?>)</span>
      </a>
    </li>
  <?php } ?>
  </ul>
</div>
<?php } ?>
<br class="clearfix">