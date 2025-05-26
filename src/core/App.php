<?php

/**
 * @file
 * Main app file.
 */

use Neurox\Mfrwrk\Core\DB;
use Neurox\Mfrwrk\Core\Config;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Load environment variables and config.
Config::load();

// Initialize DB (with Idiorm).
DB::init(Config::get('db'));
\Flight::set('db', DB::get());

// Load Twig.
$loader = new FilesystemLoader(__DIR__ . '/../views');
$twig = new Environment($loader, [
  'cache' => Config::get('env') === 'dev' ? FALSE : __DIR__ . '/../cache',
]);

// Add global variables to Twig.
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$twig->addGlobal('current_path', $currentPath);

// Set Twig to Flight.
\Flight::set('twig', $twig);

// Home route.
\Flight::route('GET /', function () {
  echo \Flight::get('twig')->render('home.html.twig');
});

/**
 * Load all modules.
 *
 * @return array
 *   An array of modules.
 */
function load_modules(): array {
  $modulesPath = __DIR__ . '/../modules';
  $modules = [];

  if (is_dir($modulesPath)) {
    foreach (scandir($modulesPath) as $dir) {

      // Skip hidden directories.
      if ($dir === '.' || $dir === '..') {
        continue;
      }

      // Check if module exists.
      $modulePath = $modulesPath . '/' . $dir;
      if (is_dir($modulePath) && file_exists($modulePath . '/Module.php')) {
        require_once $modulePath . '/Module.php';
        $moduleClass = "\\Neurox\\Mfrwrk\\Modules\\{$dir}\\Module";
        $modules[$dir] = $moduleClass;
      }
    }
  }

  return $modules;
}

// Load and register all modules.
foreach (load_modules() as $name => $class) {
  $class::register();
}
