<?php

namespace Core;

/**
 * Schema class.
 */
class Schema {

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
    $stmt = self::$pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
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
      $sql = "
        CREATE TABLE $table (
          $columnsStr
        );
      ";

      // Run SQL.
      self::$pdo->exec($sql);

    } else {
      /* Table exists — ensure columns exist
        * Extract required columns (excluding id which should already exist)
        * Get current columns
        * Get existing column names
        * Add missing columns
        */
      $required = [];
      foreach ($schema as $column => $definition) {
        if ($column !== 'id') {
          // Add column.
          $required[] = "$column $definition";
        }
      }

      // Get current columns.
      $columns = self::$pdo
        ->query("PRAGMA table_info($table)")
        ->fetchAll(PDO::FETCH_ASSOC);

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
          self::$pdo->exec("ALTER TABLE $table ADD COLUMN $definition");
        }
      }
    }
  }

}
