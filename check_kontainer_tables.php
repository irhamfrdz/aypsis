<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'aypsis',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔍 Checking existing tables related to stock/kontainer:\n";
echo "====================================================\n\n";

try {
    $tables = Capsule::select('SHOW TABLES');

    echo "Tables containing 'stock' or 'kontainer':\n";
    foreach($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (strpos(strtolower($tableName), 'stock') !== false ||
            strpos(strtolower($tableName), 'kontainer') !== false) {
            echo "  - {$tableName}\n";
        }
    }

    echo "\nAll tables (filtered):\n";
    foreach($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (strpos(strtolower($tableName), 'master') !== false) {
            echo "  - {$tableName}\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
