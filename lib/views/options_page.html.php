<div class="wrap">
  <div class="icon32" style="background: url('<?php echo plugins_url('images/settings-icon.png', self::plugin_root()); ?>');">
    <br />
  </div>
  
  <h2><?php _e('NewsAtMe Settings', wpNewsAtMe::WPDOMAIN); ?> 
    <small><a href="options-general.php?page=<?php echo wpNewsAtMe::WPDOMAIN; ?>&show=how-tos">view how-tos</a></small>
  </h2>
  
  <div style="float: left;width: 70%;">
    <form method="post" action="options.php">

        <div class="stuffbox">
            <?php settings_fields(wpNewsAtMe::WPDOMAIN); ?>
            <?php do_settings_sections(wpNewsAtMe::WPDOMAIN); ?>
        </div>

        <p class="submit">
          <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>
    </form>
  </div>

</div>
