<?php

namespace Framework\WP;

class PostQuery {
  public $items = [];

  public function __construct($args = null) {
    $this->getPosts($args);
  }

  private function getPosts($args) {
    $items = [];
    if (is_null($args)) {
      $items = get_posts();
    } else {
      $items = get_posts($args);
    }

    // Transform WP_Post in Post
    foreach ($items as $key => $item) {
      $this->items[] = new Post($item);
    }
  }

  public function preview() {
    //
  }

  public function thumbnail() {
    //
  }
}
