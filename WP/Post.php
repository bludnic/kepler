<?php

namespace Framework\WP;

use WP_Post;

class Post {
  public $id;
  public $title;
  public $content;
  public $excerpt;
  public $date;
  public $status;
  public $type;
  public $password;
  public $commentCount;

  private $post;

  /**
   * @param  id|WP_Post|null  $abstract
   */
  public function __construct($abstract = null) {
    $this->post = get_post($abstract);

    $this->id = $this->post->ID;
    $this->title = $this->post->post_title;
    $this->content = $this->post->post_content;
    $this->excerpt = $this->post->post_excerpt;
    $this->date = $this->post->post_date;
    $this->status = $this->post->post_status;
    $this->type = $this->post->post_type;
    $this->password = $this->post->post_password;
    $this->commentCount = $this->post->comment_count;
  }

  public function preview() {
    if (!empty($this->excerpt)) {
      return $this->excerpt;
    } else {
      return wp_trim_words($this->content, 55, '...');
    }
  }

  public function thumbnail() {
    //
  }
}
