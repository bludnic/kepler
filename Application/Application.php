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
    $this->loadConfig();
    $this->autoload();
    $this->createContainer();

    $this->registerProviders();
    $this->createInstances();
    $this->bootProviders();
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
   * Create class instances.
   *
   * @return instance
   */
  public function createInstances() {
    $classes = [
      'sidebars',
      'metaboxes',
      'widgets',
      'navs',
      'assets',
      'supports'
    ];

    $container = static::$container;

    // Make objects
    foreach ($classes as $key) {
      $abstract = $container->get($key);

      if (is_array($abstract)) {
        foreach($abstract as $class) {
          if (class_exists($class)) {
            $container->make($class);
          }
        }
      } elseif (class_exists($abstract)) {
        $container->make($abstract);
      }
    }

    // We need customizer instance for access
    // $customizer->getOption method
    $customizer = $container->get('customizer');
    $container->set('customizer', $customizer);

    // Load Templates
    //$this->container->make($this->container->get('templates'));
  }

  /**
   * Register all providers.
   *
   * @return void
   */
  private function registerProviders() {
    $providers = static::$container->get('providers');

    if (is_array($providers)) {
      foreach ($providers as $class) {
        $provider = static::$container->make($class, [
          'container' => static::$container
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
