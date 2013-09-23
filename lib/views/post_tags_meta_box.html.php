<h1>new@me tags</h1>
<input type="hidden" name="postmeta_noncename" id="newsatme_noncename" value="<?php echo $nonce; ?>" /> 
<input type="text" id="newsatme_postmeta_tags_field" name="<?php echo wpNewsAtMe::TAGS_INPUT_NAME; ?>" value="<?php echo $tags; ?>" class="widefat" />

<?php if ($error) { ?>
  <div class="error">
    <p>Impossible to connect to news@me to retrieve tags.</p>
  </div>
<?php } ?>

<?php if ($available_tags) { ?>
<div class="newsatme_tags_sorting">
Sorty by
  <a href="javascript:;" id="newsatme_tags_sort_name">Name</a> |
  <a href="javascript:;" id="newsatme_tags_sort_count">N. of subscribers following the tag</a>
</div>

<ul id="newsatme_tags_list">
  <?php foreach($available_tags as $key => $value) { ?>
  <li class="newsatme_available_tag_row">
    <a href="javascript:void(0);">
    <span class="tag_name"><?php echo $value['tag'] ?></span>
    <span class="subs_count" data-count="<?php echo $value['subs'] ;?>">(<?php echo $value['subs'] ?>)</span>
    </a>
  <li>
  <?php } ?>
</ul>
<?php } ?>
