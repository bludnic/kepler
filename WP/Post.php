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
  public $authorId;

  private $post;

  /**
   * @param  id|WP_Post|null  $abstract
   */
  public function __construct($abstract = null) {
    $this->post = get_post($abstract);

    $this->id = $this->post->ID;
    $this->title = $this->post->post_title;
    // replace break line with <p>
    // https://wordpress.stackexchange.com/questions/165583/get-post-content-with-p-tags
    $this->content = apply_filters('the_content', $this->post->post_content);
    $this->excerpt = $this->post->post_excerpt;
    $this->date = $this->post->post_date;
    $this->status = $this->post->post_status;
    $this->type = $this->post->post_type;
    $this->password = $this->post->post_password;
    $this->commentCount = $this->post->comment_count;
    $this->authorId = $this->post->post_author;
  }

  public function link() {
    return get_permalink($this->id);
  }

  public function preview($words = 55) {
    if (!empty($this->excerpt)) {
      return $this->excerpt;
    } else {
      return wp_trim_words($this->content, $words, '...');
    }
  }

  public function date() {
    return get_the_date(null, $this->id);
  }

  public function thumbnail() {
    //
  }

  public function author() {
    return get_the_author_meta('display_name', $this->authorId);
  }
}
