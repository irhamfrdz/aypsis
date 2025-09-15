<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== TESTING DIFFERENT MODULES ===\n\n";

// Create UserController instance
$controller = new UserController();

// Test different modules to see if the method works for others
$testCases = [
    'master-user' => [
        'permissions' => [
            'master-user' => [
                'view' => '1'
            ]
        ]
    ],
    'pranota' => [
        'permissions' => [
            'pranota' => [
                'view' => '1'
            ]
        ]
    ],
    'pranota-supir' => [
        'permissions' => [
            'pranota-supir' => [
                'view' => '1'
            ]
        ]
    ]
];

foreach ($testCases as $caseName => $data) {
    echo "\n--- Testing {$caseName} ---\n";
    echo "Data: " . print_r($data, true);

    $permissionIds = $controller->testConvertMatrixPermissionsToIds($data);
    echo "Result: " . print_r($permissionIds, true);

    if (!empty($permissionIds)) {
        echo "SUCCESS! Found permissions:\n";
        foreach ($permissionIds as $id) {
            $permission = Permission::find($id);
            if ($permission) {
                echo "- {$permission->name} (ID: {$id})\n";
            }
        }
    } else {
        echo "FAILED: No permissions found\n";
    }
}

// Also test if the method exists and is callable
echo "\n=== METHOD EXISTENCE CHECK ===\n";
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
echo "Method exists: " . ($method ? 'YES' : 'NO') . "\n";
echo "Method is private: " . ($method->isPrivate() ? 'YES' : 'NO') . "\n";
echo "Method is accessible: " . ($method->isPublic() ? 'YES' : 'NO') . "\n";

?>
