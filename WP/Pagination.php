<?php

namespace Framework\WP;

class Pagination {
  public $pages = [];

  /**
   * Max number of pages.
   *
   * @var int
   */
  public $max;

  /**
   * Current page number.
   *
   * @var int
   */
  public $page;

  /**
   * Previous page.
   */
  public $prev;

  /**
   * Next page.
   */
  public $next;

  public function __construct() {
    $this->getPagination();
    $this->createPages();
  }

  public function getPagination() {
    global $wp_query;
    $this->max = $wp_query->max_num_pages;
    $this->page = (get_query_var('paged') >= 1) ? get_query_var('paged') : 1;

    if ($this->page > 1) {
      $prev = $this->page - 1;
      $this->prev = [
        'title' => $prev,
        'link' => get_pagenum_link($prev)
      ];
    }
    if ($this->page < $this->max) {
      $next = $this->page + 1;
      $this->next = [
        'title' => $next,
        'link' => get_pagenum_link($next)
      ];
    }
  }

  public function createPages() {
    // If only one page, return
    if ($this->max <= 1) return;

    for ($i = 1; $i <= $this->max; $i++) {
      $page = [
        'title' => $i,
        'link' => get_pagenum_link($i)
      ];

      // If is current
      if ($i == $this->page) {
        $page['current'] = true;
      }

      $this->pages[] = $page;
    }
  }
}