<?php

use Core\DB;
use Core\Config;

// Load environment variables and config.
Config::load();

// Initialize DB (with Idiorm).
DB::init(Config::get('db'));
Flight::set('db', DB::get());

// Load Twig.
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
$twig = new \Twig\Environment($loader, [
    'cache' => Config::get('env') === 'dev' ? false : __DIR__ . '/../cache',
]);

// Add global variables to Twig.
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$twig->addGlobal('current_path', $currentPath);

// Set Twig to Flight.
Flight::set('twig', $twig);

// Home route.
Flight::route('GET /', function () {
    echo Flight::get('twig')->render('home.html.twig');
});

// Function to autoload modules.
function loadModules(): array {
    $modulesPath = __DIR__ . '/../modules';
    $modules = [];

    if (is_dir($modulesPath)) {
        foreach (scandir($modulesPath) as $dir) {
            if ($dir === '.' || $dir === '..') continue;

            $modulePath = $modulesPath . '/' . $dir;
            if (is_dir($modulePath) && file_exists($modulePath . '/Module.php')) {
                require_once($modulePath . '/Module.php');
                $moduleClass = "\\Modules\\{$dir}\\Module";
                $modules[$dir] = $moduleClass;
            }
        }
    }

    return $modules;
}

// Load and register all modules.
foreach (loadModules() as $name => $class) {
    $class::register();
}
