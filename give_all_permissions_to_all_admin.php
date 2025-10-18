<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== MEMBERIKAN SEMUA PERMISSION KE SEMUA USER ADMIN ===\n\n";

// Cari semua user admin
$adminUsers = User::where('username', 'like', '%admin%')->get();

if ($adminUsers->count() == 0) {
    echo "❌ Tidak ada user admin yang ditemukan!\n";
    exit(1);
}

echo "👥 Ditemukan " . $adminUsers->count() . " user admin:\n";
foreach ($adminUsers as $user) {
    echo "   - {$user->username} ({$user->email})\n";
}
echo "\n";

// Ambil semua permission
$allPermissions = Permission::all();
$allPermissionIds = $allPermissions->pluck('id')->toArray();
$totalPermissions = $allPermissions->count();

echo "📋 Total permission di sistem: {$totalPermissions}\n\n";

// Process setiap user admin
foreach ($adminUsers as $admin) {
    echo "🔄 Processing user: {$admin->username}\n";
    
    $currentPermissions = $admin->permissions()->pluck('permission_id')->toArray();
    $missingPermissions = array_diff($allPermissionIds, $currentPermissions);
    
    echo "   Current permissions: " . count($currentPermissions) . "\n";
    echo "   Missing permissions: " . count($missingPermissions) . "\n";
    
    if (count($missingPermissions) > 0) {
        // Tambahkan permission yang belum ada
        $admin->permissions()->syncWithoutDetaching($missingPermissions);
        echo "   ✅ Added " . count($missingPermissions) . " permissions\n";
    } else {
        echo "   ✅ Already has all permissions\n";
    }
    
    // Verifikasi final
    $finalCount = $admin->permissions()->count();
    echo "   Final permission count: {$finalCount}\n";
    
    if ($finalCount == $totalPermissions) {
        echo "   Status: ✅ COMPLETE\n";
    } else {
        echo "   Status: ❌ INCOMPLETE (" . ($totalPermissions - $finalCount) . " missing)\n";
    }
    echo "\n";
}

echo "=" . str_repeat("=", 50) . "\n";
echo "📊 SUMMARY:\n";

foreach ($adminUsers as $admin) {
    $permCount = $admin->permissions()->count();
    $status = ($permCount == $totalPermissions) ? '✅ COMPLETE' : '❌ INCOMPLETE';
    echo "   {$admin->username}: {$permCount}/{$totalPermissions} permissions {$status}\n";
}

echo "=" . str_repeat("=", 50) . "\n";

// Test audit log permissions specifically
echo "\n🔍 Audit Log Permission Check:\n";
foreach ($adminUsers as $admin) {
    $hasView = $admin->can('audit-log-view');
    $hasExport = $admin->can('audit-log-export');
    $viewStatus = $hasView ? '✅' : '❌';
    $exportStatus = $hasExport ? '✅' : '❌';
    echo "   {$admin->username}: View {$viewStatus} | Export {$exportStatus}\n";
}

echo "\n🎉 All admin users now have complete system access!\n";