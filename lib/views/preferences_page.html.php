<div class="wrap">
  <?php echo NewsAtMe_Views::navigation(array('page' => 'preferences')); ?>

  <div class="wrap-form-options">
    <form method="post" action="options.php">
      <div>
        <h2>
          <?php _e('Select post types', 'wpnewsatme'); ?>
        </h2>
        <p>
          <?php _e('Enable News@me to work with any preferred type of content.', 'wpnewsatme'); ?>
        </p>

        <ul>
        <?php foreach($post_types as $pt) { ?> 
          <?php $name = $pt->name; ?>
          <?php $label = $pt->labels->name; ?> 
          <li>
          <input type="hidden" name="newsatme-post-types[<?php echo $name; ?>]" value="0" />
          <input type="checkbox" name="newsatme-post-types[<?php echo $name; ?>]" value="1" <?php echo (wpNewsAtMe::postTypeEnabled($name) ? 'checked=\"checked\"' : '') ?>" id="<?php echo $name; ?>" />
          <label for="<?php echo $pt->name ?>"><?php echo $label; ?></label>
          </li>
        <?php } ?>
        </ul>
        <?php settings_fields(NEWSATME_POST_TYPES_OPTION_GROUP); ?>
      </div>
      <p class="submit">
          <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save') ?>" />
      </p>
      </form>

      <form method="post" action="options.php">
      <div>
        <h2>
          <?php _e('Adding topics to posts', 'wpnewsatme'); ?>
        </h2>
        <p>
          <?php _e('Enable News@me to add tags or categories as topics to your posts. You can add or remove topics at any time.<br><i>By disabling this preference, News@me will work only on those posts which topics have been added to.</i>', 'wpnewsatme'); ?>
        </p>
        <ul>
          <li>
          <input type="hidden" name="newsatme-auto-mode" value="0" />
          <input type="checkbox" name="newsatme-auto-mode" value="1" <?php echo (wpNewsAtMe::autoModeEnbled() ? 'checked=\"checked\"' : '') ?>" id="<?php echo 'auto-mode'; ?>" />
          <label for="<?php echo 'auto-mode' ?>"><?php _e('Add tags or categories as topics', 'wpnewsatme'); ?></label>
          </li>
        </ul>
        <?php settings_fields(NEWSATME_VISIBILITY_OPTION_GROUP); ?>
      </div>

        <p class="submit">
          <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save') ?>" />
        </p>
      </form> 
  </div>
</div>



