<?php

namespace Kepler\View;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Function;
use Kepler\View\View;

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

  /**
   * Share WP functions to Twig.
   *
   * @return void
   */
  private function registerFunctions() {
    $functions = [
      // Theme functions
      'wp_head',
      'wp_footer',
      'language_attributes',
      'bloginfo',
      'dynamic_sidebar',

      // i18n functions
      '__',
      '_n',
      '_x',
      '_nx',
      'esc_html__',
      'esc_attr_x',
      'esc_html_x',
      '_e',
      '_ex',
      'esc_html_e'
    ];

    foreach ($functions as $function) {
      $this->engine->addFunction(new Twig_Function($function, $function));
    }
  }

  /**
   * Share global variables to Twig.
   *
   * @return void
   */
  private function registerGlobals() {
    // @TODO слишком рано запрашивается get_body_class(), тема еще не загружена
    // $classes = get_body_class();
    // $classesString = implode(' ', $classes);

    // $this->engine->addGlobal('body_classes', $classesString);
  }

  public function render($template, $data = []) {
    return $this->engine->render($template, $data);
  }

  /**
   * Helper method for share variables to Twig.
   *
   * @return void
   */
  public function share($key, $value) {
    $this->engine->addGlobal($key, $value);
  }

  /**
   * Helper method for share functions to Twig.
   *
   * @return void
   */
  public function define($name, $fn) {
    $this->engine->addFunction(new Twig_Function($name, $fn));
  }
}
