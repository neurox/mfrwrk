<?php

namespace Core;

/**
 * Database class.
 */
class DB {

  /**
   * Initialize database connection.
   */
  public static function init($config) {

    // Load database connection.
    if ($config['driver'] === 'sqlite') {
      $dbPath = $config['path'];
      $dir = dirname($dbPath);

      // Ensure the directory exists.
      if (!is_dir($dir)) {
        mkdir($dir, 0755, TRUE);
      }

      $isFirstRun = !file_exists($dbPath);

      // Configure Idiorm.
      \ORM::configure('sqlite:' . $dbPath);
      \ORM::configure('return_result_sets', TRUE);
    }
  }

  /**
   * Get database connection.
   */
  public static function get() {
    return \ORM::get_db();
  }

}
