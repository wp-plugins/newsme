<?php
/* 
Plugin Name: News@me
Description: Convert visitors into regular readers. Keep them coming back to your site by sending them articles of your site based on their interests with News@me. 
Author: News@me 
Author URI: http://newsatme.com/
Plugin URI: http://wordpress.org/plugins/newsme/
Version: 2.2.0
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

  const VERSION = '2.2.0'; 
  const WPDOMAIN = 'wpnewsatme';
  const DEBUG = false;
  const TAGS_META_KEY = '_newsatme_tags'; 
  const TAGS_INPUT_NAME = '_newsatme_tags'; 
  const SAVED_META_KEY = '_newsatme_saved'; 
  const SEP = ',';

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
    # void 
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
    // collect submit form
    // add_action('parse_request', array(__CLASS__, 'collectSubscriptionForm'));
    wp_register_script('newsatme_front_js', NewsAtMe_Client::baseURL() . 'assets/namboot.js', array('jquery'), false, true);
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

    register_setting(self::WPDOMAIN, self::WPDOMAIN, array(__CLASS__, 'validateInput'));

    add_settings_section('wpnewsatme-api', '', '__return_false', self::WPDOMAIN);
    add_settings_field('api-key', 'News@me API key', array(__CLASS__, 'askSiteId'), self::WPDOMAIN, 'wpnewsatme-api');

    // Actions which have effect on post's remote status
    add_action('save_post', array(__CLASS__, 'savePostEvent'), 1, 2);
    add_action('trash_post', array(__CLASS__, 'trashPostEvent'), 1, 2);
    add_action('untrash_post', array(__CLASS__, 'untrashPostEvent'), 1, 1);
    add_action('admin_notices', array(__CLASS__, 'healthCheck'), 1, 1);

    if (self::getOption('api_key')) {
      self::addMetaBox('post'); 
    } 

    function newsatme_register_pointer_on_menu( $p ) {
      $p['newsatme_onmenu4'] = array(
        'target' => '#wp-newsatme-plugin-item',  // 'target' => '#menu-plugins', 
        'options' => array(
          'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
          __('News@me widget'), 
          __('We hope you find News@me useful. Here you\'ll get to your News@me settings where you\'ll be able to activate your widget and start using our service.')
          ),
        'position' => array( 'edge' => 'left', 'align' => 'center' )
        )
      );
      return $p;
    }

    function newsatme_register_pointer_on_metabox( $p ) {
      $p['newsatme_onmetabox4'] = array(
        'target' => '#wpnewsatme-post-tags', 
        'options' => array(
          'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
          __('Introducing News@me topics'), 
          __('Now you can segment your audience using topics. Add topics to your posts and allow your readers to subscribe to any of them. <br>News@me will send to each subscriber highly targeted newsletter digests of your latest posts based on their interests. Clever!')
          ),
        'position' => array( 'edge' => 'bottom', 'align' => 'center' )
        )
      );
      return $p;
    }

    add_filter('newsatme_admin_pointers-plugins', 'newsatme_register_pointer_on_menu');
    add_filter('newsatme_admin_pointers-post', 'newsatme_register_pointer_on_metabox');

  }

  static function addMetaBox($post_type) {
    add_meta_box('wpnewsatme-post-tags', 'News@me topics', array(__CLASS__, 'renderTagsMetaBox'), $post_type, 'side', 'default');
  }

  static function modifyPostContent($content) {
    if (!is_single()) return $content ; 

    global $post;
    $content .= self::renderWidget($post); 
    return $content;
  }

  static function adminMenu() {
    add_plugins_page(
      'News@me Settings',
      '<span id="wp-newsatme-plugin-item" class="wp-newsatme-menu-item">News@me</span>',
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

  static function askSiteId() {
    $api_key = self::getOption('api_key');
    NewsAtMe_Views::apiKeyForm($api_key, $site_id);
  }

  static function APIErrorReceived($method, $message) {
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
      $url = esc_url(add_query_arg(array('page' => wpNewsAtMe::WPDOMAIN), admin_url('plugins.php'))); 
      $settings = array('settings' => '<a href="' . $url . '">' . __('Settings', self::WPDOMAIN) . '</a>');
      $actions = array_merge((array) $settings, $actions);
    }

    return $actions;
  }

  static function showOptionsPage() {		
    $explicit = isset( $_GET['show'] ) && $_GET['show'] == 'enter-api-key' ; 
    if ( self::getAPIKey() || $explicit ) {
      NewsAtMe_Views::enterKeyPage(); 
    } else {
      NewsAtMe_Views::getKeyPage(); 
    }
  }

  static function dontUseTaxonomies() {
    $use = self::getOption('dont_use_taxonomies_as_topics'); 
    return $use === true; 
  }

  static function validateAPIKey($api_key) {
    self::getConnected($api_key); 

    $params = array(); 

    if (!self::isConnected()){
      add_settings_error('api-key-errors', 'api-key', 'The API key you entered is not valid. Please double-check it.', 'error'); 
    } else {
      $response = self::$newsatme_client->getSite();
      $params['api_key'] = $api_key; 

      if ($response['id'] != null) { 
        $params['site_id'] = $response['id']; 

        foreach($response as $key => $value) {
          if (strpos($key, 'wp_') === 0) $params[substr($key, 3)] = $value ; 
        }
      }

      add_settings_error('api-key-errors', 'api-key', 'Success! Your widget has been activated.', 'updated'); 
      return $params; 
    }
  }

  /**
   * Processes submitted settings from. This funciton is invoked every time 
   * an option with namespace WPDOMAIN is saved to database. 
   */
  static function validateInput($input) {
    $params = array_map('wp_strip_all_tags', $input);

    if (isset($params['site_id'])) {
      return $params ; 
    } 

    if ($params['api_key'] == '') {
      add_settings_error('api-key-errors', 'api-key', 'Please enter your API key.', 'updated'); 
    } else {
      $validated_settings = self::validateAPIKey($params['api_key']); 
    }

    return $validated_settings;
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

  static function healthCheck() {
    global $hook_suffix ; 

    settings_errors('api-key-errors'); 
    
    if (!self::getAPIKey()) {
      if ($hook_suffix == 'plugins.php') {
        NewsAtMe_Views::renderMissingApiKey(); 
      }
    }

    // Messages to only show in plugin's page
    if ($_GET['page'] == self::WPDOMAIN) {
      // check for curl libs to be there
      if (!(function_exists('curl_init') && function_exists('curl_exec')) ) {
        NewsAtMe_Views::renderCurlMissing(); 
      }
    }

    // Messages to show only on post page
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

    if ($npost->is_post_saved()) {
      try {
        $inSync = self::$newsatme_client->checkArticleSignature($post->ID, $npost->signature()); 
      } catch ( Exception $e) { }

      if (!$inSync) {
        NewsAtMe_Views::renderPostOutOfSync($npost); 
      }
    }
  }

  static function isWidgetShowable($post) {
    $npost = new NewsAtMe_Post($post);
    return $npost->has_tags() ; 
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


function newsatme_pointer_load( $hook_suffix ) {
  // Don't run on WP < 3.3
  if ( get_bloginfo( 'version' ) < '3.3' )
    return;

  $screen = get_current_screen();
  $screen_id = $screen->id;
  $pointers = apply_filters( 'newsatme_admin_pointers-' . $screen_id, array() );
  if ( ! $pointers || ! is_array( $pointers ) )
    return;
  $dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
  $valid_pointers =array();
  foreach ( $pointers as $pointer_id => $pointer ) {
    if ( in_array( $pointer_id, $dismissed ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) )
      continue;

    $pointer['pointer_id'] = $pointer_id;
    $valid_pointers['pointers'][] =  $pointer;
  }
 
  if ( empty( $valid_pointers ) )
    return;

  wp_enqueue_style( 'wp-pointer' );
  wp_enqueue_script( 'newsatme-pointer', plugins_url( 'js/newsatme-pointer.js', __FILE__ ), array( 'wp-pointer' ) );
  wp_localize_script( 'newsatme-pointer', 'newsatmePointer', $valid_pointers );
}



add_action( 'admin_enqueue_scripts', 'newsatme_pointer_load', 10000 );

// Register `the_content` filter here, due to the high priority required. 
add_filter('the_content', array('wpNewsAtMe', 'modifyPostContent'), 0); 

add_action('init', 'wpnewsatme_init');

?>
