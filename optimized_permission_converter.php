<?php
/**
 * Optimized Permission Matrix Converter
 * Mengganti method convertPermissionsToMatrix yang lambat dengan versi yang dioptimalkan
 */

/**
 * Convert permission names to matrix format for the view (OPTIMIZED VERSION)
 *
 * @param array $permissionNames
 * @return array
 */
function convertPermissionsToMatrixOptimized(array $permissionNames): array
{
    $matrixPermissions = [];

    // Pre-compile regex patterns for better performance
    static $patterns = null;
    if ($patterns === null) {
        $patterns = [
            'dot_master' => '/^master\.([^.]+)\.(.+)$/',
            'dot_general' => '/^([^.]+)\.(.+)$/',
            'dash_master' => '/^master-([^-]+)-(.+)$/',
            'dash_general' => '/^([^-]+)-(.+)$/',
        ];
    }

    // Action mapping cache
    static $actionMap = [
        'index' => 'view',
        'create' => 'create',
        'store' => 'create',
        'show' => 'view',
        'edit' => 'update',
        'update' => 'update',
        'destroy' => 'delete',
        'print' => 'print',
        'export' => 'export',
        'import' => 'import',
        'approve' => 'approve',
        'template' => 'template',
        'single' => 'print'
    ];

    // Process permissions in batches to avoid memory issues
    $batchSize = 100;
    $batches = array_chunk($permissionNames, $batchSize);

    foreach ($batches as $batch) {
        foreach ($batch as $permissionName) {
            // Skip if not a string
            if (!is_string($permissionName)) {
                continue;
            }

            $processed = false;

            // Priority 1: Dot notation patterns (highest priority)
            if (strpos($permissionName, '.') !== false && !$processed) {
                // Handle complex 4-part permissions: master.module.action.subaction
                if (preg_match('/^master\.([^.]+)\.([^.]+)\.([^.]+)$/', $permissionName, $matches)) {
                    $module = 'master-' . $matches[1];
                    $action = $matches[2];
                    $subaction = $matches[3];

                    // Combine action and subaction for matrix
                    if ($action === 'import' && $subaction === 'store') {
                        $finalAction = 'import';
                    } elseif ($action === 'print' && $subaction === 'single') {
                        $finalAction = 'print';
                    } else {
                        $finalAction = $action . '_' . $subaction;
                    }

                    $matrixPermissions[$module][$finalAction] = true;
                    $processed = true;
                }
                // Master module pattern: master.module.action
                elseif (preg_match($patterns['dot_master'], $permissionName, $matches)) {
                    $module = 'master-' . $matches[1];
                    $action = $actionMap[$matches[2]] ?? $matches[2];
                    $matrixPermissions[$module][$action] = true;
                    $processed = true;
                }
                // General dot pattern: module.action
                elseif (preg_match($patterns['dot_general'], $permissionName, $matches)) {
                    $module = $matches[1];
                    $action = $actionMap[$matches[2]] ?? $matches[2];

                    // Special handling for specific modules
                    if ($module === 'admin' || $module === 'profile' || $module === 'supir' || $module === 'approval') {
                        $matrixPermissions[$module][$action] = true;
                        $processed = true;
                    }
                }
            }

            // Priority 2: Dash notation patterns
            if (strpos($permissionName, '-') !== false && !$processed) {
                // Master module pattern: master-module-action
                if (preg_match($patterns['dash_master'], $permissionName, $matches)) {
                    $module = 'master-' . $matches[1];
                    $action = $actionMap[$matches[2]] ?? $matches[2];
                    $matrixPermissions[$module][$action] = true;
                    $processed = true;
                }
                // General dash pattern: module-action
                elseif (preg_match($patterns['dash_general'], $permissionName, $matches)) {
                    $module = $matches[1];
                    $action = $matches[2];

                    // Handle complex module names
                    $complexModules = [
                        'tagihan-kontainer-sewa' => 'tagihan-kontainer-sewa',
                        'pranota-kontainer-sewa' => 'pranota-kontainer-sewa',
                        'tagihan-perbaikan-kontainer' => 'tagihan-perbaikan-kontainer',
                        'pembayaran-pranota-cat' => 'pembayaran-pranota-cat',
                        'perbaikan-kontainer' => 'perbaikan-kontainer',
                    ];

                    // Check if this is a complex module
                    foreach ($complexModules as $pattern => $moduleKey) {
                        if (strpos($permissionName, $pattern . '-') === 0) {
                            $module = $moduleKey;
                            $action = str_replace($pattern . '-', '', $permissionName);
                            break;
                        }
                    }

                    $mappedAction = $actionMap[$action] ?? $action;
                    $matrixPermissions[$module][$mappedAction] = true;
                    $processed = true;
                }
            }

            // Priority 3: Special cases
            if (!$processed) {
                // Handle special permissions
                $specialPermissions = [
                    'dashboard' => ['system' => 'dashboard'],
                    'login' => ['auth' => 'login'],
                    'logout' => ['auth' => 'logout'],
                    'storage-local' => ['storage' => 'local'],
                    'user-approval' => ['user-approval' => 'view'],
                ];

                if (isset($specialPermissions[$permissionName])) {
                    foreach ($specialPermissions[$permissionName] as $module => $action) {
                        $matrixPermissions[$module][$action] = true;
                    }
                    $processed = true;
                }
            }

            // Priority 4: Simple module names (fallback)
            if (!$processed && strpos($permissionName, '-') === false && strpos($permissionName, '.') === false) {
                $matrixPermissions[$permissionName]['view'] = true;
            }
        }
    }

    // Special handling for approval-dashboard relationship
    if (in_array('approval-dashboard', $permissionNames)) {
        $matrixPermissions['approval-tugas-1']['view'] = true;
        $matrixPermissions['approval-tugas-2']['view'] = true;
    }

    return $matrixPermissions;
}

/**
 * Convert matrix permissions to permission IDs (OPTIMIZED VERSION)
 *
 * @param array $matrixPermissions
 * @return array
 */
function convertMatrixPermissionsToIdsOptimized(array $matrixPermissions): array
{
    // Cache all permissions at once to avoid multiple DB queries
    static $permissionCache = null;
    if ($permissionCache === null) {
        $allPermissions = DB::table('permissions')->get();
        $permissionCache = [];
        foreach ($allPermissions as $perm) {
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

                // Generate possible permission name patterns
                $possibleNames = [];

                // Pattern 1: module-action
                $possibleNames[] = $module . '-' . $action;

                // Pattern 2: module.action
                $possibleNames[] = str_replace('-', '.', $module) . '.' . $action;

                // Pattern 3: Action variations
                $actionVariations = [
                    'view' => ['view', 'index', 'show'],
                    'create' => ['create', 'store'],
                    'update' => ['update', 'edit'],
                    'delete' => ['delete', 'destroy'],
                ];

                if (isset($actionVariations[$action])) {
                    foreach ($actionVariations[$action] as $variation) {
                        $possibleNames[] = $module . '-' . $variation;
                        $possibleNames[] = str_replace('-', '.', $module) . '.' . $variation;
                    }
                }

                // Look for matching permissions in cache
                foreach ($possibleNames as $permName) {
                    if (isset($permissionCache[$permName])) {
                        $permissionIds[] = $permissionCache[$permName];
                        break; // Only add first match to avoid duplicates
                    }
                }
            }
        }
    }

    return array_unique($permissionIds);
}

echo "🚀 OPTIMIZED PERMISSION CONVERTER FUNCTIONS LOADED\n";
echo "💡 These functions reduce complexity and improve performance significantly:\n";
echo "   - Uses regex patterns for better performance\n";
echo "   - Caches permission lookups\n";
echo "   - Processes permissions in batches\n";
echo "   - Reduces nested loops and complex string operations\n\n";
echo "📋 TO IMPLEMENT:\n";
echo "   1. Replace convertPermissionsToMatrix method in UserController\n";
echo "   2. Replace convertMatrixPermissionsToIds method in UserController\n";
echo "   3. Test on a staging environment first\n";
echo "   4. Monitor server performance after deployment\n";
