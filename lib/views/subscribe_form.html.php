<div id="newsatme_article_subscription_wrapper"><!-- Wrapper subscription starts here -->
  <p class="newsatme_article_title_callout">
    <?php _e('If you want updates for', wpNewsAtMe::WPDOMAIN); ?> 
  <span class="newsatme_subscribe_post_subject">
    <?php if (NewsAtMe_Views::tagsOnCallToAction($post)) { ?>
      <span class="tags-icon"></span>
    <?php } ?>
    <?php echo NewsAtMe_Views::callToActionSubject($post); ?>
  </span>
    <?php _e('enter your email in the box below:', wpNewsAtMe::WPDOMAIN); ?> 
  </p>
    
  <div class="newsatme_article_subscription_callout newsatme_clearfix"><!-- Subscription callout starts here -->
    
    <!-- Top arrow -->
    <div class="newsatme_border_arrow_up">
      <div class="newsatme_arrow_up"></div>
    </div>
    
    <div id="newsatme_article_subscription_<?php echo $post->ID; ?>" class="newsatme_article_subscription newsatme_clearfix"><!-- Article subscription starts here -->
    
      <form id="newsatme_subscribe_form_<?php echo $post->ID; ?>" class="newsatme_subscribe_form" method="POST" action="<?php echo get_permalink($post->ID); ?>">
        
       <div class="newsatme_form_box"><!-- Form box starts here -->
       <input type="text" name="newsatme_subscription[email]" class="newsatme_email_field" placeholder="<?php _e('Enter your email address', wpNewsAtMe::WPDOMAIN); ?>" />
          <input type="hidden" name="newsatme_subscription[ID]" value="<?php echo $post->ID; ?>" />
          
        <!-- Form box ends here --></div>
  
    
       <div class="newsatme_iscriviti_box"> <!-- Iscriviti button starts here -->
       <input type="submit" value="<?php _e('Subscribe', wpNewsAtMe::WPDOMAIN); ?>" class="newsatme_iscriviti_button" />
       <!-- Iscriviti button ends here --></div> 
       
<div class="newsatme_privacy_box"><!-- Privacy box starts here -->
      
           <div class="newsatme_privacy_list_element"><!-- Privacy list element starts here -->
               <label for="newsatme_<?php echo $post->ID; ?>_p1_c1"></label> 
              <input type="checkbox" 
                  id="newsatme_<?php echo $post->ID; ?>_p1_c1"
                  class="newsatme_privacy1_choice1" 
                  name="newsatme_subscription[privacy1]" 
                  value="1" checked="checked"/>
                
                  <span class="newsatme_privacy_note">
                    <?php   
                        $link = '<a class="newsatme_privacy_note_link" href="' . NewsAtMe_Views::newsatme_privacy_url() . '">' . __('Privacy Policy', wpNewsAtMe::WPDOMAIN) . '</a>'; 
                        printf(__('I have read and agree to the %s', wpNewsAtMe::WPDOMAIN), $link); ?>
                  </span>
            <!-- Privacy list element ends here --></div>
  
            <?php if (NewsAtMe_Views::showDemChoice()) { ?>
              
            <div class="newsatme_privacy_list_element"><!-- Privacy list element starts here -->
              <label for="newsatme_<?php echo $post->ID; ?>_p2_c1"></label> 
              <input type="checkbox" 
                id="newsatme_<?php echo $post->ID; ?>_p2_c1"
                class="newsatme_privacy2_choice1" 
                name="newsatme_subscription[privacy2]" 
                value="1" />

                <span class="newsatme_privacy_note">
                <?php 
                  $link = '<a class="newsatme_privacy_note_link" href="' . NewsAtMe_Views::newsatme_dem_statement_url() . '">' . __('Purpose of the Treatment', wpNewsAtMe::WPDOMAIN) . '</a>';
                  printf(__('I consent to the processing of personal data referred in %s', wpNewsAtMe::WPDOMAIN), $link); 
                ?>
                </span>
            <!-- Privacy list element ends here --></div>

            <?php } ?>
                
          <!-- Privacy box ends here --></div>
                
       
      </form>   
    <!-- Article subscription ends here --></div>
    
    <div class="newsatme_powered"><?php _e('Discover', wpNewsAtMe::WPDOMAIN); ?> <a href="https://newsatme.com" class="newsatme_powered_link" title="Un modo innovativo di ricevere news">News@Me</a><span class="newsatme_mini_logo">&nbsp;</span>
    </div>
    
  <!-- Subscription callout ends here --></div>
  <!-- Wrapper subscription ends here --></div>
