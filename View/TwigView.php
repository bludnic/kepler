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
    do_action('twig_setup_loader', $loader);
    $this->engine = new Twig_Environment($loader);

    $this->registerFunctions();
    $this->registerGlobals();
  }

  private function registerFunctions() {
    $wp_head = new Twig_Function('wp_head', 'wp_head');
    $wp_footer = new Twig_Function('wp_footer', 'wp_footer');

    $this->engine->addFunction($wp_head);
    $this->engine->addFunction($wp_footer);
    $this->engine->addFunction(new Twig_Function('__', '__'));
    $this->engine->addFunction(new Twig_Function('_n', '_n'));
    $this->engine->addFunction(new Twig_Function('language_attributes', 'language_attributes'));
    $this->engine->addFunction(new Twig_Function('bloginfo', 'bloginfo'));
    $this->engine->addFunction(new Twig_Function('dynamic_sidebar', 'dynamic_sidebar'));
  }

  private function registerGlobals() {
    $classes = get_body_class();
    $classesString = implode(' ', $classes);

    $this->engine->addGlobal('body_classes', $classesString);
  }

  public function render($template, $data = []) {
    return $this->engine->render($template, $data);
  }

  /**
   * Share objects in Twig.
   *
   * @return void
   */
  public function share($key, $value) {
    $this->engine->addGlobal($key, $value);
  }

  /**
   * Define functions in twig.
   *
   * @return void
   */
  public function define($name, $fn) {
    $this->engine->addFunction(new Twig_Function($name, $fn));
  }
}
