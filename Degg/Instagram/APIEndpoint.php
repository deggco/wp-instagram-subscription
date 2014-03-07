<?php

namespace Degg\Instagram;

class APIEndpoint {

  $client_id;

  $tags;

  public function __construct($client_id, $tag) {
    $this->client_id = $client_id;
    $this->tag = $tag;
    add_filter('query_vars', array($this, 'add_query_vars'), 0);
    add_action('parse_request', array($this, 'sniff_requests'), 0);
    add_action('init', array($this, 'add_endpoint'), 0);
  }

  public function add_query_vars($vars){
    $vars[] = '__api';
    $vars[] = 'instagram';
    return $vars;
  }

  public function add_endpoint() {
    add_rewrite_rule('^api/instagram/([^/]*)','index.php?__api=1&instagram=$matches[1]','top');
  }

  public function sniff_requests() {
    global $wp;
    if (isset($wp->query_vars['__api']) && isset($wp->query_vars['instagram'])) {
      $type = $wp->query_vars['instagram'];
      if (is_callable(array($this, 'handle_'.$type))) {
        $handler = 'handle_'.$type;
        $this->$handler();
      } else {
        $this->send_response('Something went wrong with the api');
      }
      exit;
    }
  }

  private function handle_subscription() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      Header('Content-Type: text/plain');
      $this->send_text('200 OK', $_GET['hub_challenge']);
    } else {
      wp_schedule_single_event(time(), 'degg_instagram_fetch', array($this->client_id, $this->tag));
      spawn_cron(time());
      $this->send_text('200 OK', "OK");
    }
  }

  private function send_text($status, $content) {
    if (preg_match('/^\d\d\d /', $status)) {
      Header("Status: $status");
    } else {
      Header("Status: 500");
    }

    echo $content;
  }

  public static function create($client_id, $tag) {
    return new static($client_id, $tag);
  }

}
