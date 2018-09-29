<?php

namespace Framework\Support;

use Framework\Support\ServiceProvider;

class WordpressServiceProvider extends ServiceProvider {
  protected $posttypes = [];
  protected $metaboxes = [];
  protected $widgets = [];
  protected $sidebars = [];
  protected $navs = [];
  protected $shortcodes = [];
  protected $thumbnails = [];
  protected $assets = [];
  protected $supports = [];

  protected $singletons = [];

  /**
   * Abstract.
   */
  public function register() {
    //
  }

  /**
   * Register WordPress components.
   *
   * @return void
   */
  public function registerComponents() {
    $this->registerPosttypes();
    $this->registerMetaboxes();
    $this->registerWidgets();
    $this->registerSidebars();
    $this->registerNavs();
    $this->registerShortcodes();
    $this->registerThumbnails();
    $this->registerAssets();
    $this->registerSupports();

    $this->createSingletons();
  }

  /**
   * Create singletons.
   *
   * @return void
   */
  private function createSingletons() {
    foreach ($this->singletons as $key => $class) {
      $this->container->set($key, new $class);
    }
  } 

  /**
   * Register PostTypes.
   *
   * @return void
   */
  private function registerPosttypes() {
    foreach ($this->posttypes as $class) {
      add_action('init', function () use ($class) {
        new $class;
      });
    }
  }

  /**
   * Register Metaboxes.
   *
   * @return void
   */
  private function registerMetaboxes() {
    foreach ($this->metaboxes as $class) {
      add_action('add_meta_boxes', function () use ($class) {
        new $class;
      });
    }
  }

  /**
   * Register Widgets.
   *
   * @return void
   */
  private function registerWidgets() {
    foreach ($this->widgets as $class) {
      add_action('widgets_init', function () use ($class) {
        new $class;
      });
    }
  }

  /**
   * Register Sidebars.
   *
   * @return void
   */
  private function registerSidebars() {
    foreach ($this->sidebars as $class) {
      add_action('widgets_init', function () use ($class) {
        new $class;
      });
    }
  }

  /**
   * Register Navigation Menus.
   *
   * @return void
   */
  private function registerNavs() {
    foreach ($this->navs as $class) {
      add_action('init', function () use ($class) {
        new $class;
      });
    }
  }

  /**
   * Register Shortcodes.
   *
   * @return void
   */
  private function registerShortcodes() {
    //
  }

  /**
   * Register Thumbnails.
   *
   * @return void
   */
  private function registerThumbnails() {
    //
  }

  /**
   * Register Assets.
   *
   * @return void
   */
  private function registerAssets() {
    foreach ($this->assets as $class) {
      new $class;
    }
  }

  /**
   * Register Supports.
   *
   * @return void
   */
  private function registerSupports() {
    foreach ($this->supports as $class) {
      new $class;
    }
  }
}
