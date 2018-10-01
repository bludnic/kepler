<?php

namespace Framework\WP;

class AdminMenu {
  /**
   * Page Title
   *
   * The text to be displayed in the title tags of the page
   * when the menu is selected.
   *
   * @var string
   */
  public $pageTitle = '';

  /**
   * Menu Title
   *
   * The text to be used for the menu.
   *
   * @var string
   */
  public $menuTitle;

  /**
   * The capability required for this menu to be displayed
   * to the user.
   *
   * @var string
   */
  public $capability = 'manage_options';

  /**
   * The slug name to refer to this menu.
   *
   * @var string
   */
  public $slug;

  /**
   * The URL to the icon to be used for this menu.
   *
   * - Pass a base64-encoded SVG using a data URI, which will
   *   be colored to match the color scheme. This should begin
   *   with 'data:image/svg+xml;base64,'.
   *
   * - Pass the name of a Dashicons helper class to use a font
   *   icon, e.g. 'dashicons-chart-pie'.
   *
   * - Pass 'none' to leave div.wp-menu-image empty so an icon
   *   can be added via CSS.
   *
   * @var string
   */
  public $icon = '';

  /**
   * The position in the menu order this one should appear.
   *
   * @var int
   */
  public $position;

  public function __construct() {
    add_action('admin_menu', [$this, 'register']);
  }

  /**
   * Register menu callback.
   *
   * @return void
   */
  public function register() {
    add_menu_page(
      $this->pageTitle,
      $this->menuTitle,
      $this->capability,
      $this->slug,
      [$this, 'render'],
      $this->icon,
      $this->position
    );
  }

  /**
   * The function to be called to output the content for
   * this page.
   *
   * @return void
   */
  public function render() {}
}
