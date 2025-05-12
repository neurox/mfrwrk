<?php
$config = require __DIR__ . '/config/config.php';

// Load flight and twig.
Flight::register('view', 'Twig\Environment', [new Twig\Loader\FilesystemLoader(__DIR__ . '/views'), [
    'cache' => false,
    'debug' => $config['debug']
]]);

// Load database connection.
Flight::register('db', PDO::class, [
    'sqlite:' . $config['db']['path']
]);

Flight::map('db', function () use ($config) {
    $pdo = new PDO('sqlite:' . $config['db']['path']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});

// Test database connection.
Flight::route('/test-db', function () {
    $db = Flight::db();
    $db->exec("CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY, nombre TEXT)");
    $db->exec("INSERT INTO test (nombre) VALUES ('ejemplo')");
    $stmt = $db->query("SELECT * FROM test");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($result);
});
