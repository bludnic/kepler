<?php

namespace Framework\Application;

use DI\ContainerBuilder;
use DI\Container;

class Application {
  /**
   * The Framework version.
   *
   * @var string
   */
  const VERSION = '0.0.1';

  /**
   * The baase path of the Theme.
   * @var string
   */
  protected $basePath;

  /**
   * The custom environment path defined by the developer.
   *
   * @var string
   */
  protected $environmentPath;

  /**
   * The environment file to load during bootstrapping.
   *
   * @var string
   */
  protected $environmentFile = '.env';

  /**
   * The current globally available container (if any).
   *
   * @var static
   */
  protected static $instance;

  /**
   * @var DI\Container
   */
  protected static $container;

  /**
   * @var DI\ContainerBuilder
   */
  private $builder;

  /**
   * Create a new Application instance.
   *
   * @param  string|null  $basePath
   * @return void
   */
  public function __construct($basePath = null) {
    if ($basePath) {
      $this->setBasePath($basePath);
    }

    $this->createBuilder();
    $this->loadConfig();
    $this->autoload();
    $this->createContainer();

    $this->makeObjects();
    // $this->registerBaseBindings();
  }

  /**
   * Get the version number of the appliction.
   *
   * @return string
   */
  public function version() {
    return static::VERSION;
  }

  /**
   * Register the basic bindings into the container.
   *
   * @return void
   */
  protected function registerBaseBindings() {
    static::setInstance($this);
  }

  /**
   * Load config from config/app.php.
   *
   * @return void
   */
  protected function loadConfig() {
    $this->builder->addDefinitions($this->basePath() . '/config/app.php');
  }

  /**
   * Autoload metaboxes, taxonomies,
   * posttypes, widgets, sidebars 
   * from config/app.php.
   *
   * @return void
   */
  protected function autoload() {
    $this->builder->addDefinitions($this->basePath() . '/config/autoload.php');
  }

  /**
   * Set the base path for the application.
   *
   * @param  string  $basePath
   * @return $this
   */
  public function setBasePath($basePath) {
    $this->basePath = rtrim($basePath, '\/');

    // $this->bindPathsInContainer();

    return $this;
  }

  /**
   * Bind all of the application paths in the container.
   *
   * @return void
   */
  protected function bindPathsInContainer() {
    $this->instance('path', $this->path());
    $this->instance('path.base', $this->basePath());
    $this->instance('path.lang', $this->langPath());
    $this->instance('path.config', $this->configPath());
    $this->instance('path.public', $this->publicPath());
    $this->instance('path.resources', $this->resourcePath());
    $this->instance('path.bootstrap', $this->bootstrapPath());
  }

  /**
   * Get the path to the theme "app" directory.
   *
   * @param  string  $path
   * @return string
   */
  public function path($path = '') {
    return $this->basePath . '/app' . ($path ? '/' . $path : $path);
  }

  /**
   * Get the base path of the theme.
   *
   * @param  string  $path
   * @return string
   */
  public function basePath($path = '') {
    return $this->basePath . ($path ? '/' . $path : $path);
  }

  /**
   * Get the path to the resources directory.
   *
   * @param  string  $path
   * @return string 
   */
  public function resourcePath($path = '') {
    return $this->basePath . '/resources' . ($path ? '/' . $path : $path);
  }

  /**
   * Get the path to the templates.
   *
   * @param  string  $path
   * @return string 
   */
  public function templatesPath() {
    return $this->resourcePath() . '/templates';
  }

  /**
   * Get the path to the language files.
   *
   * @return string
   */
  public function langPath() {
    return $this->resourcePath() . '/languages';
  }

  /**
   * Get the path to the public / web directory.
   *
   * @return string
   */
  public function publicPath() {
    return $this->basePath . '/public';
  }

  /**
   * Get the path to the application configuration files.
   *
   * @param  string  $path
   * @return string
   */
  public function configPath($path = '') {
    return $this->basePath . '/config' . ($path ? '/' . $path : $path);
  }

  /**
   * Get the path to the bootstrap directory.
   *
   * @param  string  $path
   * @return string
   */
  public function bootstrapPath($path = '') {
    return $this->basePath . '/bootstrap' . ($path ? '/' . $path : $path);
  }

  /**
   * Set the shared instance of the container.
   *
   * @param  \DI\ContainerBuilder|null  $container
   * @return static
   */
  public static function setInstance($container = null) {
    return static::$instance = $container;
  }

  /**
   * Set the globally available instance of the container.
   *
   * @return Application
   */
  public static function getInstance(): Application {
    if (is_null(static::$instance)) {
      static::$instance = new static;
    }
    return static::$instance;
  }

  public static function getContainer(): Container {
    return static::$container;
  }

  /**
   * Create ContainerBuilder.
   *
   * @return void
   */
  private function createBuilder() {
    $this->builder = new ContainerBuilder();
  }

  /**
   * Create Container.
   *
   * @return void
   */
  private function createContainer() {
    static::$container = $this->builder->build();
  }

  /**
   * Make objects.
   *
   * @return instance
   */
  public function makeObjects() {
    // Load Sidebars
    foreach (static::$container->get('sidebars') as $key => $value) {
      static::$container->make($value);
    }

    // Load Metaboxes
    foreach (static::$container->get('metaboxes') as $key => $value) {
      static::$container->make($value);
    }

    // Load Widgets
    foreach (static::$container->get('widgets') as $key => $value) {
      static::$container->make($value);
    }

    // Load Templates
    //$this->container->make($this->container->get('templates'));
  }
}