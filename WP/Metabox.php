<?php

namespace Kepler\WP;

use Rakit\Validation\Validator;

class Metabox {
  /**
   * View instance for render metabox template.
   *
   * @var Twig_Environment
   */
  //private $view;

  /**
   * Template name for render metabox.
   *
   * @var string
   */
  //private $templateName;

  /**
   * Meta box ID.
   *
   * Used in the 'id' attribute for the meta box.
   * Also for name in $_POST[$id] when send form.
   *
   * @var string
   */
  protected $id;

  /**
   * Title of the meta box.
   *
   * No need to call translate __().
   *
   * @var string
   */
  protected $title;

  /**
   * The screen or screens on which to show the box.
   *
   * Such as a post type, 'link', or 'comment'. Accepts 
   * a single screen ID, WP_Screen object, or array of 
   * screen IDs. Default is the current screen. If you 
   * have used add_menu_page() or add_submenu_page() to
   * create a new screen (and hence screen_id), make 
   * sure your menu slug conforms to the limits of 
   * sanitize_key() otherwise the 'screen' menu may not 
   * correctly render on your page.
   *
   * @var string|array|WP_Screen
   */
  protected $screen;

  /**
   * The context within the screen where the boxes should display.
   *
   * Available contexts vary from screen to screen. Post 
   * edit screen contexts include 'normal', 'side', and 
   * 'advanced'. Comments screen contexts include 'normal' 
   * and 'side'. Menus meta boxes (accordion sections) all 
   * use the 'side' context.
   *
   * @var string
   */
  protected $context = 'advanced';

  /**
   * The priority within the context where the boxes should 
   * show ('high', 'low').
   *
   * @var string
   */
  protected $priority;

  /**
   * Data that should be set as the $args property of the 
   * box array (which is the second parameter passed to 
   * your callback).
   *
   * @var string
   */
  protected $args;

  /**
   * Validation rules.
   *
   * @var Array
   */
  protected $rules = [];

  /**
   * Create new metabox.
   *
   * @param  $view  View
   * @param  $config  Config
   * @param  $options  Array | Metabox options.
   */
  public function __construct() {
    if (is_admin()) {
      add_action('load-post.php', [$this, 'init']);
      add_action('load-post-new.php', [$this, 'init']);
    }
  }

  /**
   * Meta box initialization.
   */
  public function init() {
    add_action('add_meta_boxes', [$this, 'register']);
    add_action('save_post', [$this, 'presave'], 10, 2);
  }

  /**
   * Register metabox.
   *
   * @return void
   */
  public function register() {
    add_meta_box(
      $this->id,
      __($this->title, 'textdomain'), // @TODO config('textdomain')
      [$this, 'prerender'],
      $this->screen,
      $this->context
    );
  }

  /**
   * Render first wp_nonce_field input.
   *
   * @return void
   */
  public function prerender($post) {
    wp_nonce_field($this->id . '_nonce_action', $this->id . '_nonce');

    $this->render($post);
  }

  /**
   * Render metabox.
   *
   * @return void
   */
  public function render($post) {}

  /**
   * First call validate(), and then save()
   *
   * @param  $id  int
   * @param  $post  WP_Post
   * @return void
   */
  public function presave($id, $post) {
    $data = $_POST[$this->id];

    if (empty($data)) {
      $data = [];
    }

    // Verify nonce & permissions.
    if ($this->verify($id)) {
      $this->validate($data);
      $this->save($id, $data, $post);
    }
  }

  /**
   * Verify nonce, verify user permissions,
   * check autosave, check revision.
   *
   * @param  $id | Post ID
   * @return boolean
   */
  private function verify($id) {
    $nonce = $this->verifyNonce();
    $permissions = $this->verifyPermissions($id);
    $autosave = $this->verifyIsNotAutosave($id);
    $revision = $this->verifyIsNotRevision($id);

    if ($nonce && $permissions && $autosave && $revision) {
      return true;
    }

    return false;
  }

  /**
   * Verify nonce field.
   *
   * @return boolean
   */
  private function verifyNonce() {
    $name = $_POST[$this->id . '_nonce'];
    $action = $this->id . '_nonce_action';

    if (isset($name) && wp_verify_nonce($name, $action)) {
      return true;
    }

    return false;
  }

  /**
   * Verify permissions.
   *
   * @param  $id | Post ID
   * @return boolean
   */
  private function verifyPermissions($id) {
    if (current_user_can('edit_post', $id)) {
      return true;
    }
    return false;
  }

  /**
   * Check if not an autosave.
   *
   * @param  $id | Post ID
   * @return boolean
   */
  private function verifyIsNotAutosave($id) {
    if (!wp_is_post_autosave($id)) {
      return true;
    }
    return false;
  }

  /**
   * Check if not a revision.
   *
   * @param  $id | Post ID
   * @return boolean
   */
  private function verifyIsNotRevision($id) {
    if (!wp_is_post_revision($id)) {
      return true;
    }
    return false;
  }

  /**
   * Validate metabox data.
   *
   * Check if nonce is valid.
   * Check if user has permissions to save data.
   * Check if not an autosave.
   * Check if not a revision.
   *
   * @param  $post  WP_Post
   * @return void
   */
  private function validate($data) {
    $validator = new Validator;

    $validation = $validator->make($data, $this->rules);

    $validation->validate();

    if ($validation->fails()) {
      $errors = $validation->errors();
      echo "<pre>";
      print_r($errors->firstOfAll());
      echo "</pre>";
      exit;
    }
  }

  /**
   * This method is called when "save post" or "publish".
   *
   * @return void
   */
  public function save($id, $data, $post) {}
}
