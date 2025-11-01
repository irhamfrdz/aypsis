<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== Debug Pranota Rit Menu & Permissions ===\n";

// 1. Check admin user permissions
echo "\n🔍 1. Checking Admin User Permissions:\n";
$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

$adminPermissions = $admin->permissions()->pluck('name')->toArray();
$pranotaRitPermissions = array_filter($adminPermissions, function($perm) {
    return strpos($perm, 'pranota-rit') !== false;
});

echo "   👤 Admin user: {$admin->username} (ID: {$admin->id})\n";
echo "   📊 Total permissions: " . count($adminPermissions) . "\n";
echo "   🚗 Pranota Rit permissions: " . count($pranotaRitPermissions) . "\n";

if (!empty($pranotaRitPermissions)) {
    echo "\n   ✅ Admin Pranota Rit Permissions:\n";
    foreach ($pranotaRitPermissions as $perm) {
        echo "      • {$perm}\n";
    }
} else {
    echo "\n   ❌ No pranota rit permissions found for admin\n";
}

// 2. Check routes in web.php
echo "\n🛣️  2. Checking Routes in web.php:\n";
$webRoutes = file_get_contents('routes/web.php');

$routeChecks = [
    'pranota-uang-rit routes' => 'pranota-uang-rit',
    'pranota-rit routes' => 'Route::resource(\'pranota-rit\'',
    'pranota-rit controller' => 'PranotaRitController',
    'pranota-rit-kenek routes' => 'pranota-rit-kenek'
];

foreach ($routeChecks as $checkName => $pattern) {
    if (strpos($webRoutes, $pattern) !== false) {
        echo "   ✅ {$checkName}\n";
    } else {
        echo "   ❌ {$checkName} - NOT FOUND\n";
    }
}

// 3. Check sidebar file for menu entries
echo "\n📋 3. Checking Sidebar Menu Configuration:\n";
$sidebarPath = 'resources/views/layouts/sidebar.blade.php';
if (file_exists($sidebarPath)) {
    $sidebarContent = file_get_contents($sidebarPath);
    
    $menuChecks = [
        'Pranota Rit menu' => 'Pranota Rit',
        'pranota-rit permission check' => 'can:pranota-rit',
        'pranota-uang-rit permission check' => 'can:pranota-uang-rit'
    ];
    
    foreach ($menuChecks as $checkName => $pattern) {
        if (strpos($sidebarContent, $pattern) !== false) {
            echo "   ✅ {$checkName}\n";
        } else {
            echo "   ❌ {$checkName} - NOT FOUND\n";
        }
    }
} else {
    echo "   ❌ Sidebar file not found at: {$sidebarPath}\n";
}

// 4. Check permission names in database
echo "\n💾 4. Database Permission Names:\n";
$allPranotaRitPerms = Permission::where('name', 'like', 'pranota-rit%')->orderBy('name')->get();

foreach ($allPranotaRitPerms as $perm) {
    $hasAdmin = $admin->permissions()->where('name', $perm->name)->exists();
    $status = $hasAdmin ? '✅' : '❌';
    echo "   {$status} {$perm->name} (ID: {$perm->id})\n";
}

// 5. Permission pattern analysis
echo "\n🔍 5. Permission Pattern Analysis:\n";
echo "   Expected permission patterns for menu:\n";
echo "   • For view access: pranota-rit-view\n";
echo "   • For create access: pranota-rit-create\n";
echo "   • Alternative pattern: pranota-uang-rit-view\n";

// Check specific permissions that might be used in sidebar
$requiredForMenu = ['pranota-rit-view', 'pranota-uang-rit-view', 'pranota-rit', 'pranota-uang-rit'];
echo "\n   🎯 Checking specific permissions for menu access:\n";
foreach ($requiredForMenu as $requiredPerm) {
    if (in_array($requiredPerm, $adminPermissions)) {
        echo "   ✅ {$requiredPerm} - FOUND\n";
    } else {
        $exists = Permission::where('name', $requiredPerm)->exists();
        if ($exists) {
            echo "   ⚠️  {$requiredPerm} - EXISTS but NOT assigned to admin\n";
        } else {
            echo "   ❌ {$requiredPerm} - DOES NOT EXIST\n";
        }
    }
}

echo "\n🎯 6. Recommendations:\n";
echo "   1. Check if sidebar uses 'pranota-uang-rit-view' instead of 'pranota-rit-view'\n";
echo "   2. Verify permission name in @can directive matches database permission\n";
echo "   3. Clear cache: php artisan cache:clear\n";
echo "   4. Check if route name matches sidebar menu route reference\n";

echo "\n✨ Debug complete! Check the issues above to fix the menu visibility.\n";

?>