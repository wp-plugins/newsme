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


