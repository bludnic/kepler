<?php

namespace Framework\WP;

use IteratorAggregate;
use ArrayIterator;
use Traversable;

class Pagination implements IteratorAggregate {
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

  /**
   * Default pagination options.
   *
   * @var array
   *
   * $options = [
   *   'leftSize' => 3,
   *   'rightSize' => 3,
   *   'showFirst' => true,
   *   'showLast' => true,
   *   'showPrevious' => true,
   *   'showNext' => true,
   *   'showDots' => true,
   * ]
   */
  public $options;

  public function __construct($options = []) {
    $this->setOptions($options);
    $this->setPaginationProperties();
    $this->createPagination();
  }

  /**
   * Merge client options with defaults.
   *
   * @return void
   */
  public function setOptions($options) {
    $defaults = [
      'leftSize' => 3,
      'rightSize' => 3,
      'showFirst' => true,
      'showLast' => true,
      'showPrevious' => true,
      'showNext' => true,
      'showDots' => true
    ];

    $this->options = array_merge($defaults, $options);
  }

  /**
   * Set class properties.
   *
   * $this->max, $this->page, $this->prev, $this->next
   *
   * @return void
   */
  public function setPaginationProperties() {
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

  /**
   * Create pagination and set to $this->pages.
   *
   * @return void
   */
  public function createPagination() {
    $numOfPages = $this->options['leftSize'] + $this->options['rightSize'];

    // If too few pages, show all
    if ($this->max < $numOfPages) {
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
      return;
    }

    // Left Size
    for ($i = 1; $i <= $this->options['leftSize']; $i++) {
      $page = [
        'title' => $i,
        'link' => get_pagenum_link($i)
      ];

      if ($i == $this->page) {
        $page['current'] = true;
      }

      $this->pages[] = $page;
    }

    // Dots
    $this->pages[] = [
      'title' => '...',
      'dots' => true
    ];

    // Right size
    for ($i = $this->max - $this->options['rightSize'] + 1; $i <= $this->max; $i++) {
      $page = [
        'title' => $i,
        'link' => get_pagenum_link($i)
      ];

      if ($i == $this->page) {
        $page['current'] = true;
      }

      $this->pages[] = $page;
    }
  } 

  /**
   * IteratorAggregate method.
   *
   * @return void
   */
  public function set($key, $val){
    $this->pages[$key] = $val;
  }

  /**
   * IteratorAggregate method.
   *
   * @return array
   */
  public function get($key){
    return $this->pages[$key];
  }

  /**
   * IteratorAggregate method.
   *
   * @return array
   */
  public function getIterator(): Traversable {
    return new ArrayIterator($this->pages);
  }
}