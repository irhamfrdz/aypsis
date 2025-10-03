<?php

echo "=== FIX PERMISSION DASHBOARD MATRIX MAPPING ===\n\n";

// Path ke UserController
$controllerPath = 'app/Http/Controllers/UserController.php';

echo "1. Backup original UserController...\n";
copy($controllerPath, $controllerPath . '.backup.' . date('Y-m-d-H-i-s'));
echo "✅ Backup created: {$controllerPath}.backup." . date('Y-m-d-H-i-s') . "\n\n";

echo "2. Reading UserController content...\n";
$content = file_get_contents($controllerPath);

// Find the position to insert the dashboard mapping
// Look for the Pattern 8: Login/Logout permissions section
$pattern8Start = strpos($content, '// Pattern 8: Login/Logout permissions');

if ($pattern8Start === false) {
    echo "❌ Could not find Pattern 8 section!\n";
    exit(1);
}

// Insert the new pattern before Pattern 8
$dashboardPattern = "            // Pattern 8: Standalone dashboard permission\n";
$dashboardPattern .= "            if (\$permissionName === 'dashboard') {\n";
$dashboardPattern .= "                \$module = 'system';\n";
$dashboardPattern .= "\n";
$dashboardPattern .= "                // Initialize module array if not exists\n";
$dashboardPattern .= "                if (!isset(\$matrixPermissions[\$module])) {\n";
$dashboardPattern .= "                    \$matrixPermissions[\$module] = [];\n";
$dashboardPattern .= "                }\n";
$dashboardPattern .= "\n";
$dashboardPattern .= "                \$matrixPermissions[\$module]['dashboard'] = true;\n";
$dashboardPattern .= "                continue; // Skip other patterns\n";
$dashboardPattern .= "            }\n";
$dashboardPattern .= "\n";
$dashboardPattern .= "            // Pattern 9: Login/Logout permissions\n";

// Replace Pattern 8 with Pattern 8 + 9
$newContent = str_replace(
    '            // Pattern 8: Login/Logout permissions',
    $dashboardPattern,
    $content
);

if ($newContent === $content) {
    echo "❌ No changes made - pattern not found!\n";
    exit(1);
}

echo "3. Adding dashboard mapping to convertPermissionsToMatrix()...\n";
file_put_contents($controllerPath, $newContent);
echo "✅ Dashboard permission mapping added\n\n";

// Now we need to update convertMatrixPermissionsToIds to handle the reverse mapping
echo "4. Adding reverse mapping in convertMatrixPermissionsToIds()...\n";

// Find the convertMatrixPermissionsToIds method
$content = file_get_contents($controllerPath);

// Look for the system module handling
$systemHandlingPattern = "                // Handle system module permissions\n";
$systemHandlingPattern .= "                if (\$module === 'system') {\n";
$systemHandlingPattern .= "                    foreach (\$actions as \$action => \$enabled) {\n";
$systemHandlingPattern .= "                        if (!\$enabled) continue;\n";
$systemHandlingPattern .= "\n";
$systemHandlingPattern .= "                        if (\$action === 'dashboard') {\n";
$systemHandlingPattern .= "                            \$permission = Permission::where('name', 'dashboard')->first();\n";
$systemHandlingPattern .= "                            if (\$permission) {\n";
$systemHandlingPattern .= "                                \$permissionIds[] = \$permission->id;\n";
$systemHandlingPattern .= "                            }\n";
$systemHandlingPattern .= "                        }\n";
$systemHandlingPattern .= "                    }\n";
$systemHandlingPattern .= "                    continue;\n";
$systemHandlingPattern .= "                }\n";
$systemHandlingPattern .= "\n";

// Find where to insert this - look for the foreach loop in convertMatrixPermissionsToIds
$insertPoint = strpos($content, 'foreach ($matrixPermissions as $module => $actions) {');
if ($insertPoint === false) {
    echo "❌ Could not find convertMatrixPermissionsToIds foreach loop!\n";
    exit(1);
}

// Find the next line after "// Skip if no actions are selected for this module"
$skipLine = strpos($content, '// Skip if no actions are selected for this module', $insertPoint);
if ($skipLine === false) {
    echo "❌ Could not find skip line in convertMatrixPermissionsToIds!\n";
    exit(1);
}

// Find the end of the skip block
$nextLineAfterSkip = strpos($content, "\n", strpos($content, 'continue;', $skipLine));
if ($nextLineAfterSkip === false) {
    echo "❌ Could not find insertion point!\n";
    exit(1);
}

$insertPosition = $nextLineAfterSkip + 1;

// Insert the system module handling
$newContent = substr($content, 0, $insertPosition) .
              "\n" . $systemHandlingPattern .
              substr($content, $insertPosition);

file_put_contents($controllerPath, $newContent);
echo "✅ Reverse mapping for system module added\n\n";

echo "5. Verifying changes...\n";
$finalContent = file_get_contents($controllerPath);

if (strpos($finalContent, "if (\$permissionName === 'dashboard')") !== false) {
    echo "✅ Dashboard permission mapping found\n";
} else {
    echo "❌ Dashboard permission mapping NOT found\n";
}

if (strpos($finalContent, "if (\$module === 'system')") !== false) {
    echo "✅ System module reverse mapping found\n";
} else {
    echo "❌ System module reverse mapping NOT found\n";
}

echo "\n=== CHANGES COMPLETED ===\n";
echo "Dashboard permission 'dashboard' will now appear in matrix as:\n";
echo "Module: system\n";
echo "Action: dashboard\n\n";

echo "Next steps:\n";
echo "1. Test edit user form - checkbox untuk dashboard harus muncul\n";
echo "2. Test update user - permission dashboard tidak boleh hilang\n";
echo "3. Test create user - bisa assign permission dashboard\n\n";

echo "To verify, run:\n";
echo "php artisan route:list | grep dashboard\n";
echo "And check user edit form in browser.\n";

?>
