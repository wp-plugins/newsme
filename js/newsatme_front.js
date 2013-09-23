(function($) {

  function valid_email(e) {
    var pattern = /^[A-Za-z0-9._%+-]+@([A-Za-z0-9-]+\.)+([A-Za-z0-9]{2,4}|museum)$/;
    return String(e).search (pattern) != -1;
  }

  function subscriptionDone(data) {
    $root = $('.newsatme_article_subscription');
    $root.find('form.newsatme_subscribe_form').remove(); 
    $('.newsatme_border_arrow_up').remove();
    $root.append(data);
  }

  function disableButton($button) {
    $button.data('original-label', $button.attr('value'));
    $button.attr('value', '...');
    $button.attr('disabled', 'disabled');
  }

  function enableButton($button) {
    $button.attr('value', $button.data('original-label'));
    $button.removeAttr('disabled');
    $button.removeData('original-label');
  }; 

  $(document).bind('ready', function() {
    $(document).on('submit', 'form.newsatme_subscribe_form', function(e) {
      var $form = $(e.target);
      var p1    = $form.find('.newsatme_privacy1_choice1');
      var email = valid_email($form.find('.newsatme_email_field').val());

      if (!p1.is(':checked')) {
        alert(newsatme_strings.privacy_required);
        return false;
      }

      if (!email) {
        alert(newsatme_strings.email_invalid);
        return false;
      }

      $button = $form.find('input[type=submit]')
      disableButton($button);

      $.ajax({ 
        type : $form.attr('method'), 
        url  : $form.attr('action'), 
        data : $form.serialize()
      }).done(function(data) {
        subscriptionDone(data);
      }).fail(function() { 
        alert(newsatme_strings.subscription_error);
        enableButton($button);
      });

      return false; 
    });		
		
			// Hide Flags
			// Add class to Flag based on how many are shown thru Admin panel, 1 or 2 and then hide
			if ($('.newsatme_privacy_list_element').length == 2) { 
				$('.newsatme_privacy_list_element').addClass('half-width').hide();
					} else if ($('.newsatme_privacy_list_element').length == 1) {
				$('.newsatme_privacy_list_element').addClass('full-width').hide();
			}	
			// Fade in Flags on Keypress
			$('.newsatme_email_field').keyup(function(){
					$('.newsatme_privacy_list_element').fadeIn();
			}); 			
			// Fade out Flags on blur if field is empty
			$('.newsatme_email_field').on('blur', function () {
			if( !this.value ) {
					$('.newsatme_privacy_list_element').fadeOut();
			};
			

			
		});
		
  });
})(jQuery); 
