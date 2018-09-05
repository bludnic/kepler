<?php

namespace Framework\Support;

abstract class ServiceProvider {
  /**
   * The container instance.
   *
   * @var \DI\Container
   */
  protected $container;

  /**
   * Create a new service provider instance.
   *
   * @param  \Application\Application  $app
   * @return void
   */
  public function __construct($container) {
    $this->container = $container;
  }

  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot() {}

  /**
   * Register the service provider.
   *
   * @return void
   */
  abstract public function register();
}