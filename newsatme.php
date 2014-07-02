<?php
/* 
Plugin Name: News@me
Description: Convert visitors into regular readers. Keep them coming back to your site by sending them articles of your site based on their interests with News@me. 
Author: News@me 
Author URI: http://newsatme.com/
Plugin URI: http://wordpress.org/plugins/newsme/
Version: 3.2.1
Text Domain: wpnewsatme
 */
/*  Copyright 2013  News@me 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 */

require_once( plugin_dir_path( __FILE__ ) . '/lib/newsatme_views.class.php');
require_once( plugin_dir_path( __FILE__ ) . '/lib/newsatme_client.class.php');
require_once( plugin_dir_path( __FILE__ ) . '/lib/newsatme_post.class.php');
require_once( plugin_dir_path( __FILE__ ) . '/lib/newsatme_pointers.class.php');

function wpnewsatme_init() {
  wpNewsAtMe::on_load(); 
}

define('NEWSATME_API_KEY_OPTION_GROUP', 'newsatme-api-option-group'); 
define('NEWSAMTE_API_KEY_OPTION_NAME', 'wpnewsatme'); 

define('NEWSATME_POST_TYPES_OPTION_GROUP', 'newsatme-post-types-option-group'); 
define('NEWSATME_POST_TYPES_OPTION_NAME', 'newsatme-post-types');
define('NEWSATME_VISIBILITY_OPTION_GROUP', 'newsatme-visibility-option-group'); 
define('NEWSATME_VISIBILITY_AUTO_MODE', 'newsatme-auto-mode');

define('NEWSATME_ROOT', __FILE__); 

class wpNewsAtMe {

  const VERSION = '3.2.1'; 
  const WPDOMAIN = 'wpnewsatme';
  const DEBUG = false;
  const TAGS_META_KEY = '_newsatme_tags'; 
  const TAGS_INPUT_NAME = '_newsatme_tags'; 
  const DISABLED_POST_NAME = '_newsatme_disabled_post'; 
  const SEP = ',';

  const DONT_USE_TAXONOMIES_AS_TOPICS = 'dont_use_taxonomies_as_topics'; 

  static $report;
  static $stats;
  static $newsatme_client;
  static $conflict;
  static $placeholder = '{{NEWSATME}}';

  static function isConnected() {
    return isset(self::$newsatme_client);
  }

  static function getConnected($api_key = null) {
    if ( !isset(self::$newsatme_client) ) {
      try {
        if (!$api_key) {
          $api_key = self::getAPIKey();
        }
        self::$newsatme_client = new NewsAtMe_Client( $api_key );
      } catch ( Exception $e ) { }
    }
  }

  static function install() {
    self::ensureDefaultOptions(); 
  }

  static function ensureDefaultOptions() {
    $option = get_option(NEWSATME_POST_TYPES_OPTION_NAME, null) ; 
    if ($option == null && in_array('post', get_post_types(array('public' => true), 'names'))) {
      update_option(NEWSATME_POST_TYPES_OPTION_NAME, array('post' => '1')); 
    }
  }

  static function on_load() {
    load_plugin_textdomain(self::WPDOMAIN, false, 
      dirname( plugin_basename( __FILE__ ) ).'/lang');

    if (!is_admin()) {
      self::frontInit();
    }

    add_action('admin_init', array(__CLASS__, 'adminInit'));
    add_action('admin_menu', array(__CLASS__, 'adminMenu'));

    add_filter('plugin_action_links', array(__CLASS__, 'showPluginActionLinks'), 10, 5);
  }

  static function frontInit() {
    wp_register_script('newsatme_front_js', NewsAtMe_Client::baseURL() . 'assets/namboot.js', array('jquery'), false, true);
    wp_enqueue_script('newsatme_front_js');

    add_filter('clean_url', array(__CLASS__, 'addDeferAndAsyncToURL'), 11, 1 );
  }

  static function addDeferAndAsyncToURL($url) {
    if (FALSE === strpos( $url, 'namboot.js' )) return $url;
    else return "$url' async defer='defer";
  }

  static function adminInit() {
    wp_register_script('underscore',  plugins_url('js/underscore-min.js' , __FILE__ ));
    wp_register_script('tag-it',  plugins_url('js/tag-it.js' , __FILE__ ), array('jquery', 'jquery-ui-autocomplete', 'jquery-ui-widget'));
    wp_register_script('newsatme_admin_js', plugins_url('js/newsatme_admin.js' , __FILE__ ), array('tag-it', 'underscore'));
    wp_enqueue_script('newsatme_admin_js');

    wp_register_style('tag-it-css-zendesk', plugins_url('css/tagit.ui-zendesk.css', __FILE__));
    wp_register_style('tag-it-css', plugins_url('css/jquery.tagit.css', __FILE__), array('tag-it-css-zendesk'));
    wp_enqueue_style('tag-it-css');

    wp_register_style('newsatme_admin_css', plugins_url('css/newsatme_admin.css' , __FILE__ ));
    wp_enqueue_style('newsatme_admin_css');

    register_setting(NEWSATME_API_KEY_OPTION_GROUP,
      NEWSAMTE_API_KEY_OPTION_NAME, array(__CLASS__, 'validateAPIKey'));

    add_settings_section('wpnewsatme-api', '', '__return_false', NEWSATME_API_KEY_OPTION_GROUP);

    add_settings_field('api-key', 'News@me API key', 'void', 
      NEWSATME_API_KEY_OPTION_GROUP, 'wpnewsatme-api');

    register_setting(NEWSATME_POST_TYPES_OPTION_GROUP, NEWSATME_POST_TYPES_OPTION_NAME, 
      array(__CLASS__, 'validatePostTypes')); 
    add_settings_section('post_types', 'Post Types', '__return_false', 
      NEWSATME_POST_TYPES_OPTION_GROUP);

    register_setting(NEWSATME_VISIBILITY_OPTION_GROUP, NEWSATME_VISIBILITY_AUTO_MODE, 
      array(__CLASS__, 'validateVisibility')); 
    add_settings_section('widget_visibility', 'Visibility', '__return_false', 
      NEWSATME_VISIBILITY_OPTION_GROUP);
    add_settings_field('auto-mode', 'Auto mode', 'void', NEWSATME_VISIBILITY_OPTION_GROUP, 
      'auto_mode'); 

    add_action('save_post', array(__CLASS__, 'savePostEvent'), 1, 2);
    add_action('trash_post', array(__CLASS__, 'trashPostEvent'), 1, 2);
    add_action('untrash_post', array(__CLASS__, 'untrashPostEvent'), 1, 1);
    add_action('admin_notices', array(__CLASS__, 'healthCheck'), 1, 1);

    NewsAtMe_Pointers::init(); 

    self::registerSettingsFieldsForPostTypes(); 
    self::ensureDefaultOptions(); 

    if (self::getOption('api_key')) {
      foreach(get_option(NEWSATME_POST_TYPES_OPTION_NAME) as $k => $v) {
        self::addMetaBox($k); 
      }
    } 
  }

  static function registerSettingsFieldsForPostTypes() {
    function void() {}; 

    foreach ( self::getPostTypes() as $pt ) {
      add_settings_field($pt->name, $pt->name, 'void', 
        NEWSATME_POST_TYPES_OPTION_GROUP, 'post_types', 
        array('name' => $pt->name, 'label' => $pt->labels->name));
    }
  }

  static function addMetaBox($post_type) {
    if (self::postTypeEnabled($post_type)) {
      add_meta_box('wpnewsatme-post-tags', __('News@me topics', 'wpnewsatme'), 
        array(__CLASS__, 'renderTagsMetaBox'), $post_type, 'side', 'default');
    }
  }

  static function modifyPostContent($content) {
    global $post;
    $content .= self::renderWidget($post); 
    return $content;
  }

  static function adminMenu() {
    add_menu_page('News@me Settings',
      'News@me',
      'manage_options',
      'newsatme-activation-page',
      array(__CLASS__, 'showActivationPage'), 
      plugins_url('/images/favicon.ico', __FILE__)
    );

    add_submenu_page('newsatme-activation-page', 'News@me Account', 'Account', 
      'manage_options', 'newsatme-activation-page', 
      array(__CLASS__, 'showActivationPage' ));

    add_submenu_page('newsatme-activation-page', 'News@me Settings', __('Preferences', 'wpnewsatme'), 
      'manage_options', 'newsatme-preferences-page', 
      array(__CLASS__, 'showPreferencesPage' ));
  }

  static function showPreferencesPage() {
    NewsAtMe_Views::showPreferences(); 
  }

  static function showActivationPage() {		
    $from_link = isset( $_GET['show'] ) && $_GET['show'] == 'enter-api-key' ; 

    if ( self::getAPIKey() || $from_link ) {
      NewsAtMe_Views::setKeyPage(self::getOption('api_key')); 
    } else {
      NewsAtMe_Views::getKeyPage(); 
    }
  }

  static function deletePost($npost) {
    try {
      self::getConnected();
      if (self::isConnected()) {
        $npost->backupTopics(); 
        self::$newsatme_client->deleteArticle($npost->id);
      } else { throw new Exception('not connected'); }
    } catch ( Exception $e) {
      self::APIErrorReceived('trashPost', $e->getMessage());
    }
  }

  static function trashPostEvent($post_id, $post) {
    $npost = new NewsAtMe_Post($post);
    self::deletePost($npost); 
    return $post->ID;
  }

  static function untrashPostEvent($post_id) {
    global $post ;
    self::savePostEvent($post_id, $post);
  }

  static function invalidNonce($post_id) {
    if (isset($_POST['postmeta_noncename'])) {
      if (!wp_verify_nonce( $_POST['postmeta_noncename'], plugin_basename(__FILE__) )) {
        return $post_id; 
      }
    }
  }

  static function savePostEvent($post_id, $post) {
    if (!current_user_can('edit_post', $post_id)) {
      return $post_id; 
    }

    $npost = new NewsAtMe_Post($post); 

    if (!self::invalidNonce($post_id)) {
      $npost->setTopics($_POST[self::TAGS_INPUT_NAME]);

      if ($_POST[self::DISABLED_POST_NAME])     $npost->disable(); 
      else                                      $npost->enable(); 
    }

    if ($npost->published())        self::savePostToRemote($npost); 
    if ($npost->unpublished())      self::deletePost($npost); 

    return $post->ID;
  }

  static function savePostToRemote($npost) {
    try {
      self::getConnected();
      if (self::isConnected()) {
        self::$newsatme_client->saveArticle( $npost );

        if (!$npost->disabled) {
          $npost->flushTopicsBackup(); 
        }
      } else { throw new Exception('not connected'); }
    } catch ( Exception $e) {
      self::APIErrorReceived('savePostEvent', $e->getMessage());
    }
  }

  static function APIErrorReceived($method, $message) {
    return new WP_Error( $message );
  }

  static function renderTagsMetaBox() {
    global $post;

    $nonce = wp_create_nonce( plugin_basename(__FILE__) );

    $response = self::getArticleTopicsFromRemote($post->ID); 

    $npost = new NewsAtMe_Post($post); 
    $npost->setTopics($response['article_tags']); 

    NewsAtMe_Views::renderTopicsMetaBox($npost, $nonce, 
      $response['available_tags'], 
      $response['connection_error']); 
  }

  static function getArticleTopicsFromRemote($post_id) {
    $available_tags   = array();
    $article_tags     = "";
    $connection_error = false;

    try {
      self::getConnected();
      if (self::isConnected()) {
        $response = self::$newsatme_client->getArticleTags($post_id);
        $available_tags = $response['available_tags']; 
        $article_tags = $response['tags']; 
      } else { throw new Exception('not connected'); }
    } catch ( Exception $e) {
      $connection_error = true;
      self::APIErrorReceived('renderTagsMetaBox', $e->getMessage());
    }

    return array(
      'available_tags' => $available_tags, 
      'article_tags' => $article_tags, 
      'connection_error' => $connection_error
    ); 
  }

  static function showPluginActionLinks($actions, $plugin_file) {
    static $plugin;

    if (!isset($plugin))
      $plugin = plugin_basename(__FILE__);

    if ($plugin == $plugin_file) {
      $url = esc_url(add_query_arg(array('page' => 'newsatme-activation-page'), admin_url('plugins.php'))); 
      $settings = array('settings' => '<a href="' . $url . '">' . __('Account', self::WPDOMAIN) . '</a>');
      $actions = array_merge((array) $settings, $actions);
    }

    return $actions;
  }

  static function getPostTypes() {
    return get_post_types( array('public' => true), 'objects'); 
  }

  static function dontUseTaxonomies() {
    $use = self::getOption(self::DONT_USE_TAXONOMIES_AS_TOPICS); 
    return $use === true; 
  }

  static function getSiteIdFromRemote($api_key) {
    self::getConnected($api_key); 

    $params = array(); 

    if (!self::isConnected()){
      add_settings_error('api-key-errors', 'api-key', __('The API key you entered is not valid. Please double-check it.', 'wpnewsatme'), 'error'); 
    } else {
      $response = self::$newsatme_client->getSite();
      $params['api_key'] = $api_key; 

      if ($response['id'] != null) { 
        $params['site_id'] = $response['id']; 

        foreach($response as $key => $value) {
          if (strpos($key, 'wp_') === 0) $params[substr($key, 3)] = $value ; 
        }
      }

      add_settings_error('api-key-errors', 'api-key', __('Success! Your account has been activated.', 'wpnewsatme'), 'updated'); 
      return $params; 
    }
  }

  static function validatePostTypes($input) {
    return $input; 
  }

  static function validateVisibility($input) {
    $options = get_option('wpnewsatme'); 
    unset($options[self::DONT_USE_TAXONOMIES_AS_TOPICS]); 
    update_option('wpnewsatme', $options); 
    return $input; 
  }

  static function validateAPIKey($input) {
    $params = array_map('wp_strip_all_tags', $input);

    if (isset($params['site_id'])) {
      return $params ; 
    } 

    if ($params['api_key'] == '') {
      add_settings_error('api-key-errors', 'api-key', __('Please enter your API key.', 'wpnewsatme'), 'updated'); 
    } else {
      $validated_settings = self::getSiteIdFromRemote($params['api_key']); 
    }

    return $validated_settings;
  }

  static function getAPIKey() {
    return self::getOption('api_key');
  }


  static function getOption( $name, $default = false ) {
    $options = get_option(self::WPDOMAIN);

    if (isset($options[$name]))
      return $options[$name];

    return $default;
  }

  static function healthCheck() {
    global $hook_suffix ; 

    settings_errors('api-key-errors'); 
    
    if (!self::getAPIKey()) {
      if ($hook_suffix == 'plugins.php') {
        NewsAtMe_Views::renderMissingApiKey(); 
      }
    }

    if ($_GET['page'] == 'newsatme-activation-page') {
      if (!(function_exists('curl_init') && function_exists('curl_exec')) ) {
        NewsAtMe_Views::renderCurlMissing(); 
      }
    }

    if (strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php')) {
      self::getConnected(); 
      if (self::isConnected()) {
        self::checkCurrentPostSync(); 
      }
    }
  }

  static function checkCurrentPostSync() {
    global $post;
    $npost = new NewsAtMe_Post($post);
    $inSync = false; 

    if ($npost->is_post_saved() && !$npost->unpublished()) {
      try {
        $inSync = self::$newsatme_client->checkArticleSignature(
          $post->ID, $npost->signature()); 
      } catch ( Exception $e) { }

      if (!$inSync) {
        NewsAtMe_Views::renderPostOutOfSync($npost); 
      }
    }
  }

  static function isWidgetShowable($post) {
    $npost = new NewsAtMe_Post($post);
    return (
      self::getAPIKey() && 
      self::postTypeEnabled($post->post_type) && 
      is_single() && !$npost->disabled
    ); 
  }

  static function renderWidget($post) {
    $output = '';

    if (WpNewsAtMe::isWidgetShowable($post)) {
      ob_start();
      NewsAtMe_Views::renderSubscribeForm($post);
      $output = ob_get_contents();
      ob_end_clean();
    }

    return $output ;
  }

  static function autoModeEnbled() {
    $value = get_option(NEWSATME_VISIBILITY_AUTO_MODE, true); 
    
    return $value && !self::dontUseTaxonomies(); 
  }

  static function postTypeEnabled($name) {
    $values = get_option(NEWSATME_POST_TYPES_OPTION_NAME, array()); 
    return $values[$name] == '1'; 
  }
}

register_activation_hook( __FILE__, array('wpNewsAtMe', 'install') );

function this_plugin_first() {
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin          = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins       = get_option('active_plugins');
	$this_plugin_key      = array_search($this_plugin, $active_plugins);
  if ($this_plugin_key) { 
		array_splice($active_plugins, $this_plugin_key, 1);
		array_unshift($active_plugins, $this_plugin);
		update_option('active_plugins', $active_plugins);
	}
}
add_action("activated_plugin", "this_plugin_first");

add_filter('the_content', array('wpNewsAtMe', 'modifyPostContent'), 0); 

add_action('init', 'wpnewsatme_init');

