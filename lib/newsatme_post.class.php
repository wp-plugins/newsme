<?php
class NewsAtMe_Post {

  var $id; 
  var $permalink; 
  var $post_title; 
  var $post_content; 
  var $tags_string; 
  var $time; 
  var $title; 
  var $status; 
  var $taxonomies; 
  var $disabled; 
  var $assigned_topics; 

  const SAVED_TOPICS_META_KEY = 'newsatme_saved_topics'; 
  const DISABLED_POST = 'disabled_post'; 

  function __construct($post) {
    $this->id          = $post->ID;
    $this->permalink   = get_permalink($this->id);
    $this->title       = $post->post_title;
    $this->content     = $post->post_content;
    $this->time        = strtotime($post->post_date_gmt);
    $this->status      = $post->post_status;
    $this->updated_at  = $post->post_modified_gmt;
    $this->type        = $post->post_type;  // TODO: check for this to remove
    $this->_post       = $post; // TODO: check for this to remove
    $this->disabled    = $this->isDisabled(); 
    $this->topics      = array(); 
  }

  function NewsAtMe_Post($post) { $this->__construct($post); }

  function setTopics($topics) {
    if (is_array($topics)) {
      $this->topics = $topics; 
    }
    else {
      $this->topics = explode(wpNewsAtMe::SEP, $topics); 
    }
  }

  function getTopics() {
    if ($this->emptyTopics()) {
      $backup_topics = $this->getBackupTopics(); 

      if (!self::emptyArray($backup_topics)) {
        $this->setTopics($backup_topics); 
      } 
      else {
        $this->setupTaxonomies(); 
        $this->assignComputedTopics(); 
      }
    }

    return $this->topics;
  }

  function getTopicsString() {
    return implode(wpNewsAtMe::SEP, $this->getTopics()); 
  }

  function attributes() {
    return array(
      'remote_id'  => $this->id,
      'url'        => $this->permalink,
      'title'      => $this->title,
      'html_body'  => $this->content,
      'tags_array' => $this->getTopicsIfEnabled(),
      'publish_at' => $this->time
    ); 
  }

  function string_for_signature() {
    return $this->updated_at;
  }

  function signature() {
    return sha1($this->string_for_signature());
  }

  function is_post_saved() {
    return ($this->status != 'auto-draft');
  }

  function published() {
    return in_array($this->status, array('publish', 'future')); 
  }

  function unpublished() {
    return in_array($this->status, array('pending', 'draft', 'private')); 
  }

  function backupTopics() {
    update_post_meta($this->id, self::SAVED_TOPICS_META_KEY, $this->topics);
  }

  function flushTopicsBackup() {
    delete_post_meta($this->id, self::SAVED_TOPICS_META_KEY);
  }

  function disable() {
    update_post_meta($this->id, self::DISABLED_POST, true);
    $this->disabled = true; 
    if (!$this->emptyTopics()) {
      $this->backupTopics(); 
    }
  }

  function enable() {
    if ($this->emptyTopics()) {
      $this->setTopics($this->getBackupTopics()); 
    }
    delete_post_meta($this->id, self::DISABLED_POST);
    $this->disabled = false; 
  }

  function isDisabled() {
    return get_post_meta($this->id, self::DISABLED_POST, true); 
  }

  private function getTopicsIfEnabled() {
    if ($this->isDisabled()) {
      return array(); 
    }
    else {
      return $this->getTopics(); 
    }
  }

  private function getBackupTopics() {
    return get_post_meta($this->id, self::SAVED_TOPICS_META_KEY, true); 
  }

  function attributes_with_signature() {
    return array_merge(
      $this->attributes(), 
      array('signature' => $this->signature())
    );
  }

  function emptyTopics() {
    $diff = array_diff($this->topics, array(''));
    return empty($diff);
  }

  private function assignTopicsWithAutoMode() {
    $this->setTopics($this->getTagLikeTerms()); 
    if ($this->emptyTopics()) {
      $this->setTopics($this->getCategoryLikeTerms()); 
    }
  }

  private function assignComputedTopics() {
    if ($this->emptyTopics()) {
      if (wpNewsAtMe::autoModeEnbled()) {
        $this->assignTopicsWithAutoMode(); 
      }
      else if (wpNewsAtMe::useCategories()) {
        $this->setTopics($this->getCategoryLikeTerms());
      } 
      else if (wpNewsAtMe::useTags()) {
        $this->setTopics($this->getTagLikeTerms());
      }
    }
  }

  private function getCategoryLikeTerms() {
    return wp_get_object_terms($this->id, $this->taxonomies['hierarchical'], array('fields' => 'names'));
  }

  private function getTagLikeTerms() {
    return wp_get_object_terms($this->id, $this->taxonomies['non-hierarchical'], array('fields' => 'names'));
  }

  private function setupTaxonomies() {
    $this->taxonomies = array('hierarchical' => array(), 'non-hierarchical' => array()); 
    foreach(get_taxonomies(array(), 'object') as $k => $v) {
      if (in_array($this->type, $v->object_type) ) {
        if ($v->hierarchical) {
          $this->taxonomies['hierarchical'][] = $k; 
        } else {
          $this->taxonomies['non-hierarchical'][] = $k; 
        }
      }
    }
  }

  static function emptyArray($array) {
    if (!is_array($array)) $array = array();
    $diff = array_diff($array, array('')); 
    return empty($diff);
  }

}

?>
