<div class="wrap">
  <?php echo NewsAtMe_Views::navigation(array('page' => 'set_key')); ?>
  <?php if (!$api_key) { ?>
  <h2><?php _e('Activate your account', 'wpnewsatme'); ?></h2>
  <p></p>
  <?php } else { ?>
  <h2><?php _e('Your account is currently active', 'wpnewsatme'); ?></h2>
  <p id="newsatme-plugin-now-active-header"><?php _e('Check out your <a href="https://app.newsatme.com/dashboard?utm_source=wordpress-plugin&utm_medium=link-dashboard-plugin-activated-page&utm_campaign=wordpress-plugin" target="_blank">Dashboard</a> for widget impressions, conversions, subscriptions, audience segmentation and much more!', 'wpnewsatme'); ?></p>
  <?php } ?>
  <div class="wrap-newsatme-form-options">
    <form method="post" action="options.php">
      <div>
        <?php settings_fields(NEWSATME_API_KEY_OPTION_GROUP); ?>

        <span class="label-field"><?php _e('Your News@me API key', 'wpnewsatme'); ?></span>
        <div class="inside">
          <?php wp_nonce_field( 'update_api_key', 'apinonce' ); ?>

          <input id='api_key' name='wpnewsatme[api_key]' size='45' type='text' value="<?php esc_attr_e( $api_key ); ?>" />
          <?php if (!$api_key) { ?>
          <p><i>
            <?php _e('Please enter a valid News@me API key here. If you need an API key, you can <a href="http://newsatme.com/get-api-key-wp" target="_blank">create one here</a>.', 'wpnewsatme'); ?>
          </i></p>
          <?php } ?>
        </div>

      </div>

      <p class="submit">
      <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save') ?>" />

      </p>
    </form> 
  </div>

</div>



