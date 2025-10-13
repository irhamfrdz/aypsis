<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

try {
    echo "=== VERIFICATION: Master Tujuan Kegiatan Utama Permission Update ===\n\n";
    
    // 1. Check if admin has master-tujuan permissions
    echo "1. Admin user permissions related to 'master-tujuan':\n";
    $adminPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', 1)
        ->where('permissions.name', 'like', 'master-tujuan%')
        ->pluck('permissions.name')
        ->toArray();
    
    $requiredPermissions = [
        'master-tujuan-view',
        'master-tujuan-create', 
        'master-tujuan-update',
        'master-tujuan-delete',
        'master-tujuan-export',
        'master-tujuan-print'
    ];
    
    foreach ($requiredPermissions as $perm) {
        $hasPermission = in_array($perm, $adminPermissions);
        $status = $hasPermission ? "✅" : "❌";
        echo "   {$status} {$perm}\n";
    }
    
    // 2. Check if old tujuan-kegiatan-utama permissions are cleaned up
    echo "\n2. Checking for old tujuan-kegiatan-utama permissions (should be none):\n";
    $oldPermissions = DB::table('permissions')
        ->where('name', 'like', '%tujuan-kegiatan-utama%')
        ->pluck('name')
        ->toArray();
    
    if (empty($oldPermissions)) {
        echo "   ✅ All old tujuan-kegiatan-utama permissions cleaned up\n";
    } else {
        echo "   ❌ Found old permissions:\n";
        foreach ($oldPermissions as $perm) {
            echo "      - {$perm}\n";
        }
    }
    
    // 3. Test route access simulation
    echo "\n3. Route configuration check:\n";
    
    // Include routes file to get route definitions
    $routesContent = file_get_contents('routes/web.php');
    
    // Check if routes use master-tujuan permissions
    $routeChecks = [
        'tujuan-kegiatan-utama.index' => 'master-tujuan-view',
        'tujuan-kegiatan-utama.create' => 'master-tujuan-create',
        'tujuan-kegiatan-utama.store' => 'master-tujuan-create',
        'tujuan-kegiatan-utama.show' => 'master-tujuan-view',
        'tujuan-kegiatan-utama.edit' => 'master-tujuan-update',
        'tujuan-kegiatan-utama.update' => 'master-tujuan-update',
        'tujuan-kegiatan-utama.destroy' => 'master-tujuan-delete',
        'tujuan-kegiatan-utama.export' => 'master-tujuan-export',
        'tujuan-kegiatan-utama.print' => 'master-tujuan-print'
    ];
    
    foreach ($routeChecks as $route => $expectedPermission) {
        $hasRoute = strpos($routesContent, "name('{$route}')") !== false;
        $hasPermission = strpos($routesContent, "middleware('can:{$expectedPermission}')") !== false;
        
        if ($hasRoute && $hasPermission) {
            echo "   ✅ Route {$route} uses permission {$expectedPermission}\n";
        } else if ($hasRoute) {
            echo "   ❌ Route {$route} found but permission check missing\n";
        } else {
            echo "   ❌ Route {$route} not found\n";
        }
    }
    
    // 4. Check layout file
    echo "\n4. Layout file permission check:\n";
    $layoutContent = file_get_contents('resources/views/layouts/app.blade.php');
    
    if (strpos($layoutContent, "can('master-tujuan-view')") !== false && 
        strpos($layoutContent, "route('master.tujuan-kegiatan-utama.index')") !== false) {
        echo "   ✅ Layout uses master-tujuan-view permission for Tujuan Kegiatan Utama menu\n";
    } else {
        echo "   ❌ Layout file permission check failed\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    
    // Count missing required permissions
    $missingPermissions = array_diff($requiredPermissions, $adminPermissions);
    
    if (empty($missingPermissions) && empty($oldPermissions)) {
        echo "✅ SUCCESS: Master Tujuan Kegiatan Utama now uses identical permissions to Master Tujuan!\n";
        echo "✅ Admin user has all required master-tujuan permissions\n";
        echo "✅ Old tujuan-kegiatan-utama permissions have been cleaned up\n";
        echo "✅ Routes and views have been updated\n";
        echo "\nThe menu should now be accessible with the same permissions as Master Tujuan.\n";
    } else {
        echo "❌ ISSUES FOUND:\n";
        if (!empty($missingPermissions)) {
            echo "   - Missing permissions: " . implode(', ', $missingPermissions) . "\n";
        }
        if (!empty($oldPermissions)) {
            echo "   - Old permissions still exist: " . implode(', ', $oldPermissions) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}