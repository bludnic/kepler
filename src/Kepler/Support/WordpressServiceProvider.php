<?php

namespace Kepler\Support;

use Kepler\Support\ServiceProvider;

class WordpressServiceProvider extends ServiceProvider {
  protected $singletons = [];
  protected $components = [];

  /**
   * Abstract.
   */
  public function register() {
    //
  }

  /**
   * Register wordpress components.
   *
   * @return void
   */
  public function boot() {
    $this->registerComponents();
  }

  /**
   * Register WordPress components.
   *
   * @return void
   */
  public function registerComponents() {
    foreach($this->components as $class) {
      $component = new $class;

      $component->register();
    }
  }

  /**
   * Create singletons.
   *
   * @return void
   */
  private function createSingletons() {
    foreach ($this->singletons as $key => $class) {
      $this->container->set($key, $this->container->make($class));
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
      new $class;
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

  /**
   * Register Administration Menus.
   *
   * @return void
   */
  private function registerAdminMenus() {
    foreach ($this->menus as $class) {
      new $class;
    }
  }

  /**
   * Register Taxonomies.
   *
   * @return void
   */
  private function registerTaxonomies() {
    foreach ($this->taxonomies as $class) {
      add_action('init', function () use ($class) {
        new $class;
      });
    }
  }

  /**
   * Autoload Classes.
   *
   * @return void
   */
  private function autoload() {
    foreach ($this->autoload as $class) {
      new $class;
    }
  }
}
