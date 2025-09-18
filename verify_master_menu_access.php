<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔍 Verifying Master Data Menu Permissions for User test4\n";
echo "======================================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found!\n";
    exit(1);
}

echo "👤 User: {$user->username} (ID: {$user->id})\n\n";

// Check key master data permissions that the sidebar looks for
$masterPermissions = [
    'master-karyawan-view',
    'master-user.view',
    'master-kontainer.view',
    'master-tujuan.view',
    'master-kegiatan.view',
    'master-permission.view',
    'master-mobil.view'
];

echo "📋 Master Data Permissions Check:\n";
$allCorrect = true;

foreach ($masterPermissions as $perm) {
    $hasPermission = $user->hasPermissionTo($perm);
    $status = $hasPermission ? '✅ HAS' : '❌ MISSING';
    echo "  - {$perm}: {$status}\n";

    if (!$hasPermission) {
        $allCorrect = false;
    }
}

echo "\n";

if ($allCorrect) {
    echo "🎉 SUCCESS: All master data permissions are correctly configured!\n";
    echo "   The Master Data menu should now appear in the sidebar for user test4.\n";
} else {
    echo "⚠️  WARNING: Some master data permissions are still missing.\n";
    echo "   The Master Data menu may not appear in the sidebar.\n";
}

echo "\n🔗 Sidebar Menu Logic:\n";
echo "   The sidebar checks: \$user->can('master-karyawan-view')\n";
echo "   If this returns true, the Master Data menu will be displayed.\n";

$canAccessMaster = $user->hasPermissionTo('master-karyawan-view');
echo "\n📊 Current Status: " . ($canAccessMaster ? '✅ ACCESSIBLE' : '❌ NOT ACCESSIBLE') . "\n";
