<div class="wrap">
  <?php echo NewsAtMe_Views::navigation(array('page' => 'preferences')); ?>

  <div class="wrap-newsatme-intro-section">
    <h1>
      <?php _e('Segment your audience using "News@me topics".', 'wpnewsatme') ?></h1>
    <h2>          
      <?php _e('Add topics to your posts and allow your readers to subscribe to any of them. <br>News@me will send to each subscriber highly-targeted newsletter digests of your latest posts based on their interests. Clever!', 'wpnewsatme'); ?>
  </h2></div>
<br>
<hr>
  <div class="wrap-newsatme-form-options">
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
          <?php _e('Enable News@me to add tags or categories as topics to your posts. You can add or remove topics at any time.<br><i>By disabling this preference, the <a href="http://newsatme.com/subscription-invitation/?utm_source=wordpress-plugin&utm_medium=link-preferences-signupform&utm_campaign=wordpress-plugin" target="_blank">email signup form</a> will only show up on those posts which topics have been added to.</i>', 'wpnewsatme'); ?>
        </p>
        <ul>
          <li>
          <input type="hidden" name="newsatme-auto-mode" value="0" />
          <input type="checkbox" name="newsatme-auto-mode" value="1" <?php echo (wpNewsAtMe::autoModeEnbled() ? 'checked=\"checked\"' : '') ?>" id="auto-mode" />
          <label for="auto-mode"><?php _e('<b>Auto mode</b>: add your tags or categories as topics', 'wpnewsatme'); ?>
            <br>
            <i class="marginl-poke-fourtimes"><?php _e('Add your tags as topics. Add your categories when no tags have been added to the post.', 'wpnewsatme'); ?></i></label>
          </li>

          <li>
          <input type="hidden" name="newsatme-use-categories" value="0" />
          <input type="checkbox" name="newsatme-use-categories" value="1" <?php echo (wpNewsAtMe::useCategories() ? 'checked=\"checked\"' : '') ?>" id="use-categories" />
          <label for="use-categories"><?php _e('<b>Categories</b>: add your categories as topics', 'wpnewsatme'); ?>
            <br>
            <i class="marginl-poke-fourtimes"><?php _e('Add the categories of the post as topics.', 'wpnewsatme'); ?></i></label>
          </label>
          </li>

          <li>
          <input type="hidden" name="newsatme-use-tags" value="0" />
          <input type="checkbox" name="newsatme-use-tags" value="1" <?php echo (wpNewsAtMe::useTags() ? 'checked=\"checked\"' : '') ?>" id="use-tags" />
          <label for="use-tags"><?php _e('<b>Tags</b>: add your tags as topics', 'wpnewsatme'); ?>
            <br>
            <i class="marginl-poke-fourtimes"><?php _e('Add the tags of the post as topics.', 'wpnewsatme'); ?></i></label>
          </label>
          </li>

          <script type="text/javascript">
            jQuery(function() {
              var $ = jQuery, elements = $('#use-tags, #use-categories, #auto-mode'); 
              elements.each(function(index, el) {
                
                $(el).on('change', function(ell) {
                  if ($(this).is(':checked')) {
                    var change_id = $(this).attr('id'); 
                    $(elements).closest(':checked').each(function(index, xor) {
                      if (change_id != $(this).attr('id')) {
                        $(this).prop('checked', false); 
                      }
                    }); 
                  }
                }); 
              }); 
            }); 
          </script>
        </ul>
        <?php settings_fields(NEWSATME_VISIBILITY_OPTION_GROUP); ?>
      </div>

        <p class="submit">
          <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save') ?>" />
        </p>
      </form> 
  </div>
</div>



