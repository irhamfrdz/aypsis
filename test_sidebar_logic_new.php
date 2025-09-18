<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== SIDEBAR LOGIC TEST ===\n";

$user = User::where('username', 'user_admin')->first();

if (!$user) {
    echo "❌ User 'user_admin' not found!\n";
    exit(1);
}

echo "✅ User found: {$user->username}\n";
echo "Status: {$user->status}\n\n";

// Simulate sidebar logic
echo "🔍 Testing Sidebar Logic:\n";

// Check hasRole method
$hasRoleMethod = method_exists($user, 'hasRole');
echo "hasRole method exists: " . ($hasRoleMethod ? '✅ Yes' : '❌ No') . "\n";

$isAdmin = $hasRoleMethod && $user->hasRole('admin');
echo "isAdmin (hasRole('admin')): " . ($isAdmin ? '✅ True' : '❌ False') . "\n";

// Check master permissions
$masterPermissions = [
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

$hasMasterPermissions = false;
$permissionsFound = [];

foreach ($masterPermissions as $perm) {
    if ($user->can($perm)) {
        $hasMasterPermissions = true;
        $permissionsFound[] = $perm;
    }
}

echo "hasMasterPermissions: " . ($hasMasterPermissions ? '✅ True' : '❌ False') . "\n";
echo "Master permissions found: " . count($permissionsFound) . "\n";

if (!empty($permissionsFound)) {
    echo "Sample permissions:\n";
    foreach (array_slice($permissionsFound, 0, 5) as $perm) {
        echo "- $perm\n";
    }
    if (count($permissionsFound) > 5) {
        echo "- ... and " . (count($permissionsFound) - 5) . " more\n";
    }
}

// Final sidebar logic
$showMasterSection = $isAdmin || $hasMasterPermissions;

echo "\n🎯 FINAL RESULT:\n";
echo "showMasterSection: " . ($showMasterSection ? '✅ TRUE (Master Data will be shown)' : '❌ FALSE (Master Data will be hidden)') . "\n";

echo "\n📋 SUMMARY:\n";
echo "- Admin Role: " . ($isAdmin ? 'Yes' : 'No') . "\n";
echo "- Master Permissions: " . ($hasMasterPermissions ? 'Yes (' . count($permissionsFound) . ' found)' : 'No') . "\n";
echo "- Master Data Section: " . ($showMasterSection ? '✅ WILL BE SHOWN' : '❌ WILL BE HIDDEN') . "\n";

if ($showMasterSection) {
    echo "\n🎉 SUCCESS: Master Data section should now appear in sidebar!\n";
} else {
    echo "\n❌ ISSUE: Master Data section will still be hidden.\n";
    echo "Check if user has the required permissions.\n";
}