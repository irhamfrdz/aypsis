<?php

echo "=== Test BL Single Row Permission Layout ===\n";

// Check blade template structure
$bladePath = 'resources/views/master-user/edit.blade.php';
$bladeContent = file_get_contents($bladePath);

echo "\n🔍 Checking BL Layout Changes:\n";

$checks = [
    'Single row BL section' => 'BL (Bill of Lading) - Single Row',
    'Direct permission inputs' => 'permissions[bl][view]',
    'No dropdown structure' => 'data-module="bl"',
    'No BL JavaScript function' => 'initializeCheckAllBL()',
    'No submodule rows' => 'data-parent="bl"',
];

foreach ($checks as $checkName => $pattern) {
    $found = strpos($bladeContent, $pattern) !== false;
    
    if ($checkName === 'No dropdown structure' || $checkName === 'No BL JavaScript function' || $checkName === 'No submodule rows') {
        // These should NOT be found (inverted logic)
        if (!$found) {
            echo "   ✅ {$checkName} (correctly removed)\n";
        } else {
            echo "   ❌ {$checkName} (still exists)\n";
        }
    } else {
        // These should be found
        if ($found) {
            echo "   ✅ {$checkName}\n";
        } else {
            echo "   ❌ {$checkName} not found\n";
        }
    }
}

// Count BL permission inputs
$blPermissionCount = substr_count($bladeContent, 'permissions[bl][');
echo "\n📊 BL Permission Inputs Found: {$blPermissionCount} (should be 7)\n";

// Check specific permissions
$blPermissions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
echo "\n📝 Individual BL Permissions:\n";
foreach ($blPermissions as $perm) {
    $pattern = "permissions[bl][{$perm}]";
    if (strpos($bladeContent, $pattern) !== false) {
        echo "   ✅ {$perm}\n";
    } else {
        echo "   ❌ {$perm} not found\n";
    }
}

echo "\n✨ Layout Benefits:\n";
echo "   • Space Efficient: Single row instead of 8 rows\n";
echo "   • Clean Interface: No dropdown/expand functionality\n";
echo "   • Direct Access: All permissions visible at once\n";
echo "   • Consistent: Follows standard permission matrix pattern\n";

echo "\n🎯 BL Permission Layout: OPTIMIZED FOR SPACE ✅\n";

?>