<?php

namespace Config;

use Dotenv\Dotenv;

/**
 * The configuration service.
 *
 * @package Config
 */
class Config {

  /**
   * The configuration array.
   *
   * @var array
   *   The configuration array.
   */
  protected static array $config = [];

  /**
   * Load environment variables and initialize configuration.
   */
  public static function load(string $basePath = __DIR__ . '/../'): void {
    // Load environment variables.
    $dotenv = Dotenv::createImmutable($basePath);
    $dotenv->load();

    // Initialize configuration.
    self::$config = [
      'env' => $_ENV['APP_ENV'] ?? 'production',
      'debug' => filter_var($_ENV['APP_DEBUG'] ?? FALSE, FILTER_VALIDATE_BOOLEAN),
      'db' => [
        'driver' => $_ENV['DB_DRIVER'] ?? 'sqlite',
        'path' => $_ENV['DB_PATH']
          ? $basePath . $_ENV['DB_PATH']
          : $basePath . 'database/app.sqlite',
      ],
      'auth' => [
        'username' => $_ENV['AUTH_USERNAME'] ?? 'admin',
        'password' => $_ENV['AUTH_PASSWORD'] ?? '1234',
      ],
    ];
  }

  /**
   * Get configuration value.
   */
  public static function get(string $key, mixed $default = NULL): mixed {
    return self::$config[$key] ?? $default;
  }

  /**
   * Get all config as array.
   */
  public static function all(): array {
    return self::$config;
  }

}
