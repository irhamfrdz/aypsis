<?php
/**
 * Script untuk debugging 502 Bad Gateway error pada permission editing
 * Jalankan script ini di server untuk menganalisis masalah
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PERMISSION EDIT DEBUG ANALYSIS ===\n\n";

try {
    // 1. Check memory usage and limits
    echo "📊 MEMORY ANALYSIS:\n";
    echo "  Current Memory Usage: " . formatBytes(memory_get_usage()) . "\n";
    echo "  Peak Memory Usage: " . formatBytes(memory_get_peak_usage()) . "\n";
    echo "  Memory Limit: " . ini_get('memory_limit') . "\n";
    echo "  Max Execution Time: " . ini_get('max_execution_time') . "s\n\n";

    // 2. Check permission count
    echo "🔍 PERMISSION COUNT ANALYSIS:\n";
    $totalPermissions = DB::table('permissions')->count();
    echo "  Total Permissions in Database: {$totalPermissions}\n";

    if ($totalPermissions > 1000) {
        echo "  ⚠️  HIGH PERMISSION COUNT - This may cause performance issues!\n";
    } else {
        echo "  ✅ Permission count is reasonable.\n";
    }

    // Check for duplicate permissions
    $duplicates = DB::table('permissions')
        ->select('name')
        ->groupBy('name')
        ->havingRaw('COUNT(*) > 1')
        ->get();

    if ($duplicates->count() > 0) {
        echo "  ⚠️  Found " . $duplicates->count() . " duplicate permission names!\n";
        foreach ($duplicates as $dup) {
            echo "    - Duplicate: {$dup->name}\n";
        }
    } else {
        echo "  ✅ No duplicate permissions found.\n";
    }
    echo "\n";

    // 3. Check user-permission relationships
    echo "👥 USER-PERMISSION RELATIONSHIPS:\n";
    $userPermCount = DB::table('user_permissions')->count();
    echo "  Total User-Permission Relations: {$userPermCount}\n";

    $maxUserPerms = DB::table('user_permissions')
        ->select('user_id', DB::raw('COUNT(*) as perm_count'))
        ->groupBy('user_id')
        ->orderBy('perm_count', 'desc')
        ->first();

    if ($maxUserPerms) {
        echo "  Max Permissions per User: {$maxUserPerms->perm_count}\n";
        if ($maxUserPerms->perm_count > 500) {
            echo "  ⚠️  User has too many permissions - may cause timeout!\n";
        }
    }
    echo "\n";

    // 4. Test permission matrix conversion (potential bottleneck)
    echo "🔄 TESTING PERMISSION MATRIX CONVERSION:\n";
    $startTime = microtime(true);

    // Get a sample user with many permissions
    $testUser = DB::table('users')
        ->join('user_permissions', 'users.id', '=', 'user_permissions.user_id')
        ->select('users.id', DB::raw('COUNT(*) as perm_count'))
        ->groupBy('users.id')
        ->orderBy('perm_count', 'desc')
        ->first();

    if ($testUser) {
        echo "  Testing with User ID: {$testUser->id} ({$testUser->perm_count} permissions)\n";

        // Get user permissions
        $userPermissions = DB::table('permissions')
            ->join('user_permissions', 'permissions.id', '=', 'user_permissions.permission_id')
            ->where('user_permissions.user_id', $testUser->id)
            ->pluck('permissions.name')
            ->toArray();

        // Test matrix conversion (this is where the bottleneck likely occurs)
        $conversionStartTime = microtime(true);

        // Simulate the convertPermissionsToMatrix method (simplified version)
        $matrixPermissions = [];
        foreach ($userPermissions as $permissionName) {
            // Simple pattern matching (avoid complex regex for testing)
            if (strpos($permissionName, '.') !== false) {
                $parts = explode('.', $permissionName);
                if (count($parts) >= 2) {
                    $module = $parts[0] . '-' . $parts[1];
                    $action = $parts[2] ?? 'view';
                    $matrixPermissions[$module][$action] = true;
                }
            } elseif (strpos($permissionName, '-') !== false) {
                $parts = explode('-', $permissionName, 2);
                $module = $parts[0];
                $action = $parts[1];
                $matrixPermissions[$module][$action] = true;
            }
        }

        $conversionTime = microtime(true) - $conversionStartTime;
        echo "  Matrix Conversion Time: " . number_format($conversionTime, 4) . "s\n";

        if ($conversionTime > 2.0) {
            echo "  ⚠️  SLOW CONVERSION - This is likely causing the 502 error!\n";
            echo "  💡 Recommendation: Optimize convertPermissionsToMatrix method\n";
        } else {
            echo "  ✅ Conversion time is acceptable.\n";
        }
    }

    $totalTime = microtime(true) - $startTime;
    echo "  Total Test Time: " . number_format($totalTime, 4) . "s\n\n";

    // 5. Check for potential infinite loops or recursive issues
    echo "🔁 CHECKING FOR PROBLEMATIC PERMISSIONS:\n";
    $problematicPatterns = [
        'permissions containing multiple dots' => "SELECT name FROM permissions WHERE LENGTH(name) - LENGTH(REPLACE(name, '.', '')) > 2",
        'very long permission names' => "SELECT name FROM permissions WHERE LENGTH(name) > 100",
        'permissions with special characters' => "SELECT name FROM permissions WHERE name REGEXP '[^a-zA-Z0-9._-]'"
    ];

    // Special analysis for the multi-dot permissions found
    echo "  🔍 Analyzing multi-dot permissions:\n";
    $multiDotPermissions = DB::select("SELECT name FROM permissions WHERE LENGTH(name) - LENGTH(REPLACE(name, '.', '')) > 2");
    foreach ($multiDotPermissions as $perm) {
        echo "    - {$perm->name}\n";

        // Analyze the structure
        $parts = explode('.', $perm->name);
        if (count($parts) == 4) {
            echo "      → Pattern: {$parts[0]}.{$parts[1]}.{$parts[2]}.{$parts[3]}\n";
            echo "      → Suggested fix: {$parts[0]}-{$parts[1]}-{$parts[2]}-{$parts[3]}\n";
        }
    }

    foreach ($problematicPatterns as $description => $query) {
        $results = DB::select($query);
        if (count($results) > 0) {
            echo "  ⚠️  Found " . count($results) . " {$description}:\n";
            foreach (array_slice($results, 0, 5) as $result) {
                echo "    - {$result->name}\n";
            }
            if (count($results) > 5) {
                echo "    ... and " . (count($results) - 5) . " more\n";
            }
        } else {
            echo "  ✅ No {$description} found.\n";
        }
    }
    echo "\n";

    // 6. Generate recommendations
    echo "💡 RECOMMENDATIONS:\n";

    if ($totalPermissions > 1000) {
        echo "  1. 🔧 Consider permission cleanup - remove unused permissions\n";
    }

    if ($maxUserPerms && $maxUserPerms->perm_count > 300) {
        echo "  2. 🔧 Implement permission caching for users with many permissions\n";
    }

    echo "  3. 🔧 Check server logs: /var/log/nginx/error.log and PHP-FPM logs\n";
    echo "  4. 🔧 Increase PHP memory_limit and max_execution_time if needed\n";
    echo "  5. 🔧 Consider pagination for permission editing interface\n";
    echo "  6. 🔧 Optimize convertPermissionsToMatrix method with caching\n\n";

    // 7. Quick server health check
    echo "🏥 QUICK SERVER HEALTH CHECK:\n";
    echo "  PHP Version: " . PHP_VERSION . "\n";
    echo "  Laravel Version: " . app()->version() . "\n";

    // Check if we can connect to database
    try {
        DB::connection()->getPdo();
        echo "  ✅ Database connection: OK\n";
    } catch (Exception $e) {
        echo "  ❌ Database connection: FAILED - " . $e->getMessage() . "\n";
    }

    // Check if we can write to storage
    $testFile = storage_path('logs/test_write_' . time() . '.txt');
    if (file_put_contents($testFile, 'test') !== false) {
        echo "  ✅ Storage write access: OK\n";
        unlink($testFile);
    } else {
        echo "  ❌ Storage write access: FAILED\n";
    }

    echo "\n🎯 NEXT STEPS:\n";
    echo "  1. Check server error logs immediately\n";
    echo "  2. Try accessing a simple route first (not permission editing)\n";
    echo "  3. If issue persists, restart PHP-FPM service\n";
    echo "  4. Consider temporary permission matrix simplification\n";

} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "📝 Stack trace:\n" . $e->getTraceAsString() . "\n";
    echo "\n🚨 IMMEDIATE ACTION REQUIRED:\n";
    echo "  1. Check if PHP-FPM service is running\n";
    echo "  2. Check server error logs\n";
    echo "  3. Verify database connectivity\n";
    echo "  4. Consider rolling back recent changes\n";
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

echo "\n=== DEBUG ANALYSIS COMPLETE ===\n";
