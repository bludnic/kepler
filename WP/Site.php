<?php

namespace Framework\WP;

class Site {
  /**
   * Gets the language attributes for the html tag.
   *
   * Builds up a set of html attributes containing the
   * text direction and language information for the
   * page.
   *
   * @var string
   */
  public $languageAttributes;

  /**
   * Document encoding.
   *
   * Displays the “Encoding for pages and feeds” set
   * in Settings > Reading. This data is retrieved from
   * the “blog_charset” record in the wp_options table.
   *
   * @var string
   */
  public $charset;

  /**
   * Display the classes for the body element.
   *
   * @var string
   */
  public $bodyClass;

  /**
   * Website home URL.
   *
   * @var string
   */
  public $url;

  public function __construct() {
    $this->setBaseProperties();
  }

  /**
   * Retrieves information about the current site.
   *
   * @var string
   */
  public function info($key) {
    return get_bloginfo($key);
  }

  /**
   * Set base properties.
   *
   * @return void
   */
  private function setBaseProperties() {
    $this->languageAttributes = get_language_attributes();
    $this->charset = get_bloginfo('charset');
    $this->bodyClass = get_body_class();
    $this->url = get_home_url();
  }
}
