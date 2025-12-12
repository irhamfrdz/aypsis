<?php

/**
 * Script untuk menambahkan permission Pranota OB
 * Jalankan: php add_pranota_ob_permission.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Menambahkan Permission Pranota OB ===\n\n";

try {
    $permissions = [
        'pranota-ob-view',
        'pranota-ob-create',
        'pranota-ob-update',
        'pranota-ob-delete',
        'pranota-ob-print',
        'pranota-ob-export',
    ];

    foreach ($permissions as $permName) {
        $perm = DB::table('permissions')->where('name', $permName)->first();
        if (!$perm) {
            $id = DB::table('permissions')->insertGetId([
                'name' => $permName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Permission '$permName' dibuat (ID: $id)\n";
        } else {
            echo "ℹ️  Permission '$permName' sudah ada (ID: {$perm->id})\n";
        }
    }

    echo "\n✅ SELESAI! Permission berhasil ditambahkan ke database.\n";
    echo "\nℹ️  Tidak ada user yang otomatis mendapatkan permission ini.\n";
    echo "ℹ️  Admin dapat mengatur permission melalui halaman Edit User.\n";

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
