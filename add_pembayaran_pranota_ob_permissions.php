<?php

/**
 * Script untuk menambahkan permissions Pembayaran Pranota OB ke database
 * Jalankan script ini dengan: php add_pembayaran_pranota_ob_permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "========================================\n";
echo "Add Pembayaran Pranota OB Permissions\n";
echo "========================================\n\n";

try {
    // Daftar permissions yang akan ditambahkan
    $permissions = [
        [
            'name' => 'pembayaran-pranota-ob-view',
            'description' => 'Izin untuk melihat pembayaran pranota OB'
        ],
        [
            'name' => 'pembayaran-pranota-ob-create',
            'description' => 'Izin untuk membuat pembayaran pranota OB'
        ],
        [
            'name' => 'pembayaran-pranota-ob-update',
            'description' => 'Izin untuk mengubah pembayaran pranota OB'
        ],
        [
            'name' => 'pembayaran-pranota-ob-delete',
            'description' => 'Izin untuk menghapus pembayaran pranota OB'
        ],
        [
            'name' => 'pembayaran-pranota-ob-approve',
            'description' => 'Izin untuk menyetujui pembayaran pranota OB'
        ],
        [
            'name' => 'pembayaran-pranota-ob-print',
            'description' => 'Izin untuk mencetak pembayaran pranota OB'
        ],
        [
            'name' => 'pembayaran-pranota-ob-export',
            'description' => 'Izin untuk mengekspor pembayaran pranota OB'
        ],
    ];

    $now = Carbon::now();
    $addedCount = 0;
    $skippedCount = 0;

    echo "Memproses " . count($permissions) . " permissions...\n\n";

    foreach ($permissions as $permission) {
        // Cek apakah permission sudah ada
        $exists = DB::table('permissions')
            ->where('name', $permission['name'])
            ->exists();

        if ($exists) {
            echo "⏭️  SKIP: {$permission['name']} (sudah ada)\n";
            $skippedCount++;
            continue;
        }

        // Insert permission baru
        DB::table('permissions')->insert([
            'name' => $permission['name'],
            'description' => $permission['description'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        echo "✅ ADDED: {$permission['name']}\n";
        $addedCount++;
    }

    echo "\n========================================\n";
    echo "Ringkasan:\n";
    echo "✅ Berhasil ditambahkan: {$addedCount}\n";
    echo "⏭️  Dilewati (sudah ada): {$skippedCount}\n";
    echo "📊 Total diproses: " . count($permissions) . "\n";
    echo "========================================\n\n";

    // Tampilkan permissions yang sudah ditambahkan
    if ($addedCount > 0) {
        echo "Permissions yang berhasil ditambahkan:\n";
        $addedPermissions = DB::table('permissions')
            ->whereIn('name', array_column($permissions, 'name'))
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        foreach ($addedPermissions as $perm) {
            echo "  [{$perm->id}] {$perm->name}\n";
            echo "      → {$perm->description}\n";
        }
        echo "\n";
    }

    // Berikan instruksi tambahan
    echo "========================================\n";
    echo "Langkah Selanjutnya:\n";
    echo "========================================\n";
    echo "1. Buka halaman Master User di aplikasi\n";
    echo "2. Edit user yang ingin diberi akses\n";
    echo "3. Scroll ke bagian 'Pembayaran'\n";
    echo "4. Centang permission yang diinginkan untuk 'Pembayaran Pranota OB'\n";
    echo "5. Klik 'Perbarui' untuk menyimpan\n\n";

    echo "Atau jalankan query berikut untuk memberikan semua permission ke admin:\n";
    echo "----------------------------------------\n";
    
    // Ambil admin user ID
    $adminUser = DB::table('users')->where('username', 'admin')->first();
    if ($adminUser) {
        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_column($permissions, 'name'))
            ->pluck('id')
            ->toArray();

        echo "-- Berikan permission ke user admin (ID: {$adminUser->id})\n";
        foreach ($permissionIds as $permId) {
            echo "INSERT INTO permission_user (user_id, permission_id, created_at, updated_at) VALUES ({$adminUser->id}, {$permId}, NOW(), NOW());\n";
        }
    }
    echo "----------------------------------------\n\n";

    echo "✅ Script selesai dijalankan!\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    exit(1);
}
