<?php

namespace Modules\Auth;

use Flight;
use Modules\Auth\Controllers\AuthController;

/**
 * Auth module routes.
 */
class Routes {
  
  /**
   * Register all routes for the Auth module.
   */
  public static function register() {
    // // Logout route
    // Flight::route('GET /logout', ['\Modules\Auth\Controllers\AuthController', 'logout']);
    
    // // Registration routes
    // Flight::route('GET /register', ['\Modules\Auth\Controllers\AuthController', 'registerForm']);
    // Flight::route('POST /register', ['\Modules\Auth\Controllers\AuthController', 'register']);
    
    // // Password reset routes
    // Flight::route('GET /forgot-password', ['\Modules\Auth\Controllers\AuthController', 'forgotPasswordForm']);
    // Flight::route('POST /forgot-password', ['\Modules\Auth\Controllers\AuthController', 'forgotPassword']);
    
    // // Admin routes (with middleware)
    // Flight::route('GET /admin/users', function() {
    //   // Check if user is admin before proceeding
    //   if (!Auth::isAdmin()) {
    //     Flight::redirect('/login');
    //   }
      
    //   Flight::callClassMethod('\Modules\Auth\Controllers\AdminController', 'listUsers');
    // });

    // Auth group.
    Flight::group('/auth', function() {
      Flight::route('GET /login', ['\Modules\Auth\Controllers\AuthController', 'loginForm']);
      Flight::route('POST /login', ['\Modules\Auth\Controllers\AuthController', 'login']);
    });

    // // Admin group.
    // Flight::group('/admin', function() {
    //   Flight::route('GET /users', ['\Modules\Auth\Controllers\AdminController', 'listUsers']);
    // }, function() {

    //   // Check if user is admin before proceeding.
    //   if (!Auth::isAdmin()) {
    //     Flight::redirect('/auth/login');
    //     return false;
    //   }
    //   return true;
    // });
  }
}
