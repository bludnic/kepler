<?php

namespace Framework\View;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Framework\View\View;

class TwigView implements View {
  /**
   * @var $twig Twig_Environment
   */
  public $engine;

  /**
   * Create twig instance.
   *
   * @return $twig Twig_Environment
   */
  public function __construct($path = '') {
    $loader = new Twig_Loader_Filesystem($path);
    $this->engine = new Twig_Environment($loader);
  }

  public function render($template, $data = []) {
    return $this->engine->render($template, $data);
  }
}
