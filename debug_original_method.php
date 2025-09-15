<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== DEBUGGING ORIGINAL METHOD: convertMatrixPermissionsToIds ===\n\n";

// Create UserController instance
$controller = new UserController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Test data simulating form submission when pranota-supir-view is checked
$testMatrixData = [
    'permissions' => [
        'pranota' => [
            'supir-view' => '1'
        ]
    ]
];

echo "Test Matrix Data:\n";
print_r($testMatrixData);
echo "\n";

// Let's also test with different data structures that might be sent by the form
$testCases = [
    'case1' => [
        'permissions' => [
            'pranota' => [
                'supir-view' => '1'
            ]
        ]
    ],
    'case2' => [
        'permissions' => [
            'pranota' => [
                'supir-view' => 1
            ]
        ]
    ],
    'case3' => [
        'permissions' => [
            'pranota' => [
                'supir-view' => true
            ]
        ]
    ],
    'case4' => [
        'permissions' => [
            'pranota' => [
                'supir-view' => 'on'  // HTML checkbox value
            ]
        ]
    ]
];

foreach ($testCases as $caseName => $data) {
    echo "\n--- Testing {$caseName} ---\n";
    echo "Data: " . print_r($data, true);

    $permissionIds = $method->invoke($controller, $data);
    echo "Result: " . print_r($permissionIds, true);

    if (!empty($permissionIds)) {
        echo "SUCCESS: Found permissions!\n";
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

// Let's also check what the actual form might be sending
echo "\n=== CHECKING ACTUAL FORM STRUCTURE ===\n";

// Check if there's an edit form to see the actual structure
$editFormPath = __DIR__ . '/resources/views/master-user/edit.blade.php';
if (file_exists($editFormPath)) {
    echo "Edit form exists, checking for permission matrix structure...\n";
    $content = file_get_contents($editFormPath);

    // Look for pranota-supir related inputs
    if (strpos($content, 'pranota') !== false) {
        echo "Found 'pranota' in edit form\n";

        // Extract lines containing pranota
        $lines = explode("\n", $content);
        $pranotaLines = array_filter($lines, function($line) {
            return strpos($line, 'pranota') !== false;
        });

        echo "Pranota-related lines in form:\n";
        foreach ($pranotaLines as $line) {
            echo "  " . trim($line) . "\n";
        }
    } else {
        echo "No 'pranota' found in edit form\n";
    }
} else {
    echo "Edit form not found at: {$editFormPath}\n";
}

?>
