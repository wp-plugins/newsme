<?php 
class NewsAtMe_Pointers {

  static function init() {

    function pointer_on_admin_posts( $p ) {
      $p['newsatme_onmenu4a'] = array(
        'target' => '#plugin-now-active-header',  // 'target' => '#menu-plugins', 
        'options' => array(
          'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
          __('New! Audience segmentation gets smarter'), 
          __('News@me now works right after the activation. The News@me widget is now shown across your site at the bottom of any post that has "Tags" added or "Categories" assigned to it.
            <br><br>
            "Tags" and "Categories" are not good to you for segmenting your audience? Add "News@me topics" to your posts.')
          ),
        'position' => array( 'edge' => 'left', 'align' => 'center' )
        )
      );
      return $p;
    }

    function pointer_on_metabox( $p ) {
      $p['newsatme_onmetabox4'] = array(
        'target' => '#wpnewsatme-post-tags', 
        'options' => array(
          'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
          __('Introducing News@me topics'), 
          __('"Tags" and "Categories" are not good for segmenting your audience? Now you can segment your audience using "News@me topics".<br>Add topics to your posts and allow your readers to subscribe to any of them. <br>News@me will send to each subscriber highly targeted newsletter digests of your latest posts based on their interests. Clever!')
          ),
        'position' => array( 'edge' => 'bottom', 'align' => 'center' )
        )
      );
      return $p;
    }


    function pointer_load( $hook_suffix ) {
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
      wp_enqueue_script( 'newsatme-pointer', plugins_url( 'js/newsatme-pointer.js', NEWSATME_ROOT ), array( 'wp-pointer' ) );
      wp_localize_script( 'newsatme-pointer', 'newsatmePointer', $valid_pointers );
    }

    add_action( 'admin_enqueue_scripts', 'pointer_load', 10000 );

    add_filter('newsatme_admin_pointers-toplevel_page_newsatme-activation-page', 'pointer_on_admin_posts');
    add_filter('newsatme_admin_pointers-post', 'pointer_on_metabox');
  }

}
