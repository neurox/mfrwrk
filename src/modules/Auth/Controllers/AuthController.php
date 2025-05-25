<?php

namespace Modules\Auth\Controllers;

use Core\BaseController;
use Core\Validation;
use Modules\Auth\Helpers\UserHelper;

/**
 * Autentication of the user.
 *
 * @package Modules\Auth\Controllers
 */
class AuthController extends BaseController {

  /**
   * User helper class.
   *
   * @var \Modules\Auth\Helpers\UserHelper
   */
  private $userHelper;

  /**
   * AuthController constructor.
   */
  public function __construct() {
    $this->userHelper = new UserHelper();
  }

  /**
   * Display the login form.
   */
  public function loginForm() {
    // Check if user is already logged in.
    if ($this->userHelper->isUserLogged()) {
      self::redirect('/admin/dashboard');
    }

    // Render the login form.
    self::render('@Auth/login.html.twig', [
      'title' => 'Login',
      'errors' => self::input('errors'),
      'csrf_token' => Validation::generateCsrfToken(),
    ]);
  }

  /**
   * Display the login form.
   */
  public function registerForm() {
    self::render('@Auth/register.html.twig', [
      'title' => 'Create Admin',
      'errors' => self::input('errors'),
      'csrf_token' => Validation::generateCsrfToken(),
    ]);
  }

  /**
   * Process login form submission.
   */
  public function login() {
    // Get form data.
    $request = \Flight::request();
    $data = $request->data->getData();
    $errors = [];

    // Get username from form data.
    $lockStatus = $this->checkLoginAttempts($data['username']);

    // If user is locked, show error message.
    if ($lockStatus['locked']) {
      $waitMinutes = ceil($lockStatus['wait_time'] / 60);
      $errors['username'] = "Haz excedido el número máximo de intentos. Por favor, inténtalo de nuevo en {$waitMinutes} minutos.";
    }

    if (!$lockStatus['locked']) {
      $rules = [
        'csrf_token' => 'csrf_token',
        'username' => 'required',
        'password' => 'required',
      ];

      // Validate form data.
      $validation = Validation::validate($data, $rules);
      $errors = Validation::getErrors();
    }

    // If no errors, check if user exists and password is correct.
    if (empty($errors)) {

      // Set session using the session method.
      $user = \ORM::for_table('users')
        ->where_raw('email = ? OR username = ?', [$data['username'], $data['username']])
        ->find_one();

      // Check if user exists and password is correct.
      if ($user && password_verify($data['password'], $user->password)) {

        // Record login attempt.
        $this->recordLoginAttempt($data['username'], TRUE);

        // Set user data in session.
        $this->userHelper->setUserData($user);

        // Redirect to admin dashboard.
        self::redirect('/admin/dashboard');
      }
      else {
        // Record login attempt.
        $this->recordLoginAttempt($data['username'], FALSE);

        // Set error message.
        $errors['username'] = 'El nombre de usuario y/o contraseña son incorrectos';
      }
    }

    // Redirect to login page with error message.
    self::render('@Auth/login.html.twig', [
      'title' => 'Login',
      'old' => $data,
      'errors' => $errors,
      'csrf_token' => Validation::generateCsrfToken(),
    ]);
  }

  /**
   * Process register form submission.
   */
  public function register() {
    // Get form data.
    $request = \Flight::request();
    $data = $request->data->getData();
    $errors = [];

    $rules = [
      'csrf_token' => 'csrf_token',
      'firstName' => 'required|alpha|min:3',
      'lastName' => 'required|alpha|min:3',
      'username' => 'required|alnum|min:3',
      'email' => 'required|email',
      'password' => 'required|min:8',
      'password_confirmation' => 'required|same:password',
    ];

    // Validate form data.
    $validation = Validation::validate($data, $rules);
    $errors = Validation::getErrors();

    if (empty($errors)) {
      $existing_user = \ORM::for_table('users')
        ->where_equal('username', $data['username'])
        ->find_one();

      // Check if username already exists.
      if ($existing_user) {
        $errors['username'] = 'El nombre de usuario ya esta registrado';
      }

      // Check if email already exists.
      $existing_user = \ORM::for_table('users')
        ->where_equal('email', $data['email'])
        ->find_one();

      if ($existing_user) {
        $errors['email'] = 'El correo ya esta registrado';
      }
    }

    // If no errors, create new user.
    if (empty($errors)) {
      // Create new user.
      $user = \ORM::for_table('users')->create();
      $user->username = $data['username'];
      $user->first_name = $data['firstName'];
      $user->last_name = $data['lastName'];
      $user->email = $data['email'];
      $user->password = password_hash($data['password'], PASSWORD_DEFAULT);

      // Set role to admin if password confirmation is not empty.
      if ($this->userHelper->existAdminUser()) {
        $user->role = 'admin';
      }

      // Save user.
      $user_created = $user->save();

      // Redirect to user dashboard.
      if ($user_created) {
        $this->userHelper->setUserData($user);
        self::redirect('/admin/dashboard');
      }
      else {
        // Set error message.
        $errors['username'] = 'Error al crear el usuario, intente nuevamente';
      }

      // Redirect to login page with error message.
      self::render('@Auth/login.html.twig', [
        'title' => 'Admin Access',
        'old' => $data,
        'errors' => $errors,
        'csrf_token' => Validation::generateCsrfToken(),
      ]);
    }
    else {
      self::render('@Auth/register.html.twig', [
        'title' => 'Register User',
        'old' => $data,
        'errors' => $errors,
        'csrf_token' => Validation::generateCsrfToken(),
      ]);
    }
  }

  /**
   * Log out the user.
   */
  public function logout() {
    $this->userHelper->logout();
    self::redirect('/auth/login');
  }

  /**
   * Check login attempts.
   */
  private function checkLoginAttempts($username) {
    // Set maximum login attempts and lockout time.
    $maxAttempts = 5;
    $lockoutTime = 15 * 60;

    // Check if user has not exceeded maximum login attempts.
    if (!isset($_SESSION['login_attempts'][$username])) {
      $_SESSION['login_attempts'][$username] = [
        'count' => 0,
        'last_attempt' => 0,
      ];
    }

    // Get login attempts for user.
    $attempts = &$_SESSION['login_attempts'][$username];

    // Check if user has exceeded maximum login attempts.
    if ($attempts['count'] >= $maxAttempts) {

      // Calculate time elapsed since last login attempt.
      $timeElapsed = time() - $attempts['last_attempt'];

      // Check if user has exceeded lockout time.
      if ($timeElapsed < $lockoutTime) {
        return [
          'locked' => TRUE,
          'wait_time' => $lockoutTime - $timeElapsed,
        ];
      }
      else {
        // Reset login attempts.
        $attempts['count'] = 0;
      }
    }

    // Return success response.
    return ['locked' => FALSE];
  }

  /**
   * Record login attempt.
   */
  private function recordLoginAttempt($username, $success) {

    // Initialize login attempts if not set.
    if (!isset($_SESSION['login_attempts'][$username])) {

      // Initialize login attempts array.
      $_SESSION['login_attempts'][$username] = [
        'count' => 0,
        'last_attempt' => 0,
      ];
    }

    // Get login attempts for user.
    $attempts = &$_SESSION['login_attempts'][$username];
    $attempts['last_attempt'] = time();

    // Increment login attempts.
    if (!$success) {
      $attempts['count']++;
    }
    else {
      // Reset on successful login.
      $attempts['count'] = 0;
    }
  }

}
