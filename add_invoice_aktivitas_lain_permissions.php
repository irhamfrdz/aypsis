<?php

/**
 * Script untuk menambahkan permissions Invoice Aktivitas Lain ke database
 * 
 * Jalankan dengan command:
 * php add_invoice_aktivitas_lain_permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Menambahkan Permission Invoice Aktivitas Lain\n";
echo "===========================================\n\n";

// Array of permissions to create
$permissions = [
    'invoice-aktivitas-lain-view',
    'invoice-aktivitas-lain-create',
    'invoice-aktivitas-lain-update',
    'invoice-aktivitas-lain-delete',
];

$createdCount = 0;
$existingCount = 0;

// Create permissions if they don't exist
foreach ($permissions as $permissionName) {
    $exists = DB::table('permissions')
        ->where('name', $permissionName)
        ->exists();
    
    if (!$exists) {
        DB::table('permissions')->insert([
            'name' => $permissionName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Permission '{$permissionName}' berhasil ditambahkan\n";
        $createdCount++;
    } else {
        echo "- Permission '{$permissionName}' sudah ada\n";
        $existingCount++;
    }
}

echo "\n";
echo "===========================================\n";
echo "Summary:\n";
echo "- Permission baru ditambahkan: {$createdCount}\n";
echo "- Permission yang sudah ada: {$existingCount}\n";
echo "===========================================\n\n";

echo "===========================================\n";
echo "Selesai!\n";
echo "Silakan atur permission untuk user melalui:\n";
echo "Menu Master User > Data User > Edit User\n";
echo "===========================================\n";
