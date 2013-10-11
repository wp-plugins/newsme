<div class="wrap">
<?php screen_icon(); ?>
  
  <h2><?php _e('News@Me Settings', wpNewsAtMe::WPDOMAIN); ?> 
    <small><a href="options-general.php?page=<?php echo wpNewsAtMe::WPDOMAIN; ?>&show=how-tos">view how-tos</a></small>
  </h2>
  
<div id="poststuff">
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
  	
  		<div style="float: left;width: 30%;">
  	
  	<div class="stuffbox postbox">
  		<h3>Plugin Info</h3>
  	  <div class="inside">
      <p>Name : News@Me <?php echo WpNewsAtMe::VERSION; ?></p>
  				<p>Author : News@Me</p>
  				<p>Website : <a href="https://newsatme.com" target="_blank">newsatme.com</a></p>
  				<p>Email : <a href="mailto:support@newsatme.com" target="_blank">support@newsatme.com</a></p>
  	    <p>Support: <a href="http://help.newsatme.com" target="_blank">help.newsatme.com</a></p>
  	  </div>
  	</div>
  	</div>
  </div>

</div>
