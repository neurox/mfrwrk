<?php

namespace Neurox\Mfrwrk\Modules\Auth\Helpers;

use Neurox\Mfrwrk\Core\BaseController;

/**
 * User helper class.
 */
class UserHelper extends BaseController {

  /**
   * Check if user is admin.
   */
  public static function isAdmin() {
    return self::session('role') == 'admin';
  }

  /**
   * Get user role from session.
   */
  public static function getRole() {
    return self::session('role');
  }

  /**
   * Set user data in session.
   */
  public static function setUserData($user) {

    // Get user name.
    $userName = isset($user->first_name) && $user->first_name != '' ?
      strtok($user->first_name, ' ') : $user->username;

    // Set session.
    self::session('user', [
      'id' => $user->id,
      'username' => $user->username,
      'name' => $userName,
      'email' => $user->email,
      'role' => $user->role,
    ]);
  }

  /**
   * Get user data from session.
   */
  public static function getUserData() {

    // Get user data from session.
    $userData = self::session('user');

    return $userData;
  }

  /**
   * Check if admin user exists.
   */
  public static function existAdminUser() {
    $userCount = \ORM::for_table('users')
      ->where_equal('role', 'admin')
      ->count();
    return $userCount > 0;
  }

  /**
   * Check if user is logged in.
   */
  public static function isUserLogged() {
    return self::session('user') !== NULL;
  }

  /**
   * Start the PHP session.
   */
  public static function startSession() {

    // Check if session has already started.
    if (session_status() === PHP_SESSION_NONE) {

      // Set session parameters.
      $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
      $samesite = 'Lax';

      ini_set('session.cookie_httponly', 1);
      ini_set('session.use_only_cookies', 1);
      ini_set('session.cookie_secure', $secure);

      // Set session cookie parameters.
      if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
          'lifetime' => 3600,
          'path' => '/',
          'domain' => $_SERVER['HTTP_HOST'],
          'secure' => $secure,
          'httponly' => TRUE,
          'samesite' => $samesite,
        ]);
      }
      else {
        session_set_cookie_params(
          3600,
          '/; samesite=' . $samesite,
          $_SERVER['HTTP_HOST'],
          $secure,
          TRUE,
        );
      }

      // Start the session.
      session_start();
    }
  }

  /**
   * Log out the user.
   */
  public static function logout() {
    // Destroy the PHP session.
    session_destroy();
  }

}
