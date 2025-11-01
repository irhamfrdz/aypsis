<?php

echo "=== TESTING MASTER KAPAL UI INTEGRATION ===\n\n";

// Test UI integration dengan membuat matrix permissions seperti yang akan dikirim dari form
$userController = new App\Http\Controllers\UserController();

// Simulasi input dari form UI - user mencentang view dan create untuk master-kapal
$formInput = [
    'master-kapal' => [
        'view' => '1',
        'create' => '1',
        'update' => '0', // unchecked
        'delete' => '0', // unchecked
        'print' => '0',  // unchecked
        'export' => '0'  // unchecked
    ]
];

echo "1. Form input simulation (user checked view and create only):\n";
echo json_encode($formInput, JSON_PRETTY_PRINT) . "\n\n";

echo "2. Converting form input to permission IDs:\n";
$permissionIds = $userController->testConvertMatrixPermissionsToIds($formInput);
echo "Permission IDs: " . json_encode($permissionIds) . "\n\n";

echo "3. Getting permission names from IDs:\n";
$permissions = App\Models\Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
echo "Permissions: " . json_encode($permissions) . "\n\n";

echo "4. Expected result: Only master-kapal.view and master-kapal.create\n";
$expected = ['master-kapal.view', 'master-kapal.create'];
sort($permissions);
sort($expected);

if ($permissions === $expected) {
    echo "✅ UI INTEGRATION SUCCESS: Form correctly handles master-kapal permissions!\n";
} else {
    echo "❌ UI INTEGRATION ISSUE:\n";
    echo "Expected: " . json_encode($expected) . "\n";
    echo "Got: " . json_encode($permissions) . "\n";
}

echo "\n=== UI INTEGRATION TEST COMPLETE ===\n";