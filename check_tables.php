<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
echo 'Permission-related tables:' . PHP_EOL;
foreach($tables as $table) {
    $tableName = current($table);
    if(str_contains(strtolower($tableName), 'permission') ||
       str_contains(strtolower($tableName), 'role') ||
       str_contains(strtolower($tableName), 'user')) {
        echo '- ' . $tableName . PHP_EOL;

        // Get table structure
        try {
            $columns = DB::select('DESCRIBE ' . $tableName);
            echo '  Columns: ';
            $columnNames = array_map(function($col) { return $col->Field; }, $columns);
            echo implode(', ', $columnNames) . PHP_EOL;
        } catch (Exception $e) {
            echo '  Error getting columns: ' . $e->getMessage() . PHP_EOL;
        }
        echo PHP_EOL;
    }
}
?>
