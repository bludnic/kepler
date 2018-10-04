<?php

namespace Kepler\View;

use Kepler\Support\ServiceProvider;
use Kepler\View\TwigView;

class ViewServiceProvider extends ServiceProvider {
  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    $this->registerTwigEngine();
  }

  public function registerTwigEngine() {
    $container = $this->container;

    $container->set('view', function ($container) {
      $path = $container->get('paths')['directory'] . $container->get('directories')['templates'];
      $cache = $container->get('paths')['tmp'];

      return new TwigView($path, $cache);
    });
  }
}