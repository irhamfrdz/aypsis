<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== FINAL VERIFICATION FOR USER ADMIN TUJUAN KIRIM ACCESS ===\n\n";

$userAdmin = User::where('username', 'user_admin')->first();

if (!$userAdmin) {
    echo "❌ ERROR: user_admin not found!\n";
    exit(1);
}

echo "✅ User admin found: {$userAdmin->username}\n";
echo "✅ User status: {$userAdmin->status}\n\n";

// Check all required permissions
$requiredPermissions = [
    'master-tujuan-kirim-view',
    'master-tujuan-kirim-create',
    'master-tujuan-kirim-update',
    'master-tujuan-kirim-delete'
];

echo "=== PERMISSION VERIFICATION ===\n";
$allPermissionsOk = true;

foreach ($requiredPermissions as $permission) {
    $hasPermission = $userAdmin->can($permission);
    $status = $hasPermission ? '✅ YES' : '❌ NO';
    echo "- {$permission}: {$status}\n";

    if (!$hasPermission) {
        $allPermissionsOk = false;

        // Try to assign missing permission
        $perm = Permission::where('name', $permission)->first();
        if ($perm) {
            $userAdmin->permissions()->syncWithoutDetaching([$perm->id]);
            echo "  └─ 🔧 Permission assigned automatically\n";
        } else {
            echo "  └─ ❌ Permission not found in database!\n";
        }
    }
}

echo "\n=== ACCESS TEST ===\n";

// Test route access
try {
    $indexUrl = route('tujuan-kirim.index');
    echo "✅ Route 'tujuan-kirim.index' exists: {$indexUrl}\n";
} catch (Exception $e) {
    echo "❌ Route 'tujuan-kirim.index' not found: {$e->getMessage()}\n";
}

// Test sidebar conditions
$hasMasterPermissions = $userAdmin && (
    $userAdmin->can('master-permission-view') ||
    $userAdmin->can('master-cabang-view') ||
    $userAdmin->can('master-pengirim-view') ||
    $userAdmin->can('master-jenis-barang-view') ||
    $userAdmin->can('master-term-view') ||
    $userAdmin->can('master-coa-view') ||
    $userAdmin->can('master-kode-nomor-view') ||
    $userAdmin->can('master-nomor-terakhir-view') ||
    $userAdmin->can('master-tipe-akun-view') ||
    $userAdmin->can('master-tujuan-view') ||
    $userAdmin->can('master-tujuan-kirim-view') ||
    $userAdmin->can('master-kegiatan-view')
);

$isAdmin = $userAdmin && method_exists($userAdmin, 'hasRole') && $userAdmin->hasRole('admin');
$showMasterSection = $isAdmin || $hasMasterPermissions;

echo "✅ Sidebar master section will show: " . ($showMasterSection ? 'YES' : 'NO') . "\n";
echo "✅ Menu tujuan kirim will show: " . ($userAdmin->can('master-tujuan-kirim-view') ? 'YES' : 'NO') . "\n";

echo "\n=== INSTRUCTIONS FOR USER ===\n";
echo "1. 🌐 Open browser and go to: http://localhost:8000\n";
echo "2. 🔐 Login with:\n";
echo "   - Username: user_admin\n";
echo "   - Password: admin123\n";
echo "3. 🔄 After login, do a HARD REFRESH (Ctrl+F5 or Cmd+Shift+R)\n";
echo "4. 🔍 Look for 'Master Data' section in the sidebar\n";
echo "5. 📋 Click 'Master Data' to expand, then look for 'Tujuan Kirim'\n";
echo "6. 🎯 Test page available at: http://localhost:8000/test-tujuan-kirim\n\n";

if ($allPermissionsOk && $showMasterSection) {
    echo "🎉 ALL CHECKS PASSED! Menu should be visible now.\n";
    echo "💡 If still not visible, try logging out and logging back in.\n";
} else {
    echo "⚠️  Some issues found. Please run this script again.\n";
}

echo "\n=== TROUBLESHOOTING ===\n";
echo "If menu still not showing:\n";
echo "1. Clear browser cache completely\n";
echo "2. Logout and login again\n";
echo "3. Check browser console for JavaScript errors\n";
echo "4. Run: php artisan cache:clear\n";
echo "5. Run: php artisan view:clear\n";
echo "6. Run: php artisan config:clear\n\n";
