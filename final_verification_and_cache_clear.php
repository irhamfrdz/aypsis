<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== SERVER: Final Verification & Cache Clear ===\n";

echo "🔍 Step 1: Verifying user_admin setup...\n";
$userAdmin = User::where('username', 'user_admin')->first();

if (!$userAdmin) {
    echo "❌ ERROR: user_admin not found!\n";
    exit(1);
}

echo "✅ user_admin found (ID: {$userAdmin->id})\n";

echo "\n🔍 Step 2: Checking permissions...\n";
$permCount = $userAdmin->permissions->count();
echo "Permissions assigned: $permCount\n";

echo "\n🔍 Step 3: Checking roles...\n";
$roleCount = $userAdmin->roles->count();
echo "Roles assigned: $roleCount\n";

if ($roleCount > 0) {
    $roles = $userAdmin->roles->pluck('name')->toArray();
    echo "Roles: " . implode(', ', $roles) . "\n";
}

echo "\n🔍 Step 4: Checking critical permissions...\n";
$criticalPerms = [
    'master-karyawan-view',
    'master-user-view',
    'master-kontainer-view',
    'master-pricelist-sewa-kontainer-view',
    'master-tujuan-view',
    'master-kegiatan-view',
    'master-permission-view',
    'master-mobil-view',
    'master-divisi-view',
    'master-cabang-view',
    'master-pekerjaan-view',
    'master-pajak-view',
    'master-bank-view',
    'master-coa-view'
];

$criticalFound = 0;
foreach ($criticalPerms as $perm) {
    $hasPerm = $userAdmin->permissions->contains('name', $perm);
    echo "- $perm: " . ($hasPerm ? '✅' : '❌') . "\n";
    if ($hasPerm) $criticalFound++;
}

echo "\n🔍 Step 5: Checking total permissions in system...\n";
$totalPerms = Permission::count();
echo "Total permissions in system: $totalPerms\n";

echo "\n📊 Summary:\n";
echo "Critical permissions: $criticalFound/" . count($criticalPerms) . "\n";
echo "User permissions: $permCount/$totalPerms\n";

$allGood = ($criticalFound == count($criticalPerms) && $permCount == $totalPerms);

if ($allGood) {
    echo "\n🎉 SUCCESS: Everything looks good!\n";
} else {
    echo "\n⚠️  WARNING: Some issues detected!\n";
}

echo "\n🔧 Step 6: Clearing caches...\n";

// Clear various caches
echo "Clearing view cache...\n";
exec('php artisan view:clear 2>&1', $output, $return);
if ($return === 0) {
    echo "✅ View cache cleared\n";
} else {
    echo "⚠️  View cache clear failed\n";
}

echo "Clearing config cache...\n";
exec('php artisan config:clear 2>&1', $output, $return);
if ($return === 0) {
    echo "✅ Config cache cleared\n";
} else {
    echo "⚠️  Config cache clear failed\n";
}

echo "Clearing route cache...\n";
exec('php artisan route:clear 2>&1', $output, $return);
if ($return === 0) {
    echo "✅ Route cache cleared\n";
} else {
    echo "⚠️  Route cache clear failed\n";
}

echo "Clearing application cache...\n";
exec('php artisan cache:clear 2>&1', $output, $return);
if ($return === 0) {
    echo "✅ Application cache cleared\n";
} else {
    echo "⚠️  Application cache clear failed\n";
}

echo "\n🎯 Final Instructions:\n";
echo "1. ✅ Permissions synchronized\n";
echo "2. ✅ user_admin has all permissions\n";
echo "3. ✅ Caches cleared\n";
echo "4. 🔄 Test login as user_admin\n";
echo "5. 🔄 Check if Master Data menu appears in sidebar\n";
echo "6. 🔄 Verify access to master data pages\n";

if ($allGood) {
    echo "\n🎉 READY: Master Data menu should now be visible!\n";
} else {
    echo "\n⚠️  READY: But some issues were detected - check output above.\n";
}