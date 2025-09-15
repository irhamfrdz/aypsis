<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🧪 Test Blokir Akses Master-Karyawan untuk User test4\n";
echo "====================================================\n\n";

$userTest4 = User::where('username', 'test4')->first();
if (!$userTest4) {
    echo "❌ User test4 not found\n";
    exit;
}

// Simpan permission awal untuk restore nanti
$originalPermissions = $userTest4->permissions->pluck('id')->toArray();
echo "💾 Original permissions saved for restore\n\n";

// Hapus semua permission karyawan dari user test4
$karyawanPermissionIds = DB::table('permissions')
    ->where('name', 'like', 'master-karyawan%')
    ->pluck('id')
    ->toArray();

DB::table('user_permissions')
    ->where('user_id', $userTest4->id)
    ->whereIn('permission_id', $karyawanPermissionIds)
    ->delete();

echo "🗑️  Removed karyawan permissions from test4\n\n";

// Refresh user data
$userTest4->refresh();

echo "👤 User: test4 (after permission removal)\n";
echo "   Current permissions: " . $userTest4->permissions->pluck('name')->join(', ') . "\n\n";

// Test akses ke berbagai route master-karyawan
$routesToTest = [
    'master-karyawan.view' => 'View Karyawan',
    'master-karyawan.create' => 'Create Karyawan',
    'master-karyawan.update' => 'Update Karyawan',
    'master-karyawan.delete' => 'Delete Karyawan',
    'master-karyawan.print' => 'Print Karyawan',
    'master-karyawan.export' => 'Export Karyawan',
];

echo "🔍 Testing permission access after removal:\n";
$accessBlocked = true;
foreach ($routesToTest as $permission => $description) {
    $hasAccess = $userTest4->hasPermissionTo($permission);
    echo "   {$description} ({$permission}): " . ($hasAccess ? '✅ ALLOWED' : '❌ DENIED') . "\n";
    if ($hasAccess) $accessBlocked = false;
}

echo "\n📝 Summary:\n";
$karyawanPermissions = $userTest4->permissions->filter(function($perm) {
    return str_starts_with($perm->name, 'master-karyawan');
});

if ($karyawanPermissions->count() > 0) {
    echo "   ⚠️  User test4 still has " . $karyawanPermissions->count() . " karyawan permissions\n";
    echo "   📋 Permissions: " . $karyawanPermissions->pluck('name')->join(', ') . "\n";
    $accessBlocked = false;
} else {
    echo "   ✅ User test4 has NO karyawan permissions\n";
}

if ($accessBlocked) {
    echo "\n🎯 Result: ✅ SUCCESS - User test4 is properly blocked from karyawan access\n";
} else {
    echo "\n🎯 Result: ❌ FAILED - User test4 still has karyawan access\n";
}

// Restore permissions (skip duplicates)
foreach ($originalPermissions as $permId) {
    try {
        DB::table('user_permissions')->insert([
            'user_id' => $userTest4->id,
            'permission_id' => $permId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    } catch (\Illuminate\Database\QueryException $e) {
        // Skip if permission already exists
        if ($e->getCode() !== '23000') {
            throw $e;
        }
    }
}

echo "\n💾 Permissions restored to original state\n";
echo "\n🧪 Test selesai!\n";
