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

try {
    $count = Capsule::table('pergerakan_kapal')->count();
    echo "✅ Table pergerakan_kapal exists with {$count} records\n";
} catch(Exception $e) {
    echo "❌ Table pergerakan_kapal does not exist: " . $e->getMessage() . "\n";

    // Show available tables
    echo "\nAvailable tables:\n";
    $tables = Capsule::select('SHOW TABLES');
    foreach($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (strpos($tableName, 'kapal') !== false) {
            echo "  - {$tableName}\n";
        }
    }
}
