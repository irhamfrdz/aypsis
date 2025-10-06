<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Database Tables ===\n";

$tables = DB::select('SHOW TABLES');
echo "Available tables:\n";
foreach($tables as $table) {
    $tableName = 'Tables_in_' . env('DB_DATABASE', 'aypsis');
    echo "- " . $table->{$tableName} . "\n";
}

echo "\n=== Checking Permission Tables ===\n";

// Check if user_has_permissions exists
try {
    $count = DB::table('user_has_permissions')->count();
    echo "✓ user_has_permissions table exists with $count records\n";
} catch (\Exception $e) {
    echo "❌ user_has_permissions table does not exist: " . $e->getMessage() . "\n";
}

// Check permissions table
try {
    $count = DB::table('permissions')->count();
    echo "✓ permissions table exists with $count records\n";
} catch (\Exception $e) {
    echo "❌ permissions table does not exist: " . $e->getMessage() . "\n";
}

// Check if there's a user_permissions table
try {
    $count = DB::table('user_permissions')->count();
    echo "✓ user_permissions table exists with $count records\n";
} catch (\Exception $e) {
    echo "❌ user_permissions table does not exist\n";
}