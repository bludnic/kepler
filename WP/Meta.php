<?php

namespace Framework\WP;

class Meta {
  /**
   * Retrieve post meta field for a post.
   *
   * Will be an array if $single is false. Will be 
   * value of meta data field if $single is true.
   *
   * @param int $postId
   * @param string $key
   * @param boolean $single
   * @param boolean $decode Decode JSON string.
   * @return array|object|string
   */
  public static function get($postId = null, $key = '', $single = true, $decode = true) {
    $id = !is_null($postId) ? $postId : get_the_ID();

    $meta = get_post_meta($id, $key, $single);

    if ($decode) {
      $meta = json_decode($meta, true);
    }

    return $meta;
  }

  /**
   * Update post meta field based on post ID.
   *
   * Use the $prev_value parameter to differentiate between
   * meta fields with the same key and post ID. If the meta
   * field for the post does not exist, it will be added.
   *
   * @param int $postId
   * @param string $key
   * @param mixed $value
   * @param mixed $prevValue
   * @param boolean $encode Encode in JSON.
   * @return void
   */
  public static function set($postId = null, $key, $value, $prevValue = '', $encode = true) {
    $id = !is_null($postId) ? $postId : get_the_ID();

    if ($encode) {
      $value = json_encode($value, JSON_HEX_APOS);
    }

    update_post_meta($id, $key, $value, $prevValue);
  }
}
