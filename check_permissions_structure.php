<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking permissions table structure...\n";

try {
    $columns = Schema::getColumnListing('permissions');
    echo "Permissions table columns:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }

    echo "\nChecking if permissions table has data:\n";
    $count = DB::table('permissions')->count();
    echo "Total permissions: $count\n";

    if ($count > 0) {
        echo "\nSample permissions:\n";
        $permissions = DB::table('permissions')->limit(5)->get();
        foreach ($permissions as $permission) {
            echo "- " . $permission->name . "\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
