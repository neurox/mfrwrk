<?php

namespace Core;

use Flight;

/**
 * Base controller class with common functionality for all controllers.
 */
abstract class BaseController {

  /**
   * The Twig instance.
   *
   * @var \Twig\Environment
   */
  protected static $twig;

  /**
   * Initialize the controller.
   */
  public static function init() {
    // Get the Twig instance from Flight
    self::$twig = Flight::get('twig');
  }

  /**
   * Render a Twig template with the given data.
   *
   * @param string $template The template name to render
   * @param array $data Data to pass to the template
   * @return void Outputs the rendered template
   */
  protected static function render($template, array $data = []) {
    // Initialize if not already done
    if (!self::$twig) {
      self::init();
    }

    echo self::$twig->render($template, $data);
  }

  /**
   * Redirect to the given URL.
   *
   * @param string $url The URL to redirect to
   * @param int $status HTTP status code (default: 302)
   * @return void
   */
  protected static function redirect($url, $status = 302) {
    Flight::redirect($url, $status);
  }

  /**
   * Get request data.
   *
   * @param string $key The data key to get (optional)
   * @param mixed $default Default value if key not found
   * @return mixed Request data
   */
  protected static function input($key = null, $default = null) {
    $request = Flight::request();

    if ($key === null) {
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
   * @param string $key The session key
   * @param mixed $value The value to set (optional)
   * @return mixed Session data if getting, or null if setting
   */
  protected static function session($key, $value = null) {
    // No need to start the session here anymore
    if ($value !== null) {
      $_SESSION[$key] = $value;
      return null;
    }

    return $_SESSION[$key] ?? null;
  }
}
