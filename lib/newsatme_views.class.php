<?php

class NewsAtMe_Views {  

  static function apiKeyForm($api_key, $site_id, $valid=true) {
    include self::template_path('api_form');
  }

  static function isChecked($display) {
    if ($display == '' || $display == 'no') $checked = "" ;
    else $checked = 'checked="checked"';
    return $checked; 
  }

  static function keyConfigPage() {
    include self::template_path('key_config_page');
  }

  static function showDemChoice() {
    return !wpNewsAtMe::getOption('widget_hide_dem_choice'); 
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

  static function renderMissingApiKey() {
    $link = esc_url(add_query_arg(array('page' => wpNewsAtMe::WPDOMAIN), admin_url('plugins.php'))); 
    include self::template_path('missing_api_key');
  }

  static function renderServerStatus() {
    include self::template_path('remote_status');
  }

  static function enterKeyPage() {
    $link = esc_url(add_query_arg(array('page' => wpNewsAtMe::WPDOMAIN), admin_url('plugins.php'))); 
    include self::template_path('enter_key_page');
  }

  static function getKeyPage() {
    $link = esc_url(add_query_arg(array('page' => wpNewsAtMe::WPDOMAIN, 'show' => 'enter-api-key'), admin_url('plugins.php'))); 
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
  
  static function renderTagsMetaBox($nonce, $tags, $available_tags, $error) {
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

  // private
  private static function template_path($template) {
    return self::base_path() . $template . '.html.php';
  }
}

?>
