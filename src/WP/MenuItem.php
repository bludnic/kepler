<?php

namespace Kepler\WP;

use WP_Post;

class MenuItem {
  public $id;
  public $title;
  public $link;
  public $parent;
  public $order;
  public $children = [];

  public function __construct(WP_Post $menuItem) {
    $this->id = $menuItem->ID;
    $this->title = $menuItem->title;
    $this->link = $menuItem->url;
    $this->parent = $menuItem->menu_item_parent;
    $this->order = $menuItem->menu_order;
  }

  public function hasChildren() {
    return !empty($this->children);
  }
}
