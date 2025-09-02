<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Setup database connection
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Database Structure Check ===\n\n";

try {
    $columns = DB::select('DESCRIBE pembayaran_pranota_kontainer');

    echo "Columns in pembayaran_pranota_kontainer table:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";
