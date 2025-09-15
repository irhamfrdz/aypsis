<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "=== TEST SISTEM PERMISSION SETELAH CLEANUP ===\n\n";

// Test 1: Cek permission tersisa
echo "1. JUMLAH PERMISSION:\n";
echo "   - Total permission: " . Permission::count() . "\n";
echo "   - Permission sederhana: " . Permission::where('name', 'not like', '%.%')->count() . "\n";
echo "   - Permission detail: " . Permission::where('name', 'like', '%.%')->count() . "\n\n";

// Test 2: Cek user dengan permission
echo "2. USER DENGAN PERMISSION:\n";
$users = User::with('permissions')->whereHas('permissions')->get();
foreach ($users as $user) {
    echo "   - {$user->name}: {$user->permissions->count()} permission\n";
}
echo "\n";

// Test 3: Cek permission sidebar (yang sering digunakan)
echo "3. PERMISSION SIDEBAR UTAMA:\n";
$sidebarPermissions = [
    'master-karyawan',
    'master-user',
    'master-kontainer',
    'master-tujuan',
    'master-kegiatan',
    'master-mobil',
    'master-permission',
    'master-pricelist-sewa-kontainer',
    'tagihan-kontainer',
    'pranota-supir',
    'pembayaran-pranota-supir',
    'permohonan',
    'master-pranota-tagihan-kontainer',
    'master-pranota-supir',
    'master-pembayaran-pranota-supir',
    'master-permohonan'
];

foreach ($sidebarPermissions as $perm) {
    $exists = Permission::where('name', $perm)->exists();
    echo "   - $perm: " . ($exists ? "✅ Ada" : "❌ Hilang") . "\n";
}
echo "\n";

// Test 4: Cek duplikasi permission
echo "4. CEK DUPLIKASI:\n";
$duplicates = Permission::select('name')
    ->groupBy('name')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($duplicates->isEmpty()) {
    echo "   ✅ Tidak ada duplikasi permission\n";
} else {
    echo "   ⚠️  Ditemukan duplikasi:\n";
    foreach ($duplicates as $dup) {
        echo "      - {$dup->name}\n";
    }
}
echo "\n";

// Test 5: Cek permission admin
echo "5. PERMISSION ADMIN:\n";
$adminPerms = [
    'admin.debug.perms',
    'admin.features',
    'admin.user-approval.index',
    'admin.user-approval.show',
    'admin.user-approval.approve',
    'admin.user-approval.reject'
];

foreach ($adminPerms as $perm) {
    $exists = Permission::where('name', $perm)->exists();
    echo "   - $perm: " . ($exists ? "✅ Ada" : "❌ Hilang") . "\n";
}
echo "\n";

echo "=== TEST SELESAI ===\n";
echo "Jika semua permission sidebar dan admin masih ada, sistem siap digunakan.\n";
