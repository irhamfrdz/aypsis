<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Updating Perbaikan Kontainer permissions...\n";

$oldPermissions = [
    'master-perbaikan-kontainer.view',
    'master-perbaikan-kontainer.create',
    'master-perbaikan-kontainer.update',
    'master-perbaikan-kontainer.delete',
];

$newPermissions = [
    [
        'name' => 'perbaikan-kontainer.view',
        'description' => 'Melihat daftar perbaikan kontainer',
    ],
    [
        'name' => 'perbaikan-kontainer.create',
        'description' => 'Membuat perbaikan kontainer baru',
    ],
    [
        'name' => 'perbaikan-kontainer.update',
        'description' => 'Mengupdate data perbaikan kontainer',
    ],
    [
        'name' => 'perbaikan-kontainer.delete',
        'description' => 'Menghapus data perbaikan kontainer',
    ],
];

$updated = 0;
$skipped = 0;

foreach ($oldPermissions as $index => $oldPermission) {
    $exists = DB::table('permissions')->where('name', $oldPermission)->exists();

    if ($exists) {
        // Update existing permission
        DB::table('permissions')
            ->where('name', $oldPermission)
            ->update([
                'name' => $newPermissions[$index]['name'],
                'description' => $newPermissions[$index]['description'],
                'updated_at' => now(),
            ]);
        echo "✓ Updated permission: {$oldPermission} -> {$newPermissions[$index]['name']}\n";
        $updated++;
    } else {
        // Create new permission if old one doesn't exist
        DB::table('permissions')->insert([
            'name' => $newPermissions[$index]['name'],
            'description' => $newPermissions[$index]['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Created new permission: {$newPermissions[$index]['name']}\n";
        $updated++;
    }
}

echo "\nSummary:\n";
echo "Updated: $updated permissions\n";
echo "Skipped: $skipped permissions\n";
echo "Total processed: " . ($updated + $skipped) . " permissions\n";

echo "\nPerbaikan Kontainer permissions updated successfully!\n";
