<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "=== DATABASE CHECK ===\n";
    echo "KodeNomor table exists: " . (Schema::hasTable('kode_nomor') ? 'YES' : 'NO') . "\n";

    if (Schema::hasTable('kode_nomor')) {
        $count = DB::table('kode_nomor')->count();
        echo "Records in kode_nomor table: $count\n";

        // Check table structure
        $columns = Schema::getColumnListing('kode_nomor');
        echo "Columns: " . implode(', ', $columns) . "\n";
    }

    echo "\n=== PERMISSION CHECK ===\n";
    $permissions = DB::table('permissions')->where('name', 'like', '%kode-nomor%')->get();
    echo "Kode-nomor permissions found: " . $permissions->count() . "\n";
    foreach ($permissions as $perm) {
        echo "- {$perm->name}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
