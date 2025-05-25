<?php

namespace Modules\Auth\Controllers;

use Core\BaseController;
use Modules\Auth\Helpers\UserHelper;

/**
 * Controller for admin sections.
 */
class AdminController extends BaseController {

  /**
   * User helper.
   *
   * @var \Modules\Auth\Helpers\UserHelper
   */
  private $userHelper;

  public function __construct() {
    $this->userHelper = new UserHelper();
  }

  /**
   * Display the admin dashboard.
   */
  public function dashboard() {
    self::render('@Auth/admin-dashboard.html.twig', [
      'title' => 'Dashboard',
      'user_data' => $this->userHelper->getUserData(),
    ]);
  }

  /**
   * Display the account page.
   */
  public function account() {
    self::render('@Auth/account.html.twig', [
      'title' => 'Mi Cuenta',
      'error' => self::input('error'),
    ]);
  }

}
