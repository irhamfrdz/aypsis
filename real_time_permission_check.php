<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Permission;

echo "=== REAL-TIME ADMIN PERMISSION CHECK ===\n\n";

// Find admin user
$adminUser = User::where('username', 'admin')->first();
if (!$adminUser) {
    echo "❌ Admin user not found!\n";
    exit;
}

echo "✅ Found admin user: {$adminUser->username} (ID: {$adminUser->id})\n\n";

// Simulate login
Auth::login($adminUser);
echo "✅ Admin logged in\n\n";

echo "=== PERMISSION ANALYSIS ===\n";

// Check all kode-nomor related permissions
$kodeNomorPermissions = Permission::where('name', 'like', '%kode-nomor%')->get();
echo "All kode-nomor permissions in database:\n";
foreach ($kodeNomorPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}
echo "\n";

// Check user's permissions
$userPermissions = $adminUser->permissions->pluck('name')->toArray();
echo "Admin user's permissions:\n";
foreach ($userPermissions as $perm) {
    echo "- $perm\n";
}
echo "\n";

// Check specific permission
$viewPerm = Permission::where('name', 'master-kode-nomor-view')->first();
if ($viewPerm) {
    $userHasPermission = $adminUser->permissions->contains('id', $viewPerm->id);
    echo "Has 'master-kode-nomor-view' permission: " . ($userHasPermission ? 'YES' : 'NO') . "\n";
} else {
    echo "❌ Permission 'master-kode-nomor-view' not found in database!\n";
}

echo "\n=== GATE CHECK ===\n";

// Test Gate directly (if available)
try {
    $gateAllows = Gate::allows('master-kode-nomor-view');
    echo "Gate::allows('master-kode-nomor-view'): " . ($gateAllows ? 'YES' : 'NO') . "\n";

    $gateDenies = Gate::denies('master-kode-nomor-view');
    echo "Gate::denies('master-kode-nomor-view'): " . ($gateDenies ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "Gate check failed: " . $e->getMessage() . "\n";
}

echo "\n=== SIDEBAR LOGIC SIMULATION ===\n";

// Simulate the exact sidebar logic from app.blade.php
$user = Auth::user();

// Check master permissions logic - simplified version
$masterPermissionsToCheck = [
    'master-karyawan-view',
    'master-user-view',
    'master-kontainer-view',
    'master-tujuan-view',
    'master-kegiatan-view',
    'master-permission-view',
    'master-mobil-view',
    'master-divisi-view',
    'master-pajak-view',
    'master-pricelist-sewa-kontainer-view',
    'master-bank-view',
    'master-coa-view',
    'master-vendor-bengkel-view',
    'master-kode-nomor-view'
];

$hasMasterPermissions = false;
foreach ($masterPermissionsToCheck as $perm) {
    $permRecord = Permission::where('name', $perm)->first();
    if ($permRecord && $adminUser->permissions->contains('id', $permRecord->id)) {
        $hasMasterPermissions = true;
        echo "✅ Has master permission: $perm\n";
        break;
    }
}

if (!$hasMasterPermissions) {
    echo "❌ No master permissions found\n";
}

// Check specific kode-nomor permission
$specificPermission = false;
if ($viewPerm && $adminUser->permissions->contains('id', $viewPerm->id)) {
    $specificPermission = true;
    echo "✅ Has master-kode-nomor-view permission\n";
} else {
    echo "❌ Missing master-kode-nomor-view permission\n";
}

echo "\n=== CONCLUSION ===\n";
if ($hasMasterPermissions && $specificPermission) {
    echo "🎉 MENU SHOULD APPEAR!\n";
    echo "📍 Location: Inside 'Master Data' dropdown\n";
} else {
    echo "❌ Menu will NOT appear due to:\n";
    if (!$hasMasterPermissions) {
        echo "   - Missing master permissions\n";
    }
    if (!$specificPermission) {
        echo "   - Missing master-kode-nomor-view permission\n";
    }
}

echo "\n=== TROUBLESHOOTING STEPS ===\n";
echo "1. Check if you're logged in as admin\n";
echo "2. Clear Laravel cache: php artisan cache:clear\n";
echo "3. Clear config cache: php artisan config:clear\n";
echo "4. Clear route cache: php artisan route:clear\n";
echo "5. Clear view cache: php artisan view:clear\n";
echo "6. Restart web server\n";
echo "7. Hard refresh browser (Ctrl+F5)\n";

if (!$specificPermission && $viewPerm) {
    echo "\n⚠️  ASSIGNING PERMISSION MANUALLY...\n";
    if (!$adminUser->permissions->contains('id', $viewPerm->id)) {
        $adminUser->permissions()->attach($viewPerm->id);
        echo "✅ Permission assigned successfully!\n";
        echo "🔄 Please logout and login again\n";
    }
}
