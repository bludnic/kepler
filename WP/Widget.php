<?php

namespace Kepler\WP;

use WP_Widget;

class Widget extends WP_Widget {
  /**
   * Widget unique identifier.
   *
   * @var string
   */
  public $id;

  /**
   * Widget name.
   *
   * @var string
   */
  public $name;

  /**
   * Widget description.
   *
   * @var string
   */
  public $description;

  /**
   * Append class to widget.
   *
   * @var string
   */
  public $classname;

  public function __construct() {
    $options = [];

    if (isset($this->classname)) {
      $options['classname'] = $this->classname;
    }

    if (isset($this->description)) {
      $options['description'] = $this->description;
    }

    parent::__construct($this->id, $this->name, $options);

    add_action('widgets_init', function () {
      register_widget(get_class($this));
    });
  }

  /**
   * Render widget.
   *
   * @param  $args  Array
   * @param  $instance  Array
   * @return void
   */
  public function widget($args, $instance) {
    $this->render($args, $instance);
  }

  public function render($args, $instance) {}

  /**
   * This method is called when "save post" or "publish".
   *
   * @param  $newInstance  Array
   * @param  $oldInstance  Array
   * @return void
   */
  public function update($newInstance, $oldInstance) {
    return $newInstance;
  }

  /**
   * Render widget form.
   *
   * @param  $instance  Array
   */
  public function form($instance) {
    // @TODO
    // Создать свойство $this->fields
    // где задается ID, name, sanitize_callback, type

    $data = [
      'title' => [
        'id' => $this->get_field_id('title'),
        'name' => $this->get_field_name('title'),
        'value' => isset($instance['title']) ? $instance['title'] : ''
      ]
    ];

    $this->edit($data);
  }

  /**
   * Render from.
   *
   * @return void
   */
  public function edit($data) {}
}