<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Database Tables ===\n";

$tables = DB::select('SHOW TABLES');
foreach($tables as $table) {
    $tableName = array_values((array)$table)[0];
    echo "- {$tableName}\n";
}

echo "\n=== Users Table Structure ===\n";
$columns = DB::select('DESCRIBE users');
foreach($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

?>
