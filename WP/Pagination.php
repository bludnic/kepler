<?php

namespace Kepler\WP;

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
    // If too few pages, show all
    $leftPlusRight = $this->options['leftSize'] + $this->options['rightSize'];
    if ($this->max < $leftPlusRight) {
      $this->eachPages(1, $this->max);
      return;
    }

    // If current page is near $this->max
    $nearRight = $this->max - $this->options['leftSize'];
    if ($this->page >= $nearRight) {
      $this->eachPages($nearRight, $this->max);
      return;
    }

    // Left Size
    $leftLeftSize = floor($this->options['leftSize'] / 2);
    $startPage = 1;

    if ($this->page > $leftLeftSize) {
      $startPage = $this->page - $leftLeftSize;
    }

    $this->eachPages($startPage, $startPage + $this->options['leftSize'] - 1);

    // Dots
    $this->pages[] = [
      'title' => '...',
      'dots' => true
    ];

    // Right size
    $this->eachPages($this->max - $this->options['rightSize'] + 1, $this->max);
  }

  private function eachPages($start, $end) {
    for ($i = $start; $i <= $end; $i++) {
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