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

    /**
     * Render the admin dashboard.
     */
    return self::render('@Auth/admin-dashboard.html.twig', [
      'title' => 'Panel de Control',
      'error' => self::input('error')
    ]);
  }

  /**
   * Display the account page.
   */
  public function account() {
    return self::render('@Auth/account.html.twig', [
      'title' => 'Mi Cuenta',
      'error' => self::input('error')
    ]);
  }

}
