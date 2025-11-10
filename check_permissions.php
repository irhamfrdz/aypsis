<?php

// Simple permission check
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Cek permissions Master Pricelist OB
    $count = DB::table('permissions')
               ->where('name', 'LIKE', 'master-pricelist-ob%')
               ->count();
               
    echo "Total Master Pricelist OB permissions: {$count}\n";
    
    if ($count == 0) {
        // Insert permissions
        $permissions = [
            ['name' => 'master-pricelist-ob-view', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-ob-create', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-ob-update', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-ob-delete', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert($permission);
            echo "✓ Inserted: {$permission['name']}\n";
        }
    } else {
        echo "Permissions sudah ada.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}