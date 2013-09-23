<div class="inside">
   <input id='api_key' name='wpnewsatme[api_key]' size='45' type='text' value="<?php esc_attr_e( $api_key ); ?>" />
   
  <?php if (!$valid) { ?>
    <br/>
    <p class="setting-description error">
      <strong> Warning! This settings key does not seem to be connecting. Please verify.  </strong>
    </p>
  <?php } ?>
</div>
