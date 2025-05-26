<?php

namespace Neurox\Mfrwrk\Modules\Auth;

use Neurox\Mfrwrk\Modules\Auth\Helpers\UserHelper;
use Neurox\Mfrwrk\Modules\Auth\Controllers\AuthController;
use Neurox\Mfrwrk\Modules\Auth\Controllers\AdminController;

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
      \Flight::route('GET /login', [AuthController::class, 'loginForm']);
      \Flight::route('GET /register', [AuthController::class, 'registerForm']);
      \Flight::route('GET /logout', [AuthController::class, 'logout']);
      \Flight::route('POST /register', [AuthController::class, 'register']);
      \Flight::route('POST /login', [AuthController::class, 'login']);
    });

    \Flight::group('/admin', function () {
      \Flight::route('GET /dashboard', [AdminController::class, 'dashboard']);
      \Flight::route('GET /account', [AdminController::class, 'account']);
    },
    [
      function () {
        // Check if user is admin before proceeding.
        if (!UserHelper::isUserLogged()) {
          \Flight::redirect('/auth/login');
        }
        return TRUE;
      },
    ]);
  }

}
