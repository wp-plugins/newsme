<?php

class NewsAtMe_Views {  

  static function isChecked($display) {
    if ($display == '' || $display == 'no') $checked = "" ;
    else $checked = 'checked="checked"';
    return $checked; 
  }

  static function renderPostOutOfSync($npost) {
    $id = $npost->id; 
    $title = $npost->title; 
    $signature = $npost->signature(); 
    $string = $npost->string_for_signature(); 
    include self::template_path('post_out_of_sync');
  }

  static function renderMissingApiKey() {
    $link = esc_url(add_query_arg(array('page' => 'newsatme-activation-page'), 
    admin_url('plugins.php'))); 
    include self::template_path('missing_api_key');
  }

  static function navigation($options) {
    include self::template_path('navigation'); 
  }

  static function showPreferences() {
    $post_types = wpNewsAtMe::getPostTypes(); 
    include self::template_path('preferences_page'); 
  }

  static function renderServerStatus() {
    include self::template_path('remote_status');
  }

  static function setKeyPage($api_key) {
    $link = esc_url(add_query_arg(
      array('page' => 'newsatme-activation-page'), admin_url('plugins.php'))); 
    include self::template_path('set_key_page');
  }

  static function getKeyPage() {
    $link = esc_url(add_query_arg(
      array('page' => 'newsatme-activation-page', 'show' => 'enter-api-key'), 
      admin_url('admin.php'))); 
    include self::template_path('get_key_page');
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
  
  static function renderTopicsMetaBox($post, $nonce, $available_tags, $error) {
    $disabled = $post->disabled; 
    $disabled_checked = null; 
    if ($disabled) {
      $disabled_checked = " checked=\"checked\" ";
    }
    $url_to_preferences = esc_url(add_query_arg(array('page' => 'newsatme-preferences-page'), admin_url('admin.php'))); 
    include self::template_path('post_tags_meta_box');
  }

  static function renderSubscribeForm($post) {
    $post = new NewsAtMe_Post($post); 
    include self::template_path('subscribe_form');
  }

  static function renderSubscriptionDone($cookie_content) {
    global $post; 
    include self::template_path('subscription_done');
  }

  private static function template_path($template) {
    return self::base_path() . $template . '.html.php';
  }
}

