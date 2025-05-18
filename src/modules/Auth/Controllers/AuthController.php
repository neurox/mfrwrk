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
      'errors' => self::input('errors'),
      'csrf_token' => self::generateCsrfToken(),
    ]);
  }

  /**
   * Display the login form
   */
  public function registerForm() {
    return self::render('@Auth/register.html.twig', [
      'title' => 'Create Admin',
      'errors' => self::input('errors'),
      'csrf_token' => self::generateCsrfToken(),
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

    // Validate form data.
    if (!$this->validateCsrfToken($data['csrf_token'] ?? '')) {
      $errors['csrf_token'] = 'Invalid Form, refresh the page and try again';
    }

    if (empty($data['username'])) {
      $errors['username'] = 'Ingrese un nombre de usuario';
    }

    if (empty($data['password'])) {
      $errors['password'] = 'Ingrese una contraseña';
    }

    // If no errors, check if user exists and password is correct.
    if (empty($errors)) {

      // Set session using the session method.
      $user = ORM::for_table('users')
        ->where_raw('email = ? OR username = ?', [$data['username'], $data['username']])
        ->find_one();

      // Check if user exists and password is correct.
      if ($user && password_verify($data['password'], $user->password)) {

        // Set user data in session.
        $this->userHelper->setUserData($user);

        // Redirect using the redirect method
        self::redirect('/admin/dashboard');
      }
      else {
        $errors['username'] = 'El nombre de usuario o contraseña son incorrectos';
      }
    }

    // Redirect to login page with error message.
    return self::render('@Auth/login.html.twig', [
      'title' => 'Login',
      'errors' => $errors,
      'csrf_token' => self::generateCsrfToken(),
    ]);
  }

  public function register() {
    // Get form data.
    $request = Flight::request();
    $data = $request->data->getData();
    $errors = [];

    // Validate form data.
    if (!$this->validateCsrfToken($data['csrf_token'] ?? '')) {
      $errors['csrf_token'] = 'Formulario invalido, recargue la pagina y vuelva a intentarlo';
    }

    // Sanitize form data.
    $data['username'] = trim($data['username']);
    $data['username'] = strtolower($data['username']);
    $data['email'] = trim($data['email']);
    $data['email'] = strtolower($data['email']);

    // Validate first name.
    if ($data['firstName'] === '') {
      $errors['firstName'] = 'El nombre es requerido';
    }

    // Validate last name.
    if ($data['lastName'] === '') {
      $errors['lastName'] = 'El apellido es requerido';
    }

    // Validate username.
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

    // Validate email.
    if ($data['email'] === '') {
      $errors['email'] = 'El correo es requerido';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'Ingrese un correo valido';
    }

    // Check if email already exists.
    $existing_user = ORM::for_table('users')
      ->where_equal('email', $data['email'])
      ->find_one();

    if ($existing_user) {
      $errors['email'] = 'El correo ya esta registrado';
    }

    // Validate password.
    if ($data['password'] === '') {
      $errors['password'] = 'La contraseña es requerida';
    }

    // Validate password confirmation.
    if ($data['password_confirmation'] === '') {
      $errors['password_confirmation'] = 'La confirmacion de la contraseña es requerida';
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
        'message' => 'El usuario ' . $data['username'] . ' ha sido creado exitosamente!',
        'csrf_token' => self::generateCsrfToken(),
      ]);
    }
    else {
      return self::render('@Auth/register.html.twig', [
        'title' => 'Register User',
        'old' => $data,
        'errors' => $errors,
        'csrf_token' => self::generateCsrfToken(),
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
