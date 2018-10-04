<?php

namespace Kepler\WP;

class TemplateFilter {
  /**
   * The container instance.
   *
   * @var \DI\Container
   */
  protected $container;

  /**
   * Path to templates.
   *
   * @var string
   */
  protected $path;

  /**
   * Create a new service provider instance.
   *
   * @param \Application\Application $app
   * @return void
   */
  public function __construct($container) {
    $this->container = $container;

    $this->path = $this->container->get('paths')['directory'] . $this->container->get('directories')['wptemplates'];
  }

  /**
   * The "single_template" filter can be used
   * to load a custom template for a given post.
   * It will replace the template used whenever
   * the "single" template is called.
   *
   * @param string $postType
   * @param string $fileName
   * @param string $path Path to the WP templates inside plugin
   * @return void
   */
  public function single($postType, $fileName) {
    add_filter('single_template', function ($templates) use ($postType, $fileName, $path) {
      global $post;

      if ($post->post_type == $postType) {
        return $this->path . '/' . $fileName;
      }

      return $template;
    });
  }
}