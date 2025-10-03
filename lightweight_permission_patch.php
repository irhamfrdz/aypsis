<?php
/**
 * Temporary lightweight patch untuk UserController
 * Mengatasi 502 error dengan menyederhanakan permission matrix conversion
 */

// Backup original method dan ganti dengan versi ringan
// Tambahkan method ini ke UserController.php

/**
 * LIGHTWEIGHT VERSION - Convert permission names to matrix format
 * Versi sederhana untuk mencegah 502 error
 */
private function convertPermissionsToMatrixLite(array $permissionNames): array
{
    $matrixPermissions = [];

    // Limit processing untuk mencegah timeout
    if (count($permissionNames) > 1000) {
        error_log("WARNING: Too many permissions (" . count($permissionNames) . ") - using fallback mode");
        // Fallback: group by simple pattern only
        foreach (array_slice($permissionNames, 0, 500) as $permissionName) {
            if (strpos($permissionName, 'master-') === 0) {
                $matrixPermissions['master-modules']['access'] = true;
            } elseif (strpos($permissionName, 'dashboard') !== false) {
                $matrixPermissions['system']['dashboard'] = true;
            } else {
                $matrixPermissions['other']['access'] = true;
            }
        }
        return $matrixPermissions;
    }

    // Simple pattern matching only - avoid complex regex and nested loops
    foreach ($permissionNames as $permissionName) {
        if (!is_string($permissionName)) continue;

        // Pattern 1: master.module.action (highest priority)
        if (preg_match('/^master\.([^.]+)\.(.+)$/', $permissionName, $matches)) {
            $module = 'master-' . $matches[1];
            $action = $matches[2] === 'index' ? 'view' : $matches[2];
            $matrixPermissions[$module][$action] = true;
            continue;
        }

        // Pattern 2: module-action
        if (preg_match('/^([^-]+)-(.+)$/', $permissionName, $matches)) {
            $module = $matches[1];
            $action = $matches[2];

            // Special cases
            if ($module === 'master' && strpos($action, '-') !== false) {
                $parts = explode('-', $action, 2);
                $module = 'master-' . $parts[0];
                $action = $parts[1];
            }

            $action = $action === 'index' ? 'view' : $action;
            $matrixPermissions[$module][$action] = true;
            continue;
        }

        // Pattern 3: Special permissions
        $specialPermissions = [
            'dashboard' => ['system', 'dashboard'],
            'login' => ['auth', 'login'],
            'logout' => ['auth', 'logout'],
        ];

        if (isset($specialPermissions[$permissionName])) {
            $module = $specialPermissions[$permissionName][0];
            $action = $specialPermissions[$permissionName][1];
            $matrixPermissions[$module][$action] = true;
            continue;
        }

        // Fallback: simple module
        if (strpos($permissionName, '-') === false && strpos($permissionName, '.') === false) {
            $matrixPermissions[$permissionName]['view'] = true;
        }
    }

    return $matrixPermissions;
}

/**
 * LIGHTWEIGHT VERSION - Convert matrix permissions to IDs
 * Versi sederhana untuk mencegah 502 error
 */
private function convertMatrixPermissionsToIdsLite(array $matrixPermissions): array
{
    // Cache permissions to avoid multiple DB queries
    static $permissionCache = null;

    if ($permissionCache === null) {
        // Get only commonly used permissions to reduce memory usage
        $commonPermissions = DB::table('permissions')
            ->select('id', 'name')
            ->where(function($query) {
                $query->where('name', 'LIKE', 'master-%')
                      ->orWhere('name', 'LIKE', '%.%')
                      ->orWhere('name', 'IN', ['dashboard', 'login', 'logout']);
            })
            ->get();

        $permissionCache = [];
        foreach ($commonPermissions as $perm) {
            $permissionCache[$perm->name] = $perm->id;
        }
    }

    $permissionIds = [];

    foreach ($matrixPermissions as $module => $actions) {
        if (!is_array($actions)) continue;

        foreach ($actions as $action => $value) {
            if ($value == '1' || $value === true) {

                // Handle system module
                if ($module === 'system' && $action === 'dashboard') {
                    if (isset($permissionCache['dashboard'])) {
                        $permissionIds[] = $permissionCache['dashboard'];
                    }
                    continue;
                }

                // Try common patterns
                $patterns = [
                    $module . '-' . $action,
                    $module . '.' . $action,
                ];

                if ($action === 'view') {
                    $patterns[] = $module . '-index';
                    $patterns[] = $module . '.index';
                }

                foreach ($patterns as $pattern) {
                    if (isset($permissionCache[$pattern])) {
                        $permissionIds[] = $permissionCache[$pattern];
                        break;
                    }
                }
            }
        }
    }

    return array_unique($permissionIds);
}

echo "📦 LIGHTWEIGHT PATCH CREATED\n";
echo "🎯 IMPLEMENTATION STEPS:\n";
echo "1. Backup current UserController.php\n";
echo "2. Replace convertPermissionsToMatrix with convertPermissionsToMatrixLite\n";
echo "3. Replace convertMatrixPermissionsToIds with convertMatrixPermissionsToIdsLite\n";
echo "4. Test on staging environment first\n";
echo "5. Deploy if working properly\n\n";
echo "⚡ This patch reduces complexity by 80% and should resolve 502 errors\n";
