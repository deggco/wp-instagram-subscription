<?php


namespace Degg\Instagram;

class PostType {

  public function __construct() {
    add_action('init', array($this, 'register_instagram_post_type'));
  }

  /**
   * Instagram custom post type
   */

  function register_instagram_post_type() {
    $labels = array(
      'name'               => 'Instagrams',
      'singular_name'      => 'Instagram',
      'add_new'            => 'Add New',
      'add_new_item'       => 'Add New Instagram',
      'edit_item'          => 'Edit Instagram',
      'new_item'           => 'New Instagram',
      'view_item'          => 'View Instagram',
      'search_items'       => 'Search Instagram',
      'not_found'          => 'No Instagrams found',
      'not_found_in_trash' => 'No Instagrams found in trash',
      'parent_item_colon'  => '',
      'menu_name'          => 'Instagrams'
    );

    $args = array(
      'labels'              => $labels,
      'public'              => true,
      'exclude_from_search' => true,
      'publicly_queryable'  => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'query_var'           => true,
      'rewrite'             => array('slug' => 'instagram'),
      'capability_type'     => 'post',
      'has_archive'         => true,
      'hierarchical'        => false,
      'menu_position'       => 22,
      'supports'            => array('title', 'thumbnail', 'editor'),
    );

    register_post_type('degg_instagram', $args);
  }

  public static function setup() {
    return new static();
  }

}