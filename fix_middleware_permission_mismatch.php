<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PERBAIKI MASALAH PERMISSION DAN MIDDLEWARE ===\n\n";

try {
    $userId = 1; // admin user

    echo "=== 1. TAMBAHKAN PERMISSION CREATE YANG HILANG ===\n";

    // Permission create yang hilang
    $missingCreatePermissions = [
        'master-cabang-create',
        'master-coa-create',
        'master-kode-nomor-create',
        'master-nomor-terakhir-create',
        'master-tipe-akun-create',
        'master-tujuan-create',
        'master-kegiatan-create'
    ];

    foreach ($missingCreatePermissions as $permissionName) {
        // Cek apakah permission sudah ada
        $permission = DB::table('permissions')
            ->where('name', $permissionName)
            ->first();

        if (!$permission) {
            // Buat permission baru
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $permissionName,
                'description' => ucwords(str_replace(['-', '_'], ' ', $permissionName)),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "➕ CREATED permission: $permissionName\n";
        } else {
            echo "✅ Permission sudah ada: $permissionName\n";
            $permissionId = $permission->id;
        }

        // Cek apakah user sudah punya permission ini
        $userHasPermission = DB::table('user_permissions')
            ->where('user_id', $userId)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$userHasPermission) {
            DB::table('user_permissions')->insert([
                'user_id' => $userId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✅ Ditambahkan ke user admin\n";
        } else {
            echo "   ✅ User admin sudah punya permission ini\n";
        }
    }

    echo "\n=== 2. ANALISA MASALAH ROUTE MIDDLEWARE ===\n";

    echo "MASALAH YANG DITEMUKAN:\n";
    echo "- Routes menggunakan MULTIPLE middleware 'can:' yang menyebabkan user harus punya SEMUA permission\n";
    echo "- Contoh: Route cabang.index butuh master-cabang-view DAN master-cabang-create\n";
    echo "- Seharusnya: Route cabang.index hanya butuh master-cabang-view\n\n";

    echo "ROUTES YANG BERMASALAH:\n";
    echo "1. master/cabang - Butuh view+create untuk akses index\n";
    echo "2. master/coa - Butuh view+create untuk akses index\n";
    echo "3. master/tujuan - Butuh view+create untuk akses index\n\n";

    echo "ROUTES YANG SUDAH BENAR:\n";
    echo "1. master/kegiatan - Hanya butuh view untuk akses index ✅\n";
    echo "2. master/kode-nomor - Ada route tapi nama berbeda (master.kode-nomor)\n";
    echo "3. master/tipe-akun - Ada route tapi nama berbeda (master.tipe-akun)\n";
    echo "4. master/nomor-terakhir - Ada route tapi nama berbeda (master.nomor-terakhir)\n\n";

    echo "=== 3. TEST AKSES DENGAN PERMISSION YANG SUDAH DIPERBAIKI ===\n";

    // Load user untuk test
    $user = \App\Models\User::find($userId);

    $testModules = [
        'cabang' => [
            'view' => 'master-cabang-view',
            'create' => 'master-cabang-create',
            'route' => 'master.cabang.index'
        ],
        'coa' => [
            'view' => 'master-coa-view',
            'create' => 'master-coa-create',
            'route' => 'master-coa-index'
        ],
        'tipe_akun' => [
            'view' => 'master-tipe-akun-view',
            'create' => 'master-tipe-akun-create',
            'route' => 'master.tipe-akun.index'
        ],
        'kode_nomor' => [
            'view' => 'master-kode-nomor-view',
            'create' => 'master-kode-nomor-create',
            'route' => 'master.kode-nomor.index'
        ],
        'nomor_terakhir' => [
            'view' => 'master-nomor-terakhir-view',
            'create' => 'master-nomor-terakhir-create',
            'route' => 'master.nomor-terakhir.index'
        ],
        'tujuan' => [
            'view' => 'master-tujuan-view',
            'create' => 'master-tujuan-create',
            'route' => 'tujuan.index'
        ],
        'kegiatan' => [
            'view' => 'master-kegiatan-view',
            'create' => 'master-kegiatan-create',
            'route' => 'kegiatan.index'
        ]
    ];

    foreach ($testModules as $module => $perms) {
        echo "\n📋 MODULE: " . strtoupper($module) . "\n";

        $hasView = $user->can($perms['view']);
        $hasCreate = $user->can($perms['create']);

        echo "   View: " . ($hasView ? '✅' : '❌') . " can('{$perms['view']}')\n";
        echo "   Create: " . ($hasCreate ? '✅' : '❌') . " can('{$perms['create']}')\n";
        echo "   Route: {$perms['route']}\n";

        if ($hasView && $hasCreate) {
            echo "   🟢 STATUS: AKSES GRANTED (view+create ada)\n";
        } else if ($hasView && !$hasCreate) {
            echo "   🟡 STATUS: PARTIAL ACCESS (hanya view, butuh create untuk middleware route)\n";
        } else {
            echo "   🔴 STATUS: AKSES DENIED (view tidak ada)\n";
        }
    }

    echo "\n=== 4. SOLUSI UNTUK MENGATASI MASALAH ===\n";

    echo "PILIHAN A - RECOMMENDED: Perbaiki Route Middleware (butuh edit routes/web.php)\n";
    echo "- Ubah middleware resource routes agar index hanya butuh 'view' permission\n";
    echo "- Contoh: 'index' => 'can:master-cabang-view' (bukan view+create)\n\n";

    echo "PILIHAN B - QUICK FIX: Tambahkan semua create permission ke admin\n";
    echo "- User admin sudah punya semua create permission sekarang\n";
    echo "- Routes dengan multiple middleware akan bisa diakses\n\n";

    echo "=== 5. TEST AKSES SEKARANG ===\n";
    echo "Dengan create permission yang sudah ditambahkan:\n\n";

    // Test akses untuk routes yang bermasalah
    $problematicRoutes = [
        'master.cabang.index' => ['master-cabang-view', 'master-cabang-create'],
        'master-coa-index' => ['master-coa-view', 'master-coa-create'],
        'tujuan.index' => ['master-tujuan-view', 'master-tujuan-create']
    ];

    foreach ($problematicRoutes as $routeName => $requiredPerms) {
        echo "🛣️  Route: $routeName\n";

        $allGranted = true;
        foreach ($requiredPerms as $perm) {
            $hasAccess = $user->can($perm);
            $status = $hasAccess ? '✅' : '❌';
            echo "   $status $perm\n";
            if (!$hasAccess) {
                $allGranted = false;
            }
        }

        if ($allGranted) {
            echo "   🟢 AKSES: GRANTED - Sekarang bisa diakses!\n\n";
        } else {
            echo "   🔴 AKSES: DENIED - Masih ada permission yang kurang\n\n";
        }
    }

    echo "=== INSTRUKSI FINAL ===\n";
    echo "1. ✅ Permission create sudah ditambahkan\n";
    echo "2. 🔄 LOGOUT dan LOGIN kembali\n";
    echo "3. 🧪 Test akses menu: Cabang, COA, Kode Nomor, etc.\n";
    echo "4. ✅ Sekarang seharusnya bisa diakses semua!\n\n";

    echo "💡 NOTE: Jika masih bermasalah, kemungkinan ada cache browser.\n";
    echo "   Coba: Ctrl+F5 atau buka di incognito/private browser\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
