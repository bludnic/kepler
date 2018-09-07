<?php

namespace Framework\WP;

class Menu {
  private $slug;
  public $items;

  public function __construct($slug) {
    $this->slug = $slug;
    $this->items = $this->getMenuItems();
    $this->orderChildren();
  }

  /**
   * @return array MenuItem
   */
  private function getMenuItems() {
    $items = [];
    $wpItems = wp_get_nav_menu_items($this->slug);

    if (empty($wpItems)) return [];

    // Transform WP_Post in MenuItem instance
    foreach ($wpItems as $key => $item) {
      $items[] = new MenuItem($item);
    }

    return $items;
  }

  /**
   * @return void
   */
  private function orderChildren() {
    foreach($this->items as $key => $item) {
      if ($item->parent != 0) {
        $this->addChild($item);
        unset($this->items[$key]);
      }
    }
  }

  /**
   * @param  MenuItem  $item
   * @return void
   */
  private function addChild(MenuItem $child) {
    foreach ($this->items as $key => $item) {
      if ($item->id == $child->parent) {
        $this->items[$key]->children[] = $child;
      }
    }
  }
}
