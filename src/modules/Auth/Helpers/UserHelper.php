<?php

namespace Modules\Auth\Helpers;

use Core\BaseController;
use ORM;
use Flight;

class UserHelper extends BaseController {
  public static function isAdmin() {
    return self::session('role') == 'admin';
  }

  public static function getRole() {
    return self::session('role');
  }

  public static function setUserData($user) {

    // Set session.
    self::session('user', [
      'id' => $user->id,
      'username' => $user->username,
      'name' => $user->first_name . ' ' . $user->last_name,
      'email' => $user->email,
      'role' => $user->role,
    ]);
  }

  public static function getUserData() {

    // Get user data from session.
    $userData = self::session('user');

    return $userData;
  }

  public static function existAdminUser() {
    $userCount = ORM::for_table('users')->count();
    return $userCount > 0;
  }

  public static function isUserLogged() {
    return self::session('user') !== null;
  }
}
