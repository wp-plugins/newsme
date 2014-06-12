<div class="wrap">

  <?php echo NewsAtMe_Views::navigation(array('page' => 'preferences')); ?>

  <div class="wrap-form-options">
    <form method="post" action="options.php">
      <div>
        <h2>
          Select post types
        </h2>
        <p>
          Enable News@me to work with any preferred type of content.
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
        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
      </p>
    </form> 
  </div>

</div>



