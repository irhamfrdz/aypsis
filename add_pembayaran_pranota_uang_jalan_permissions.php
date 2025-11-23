<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Menambahkan Permission Pembayaran Pranota Uang Jalan ===\n\n";

$permissions = [
    [
        'name' => 'pembayaran-pranota-uang-jalan-view',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'pembayaran-pranota-uang-jalan-create',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'pembayaran-pranota-uang-jalan-update',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'pembayaran-pranota-uang-jalan-delete',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'pembayaran-pranota-uang-jalan-approve',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'pembayaran-pranota-uang-jalan-print',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'pembayaran-pranota-uang-jalan-export',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

try {
    DB::beginTransaction();
    
    $insertedCount = 0;
    $skippedCount = 0;
    
    foreach ($permissions as $permission) {
        // Check if permission already exists
        $exists = DB::table('permissions')
            ->where('name', $permission['name'])
            ->exists();
        
        if (!$exists) {
            DB::table('permissions')->insert($permission);
            echo "✓ Permission '{$permission['name']}' berhasil ditambahkan\n";
            $insertedCount++;
        } else {
            echo "⚠ Permission '{$permission['name']}' sudah ada, dilewati\n";
            $skippedCount++;
        }
    }
    
    DB::commit();
    
    echo "\n=== SELESAI ===\n";
    echo "Total ditambahkan: {$insertedCount}\n";
    echo "Total dilewati: {$skippedCount}\n";
    echo "\nPermission Pembayaran Pranota Uang Jalan berhasil ditambahkan!\n";
    echo "Silakan assign permission ini ke user yang membutuhkan.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
