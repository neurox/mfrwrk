<?php

namespace Modules\Auth\Controllers;

use Core\BaseController;
use Modules\Auth\Helpers\UserHelper;
use ORM;
use Flight;

class AuthController extends BaseController {

  private $userHelper;

  public function __construct() {
    $this->userHelper = new UserHelper();
  }

  /**
   * Display the login form
   */
  public function loginForm() {
    // Check if user is already logged in.
    if ($this->userHelper->isUserLogged()) {
      self::redirect('/admin/dashboard');
    }

    // Check if admin user exists.
    $admin_user = $this->userHelper->existAdminUser();

    if (!$admin_user) {
      self::redirect('/admin/register');
    }

    // You can now use the render method from BaseController
    return self::render('@Auth/login.html.twig', [
      'title' => 'Login',
      'error' => self::input('error')
    ]);
  }

  /**
   * Display the login form
   */
  public function registerForm() {
    return self::render('@Auth/register.html.twig', [
      'title' => 'Create Admin',
      'error' => self::input('error')
    ]);
  }

  /**
   * Process login form submission
   */
  public function login() {
    // Get form data.
    $request = Flight::request();
    $data = $request->data->getData();
    $errors = [];

    // Validate credentials (replace with your actual authentication logic)
    if (empty($data['username'])) {
      $errors['username'] = 'Ingrese un nombre de usuario';
    }
    elseif (empty($data['password'])) {
      $errors['password'] = 'Ingrese una contraseña';
    }
    else {
      // Set session using the session method.
      $user = ORM::for_table('users')
        ->where_equal('username', $data['username'])
        ->where_raw('(email = ? OR username = ?)', [$data['username'], $data['username']])
        ->find_one();

      // Check if user exists and password is correct.
      if ($user && password_verify($data['password'], $user->password)) {

        // Set user data in session.
        $this->userHelper->setUserData($user);

        // Redirect using the redirect method
        self::redirect('/admin/dashboard');
      }
      else {
        // Redirect to login page with error message.
        return self::render('@Auth/login.html.twig', [
          'title' => 'Login',
          'error' => 'Nombre de usuario o contraseña incorrectos',
        ]);
      }
    }

    // Redirect to login page with error message.
    return self::render('@Auth/register.html.twig', [
      'title' => 'Register User',
      'old' => $data,
      'errors' => $errors,
    ]);
  }

  public function register() {
    // Get form data.
    $request = Flight::request();
    $data = $request->data->getData();

    // Validate form data.
    $errors = [];
    if ($data['username'] === '') {
      $errors['username'] = 'El nombre de usuario es requerido';
    } elseif (!ctype_alnum($data['username'])) {
      $errors['username'] = 'Solo numeros y letras son permitidos';
    } else {
      $existing_user = ORM::for_table('users')
        ->where_equal('username', $data['username'])
        ->find_one();

      // Check if username already exists.
      if ($existing_user) {
        $errors['username'] = 'El nombre de usuario ya esta registrado';
      }
    }

    // Check if email already exists.
    $existing_user = ORM::for_table('users')
      ->where_equal('email', $data['email'])
      ->find_one();

    if ($existing_user) {
      $errors['email'] = 'El correo ya esta registrado';
    }

    // Validate form data.
    if ($data['password'] !== $data['password_confirmation']) {
      $errors['password_confirmation'] = 'La contraseña no coincide';
    }

    // If no errors, create new user.
    if (empty($errors)) {
      // Create new user.
      $user = ORM::for_table('users')->create();
      $user->username = $data['username'];
      $user->first_name = $data['firstName'];
      $user->last_name = $data['lastName'];
      $user->email = $data['email'];
      $user->password = password_hash($data['password'], PASSWORD_DEFAULT);

      // Set role to admin if password confirmation is not empty.
      if ($this->userHelper->isFirstAdminUser()) {
        $user->role = 'admin';
      }

      $user->save();

      return self::render('@Auth/login.html.twig', [
        'title' => 'Admin Access',
        'old' => $data,
        'errors' => $errors,
        'message' => 'El usuario ' . $data['username'] . ' ha sido creado exitosamente!'
      ]);
    }
    else {
      return self::render('@Auth/register.html.twig', [
        'title' => 'Register User',
        'old' => $data,
        'errors' => $errors,
      ]);
    }
  }

  /**
   * Log out the user
   */
  public function logout() {
    // Destroy the PHP session.
    session_destroy();

    // Redirect to login page
    self::redirect('/auth/login');
  }

}
