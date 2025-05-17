<?php

namespace Modules\Auth;

use Flight;
use Core\DB;
use Core\Schema;
use Core\ModuleInterface;
use Core\BaseModule;
use Modules\Auth\Helpers\UserHelper;

/**
 * Auth module.
 */
class Module extends BaseModule {

  private static $templatesPath;

  /**
   * Register the module routes, templates, etc.
   * This method is called when the application starts.
   */
  public static function register() {
    // Set templates path for this module
    self::$templatesPath = __DIR__ . '/templates';

    // Add this module's templates directory to Twig loader
    $currentLoader = Flight::get('twig')->getLoader();
    $currentLoader->addPath(self::$templatesPath, 'Auth');

    // Add global variables to Twig.
    Flight::before('start', function () {
      $twig = Flight::get('twig');
      $userHelper = new UserHelper();
      $twig->addGlobal('user_is_logged', $userHelper->isUserLogged());
      $twig->addGlobal('user_is_admin', $userHelper->isAdmin());
      $twig->addGlobal('user_data', $userHelper->getUserData());
    });

    // Start session.
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // Register routes
    parent::registerRoutes();
  }

  /**
   * Install the module (create tables, initial data, etc.)
   * This method is called when the module is being installed.
   */
  public static function install() {

    // Create users database tables.
    Schema::ensureTable('users', [
        'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
        'username' => 'TEXT NOT NULL UNIQUE',
        'password' => 'TEXT NOT NULL',
        'role' => 'TEXT NOT NULL',
        'first_name' => 'TEXT',
        'last_name' => 'TEXT',
        'email' => 'TEXT',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at' => 'TIMESTAMP',
    ]);
  }

  /**
   * Uninstall the module (remove tables, data, etc.)
   * This method is called when the module is being uninstalled.
   */
  public static function uninstall() {
    // Drop database tables
    Schema::dropTable('users');
  }

}
