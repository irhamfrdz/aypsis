<?php

/**
 * Script untuk menambahkan permission import pranota kontainer sewa
 * Jalankan: php add_pranota_import_permission.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Menambahkan Permission Import Pranota Kontainer Sewa ===\n\n";

try {
    // Check if permissions already exist
    $permissions = [
        'pranota-kontainer-sewa-view',
        'pranota-kontainer-sewa-create',
        'pranota-kontainer-sewa-update',
        'pranota-kontainer-sewa-delete',
        'pranota-kontainer-sewa-print',
    ];

    // Ensure all permissions exist
    foreach ($permissions as $permName) {
        $perm = DB::table('permissions')->where('name', $permName)->first();
        if (!$perm) {
            DB::table('permissions')->insert([
                'name' => $permName,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Permission '$permName' dibuat\n";
        } else {
            echo "ℹ️  Permission '$permName' sudah ada (ID: {$perm->id})\n";
        }
    }

    echo "\n--- Menambahkan Permission ke User ---\n\n";

    // Get all users
    $users = DB::table('users')->get();

    if ($users->isEmpty()) {
        echo "❌ Tidak ada user di database\n";
        exit(1);
    }

    echo "User yang tersedia:\n";
    foreach ($users as $index => $user) {
        echo ($index + 1) . ". {$user->name} ({$user->email})\n";
    }

    echo "\nPilih user (masukkan nomor, atau 'all' untuk semua user): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);

    $selectedUsers = [];

    if (strtolower($line) === 'all') {
        $selectedUsers = $users;
        echo "\n✅ Memproses semua user...\n\n";
    } else {
        $index = intval($line) - 1;
        if (isset($users[$index])) {
            $selectedUsers = [$users[$index]];
            echo "\n✅ Memproses user: {$users[$index]->name}\n\n";
        } else {
            echo "❌ Pilihan tidak valid\n";
            exit(1);
        }
    }

    // Assign permissions to selected users
    foreach ($selectedUsers as $user) {
        echo "Processing user: {$user->name} (ID: {$user->id})\n";

        foreach ($permissions as $permName) {
            $permission = DB::table('permissions')->where('name', $permName)->first();

            if (!$permission) {
                echo "  ⚠️  Permission '$permName' tidak ditemukan\n";
                continue;
            }

            // Check if user already has permission
            $hasPermission = DB::table('model_has_permissions')
                ->where('permission_id', $permission->id)
                ->where('model_type', 'App\\Models\\User')
                ->where('model_id', $user->id)
                ->exists();

            if (!$hasPermission) {
                DB::table('model_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id,
                ]);
                echo "  ✅ Permission '{$permName}' ditambahkan\n";
            } else {
                echo "  ℹ️  Permission '{$permName}' sudah ada\n";
            }
        }
        echo "\n";
    }

    echo "\n✅ SELESAI! Permission berhasil ditambahkan.\n";
    echo "\nSilakan:\n";
    echo "1. Logout dari aplikasi\n";
    echo "2. Login kembali\n";
    echo "3. Coba akses fitur import pranota kontainer sewa\n";

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
