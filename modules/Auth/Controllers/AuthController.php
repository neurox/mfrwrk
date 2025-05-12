<?php

namespace Modules\Auth\Controllers;

use Core\BaseController;

class AuthController extends BaseController {
  
  /**
   * Display the login form
   */
  public static function loginForm() {
    // You can now use the render method from BaseController
    self::render('@Auth/login.html.twig', [
      'title' => 'Login',
      'error' => self::input('error')
    ]);
  }
  
  /**
   * Process login form submission
   */
  public static function login() {
    // Get form data using the input method
    $username = self::input('username');
    $password = self::input('password');
    
    // Validate credentials (replace with your actual authentication logic)
    if ($username === 'admin' && $password === 'password') {
      // Set session using the session method
      self::session('user', [
        'username' => $username,
        'role' => 'admin'
      ]);
      
      // Redirect using the redirect method
      self::redirect('/dashboard');
    } else {
      // Redirect back to login form with error
      self::redirect('/auth/login?error=invalid_credentials');
    }
  }
  
  /**
   * Log out the user
   */
  public static function logout() {
    // Clear the session
    session_start();
    session_destroy();
    
    // Redirect to login page
    self::redirect('/auth/login');
  }
}
