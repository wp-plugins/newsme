<div class="wrap">
  <?php screen_icon(); ?>

  <h2 class="newsatme-header"><?php _e('News@me', wpNewsAtMe::WPDOMAIN); ?></h2>
  <ul class="global-nav">
    <li><a href="https://newsatme.com/en/how-it-works/?utm_source=wordpress-plugin&utm_medium=link-how-it-works&utm_campaign=wordpress-plugin" target="_blank">How it works</a></li>
    <li><a href="https://app.newsatme.com/users/sign_up?utm_source=wordpress-plugin&utm_medium=link-get-started&utm_campaign=wordpress-plugin" target="_blank">Get started</a></li>
    <li><a href="http://help.newsatme.com/?utm_source=wordpress-plugin&utm_medium=link-support&utm_campaign=wordpress-plugin" target="_blank">Support</a></li>
    <li><a href="https://newsatme.com/about-us/?utm_source=wordpress-plugin&utm_medium=link-about-us&utm_campaign=wordpress-plugin" target="_blank">About us</a></li>
    <li><a href="https://app.newsatme.com/users/sign_in?utm_source=wordpress-plugin&utm_medium=link-log-in&utm_campaign=wordpress-plugin" target="_blank">Log in</a></li>
  </ul>
  <div class="wrap-form-options">
    <form method="post" action="options.php">
      <div>
        <?php settings_fields(wpNewsAtMe::WPDOMAIN); ?>
        <?php do_settings_sections(wpNewsAtMe::WPDOMAIN); ?>
      </div>

      <p class="submit">
      <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />


      </p>
    </form> 
  </div>
</div>
