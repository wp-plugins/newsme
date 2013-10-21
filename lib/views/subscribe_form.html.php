<?php // keep this line empty ?>
<div id="newsatme_article_subscription_wrapper">
  <p class="newsatme_article_title_callout">
    <?php _e('If you want updates for', wpNewsAtMe::WPDOMAIN); ?> <span class="newsatme_subscribe_post_subject"> <?php if (NewsAtMe_Views::tagsOnCallToAction($post)) : ?> <span class="tags-icon"></span> <?php endif; ?> <?php echo NewsAtMe_Views::callToActionSubject($post); ?> </span> <?php _e('enter your email in the box below:', wpNewsAtMe::WPDOMAIN); ?> 
  </p>
  <div class="newsatme_article_subscription_callout newsatme_clearfix">
    <div class="newsatme_border_arrow_up">
      <div class="newsatme_arrow_up"></div>
    </div>
    <div id="newsatme_article_subscription_<?php echo $post->ID; ?>" class="newsatme_article_subscription newsatme_clearfix">
      <form id="newsatme_subscribe_form_<?php echo $post->ID; ?>" class="newsatme_subscribe_form" method="POST" action="<?php echo get_permalink($post->ID); ?>">
        <div class="newsatme_form_box">
          <input type="text" name="newsatme_subscription[email]" class="newsatme_email_field" placeholder="<?php _e('Enter your email address', wpNewsAtMe::WPDOMAIN); ?>" /> <input type="hidden" name="newsatme_subscription[ID]" value="<?php echo $post->ID; ?>" />
        </div>
        <div class="newsatme_iscriviti_box"> <input type="submit" value="<?php _e('Subscribe', wpNewsAtMe::WPDOMAIN); ?>" class="newsatme_iscriviti_button" /> </div> 
        <div class="newsatme_privacy_box">
          <div class="newsatme_privacy_list_element">
            <label for="newsatme_<?php echo $post->ID; ?>_p1_c1"></label> <input type="checkbox" id="newsatme_<?php echo $post->ID; ?>_p1_c1" class="newsatme_privacy1_choice1" name="newsatme_subscription[privacy1]" value="1" checked="checked"/> <span class="newsatme_privacy_note"> <?php   
              $link = '<a class="newsatme_privacy_note_link" href="' . NewsAtMe_Views::newsatme_privacy_url() . '">' . __('Privacy Policy', wpNewsAtMe::WPDOMAIN) . '</a>'; 
              printf(__('I have read and agree to the %s', wpNewsAtMe::WPDOMAIN), $link); ?>
            </span>
          </div>
          <?php if (NewsAtMe_Views::showDemChoice()) { ?>
            <div class="newsatme_privacy_list_element">
              <label for="newsatme_<?php echo $post->ID; ?>_p2_c1"></label> <input type="checkbox" id="newsatme_<?php echo $post->ID; ?>_p2_c1" class="newsatme_privacy2_choice1" name="newsatme_subscription[privacy2]" value="1" /> <span class="newsatme_privacy_note"> <?php 
                  $link = '<a class="newsatme_privacy_note_link" href="' . NewsAtMe_Views::newsatme_dem_statement_url() . '">' . __('Purpose of the Treatment', wpNewsAtMe::WPDOMAIN) . '</a>';
                  printf(__('I consent to the processing of personal data referred in %s', wpNewsAtMe::WPDOMAIN), $link); 
              ?>
              </span>
            </div>
          <?php } ?>
        </div>
      </form>   
    </div>
    <div class="newsatme_powered"><?php _e('Discover', wpNewsAtMe::WPDOMAIN); ?> <a href="https://app.newsatme.com" class="newsatme_powered_link" title="Un modo innovativo di ricevere news">News@Me</a><span class="newsatme_mini_logo">&nbsp; </span>
    </div> <img src="<?php echo NewsAtMe_Views::trackWidgetView(); ?>" width="1" height="1" />
  </div>
</div>
