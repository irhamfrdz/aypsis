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

    echo "\n--- Menambahkan Permission ke User ---\n\n";

    $users = DB::table('users')->get();

    if ($users->isEmpty()) {
        echo "❌ Tidak ada user di database\n";
        exit(1);
    }

    echo "User yang tersedia:\n";
    foreach ($users as $index => $user) {
        $displayName = property_exists($user, 'name') ? $user->name : ($user->username ?? 'user');
        echo ($index + 1) . ". {$displayName} ({$user->username})\n";
    }

    echo "\nPilih user (masukkan nomor, atau 'all' untuk semua user, atau 'skip' untuk tidak menambahkan): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);

    $selectedUsers = [];

    if (strtolower($line) === 'all') {
        $selectedUsers = $users;
        echo "\n✅ Memproses semua user...\n\n";
    } elseif (strtolower($line) === 'skip' || $line === '') {
        $selectedUsers = [];
        echo "\nℹ️  Melewati penambahan kepada user. Hanya menambahkan permissions di DB.\n";
    } else {
        $index = intval($line) - 1;
        if (isset($users[$index])) {
            $selectedUsers = [$users[$index]];
            $displayName = property_exists($users[$index], 'name') ? $users[$index]->name : ($users[$index]->username ?? 'user');
            echo "\n✅ Memproses user: {$displayName}\n\n";
        } else {
            echo "❌ Pilihan tidak valid\n";
            exit(1);
        }
    }

    foreach ($selectedUsers as $user) {
        $displayName = property_exists($user, 'name') ? $user->name : ($user->username ?? 'user');
        echo "Processing user: {$displayName} (ID: {$user->id})\n";

        foreach ($permissions as $permName) {
            $permission = DB::table('permissions')->where('name', $permName)->first();

            if (!$permission) {
                echo "  ⚠️  Permission '$permName' tidak ditemukan\n";
                continue;
            }

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
    echo "3. Coba akses fitur pranota OB\n";

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
