<?php

namespace Kepler\Application;

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
   * The baase path of the Theme / Plugin.
   *
   * @var string
   */
  protected $basePath;

  /**
   * @var Kepler\Application\Application
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
   * List of providers instance.
   *
   * We save providers instance for
   * call $provider->boot() after app
   * ready.
   */
  private $providers = [];

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
    $this->loadConfigDefinitions();
    $this->createContainer();

    $this->registerProviders();
    $this->createInstances();
    $this->bootProviders();
    $this->registerBaseBindings();

    // $this->initWordpressEntities();
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
  protected function loadConfigDefinitions() {
    $this->builder->addDefinitions($this->basePath() . '/config/app.php');

    do_action('theme_container_add_definitions', $this->builder);
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
   * Set the shared instance of the application.
   *
   * @param  \Framework\Application|null  $app
   * @return static
   */
  public static function setInstance($app = null) {
    return static::$instance = $app;
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
   * Create class instances.
   *
   * @return instance
   */
  public function createInstances() {
    $container = static::$container;

    // Make objects
    if ($container->has('wordpress')) {
      $autoload = $container->get('wordpress');
      foreach ($autoload as $abstract) {
        if (is_array($abstract)) {
          foreach($abstract as $class) {
            if (class_exists($class)) {
              $container->make($class);
            }
          }
        } elseif (is_string($abstract) && class_exists($abstract)) {
          $container->make($abstract);
        }
      }
    }
  }

  /**
   * Register all providers.
   *
   * @return void
   */
  private function registerProviders() {
    $container = static::$container;

    // Check if key `providers` isset
    if (!$container->has('providers')) {
      return;
    }

    $providers = $container->get('providers');

    if (is_array($providers)) {
      foreach ($providers as $class) {
        $provider = $container->make($class, [
          'container' => $container
        ]);

        $provider->register();

        $this->providers[] = $provider;
      }
    }
  }

  /**
   * Boot ServiceProviders after register all.
   *
   * @return void
   */
  private function bootProviders() {
    foreach ($this->providers as $provider) {
      $provider->boot();
    }
  }
}
