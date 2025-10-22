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

echo "📋 Checking stock_kontainers table structure:\n";
echo "===========================================\n\n";

try {
    $columns = Capsule::select('DESCRIBE stock_kontainers');

    echo "Table: stock_kontainers\n";
    echo "Columns:\n";
    foreach($columns as $col) {
        $nullable = $col->Null === 'YES' ? '(nullable)' : '(required)';
        $default = $col->Default ? " default: {$col->Default}" : '';
        echo sprintf("  %-20s %-15s %s%s\n", $col->Field, $col->Type, $nullable, $default);
    }

    // Check some sample data
    echo "\nSample data (first 3 records):\n";
    $samples = Capsule::table('stock_kontainers')->limit(3)->get();
    foreach($samples as $sample) {
        $nomorKontainer = isset($sample->nomor_kontainer) ? $sample->nomor_kontainer : 'N/A';
        echo "  - ID: {$sample->id}, Nomor: {$nomorKontainer}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
