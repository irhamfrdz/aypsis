<?php

/**
 * Script untuk menambahkan permission Checkpoint Kontainer Masuk
 * 
 * Cara menjalankan:
 * php add_checkpoint_kontainer_masuk_permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "🚀 Memulai proses penambahan permission...\n\n";

    // Define permissions untuk Checkpoint Kontainer Masuk
    $permissions = [
        [
            'name' => 'checkpoint-kontainer-masuk-view',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'checkpoint-kontainer-masuk-create',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'checkpoint-kontainer-masuk-delete',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];

    $addedCount = 0;
    $skippedCount = 0;

    foreach ($permissions as $permission) {
        // Check if permission already exists
        $exists = DB::table('permissions')
            ->where('name', $permission['name'])
            ->exists();

        if ($exists) {
            echo "⏭️  Permission '{$permission['name']}' sudah ada, dilewati.\n";
            $skippedCount++;
        } else {
            DB::table('permissions')->insert($permission);
            echo "✅ Permission '{$permission['name']}' berhasil ditambahkan.\n";
            $addedCount++;
        }
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 SUMMARY:\n";
    echo "   ✅ Ditambahkan: {$addedCount} permission\n";
    echo "   ⏭️  Dilewati: {$skippedCount} permission (sudah ada)\n";
    echo str_repeat("=", 60) . "\n\n";

    if ($addedCount > 0) {
        echo "🎉 Selesai! Permission berhasil ditambahkan ke database.\n";
        echo "\n📝 Langkah selanjutnya:\n";
        echo "   1. Assign permission ke role yang diinginkan melalui menu Role & Permission\n";
        echo "   2. Atau assign langsung ke user tertentu\n";
        echo "   3. User dengan permission 'checkpoint-kontainer-masuk-view' dapat mengakses halaman\n\n";
    } else {
        echo "ℹ️  Tidak ada permission baru yang ditambahkan.\n\n";
    }

    // Tampilkan ID permission yang baru ditambahkan (untuk referensi)
    if ($addedCount > 0) {
        echo "📋 Daftar Permission ID:\n";
        $permissionIds = DB::table('permissions')
            ->whereIn('name', ['checkpoint-kontainer-masuk-view', 'checkpoint-kontainer-masuk-create', 'checkpoint-kontainer-masuk-delete'])
            ->select('id', 'name')
            ->get();
        
        foreach ($permissionIds as $perm) {
            echo "   ID {$perm->id}: {$perm->name}\n";
        }
        echo "\n";
    }

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "✨ Script selesai dijalankan!\n";
