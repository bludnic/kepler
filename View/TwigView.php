<?php

namespace Framework\View;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Function;
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

    $this->registerFunctions();
    $this->registerGlobals();
  }

  private function registerFunctions() {
    $wp_head = new Twig_Function('wp_head', 'wp_head');
    $wp_footer = new Twig_Function('wp_footer', 'wp_footer');

    $this->engine->addFunction($wp_head);
    $this->engine->addFunction($wp_footer);
  }

  private function registerGlobals() {
    $classes = get_body_class();
    $classesString = implode(' ', $classes);

    $this->engine->addGlobal('body_classes', $classesString);
  }

  public function render($template, $data = []) {
    return $this->engine->render($template, $data);
  }
}
