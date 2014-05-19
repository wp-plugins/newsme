<?php
class NewsAtMe_Post {

  var $id; 
  var $permalink; 
  var $post_title; 
  var $post_content; 
  var $tags_array; 
  var $tags_string; 
  var $time; 
  var $title; 
  var $status; 

  function NewsAtMe_Post($post) { $this->__construct($post); }

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

    $this->assign_tags_array(); 
    $this->make_tags_string(); 
  }

  function attributes() {
    return array(
      'remote_id'  => $this->id,
      'url'        => $this->permalink,
      'title'      => $this->title,
      'html_body'  => $this->content,
      'tags_array' => $this->tags_array,
      'publish_at' => $this->time
    ); 
  }

  function tagsString() {
    return implode(', ', $this->tags_array); 
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

  function has_tags() {
    $count = strlen(trim($this->tags_string)) ; 
    return $count; 
  }

  function published() {
    return in_array($this->status, array('publish', 'future')); 
  }

  function draft() {
    return in_array($this->status, array('draft')); 
  }

  function attributes_with_signature() {
    return array_merge(
      $this->attributes(), 
      array('signature' => $this->signature())
    );
  }

  private function make_tags_string() {
    $this->tags_string = implode(',', $this->tags_array); 
  }

  private function assign_tags_array_from_taxonomies() {
    $this->tags_array = $this->get_post_tags(); 
     if (empty($this->tags_array)) {
      $this->tags_array = $this->get_post_catgories(); 
    }
  }

  private function assign_tags_array() {
    $this->tags_array = array(); 

    if (strlen($this->get_newsatme_tags()) > 0) {
      $this->tags_array = explode(',', $this->get_newsatme_tags());
    }

    if (!wpNewsAtMe::dontUseTaxonomies() && empty($this->tags_array)) {
      $this->assign_tags_array_from_taxonomies(); 
    }
  }

  private function get_post_catgories() {
    return wp_get_object_terms($this->id, 'category', array('fields' => 'names'));
  }

  private function get_post_tags() {
    return wp_get_object_terms($this->id, 'post_tag', array('fields' => 'names'));
  }

  private function get_newsatme_tags() {
    return get_post_meta($this->id, wpNewsAtMe::TAGS_META_KEY, true);
  }
}
?>
