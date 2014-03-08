<?php

namespace Degg\Instagram;

class Setup {

  public function __construct($client_id, $tag) {
    PostType::setup();
    Fetch::setup();
    APIEndpoint::create($client_id, $tag);
  }

  public static function setup($client_id, $tag) {
    return new static($client_id, $tag);
  }

}
