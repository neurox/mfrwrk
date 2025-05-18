<?php
/**
 * Config class to load and access app configuration.
 */

namespace Config;

use Dotenv\Dotenv;

class Config
{
  protected static array $config = [];

  /**
   * Load environment variables and initialize configuration.
   */
  public static function load(string $basePath = __DIR__ . '/../'): void
  {
    $dotenv = Dotenv::createImmutable($basePath);
    $dotenv->load();

    self::$config = [
      'env' => $_ENV['APP_ENV'] ?? 'production',
      'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
      'db' => [
        'driver' => $_ENV['DB_DRIVER'] ?? 'sqlite',
        'path' => $_ENV['DB_PATH']
          ? $basePath . $_ENV['DB_PATH']
          : $basePath . 'database/app.sqlite',
      ],
      'auth' => [
        'username' => $_ENV['AUTH_USERNAME'] ?? 'admin',
        'password' => $_ENV['AUTH_PASSWORD'] ?? '1234',
      ]
    ];
  }

  /**
   * Get configuration value.
   */
  public static function get(string $key, mixed $default = null): mixed
  {
    return self::$config[$key] ?? $default;
  }

  /**
   * Get all config as array.
   */
  public static function all(): array
  {
    return self::$config;
  }

}
