<?php

namespace Core;

/**
 * The database schema class.
 */
class Schema {

  /**
   * The database connection.
   *
   * @var \PDO
   *  The database connection.
   */
  protected static $pdo;

  /**
   * Initialize database.
   */
  public static function init($pdo) {
    self::$pdo = $pdo;
  }

  /**
   * Ensure table exists.
   */
  public static function ensureTable($table, $schema) {

    // Check if table exists.
    $stmt = self::$pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
    $stmt->execute([$table]);
    $exists = $stmt->fetchColumn();

    // If table doesn't exist.
    if (!$exists) {

      // Build CREATE TABLE statement from schema.
      $columns = [];
      foreach ($schema as $column => $definition) {
        $columns[] = "$column $definition";
      }

      // Join columns.
      $columnsStr = implode(",\n", $columns);

      // Create table.
      $stmt = self::$pdo->prepare('CREATE TABLE ? (?);');
      $stmt->execute([$table, $columnsStr]);
    }
    else {
      // Get required columns.
      $required = [];
      foreach ($schema as $column => $definition) {
        if ($column !== 'id') {
          // Add column.
          $required[] = "$column $definition";
        }
      }

      // Get current columns.
      $stmt = self::$pdo->prepare("PRAGMA table_info(?)");
      $stmt->execute([$table]);
      $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

      // Get existing column names.
      $existingCols = array_column($columns, 'name');

      // Add missing columns.
      foreach ($required as $definition) {

        // Get column name.
        preg_match('/^(\w+)/', $definition, $match);

        // Add column if it doesn't exist.
        $col = $match[1];
        if (!in_array($col, $existingCols)) {
          // Add column.
          $stmt = self::$pdo->prepare("ALTER TABLE ? ADD COLUMN ?");
          $stmt->execute([$table, $definition]);
        }
      }
    }
  }

  /**
   * Drop table.
   */
  public static function dropTable($table) {
    $stmt = self::$pdo->prepare("DROP TABLE IF EXISTS ?");
    $stmt->execute([$table]);
  }

}
