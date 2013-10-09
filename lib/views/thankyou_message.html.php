<p class="newsatme_subscription_done">
<?php if ($early) { ?>
  <span class="topline_message">

  <?php $subject = '<span class="taglist_subscription">' . $subscriptionSubject . '</span>'; ?>
  <?php printf(__('Thanks for your interest in: %s', wpNewsAtMe::WPDOMAIN), $subject); ?>
  <br/>
  <span class="bottomline_message">
    <?php _e('If you find other intersting articles on our site repeat the subscription.', wpNewsAtMe::WPDOMAIN); ?>
  </span>
<?php } else { ?> 
<span class="topline_message"><?php _e('You already subscribed to this topic.', wpNewsAtMe::WPDOMAIN); ?></span>
<?php } ?> 
</p>
