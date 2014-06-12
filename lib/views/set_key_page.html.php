<div class="wrap">
  <?php echo NewsAtMe_Views::navigation(array('page' => 'set_key')); ?>
  <?php if (!$api_key) { ?>
  <h2>Activate your plugin</h2>
  <p></p>
  <?php } else { ?>
  <h2 id="">Plugin is currently active</h2>
  <p id="plugin-now-active-header">Check out your <a href="https://app.newsatme.com/dashboard?utm_source=wordpress-plugin&utm_medium=link-dashboard-plugin-activated-page&utm_campaign=wordpress-plugin" target="_blank">Dashboard</a> for widget impressions, conversions, subscriptions, audience segmentation and much more!</p>
  <?php } ?>
  <div class="wrap-form-options">
    <form method="post" action="options.php">
      <div>
        <?php settings_fields(NEWSATME_API_KEY_OPTION_GROUP); ?>

        <span class="label-field">Your News@me API Key</span>
        <div class="inside">
          <?php wp_nonce_field( 'update_api_key', 'apinonce' ); ?>
          <?php if ($site_id) { ?>
          <input id="site_id" name="wpnewsatme[site_id]" type="hidden" />
          <?php } ?> 

          <input id='api_key' name='wpnewsatme[api_key]' size='45' type='text' value="<?php esc_attr_e( $api_key ); ?>" />
          <?php if (!$api_key) { ?>
          <p><i>Please enter a valid News@me API key here. If you need an API key, you can <a href="https://app.newsatme.com/users/sign_up?utm_source=wordpress-plugin&utm_medium=link-create-one-here-helper&utm_campaign=wordpress-plugin" target="_blank">create one here</a>.</i></p>
          <?php } ?>
        </div>

      </div>

      <p class="submit">
      <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />

      </p>
    </form> 
  </div>

</div>



