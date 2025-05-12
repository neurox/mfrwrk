<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/DB.php';
require_once __DIR__ . '/core/Schema.php';
require_once __DIR__ . '/core/ModuleInterface.php';

// Initialize DB
DB::init($config['db']);
Schema::init(DB::get());

// Get all available modules
function getAvailableModules() {
    $modulesPath = __DIR__ . '/modules';
    $modules = [];
    
    if (is_dir($modulesPath)) {
        $dirs = scandir($modulesPath);
        foreach ($dirs as $dir) {
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

// Command line arguments
$operation = $argv[1] ?? '';
$moduleName = $argv[2] ?? '';

if (!in_array($operation, ['install', 'uninstall', 'list']) || ($operation !== 'list' && empty($moduleName))) {
    echo "Usage: php manage_modules.php [operation] [module_name]\n";
    echo "Operations: install, uninstall, list\n";
    exit(1);
}

$modules = getAvailableModules();

if ($operation === 'list') {
    echo "Available modules:\n";
    foreach ($modules as $name => $class) {
        echo "- {$name}\n";
    }
    exit(0);
}

if (!isset($modules[$moduleName])) {
    echo "Error: Module '{$moduleName}' not found.\n";
    exit(1);
}

$moduleClass = $modules[$moduleName];

try {
    if ($operation === 'install') {
        echo "Installing module '{$moduleName}'...\n";
        $moduleClass::install();
        echo "Module '{$moduleName}' installed successfully.\n";
    } else if ($operation === 'uninstall') {
        echo "Uninstalling module '{$moduleName}'...\n";
        $moduleClass::uninstall();
        echo "Module '{$moduleName}' uninstalled successfully.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
