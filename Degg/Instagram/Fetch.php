<?php

namespace Degg\Instagram;

class Fetch {

  public function __construct() {
    add_action('degg_instagram_fetch', array($this, 'fetch'), 10, 2);
  }

  public function fetch($client_id, $tags) {
    global $wpdb;
    $instagram = new \Instagram\Instagram;
    $instagram->setClientId($client_id);

    foreach ($tags as $tag) {
      $min_tag_id = get_option("last_instagram_tag_{$tag}_id", 0);
      
      $tag = $instagram->getTag($tag);
      $media = $tag->getMedia(array('min_tag_id' => $min_tag_id));
  
      update_option("last_instagram_tag_{$tag}_id", $media->getNextMinId());
      foreach ($media as $m) {
        $query = "SELECT posts.* FROM ".$wpdb->posts." AS posts
          INNER JOIN ".$wpdb->postmeta." AS wpostmeta ON wpostmeta.post_id = posts.ID
          AND wpostmeta.meta_key = 'degg_instagram_id'
          AND wpostmeta.meta_value = '{$m->getID()}'";
  
        $posts = $wpdb->get_results($query, ARRAY_A);
  
        if (!$posts) {
          $id = wp_insert_post(array(
            'post_title' => "{$m->getUser()} on {$m->getCreatedTime( 'M jS Y @ g:ia' )}",
            'post_content' => "<img src='{$m->getThumbnail()->url}' title='Posted by {$m->getUser()} on {$m->getCreatedTime( 'M jS Y @ g:ia' )}'>",
            'post_type' => 'degg_instagram'
          ));
          add_post_meta($id, 'degg_instagram_id', "{$m->getID()}", true);
          add_post_meta($id, 'degg_instagram_title', "Posted by {$m->getUser()} on {$m->getCreatedTime( 'M jS Y @ g:ia' )}", true);
          add_post_meta($id, 'degg_instagram_user', "{$m->getUser()}", true);
          add_post_meta($id, 'degg_instagram_caption', "{$m->getCaption()}", true);
          add_post_meta($id, 'degg_instagram_link', "{$m->getLink()}", true);
          add_post_meta($id, 'degg_instagram_thumbnail', $m->getThumbnail(), true);
          add_post_meta($id, 'degg_instagram_standard_res', $m->getStandardRes(), true);
          add_post_meta($id, 'degg_instagram_low_res', $m->getLowRes(), true);
          wp_publish_post($id);
        }
      }
    }
  }

}
