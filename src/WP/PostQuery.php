<?php

namespace Kepler\WP;

class PostQuery {
  public $items = [];

  public function __construct($args = null) {
    $this->getPosts($args);
  }

  private function getPosts($args) {
    global $wp_query;
    $items = [];

    if (is_null($args)) {
      $items = $wp_query->posts;
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
