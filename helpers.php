<?php

use Framework\Application\Application;

if (!function_exists('app')) {
  /**
   * Get the available application instance.
   *
   * @param  string  $abstract
   * @param  array   $parameters
   * @return mixed|\Framework\Application\Application
   */
    function app($abstract = null, array $parameters = []) {
      if (is_null($abstract)) {
        return Application::getContainer();
      }

      return Application::getContainer()->get($abstract, $parameters);
    }
}

if (!function_exists('view')) {
  function view($view = null, $data = []) {
    if (is_null($view)) {
      return app('view');
    }
    return app('view')->render($view, $data);
  }
}

if (!function_exists('url')) {
  function url($path = '') {
    $uri = app('paths')['uri'];
    $path = trim($path, '\/');

    return $uri . ($path ? '/' . $path : $path);
  }
}
