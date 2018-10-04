<?php

namespace Kepler\WP;

class Menu {
  private $slug;
  public $items;

  public function __construct($slug) {
    $this->slug = $slug;
    
    $items = $this->getMenuItems();
    $itemsTree = $this->buildMenuItemsTree($items);

    $this->items = $itemsTree;
  }

  /**
   * Get navigation MenuItems. 
   *
   * @return array
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
   * Build MenuItems tree.
   *
   * @return array
   */
  private function buildMenuItemsTree($items) {
    $children = [];

    // Indexing items by parent key
    foreach ($items as $item) {
      $children[$item->parent][] = $item;
    }

    // Looping through each MenuItem again, adding itself
    // to its parent's MenuItem list. The reference is
    // important here.
    foreach ($items as $item) {
      if (isset($children[$item->id])) {
        $item->children = $children[$item->id];
      }
    }

    // Return tree
    return $children[0];
  }
}
