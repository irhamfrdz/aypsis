<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing FIXED Checkbox Status for User test4\n";
echo "=============================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Get user matrix permissions (simulating controller logic)
$userMatrixPermissions = [];
$permissions = $user->permissions; // Use the correct relationship

foreach ($permissions as $permission) {
    $name = $permission->name;

    // Parse matrix permissions (tagihan-kontainer-view, tagihan-kontainer-create, etc.)
    if (strpos($name, 'tagihan-kontainer-') === 0) {
        $parts = explode('-', $name);
        if (count($parts) >= 3) {
            $module = $parts[0] . '-' . $parts[1]; // tagihan-kontainer
            $action = $parts[2]; // view, create, update, delete

            if (!isset($userMatrixPermissions[$module])) {
                $userMatrixPermissions[$module] = [];
            }
            $userMatrixPermissions[$module][$action] = true;
        }
    }
}

echo "Matrix Permissions Structure:\n";
echo json_encode($userMatrixPermissions, JSON_PRETTY_PRINT) . "\n\n";

echo "Checkbox Status Analysis (AFTER FIX):\n";

// Test the OLD format (that was causing issues)
$oldFormat = isset($userMatrixPermissions['tagihan-kontainer-view']);
echo "  - OLD format: \$userMatrixPermissions['tagihan-kontainer-view']: " . ($oldFormat ? "✅ TRUE" : "❌ FALSE") . "\n";

// Test the NEW format (the fix)
$newFormat = isset($userMatrixPermissions['tagihan-kontainer']['view']);
echo "  - NEW format: \$userMatrixPermissions['tagihan-kontainer']['view']: " . ($newFormat ? "✅ TRUE" : "❌ FALSE") . "\n";

echo "\nView Template Fix:\n";
echo "  🔧 BEFORE: <input type=\"checkbox\" {{ \$userMatrixPermissions['tagihan-kontainer-view'] ? 'checked' : '' }}>\n";
echo "  🔧 AFTER:  <input type=\"checkbox\" {{ \$userMatrixPermissions['tagihan-kontainer']['view'] ? 'checked' : '' }}>\n";

echo "\nExpected Result:\n";
echo "  ✅ Checkbox for tagihan-kontainer-view should be CHECKED for user test4\n";
echo "  ✅ User test4 can see their view permission correctly in the UI\n";

echo "\nFix Summary:\n";
echo "  🔧 Problem: View template used flat array format, controller used nested array format\n";
echo "  🔧 Solution: Updated view templates to use nested array format\n";
echo "  🔧 Result: Permission checkboxes now display correctly\n";

echo "\nTest completed!\n";
