<?php

namespace Neurox\Mfrwrk\Core;

use Twig\Environment;

/**
 * Base controller class with common functionality for all controllers.
 */
abstract class BaseController {

  /**
   * The Twig instance.
   *
   * @var \Twig\Environment|null
   *   The Twig instance.
   */
  protected static ?Environment $twig = NULL;

  /**
   * Initialize the controller.
   */
  public static function init() {
    // Get Twig instance.
    self::$twig = \Flight::get('twig');
  }

  /**
   * Render a Twig template with the given data.
   *
   * @param string $template
   *   The template to render.
   * @param array $data
   *   The data to pass to the template.
   *
   * @return void
   *   Renders the template.
   */
  protected static function render($template, array $data = []) : void {
    // Initialize if not already done.
    if (self::$twig === NULL) {
      self::init();
    }

    echo self::$twig->render($template, $data);
  }

  /**
   * Redirect to the given URL.
   *
   * @param string $url
   *   The URL to redirect to.
   * @param int $status
   *   The HTTP status code (default: 302).
   *
   * @return void
   *   Redirects to the given URL.
   */
  protected static function redirect($url, $status = 302) {
    \Flight::redirect($url, $status);
  }

  /**
   * Get request data.
   *
   * @param string $key
   *   The data key to get (optional).
   * @param mixed $default
   *   Default value if key not found.
   *
   * @return mixed
   *   Request data.
   */
  protected static function input($key = NULL, $default = NULL) {
    $request = \Flight::request();

    if ($key === NULL) {
      return (object) array_merge(
        (array) $request->query,
        (array) $request->data
      );
    }

    return $request->data->$key ?? $request->query->$key ?? $default;
  }

  /**
   * Get session data or set session data.
   *
   * @param string $key
   *   The session key.
   * @param mixed $value
   *   The value to set (optional).
   *
   * @return mixed
   *   Session data if getting, or null if setting
   */
  protected static function session($key, $value = NULL) {
    // No need to start the session here anymore.
    if ($value !== NULL) {
      $_SESSION[$key] = $value;
      return NULL;
    }

    return $_SESSION[$key] ?? NULL;
  }

}
