<?php

// Script untuk memperbaiki permission database agar sesuai UI Matrix

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== FIXING DATABASE PERMISSIONS ===\n\n";

// 1. HAPUS permission yang tidak ada di UI Matrix
$extraPermissions = [
    'master-bank-print', 'master-bank-export',
    'master-cabang-print', 'master-cabang-export',
    'master-coa-print', 'master-coa-export',
    'master-divisi-print', 'master-divisi-export',
    'master-pajak-print', 'master-pajak-export',
    'master-tujuan-print', 'master-tujuan-export',
];

foreach ($extraPermissions as $permName) {
    $deleted = DB::table('permissions')->where('name', $permName)->delete();
    if ($deleted > 0) {
        echo "✅ Deleted: $permName\n";
    }
}

// 2. TAMBAH permission approve yang missing
$missingApprovePermissions = [
    'pembayaran-pranota-cat-approve',
    'pembayaran-pranota-kontainer-approve',
    'pembayaran-pranota-perbaikan-kontainer-approve',
    'permohonan-memo-approve',
    'pranota-cat-approve',
    'pranota-kontainer-sewa-approve',
    'pranota-perbaikan-kontainer-approve',
];

foreach ($missingApprovePermissions as $permName) {
    $exists = DB::table('permissions')->where('name', $permName)->exists();
    if (!$exists) {
        DB::table('permissions')->insert([
            'name' => $permName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Created: $permName\n";
    }
}

// 3. TAMBAH permission untuk tagihan-kontainer-sewa yang kurang
$tagihanKontainerMissing = [
    'tagihan-kontainer-sewa-approve',
    'tagihan-kontainer-sewa-print',
    'tagihan-kontainer-sewa-export',
];

foreach ($tagihanKontainerMissing as $permName) {
    $exists = DB::table('permissions')->where('name', $permName)->exists();
    if (!$exists) {
        DB::table('permissions')->insert([
            'name' => $permName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Created: $permName\n";
    }
}

// 4. TAMBAH permission untuk user-approval yang kurang
$userApprovalMissing = ['user-approval-view'];

foreach ($userApprovalMissing as $permName) {
    $exists = DB::table('permissions')->where('name', $permName)->exists();
    if (!$exists) {
        DB::table('permissions')->insert([
            'name' => $permName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Created: $permName\n";
    }
}

echo "\n=== CLEANUP COMPLETED ===\n";
