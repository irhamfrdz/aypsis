<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MENAMBAHKAN PERMISSIONS YANG MISSING (FORMAT DASH) ===\n\n";

// Daftar permissions yang perlu ditambahkan dengan format DASH (sesuai check_operational_permissions.php)
$permissionsToAdd = [
    // Pembayaran Pranota Supir - yang missing dengan format dash
    'pembayaran-pranota-supir-view',
    'pembayaran-pranota-supir-create',
    'pembayaran-pranota-supir-update',
    'pembayaran-pranota-supir-delete',
    'pembayaran-pranota-supir-approve',
    'pembayaran-pranota-supir-print',
    'pembayaran-pranota-supir-export',

    // Permohonan - yang missing dengan format dash
    'permohonan-update',
    'permohonan-approve',
    'permohonan-print',
    'permohonan-export',
];

$addedCount = 0;
$skippedCount = 0;

echo "Memeriksa dan menambahkan permissions dengan format DASH...\n\n";

foreach ($permissionsToAdd as $permissionName) {
    // Cek apakah permission sudah ada menggunakan DB query langsung
    $existingPermission = DB::table('permissions')->where('name', $permissionName)->first();

    if ($existingPermission) {
        echo "⏭️  SKIPPED: Permission '{$permissionName}' sudah ada\n";
        $skippedCount++;
    } else {
        // Buat permission baru menggunakan DB query langsung
        try {
            DB::table('permissions')->insert([
                'name' => $permissionName,
                'description' => 'Akses ' . ucfirst(str_replace(['-', '.'], [' ', ' '], $permissionName)),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ ADDED: Permission '{$permissionName}' berhasil ditambahkan\n";
            $addedCount++;
        } catch (Exception $e) {
            echo "❌ ERROR: Gagal menambahkan '{$permissionName}' - " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== RINGKASAN ===\n";
echo "✅ Permissions berhasil ditambahkan: {$addedCount}\n";
echo "⏭️  Permissions sudah ada (di-skip): {$skippedCount}\n";
echo "📊 Total permissions diproses: " . count($permissionsToAdd) . "\n\n";

// Verifikasi hasil dengan format yang benar
echo "=== VERIFIKASI AKHIR (FORMAT DASH) ===\n";

$modulesToCheck = [
    'pembayaran-pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'permohonan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export']
];

foreach ($modulesToCheck as $module => $actions) {
    echo "\n📋 Status permissions untuk '{$module}' (format dash):\n";

    $totalPermissions = count($actions);
    $existingPermissions = 0;

    foreach ($actions as $action) {
        $permissionName = $module . '-' . $action;
        $exists = DB::table('permissions')->where('name', $permissionName)->exists();

        if ($exists) {
            echo "  ✅ {$action}\n";
            $existingPermissions++;
        } else {
            echo "  ❌ {$action} (MISSING)\n";
        }
    }

    echo "  📊 Total: {$existingPermissions}/{$totalPermissions} permissions\n";
}

echo "\n🎉 Proses selesai!\n";
