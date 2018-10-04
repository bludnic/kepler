<?php

namespace Kepler\WP;

/**
 * Register custom templates.
 * Useful for wp plugins.
 */
class Templates {
  /**
   * The array of templates that this plugin tracks.
   *
   * @var Array{string|string}
   */
  protected $templates = [];

  /**
   * Example: templates
   *
   * @var string
   */
  protected $templatesPath = 'templates/';

  /**
   * Base path of theme/plugin.
   *
   * @var string
   */
  protected $basePath;

  public function __construct() {
    $this->versionCompatibility();
    $this->addFilters();
  }

  /**
   * Register templates and ensure compatibility.
   *
   * @return void
   */
  private function versionCompatibility() {
    // Add a filter to the attributes metabox
    // to inject template into the cache.
    $isOldVersion = version_compare(floatval(get_bloginfo('version')), '4.7', '<' );

    if ($isOldVersion) {
      // 4.6 and older
      add_filter(
        'page_attributes_dropdown_pages_args',
        [$this, 'registerTemplates']
      );
    } else {
      // Add a filter to the wp 4.7 version
      // attributes metabox
      add_filter(
        'theme_page_templates',
        [$this, 'addTemplate']
      );
    }
  }

  /**
   * Append templates through a filter.
   *
   * @return void
   */
  private function addFilters() {
    // Add a filter to the save post to inject
    // out template into the page cache
    add_filter(
      'wp_insert_post_data', 
      [$this, 'registerTemplates']
    );

    // Add a filter to the template include to
    // determine if the page has our template
    // assigned and return it's path
    add_filter(
      'template_include', 
      [$this, 'viewTemplate']
    );
  }

  /**
   * Adds our template to the page dropdown for v4.7+.
   *
   * @param Array
   * @return Array | Array of templates
   */
  public function addTemplate($templates) {
    $templates = array_merge($templates, $this->templates);
    return $templates;
  }

  /**
   * Adds our template to the pages cache in order to
   * trick WordPress into thinking the template file
   * exists where it doens't really exist.
   *
   * @return Array
   */
  public function registerTemplates($atts) {
    // Create the key used for the themes cache
    $cacheKey = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

    // Retrieve the cache list. 
    // If it doesn't exist, or it's empty prepare
    // an array.
    $templates = wp_get_theme()->get_page_templates();
    if (empty($templates)) {
      $templates = [];
    } 

    // New cache, therefore remove the old one
    wp_cache_delete($cacheKey , 'themes');

    // Now add our template to the list of templates
    // by merging our templates with the existing
    // templates array from the cache.
    $templates = array_merge($templates, $this->templates);

    // Add the modified cache to allow WordPress to
    // pick it up for listing available templates.
    wp_cache_add($cacheKey, $templates, 'themes', 1800);

    return $atts;
  }

  /**
   * Checks if the template is assigned to the page
   *
   * @param  $template
   * @return $template
   */
  public function viewTemplate($template) {
    // Get global post
    global $post;

    // Return template if post is empty
    if (!$post) {
      return $template;
    }

    // Return default template if we don't have
    // a custom one defined
    $currentPostTemplate = get_post_meta($post->ID, '_wp_page_template', true);
    $templateObject = $this->templates[$currentPostTemplate];

    if (!isset($templateObject)) {
      return $template;
    } 

    $file = realpath($this->basePath . '/' . $this->templatesPath . $currentPostTemplate);

    // Just to be safe, we check if the file exist first
    if (file_exists($file)) {
      return $file;
    } else {
      // echo 'File does not exists';
      // echo $file;
    }

    // Return template
    return $template;
  }
}
