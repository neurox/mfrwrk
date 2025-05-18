<?php

namespace Modules\Auth\Controllers;

use Core\BaseController;
use Modules\Auth\Helpers\UserHelper;
use ORM;
use Flight;

class AdminController extends BaseController {

  private $userHelper;

  public function __construct() {
    $this->userHelper = new UserHelper();
  }

  /**
   * Display the admin dashboard.
   */
  public function dashboard() {
    // Check if user is logged in.
    if (!$this->userHelper->isUserLogged()) {
      self::redirect('/auth/login');
    }

    // echo '<pre>';
    // var_dump($this->userHelper->getUserData());
    // echo '</pre>';

    // You can now use the render method from BaseController
    return self::render('@Auth/admin-dashboard.html.twig', [
      'title' => 'Dashboard',
      'error' => self::input('error')
    ]);
  }
}
