<?php

namespace Kepler\WP;

class Customizer {
  /**
   * Prefix for options.
   *
   * @var string
   */
  protected $prefix = 'theme';

  /**
   * List of sections with options.
   *
   * @var array
   */
  protected $sections = [];

  /**
   * List of options after theme setup.
   *
   * @var array
   */
  public $options;

  public function __construct() {
    $this->registerSections();
    add_action('customize_register', [$this, 'register']);
    add_action('wp_head', [$this, 'render']);
    add_action('after_setup_theme', [$this, 'getOptions']);
  }

  /**
   * Register customizer options.
   *
   */
  public function register() {
    global $wp_customize;

    // Register sections
    foreach ($this->sections as $section) {
      $sectionSlug = $this->prefix . '_' . $section['id'];

      $wp_customize->add_section($sectionSlug, [
        'title' => $section['name']
      ]);

      // Register options
      foreach ($section['options'] as $option) {
        $optionSlug = $this->prefix . '_' . $section['id'] . '_' . $option['id'];

        $setting = [
          'default' => $option['default'],
          'transport' => $option['transport']
        ];
        if (isset($option['sanitize'])) {
          $setting['sanitize_callback'] = [$this, $option['sanitize']];
        }
        $wp_customize->add_setting($optionSlug, $setting);

        if ($option['type'] === 'color') {
          $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, $optionSlug, [
            'label' => $option['name'],
            'section' => $sectionSlug,
            'settings' => $optionSlug
          ]));
        } elseif ($option['type'] === 'select') {
          $wp_customize->add_control(new \WP_Customize_Control($wp_customize, $optionSlug, [
            'label' => $option['name'],
            'section' => $sectionSlug,
            'settings' => $optionSlug,
            'type' => $option['type'],
            'choices' => $option['choices']
          ]));
        } else {
          $wp_customize->add_control(new \WP_Customize_Control($wp_customize, $optionSlug, [
            'label' => $option['name'],
            'section' => $sectionSlug,
            'settings' => $optionSlug,
            'type' => $option['type']
          ]));
        }
      }
    }
  }

  /**
   * Get customizer options.
   *
   * @return void
   */
  public function getOptions() {
    global $wp_customize;

    $options = [];
    $isCustomizer = isset($wp_customize);

    foreach ($this->sections as $section) {
      foreach ($section['options'] as $option) {
        $optionSlug = $this->prefix . '_' . $section['id'] . '_' . $option['id'];

        // If editing in customizer, then get options from live.
        $options[$optionSlug] = get_theme_mod($optionSlug, $option['default']);
      }
    }

    $this->options = $options;
  }

  /**
   * Get customizer option.
   *
   * @return string
   */
  public function getOption($abstract) {
    $sectionOption = explode('.', $abstract);
    $section = $sectionOption[0];
    $option = $sectionOption[1];

    $key = $this->prefix . '_' . $section . '_' . $option;
    $value = $this->options[$key];

    if (isset($value)) {
      return $value;
    }

    return null;
  }

  /**
   * Called from child class.
   *
   * @return void
   */
  protected function registerSections() {}
  protected function renderStyles() {}

  /**
   * Called from child class.
   *
   * @return void
   */
  public function render() {
    //@TODO validation and get settings
    $this->renderStyles();
  }
}