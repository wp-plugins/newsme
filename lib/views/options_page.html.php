<div class="wrap">
  <?php screen_icon(); ?>

  <h2><?php _e('News@me Settings', wpNewsAtMe::WPDOMAIN); ?></h2>

  <div>
    <form method="post" action="options.php">
      <div>
        <?php settings_fields(wpNewsAtMe::WPDOMAIN); ?>
        <?php do_settings_sections(wpNewsAtMe::WPDOMAIN); ?>
      </div>

      <p class="submit">
      <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
      </p>

      <div class="stuffbox postbox">
        <h3 style="line-height:2;">Plugin Info</h3>
        <div class="inside">
          <p style="margin: 1em 0;">Name: <span style="font-weight:bold;">News@me <?php echo WpNewsAtMe::VERSION; ?></span></p>
          <p style="margin: 1em 0;">Author: <span style="font-weight:bold;">News@me<span></p>
          <p style="border-top: 1px solid #eeeeee; margin: 0.5em 0; padding: 1em 0 0;">Website: <a href="https://newsatme.com" target="_blank">newsatme.com</a></p>
          <p>Sign in: <a href="https://app.newsatme.com/users/sign_in" target="_blank">app.newsatme.com</a></p>
          <p>Email: <a href="mailto:support@newsatme.com" target="_blank">support@newsatme.com</a></p>
          <p>Support: <a href="http://help.newsatme.com" target="_blank">help.newsatme.com</a></p>
        </div>
      </div>
    </div>

  </form>
</div>
