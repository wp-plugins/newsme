<?php

class NewsAtMe_Views {  

  static function apiKeyForm($api_key, $valid) {
    include self::template_path('api_form');
  }

  static function isChecked($display) {
    if ($display == '' || $display == 'no') $checked = "" ;
    else $checked = 'checked="checked"';
    return $checked; 
  }

  static function widgetTagsInCallToAction($display) {
    $checked = self::isChecked($display);
    include self::template_path('widget_tags_in_call_to_action');
  }

  static function widgetDisplayIfNoTags($display) {
    $checked = self::isChecked($display);
    include self::template_path('widget_display_if_no_tags');
  }

  static function showDemChoice() {
    return !wpNewsAtMe::getOption('widget_hide_dem_choice'); 
  }

  static function askAccessControlAllowOrigin($allow_origin, $cookie_domain) {
    include self::template_path('ask_access_control_allow_origin');
  }

  static function askDemChoice($option) {
    $checked = self::isChecked($option); 
    include self::template_path('widget_ask_dem_choice');
  }

  static function widgetDisplayOption($display) {
    $selected = 'selected="selected"';
    if ($display == '' || $display == false) $display = 'none';
    include self::template_path('widget_display_option');
  }

  static function renderPostOutOfSync($npost) {
    $id = $npost->id; 
    $title = $npost->title; 
    $signature = $npost->signature(); 
    $string = $npost->string_for_signature(); 
    include self::template_path('post_out_of_sync');
  }

  static function newsatme_privacy_url() {
    $site_id = WpNewsAtMe::getOption('site_id');
    return NewsAtMe_Client::BASE_URL . "statements/$site_id/privacy"; 
  }

  static function newsatme_dem_statement_url() {
    $site_id = WpNewsAtMe::getOption('site_id');
    return NewsAtMe_Client::BASE_URL . "statements/$site_id/dem"; 
  }

  static function tagsOnCallToAction($post) {
    // TODO: remove this $npost duplication all around
    $npost = new NewsAtMe_Post($post);
    return WpNewsAtMe::getOption('widget_tags_in_call_to_action') && $npost->has_tags() ;
  }

  static function callToActionSubject($post) {
    $npost = new NewsAtMe_Post($post); 
    if (self::tagsOnCallToAction($post)) {
      $subject = $npost->tagsString(); 
    }
    else {
      $subject = $npost->title; 
    }
    return $subject;
  }

  static function thankyouMessage($post, $timestamp) {
    $early = $timestamp > strtotime('-1 min'); 
    $npost = new NewsAtMe_Post($post); 
    
    if (WpNewsAtMe::thankyouMessageSubject() == 'tags' && $npost->has_tags()) {
      $subscriptionSubject = $npost->tagsString();
    }
    else {
      $subscriptionSubject = $post->post_title; 
    }

    ob_start(); 
    include self::template_path('thankyou_message'); 
    $message = ob_get_contents(); 
    ob_end_clean(); 
    return $message;
  }

  static function renderMissingApiKey() {
    include self::template_path('missing_api_key');
  }

  static function renderServerStatus() {
    include self::template_path('remote_status');
  }

  static function optionsPage() {
    include self::template_path('options_page');
  }
  
  static function plugin_root() {
    return plugin_dir_path(__FILE__);
  }
  static function base_path() {
    return self::plugin_root() . 'views/';
  }
  
  static function showHowTos() {
    include self::template_path('howtos');
  }

  static function renderCurlMissing() {
    include self::template_path('curl_missing'); 
  }
  
  static function renderTagsMetaBox($nonce, $tags, $available_tags, $error) {
    include self::template_path('post_tags_meta_box');
  }

  static function renderSubscribeForm($post) {
    include self::template_path('subscribe_form');
  }

  static function renderSubscriptionDone($cookie_content) {
    global $post; 
    include self::template_path('subscription_done');
  }

  // private
  private static function template_path($template) {
    return self::base_path() . $template . '.html.php';
  }
}

?>
