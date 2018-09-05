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
    return app('view')->render($view, $data);
  }
}

if (!function_exists('config')) {
  function config($key = null) {
    if (isset($key)) {
      return app($key);
    }
    return null;
  }
}

if (!function_exists('url')) {
  function url($path = '') {
    $uri = app('paths')['uri'];
    $path = trim($path, '\/');

    return $uri . ($path ? '/' . $path : $path);
  }
}

if (!function_exists('controller')) {
  function controller($controllerMethod, $params = []) {
    $controllerMethod = explode('@', $controllerMethod);

    $controller = 'Theme\\Controllers\\' . $controllerMethod[0];
    $method = $controllerMethod[1];

    return Application::getContainer()->call([$controller, $method], $params);
  }
}
