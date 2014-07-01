
<input type="hidden" name="postmeta_noncename" id="newsatme_noncename" value="<?php echo $nonce; ?>" /> 

<?php if ($error) { ?>
  <div class="updated">
    <p><?php _e('News@me is not able to retrieve your topics at the moment. Please try again later.', 'wpnewsatme'); ?></p>
  </div>
  <div class="wrap-topics-stuff">
  <div class="newsatme_metabox_notice_updated">
    <p><?php _e('News@me is not able to retrieve your topics at the moment. Please try again later.', 'wpnewsatme'); ?></p>
  </div>
</div>
<?php } else { ?>
<div class="wrap-topics-stuff">
  <input type="text" id="newsatme_topics_input" class="newtag form-input-tip" autocomplete="off" />
  <input type="button" value="<?php _e('Add', 'wpnewsatme'); ?>" class="button" id="newsatme_topics_add_button" />
  <p class="howto"><?php _e('Separate topics with commas', 'wpnewsatme'); ?></p>
  <input type="text" id="newsatme_postmeta_tags_field" name="<?php echo wpNewsAtMe::TAGS_INPUT_NAME; ?>" value="<?php echo $post->getTopicsString(); ?>" class="widefat" />

  <?php if (!$post->isDisabled() && wpNewsAtMe::autoModeEnbled() && $post->emptyTopics()) { ?>
    <div class="newsatme_metabox_notice_info">
      <div class="topics-state-icon dashicons-info">
      <?php _e('With no topics added, News@me adds tags or categories as topics to your post. You can add or remove topics at any time. ', 'wpnewsatme'); ?>
      </div>
    </div>
  <?php } ?> 

  <?php if ($available_tags) { ?>
  <a href="javascript:void(0);" class="newsatme_toggle_available_topics"><?php _e('Choose from existing topics', 'wpnewsatme'); ?> </a>
  <div class="newsatme_available_topics existing-topics"> 
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
  </div>
</div>
  <?php } ?>
<?php } ?>
  <div id="major-visibility-actions">
    <label> 
    <input type="checkbox" name="<?php echo wpNewsAtMe::DISABLED_POST_NAME ?>" value="1" <?php echo $disabled_checked ?> />
      <?php echo _e('Hide News@me from this post', 'wpnewsatme'); ?>
    </label>
  </div>
