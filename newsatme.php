<?php
/* 
Plugin Name: News@Me
Description: News@Me is a software that simplifies subscriptions to your newsletters by attracting subscribers in a new way. It creates the newsletter and sends out the articles for you, it's all automated. 
Author: News@Me 
Author URI: http://newsatme.com/
Plugin URI: http://wordpress.org/plugins/newsme/
Version: 2.0.3
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

function wpnewsatme_init() {
  wpNewsAtMe::on_load(); 
}

class wpNewsAtMe {

  const VERSION = '2.0.3'; 
  const WPDOMAIN = 'wpnewsatme';
  const DEBUG = false;
  const TAGS_META_KEY = '_newsatme_tags'; 
  const TAGS_INPUT_NAME = '_newsatme_tags'; 
  const SAVED_META_KEY = '_newsatme_saved'; 
  const DISPLAY_OPTION_AUTO = 'auto'; 
  const DISPLAY_OPTION_PLACEHOLDER = 'placeholder'; 
  const SEP = ',';

  static $settings;
  static $report;
  static $stats;
  static $newsatme_client;
  static $conflict;
  static $shown_widget = array();
  static $placeholder = '{{NEWSATME}}';

  static function setWidgetShown($id) {
    array_push(self::$shown_widget, $id);
  }

  static function isWidgetShown($id) {
    return (array_search($id, self::$shown_widget) !== false);
  }

  static function isConnected() {
    return isset(self::$newsatme_client);
  }

  static function getConnected() {
    if ( !isset(self::$newsatme_client) ) {
      try {
        self::$newsatme_client = new NewsAtMe_Client( self::getAPIKey() );
      } catch ( Exception $e ) { }
    }
  }

  static function install() {
    add_option(self::WPDOMAIN,
      array('widget_display' => self::DISPLAY_OPTION_AUTO), '', 'no'); 
  }

  static function on_load() {
    load_plugin_textdomain(self::WPDOMAIN, false, 
      dirname( plugin_basename( __FILE__ ) ).'/lang');

    // non admin action
    if (!is_admin()) {
      self::frontInit();
    }

    // admin actions
    add_action('admin_init', array(__CLASS__, 'adminInit'));
    add_action('admin_menu', array(__CLASS__, 'adminMenu'));

    // show links to settings in plugins page
    add_filter('plugin_action_links',array(__CLASS__,'showPluginActionLinks'), 10,5);
  }

  static function frontInit() {
    // collect submit form
    // add_action('parse_request', array(__CLASS__, 'collectSubscriptionForm'));
    wp_register_script('newsatme_front_js', NewsAtMe_Client::baseURL() . 'assets/namboot.js', array('jquery'));
    wp_enqueue_script('newsatme_front_js');
  }

  static function adminInit() {
    wp_register_script('underscore',  plugins_url('js/underscore-min.js' , __FILE__ ));
    wp_register_script('tag-it',  plugins_url('js/tag-it.min.js' , __FILE__ ), array('jquery', 'jquery-ui-autocomplete', 'jquery-ui-widget'));
    wp_register_script('newsatme_admin_js', plugins_url('js/newsatme_admin.js' , __FILE__ ), array('tag-it', 'underscore'));
    wp_enqueue_script('newsatme_admin_js');

    wp_register_style('tag-it-css-zendesk', plugins_url('css/tagit.ui-zendesk.css', __FILE__));
    wp_register_style('tag-it-css', plugins_url('css/jquery.tagit.css', __FILE__), array('tag-it-css-zendesk'));
    wp_enqueue_style('tag-it-css');

    wp_register_style('newsatme_admin_css', plugins_url('css/newsatme_admin.css' , __FILE__ ));
    wp_enqueue_style('newsatme_admin_css');

    register_setting(self::WPDOMAIN, self::WPDOMAIN, array(__CLASS__,'validateInput'));

    add_settings_section('wpnewsatme-api', 'API Settings', '__return_false', self::WPDOMAIN);
    add_settings_field('api-key', 'API Key', array(__CLASS__, 'askSiteId'), self::WPDOMAIN, 'wpnewsatme-api');


    self::addMetaBox('post'); 

    // Actions which have effect on post's remote status
    add_action('save_post', array(__CLASS__, 'savePostEvent'), 1, 2);
    add_action('trash_post', array(__CLASS__, 'trashPostEvent'), 1, 2);
    add_action('untrash_post', array(__CLASS__, 'untrashPostEvent'), 1, 1);
    add_action('admin_notices', array(__CLASS__, 'healthCheck'), 1,1);
  }

  static function addMetaBox($post_type) {
    add_meta_box('wpnewsatme-post-tags', 'NewsAtMe tags', array(__CLASS__, 'renderTagsMetaBox'), $post_type, 'advanced', 'high');
  }

  static function modifyPostContent($content) {
    if (!is_single()) return $content ; 

    global $post;
    $content .= self::renderWidget($post); 
    return $content;
  }

  static function adminMenu() {
    self::$settings = add_options_page(
      'NewsAtMe Settings',
      'NewsAtMe',
      'manage_options',
      self::WPDOMAIN,
      array(__CLASS__, 'showOptionsPage')
    );
  }

  static function deletePost($post) {
    try {
      self::getConnected();
      if (self::isConnected()) {
        self::$newsatme_client->deleteArticle($post->ID);
      } else { throw new Exception('not connected'); }
    } catch ( Exception $e) {
      self::APIErrorReceived('trashPost', $e->getMessage());
    }
  }

  static function trashPostEvent($post_id, $post) {
    self::deletePost($post); 
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

  // savePostEvent can be invoked when a post is created, 
  // updated or recovered from trash
  static function savePostEvent($post_id, $post) {
    $npost = new NewsAtMe_Post($post); 


    if (!current_user_can('edit_post', $npost->id)) {
      return $npost->id ; 
    }

    // for whatever reason the user is editing this post, if plugin's nonce 
    // is valid, update tags in post's meta.
    if (!self::invalidNonce($npost->id)) {
      self::updateTagMeta($npost->id); 
    }

    if ($npost->published()) {
      self::savePost($npost->_post); 
    }

    if ($npost->draft()) {
      self::deletePost($npost->_post); 
    }

    return $post->ID;
  }

  static function updateTagMeta($post_id) {
    if (!isset($_POST[self::TAGS_INPUT_NAME])) {
      return ; 
    }

    $tags = $_POST[self::TAGS_INPUT_NAME];
    if ($tags) {
      update_post_meta($post_id, self::TAGS_META_KEY, strtolower($tags));
    } else {
      delete_post_meta($post_id, self::TAGS_META_KEY);
    }
  }

  static function savePost($post) {
    try {
      self::getConnected();
      if (self::isConnected()) {
        self::$newsatme_client->saveArticle( new NewsAtMe_Post($post) );
        update_post_meta($post->ID, self::SAVED_META_KEY, strtotime('now'));
      } else { throw new Exception('not connected'); }
    } catch ( Exception $e) {
      self::APIErrorReceived('savePostEvent', $e->getMessage());
    }
  }

  static function askAccessControlAllowOrigin() {
    NewsAtMe_Views::askAccessControlAllowOrigin(
      self::getOption('access_control_allow_origin'), 
      self::getOption('cookie_domain')
    );
  }

  static function askDemChoice() {
    NewsAtMe_Views::askDemChoice(
      self::getOption('widget_hide_dem_choice'));
  }

  static function askTagsInCallToAction() {
    NewsAtMe_Views::widgetTagsInCallToAction(
      self::getOption('widget_tags_in_call_to_action'));
  }

  static function askDisplayIfNoTags() {
    NewsAtMe_Views::widgetDisplayIfNoTags(
      self::getOption('widget_display_if_no_tags'));
  }
  
  static function askWidgetDisplay() {
    $widget_display = self::getOption('widget_display');
    NewsAtMe_Views::widgetDisplayOption($widget_display);
  }

  // Following methods generate parts of settings and test forms.
  static function askSiteId() {
    $api_key = self::getOption('api_key');
    $api_is_valid = false;

    try {
      wpNewsAtMe::getConnected();
      if (self::isConnected()) {
        $site = self::$newsatme_client->getSite();
        self::updateOption('site_id', $site['id']); 
        NewsAtMe_Views::apiKeyForm($api_key, true);
      }
      else {
        NewsAtMe_Views::apiKeyForm($api_key, false);
      }
    } catch ( Exception $e) {
      NewsAtMe_Views::apiKeyForm($api_key, false);
    }
  }

  static function APIErrorReceived($method, $message) {
    error_log( "wpNewsAtMe::$method: Exception Caught => $message ");
    return new WP_Error( $message );
  }

  static function renderTagsMetaBox() {
    global $post;

    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $newsatme_tags = get_post_meta($post->ID, self::TAGS_META_KEY, true);
    $available_tags = array();
    $connection_error = false;

    try {
      self::getConnected();
      if (self::isConnected()) {
        $available_tags = self::$newsatme_client->getTags();
      } else { throw new Exception('not connected'); }
    } catch ( Exception $e) {
      $connection_error = true;
      self::APIErrorReceived('renderTagsMetaBox', $e->getMessage());
    }


    NewsAtMe_Views::renderTagsMetaBox($nonce, $newsatme_tags, $available_tags,
      $connection_error);
  }

  // Adds link to settings page in list of plugins
  static function showPluginActionLinks($actions, $plugin_file) {
    static $plugin;

    if (!isset($plugin))
      $plugin = plugin_basename(__FILE__);

    if ($plugin == $plugin_file) {
      $settings = array('settings' => '<a href="options-general.php?page=wpnewsatme">' . __('Settings', self::WPDOMAIN) . '</a>');
      $actions = array_merge((array) $settings, $actions);
    }

    return $actions;
  }

  static function showOptionsPage() {		
    if (!current_user_can('manage_options'))
      wp_die( 'You do not have sufficient permissions to access this page.' );

    NewsAtMe_Views::optionsPage(); 
  }

  /**
   * Processes submitted settings from.
   */
  static function validateInput($input) {
    $params = array_map('wp_strip_all_tags', $input);
    return $params;
  }

  static function getAPIKey() {
    return self::getOption('api_key');
  }

  /**
   * @return mixed
   */
  static function getOption( $name, $default = false ) {
    $options = get_option(self::WPDOMAIN);

    if (isset($options[$name]))
      return $options[$name];

    return $default;
  }

  static function updateOption($name, $value) {
    $options = get_option(self::WPDOMAIN);
    $new_options = array_merge($options, array($name => $value)); 
    return update_option(self::WPDOMAIN, $new_options); 
  }

  // this method checks for any error on:
  // 1) The presence of API key to be set
  // 2) Ability to connect to news@me
  // 3) The current post to be in sync with the remote one
  static function healthCheck() {
    self::getConnected();

    if (!(function_exists('curl_init') && function_exists('curl_exec')) ) {
      NewsAtMe_Views::renderCurlMissing(); 
    }

    if (!self::getAPIKey()) {
      NewsAtMe_Views::renderMissingApiKey();
    }
    else if (!self::isConnected()) {
      NewsAtMe_Views::renderServerStatus();
    }

    if (strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php')) {
      self::checkCurrentPostSync(); 
    }
  }

  static function checkCurrentPostSync() {
    global $post;
    $npost = new NewsAtMe_Post($post);
    if ($npost->is_post_saved()) {
      try {
        if (!self::$newsatme_client->checkArticleSignature($post->ID, $npost->signature())) {
          NewsAtMe_Views::renderPostOutOfSync($npost); 
        }
      } catch ( Exception $e) {
        // TODO: handle exception here
      }
    }
  }

  static function isWidgetShowable($post) {
    $already_shown = WpNewsAtMe::isWidgetShown($post->ID); 
    $display_with_no_tags = self::getOption('widget_display_if_no_tags');
    $npost = new NewsAtMe_Post($post);
    return ( !$already_shown ) && (($npost->has_tags() || $display_with_no_tags)); 
  }

  static function renderWidget($post) {
    $output = '';
    ob_start();

    if (WpNewsAtMe::isWidgetShowable($post)) {
      NewsAtMe_Views::renderSubscribeForm($post);
    }
    $output = ob_get_contents();
    ob_end_clean();

    return $output ;
  }
}

register_activation_hook( __FILE__, array('wpNewsAtMe', 'install') );

// Ensure this plugin is loaded first. This is required for the widget to appear 
// right after the post. Other plugins may override this config. 
function this_plugin_first() {
	// ensure path to this file is via main wp plugin path
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin          = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins       = get_option('active_plugins');
	$this_plugin_key      = array_search($this_plugin, $active_plugins);
	if ($this_plugin_key) { // if it's 0 it's the first plugin already, no need to continue
		array_splice($active_plugins, $this_plugin_key, 1);
		array_unshift($active_plugins, $this_plugin);
		update_option('active_plugins', $active_plugins);
	}
}

add_action("activated_plugin", "this_plugin_first");

// Register `the_content` filter here, due to the high priority required. 
add_filter('the_content', array('wpNewsAtMe', 'modifyPostContent'), 0); 

add_action('init', 'wpnewsatme_init');

?>
