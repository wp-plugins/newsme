<div class="inside">
<p><?php _e('Select how the widget will be shown on the page', wpNewsAtMe::WPDOMAIN); ?> </p>

  <select id="display_option" name="wpnewsatme[widget_display]">
    <option value="none" <?php echo ($display == 'none' ? $selected : ''); ?> >
      <?php _e('Never display widget', wpNewsAtMe::WPDOMAIN); ?> 
    </option>
    <option value="auto" <?php echo ($display == 'auto' ? $selected : '') ?> >
      <?php _e('Automatically at the end of each article', wpNewsAtMe::WPDOMAIN); ?> 
    </option>
    <option value="placeholder" <?php echo ($display == 'placeholder' ? $selected : '') ?> >
      <?php _e('With a placeholder inside the article', wpNewsAtMe::WPDOMAIN); ?>
    </option>
    <option value="template_tag" <?php echo ($display == 'template_tag' ? $selected : '') ?> >
      <?php _e('With a tag inside the template', wpNewsAtMe::WPDOMAIN) ?>
    </option>
  </select>
</div>

<div class="inside">
  <ul>
  	<li><b>Widget placement help</b></li>
    <li>
    <strong><?php _e('Never display widget', wpNewsAtMe::WPDOMAIN); ?>:</strong> 
      <?php _e('widget is hidden in any case, even if a placeholder is set inside the article or the template function is used.', wpNewsAtMe::WPDOMAIN); ?>  
    </li>

    <li>
    <strong><?php _e('Automatically at the end of each article', wpNewsAtMe::WPDOMAIN); ?>:</strong> 
      <?php _e('widget is shown just after the article body in detail pages.', wpNewsAtMe::WPDOMAIN); ?>
    </li>
  
    <li>
    <strong><?php _e('With a placeholder inside the article', wpNewsAtMe::WPDOMAIN); ?>:</strong> 
      <?php _e('when this option is active, you need to set a placeholder inside the article\'s body where you want the widget to be shown. Placeholder to use is the following: <pre> {{NEWSATME}} </pre>', wpNewsAtMe::WPDOMAIN); ?>
    </li>

    <li>
    <strong><?php _e('With a tag inside the template', wpNewsAtMe::WPDOMAIN); ?>:</strong> 
      <?php _e('widget is shown by using the following tag inside your template:  <pre> <&#63;php echo newsatme_subscribe(); ?> </pre> Only use this tag when a single article is being displayed on the page.', wpNewsAtMe::WPDOMAIN); ?>
    </li>
  </ul>

</div>

