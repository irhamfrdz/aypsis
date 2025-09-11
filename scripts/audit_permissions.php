<?php
// Permission Audit and Management Script
// Run with: php scripts/audit_permissions.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "=== PERMISSION AUDIT & MANAGEMENT TOOL ===\n\n";

// 1. Check database structure
echo "1. DATABASE STRUCTURE CHECK:\n";
try {
    $userPermissionsCount = DB::table('user_permissions')->count();
    $permissionsCount = Permission::count();
    $usersCount = User::count();

    echo "✓ user_permissions table: {$userPermissionsCount} records\n";
    echo "✓ permissions table: {$permissionsCount} records\n";
    echo "✓ users table: {$usersCount} records\n\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n\n";
}

// 2. Analyze permission distribution
echo "2. PERMISSION DISTRIBUTION ANALYSIS:\n";
$users = User::with('permissions')->get();

$permissionStats = [];
$userPermissionCounts = [];

foreach ($users as $user) {
    $count = $user->permissions->count();
    $userPermissionCounts[] = $count;

    foreach ($user->permissions as $perm) {
        $name = $perm->name;
        if (!isset($permissionStats[$name])) {
            $permissionStats[$name] = 0;
        }
        $permissionStats[$name]++;
    }
}

echo "User permission counts:\n";
echo "- Average: " . round(array_sum($userPermissionCounts) / count($userPermissionCounts), 1) . "\n";
echo "- Min: " . min($userPermissionCounts) . "\n";
echo "- Max: " . max($userPermissionCounts) . "\n\n";

echo "Top 10 most assigned permissions:\n";
arsort($permissionStats);
$i = 0;
foreach ($permissionStats as $name => $count) {
    if ($i >= 10) break;
    echo "- {$name}: {$count} users\n";
    $i++;
}
echo "\n";

// 3. Check for orphaned permissions
echo "3. ORPHANED PERMISSIONS CHECK:\n";
$allPermissionIds = Permission::pluck('id')->toArray();
$userPermissionIds = DB::table('user_permissions')->pluck('permission_id')->unique()->toArray();
$orphanedIds = array_diff($allPermissionIds, $userPermissionIds);

if (empty($orphanedIds)) {
    echo "✓ No orphaned permissions found\n";
} else {
    echo "⚠ Found " . count($orphanedIds) . " orphaned permissions:\n";
    $orphanedPerms = Permission::whereIn('id', $orphanedIds)->pluck('name');
    foreach ($orphanedPerms as $name) {
        echo "  - {$name}\n";
    }
}
echo "\n";

// 4. Template compliance check
echo "4. TEMPLATE COMPLIANCE CHECK:\n";
$templates = config('permission_templates', []);

foreach ($templates as $templateKey => $template) {
    echo "Template: {$template['label']}\n";

    $templatePerms = $template['permissions'];
    $templatePermIds = Permission::whereIn('name', $templatePerms)->pluck('id')->toArray();

    $usersWithAllTemplatePerms = 0;
    $usersWithSomeTemplatePerms = 0;

    foreach ($users as $user) {
        $userPermIds = $user->permissions->pluck('id')->toArray();
        $matchingPerms = array_intersect($templatePermIds, $userPermIds);

        if (count($matchingPerms) === count($templatePermIds)) {
            $usersWithAllTemplatePerms++;
        } elseif (count($matchingPerms) > 0) {
            $usersWithSomeTemplatePerms++;
        }
    }

    echo "  - Users with all permissions: {$usersWithAllTemplatePerms}\n";
    echo "  - Users with some permissions: {$usersWithSomeTemplatePerms}\n";
    echo "  - Users with no permissions: " . ($users->count() - $usersWithAllTemplatePerms - $usersWithSomeTemplatePerms) . "\n\n";
}

// 5. Permission group analysis
echo "5. PERMISSION GROUP ANALYSIS:\n";
$groups = config('permission_groups', []);

foreach ($groups as $groupKey => $group) {
    echo "Group: {$group['label']}\n";

    $groupUsers = 0;
    $prefixes = $group['prefixes'];

    foreach ($users as $user) {
        $hasGroupPerm = false;
        foreach ($user->permissions as $perm) {
            foreach ($prefixes as $prefix) {
                if (strpos($perm->name, $prefix) === 0) {
                    $hasGroupPerm = true;
                    break 2;
                }
            }
        }
        if ($hasGroupPerm) {
            $groupUsers++;
        }
    }

    echo "  - Users with permissions in this group: {$groupUsers}\n";
}
echo "\n";

// 6. Recommendations
echo "6. RECOMMENDATIONS:\n";

if (!empty($orphanedIds)) {
    echo "⚠ Consider removing orphaned permissions or reassigning them\n";
}

$avgPerms = array_sum($userPermissionCounts) / count($userPermissionCounts);
if ($avgPerms > 50) {
    echo "⚠ High average permission count ({$avgPerms}). Consider using templates for better management\n";
}

$usersWithNoPerms = count(array_filter($userPermissionCounts, fn($c) => $c === 0));
if ($usersWithNoPerms > 0) {
    echo "⚠ {$usersWithNoPerms} users have no permissions assigned\n";
}

echo "✓ Consider using permission templates for consistent role assignments\n";
echo "✓ Use bulk management tools for efficient permission updates\n";
echo "✓ Regularly audit permission assignments for security\n\n";

echo "=== AUDIT COMPLETE ===\n";
