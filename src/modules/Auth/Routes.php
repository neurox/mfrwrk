<?php

namespace Modules\Auth;

/**
 * Auth module routes.
 */
class Routes {

  /**
   * Register all routes for the Auth module.
   */
  public static function register() {

    // Auth group.
    \Flight::group('/auth', function () {
      \Flight::route('GET /login', ['\Modules\Auth\Controllers\AuthController', 'loginForm']);
      \Flight::route('GET /register', ['\Modules\Auth\Controllers\AuthController', 'registerForm']);
      \Flight::route('GET /logout', ['\Modules\Auth\Controllers\AuthController', 'logout']);
      \Flight::route('POST /register', ['\Modules\Auth\Controllers\AuthController', 'register']);
      \Flight::route('POST /login', ['\Modules\Auth\Controllers\AuthController', 'login']);
    });

    \Flight::group('/admin', function () {
      \Flight::route('GET /dashboard', ['\Modules\Auth\Controllers\AdminController', 'dashboard']);
      \Flight::route('GET /account', ['\Modules\Auth\Controllers\AdminController', 'account']);
    });
  }

}
