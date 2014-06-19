<div class="wrap">

  <?php echo NewsAtMe_Views::navigation(array('page' => 'get_key')); ?>
  <h2><?php _e('Activate your account', 'wpnewsatme'); ?></h2>
	<p><?php _e('News@me converts your occasional visitors into regular readers and sends them newsletter digests with the new posts published on your site based on their interests.<br>To use News@me you may need to sign up for an API key. Get started with the button below.', 'wpnewsatme'); ?></p>
    <?php _e('<a href="http://newsatme.com/get-api-key-wp" class="button button-primary" target="_blank">Create your News@me API key</a>', 'wpnewsatme'); ?>
	<br>
  <p><a href="<?php echo $link; ?>"><?php _e('I already have a key', 'wpnewsatme'); ?></a></p>
  
</div>
