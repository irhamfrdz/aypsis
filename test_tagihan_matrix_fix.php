<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing tagihan-kontainer permission matrix conversion\n";
echo "=====================================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Get user permissions
$userPermissions = $user->permissions->pluck('name')->toArray();
echo "User permissions:\n";
foreach ($userPermissions as $perm) {
    echo "  - $perm\n";
}
echo "\n";

// Test convertPermissionsToMatrix method
$reflection = new ReflectionClass('App\Http\Controllers\UserController');
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

$controller = new App\Http\Controllers\UserController();
$userMatrixPermissions = $method->invoke($controller, $userPermissions);

echo "Matrix conversion result:\n";
if (isset($userMatrixPermissions['tagihan-kontainer'])) {
    echo "✅ tagihan-kontainer module found in matrix:\n";
    foreach ($userMatrixPermissions['tagihan-kontainer'] as $action => $value) {
        echo "  - $action: " . ($value ? "✅ TRUE" : "❌ FALSE") . "\n";
    }
} else {
    echo "❌ tagihan-kontainer module NOT found in matrix\n";
}

echo "\nSpecific checks:\n";
$hasView = isset($userMatrixPermissions['tagihan-kontainer']['view']) && $userMatrixPermissions['tagihan-kontainer']['view'];
echo "tagihan-kontainer.view: " . ($hasView ? "✅ TRUE" : "❌ FALSE") . "\n";

$hasCreate = isset($userMatrixPermissions['tagihan-kontainer']['create']) && $userMatrixPermissions['tagihan-kontainer']['create'];
echo "tagihan-kontainer.create: " . ($hasCreate ? "✅ TRUE" : "❌ FALSE") . "\n";

echo "\nCheckbox condition test:\n";
echo "Old format (wrong): isset(\$userMatrixPermissions['tagihan-kontainer-view']) && \$userMatrixPermissions['tagihan-kontainer-view']\n";
$oldFormat = isset($userMatrixPermissions['tagihan-kontainer-view']) && $userMatrixPermissions['tagihan-kontainer-view'];
echo "Result: " . ($oldFormat ? "✅ TRUE" : "❌ FALSE") . "\n";

echo "\nNew format (correct): isset(\$userMatrixPermissions['tagihan-kontainer']['view']) && \$userMatrixPermissions['tagihan-kontainer']['view']\n";
$newFormat = isset($userMatrixPermissions['tagihan-kontainer']['view']) && $userMatrixPermissions['tagihan-kontainer']['view'];
echo "Result: " . ($newFormat ? "✅ TRUE" : "❌ FALSE") . "\n";

echo "\nConclusion:\n";
if ($newFormat) {
    echo "✅ Checkbox untuk tagihan-kontainer-view akan TERCEKLIS setelah perbaikan\n";
} else {
    echo "❌ Checkbox untuk tagihan-kontainer-view masih tidak akan terceklis\n";
}

echo "\nTest completed!\n";
