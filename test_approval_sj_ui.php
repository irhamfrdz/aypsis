<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING APPROVAL SURAT JALAN UI INTEGRATION ===\n\n";

// Test UI integration dengan membuat matrix permissions seperti yang akan dikirim dari form
$userController = new App\Http\Controllers\UserController();

// Simulasi input dari form UI - user mencentang view dan approve untuk approval-surat-jalan
$formInput = [
    'approval-surat-jalan' => [
        'view' => '1',
        'create' => '0',
        'update' => '0',
        'delete' => '0',
        'approve' => '1',
        'print' => '0',
        'export' => '0'
    ]
];

echo "1. Form input simulation (user checked view and approve only):\n";
echo json_encode($formInput, JSON_PRETTY_PRINT) . "\n\n";

echo "2. Converting form input to permission IDs:\n";
$permissionIds = $userController->testConvertMatrixPermissionsToIds($formInput);
echo "Permission IDs: " . json_encode($permissionIds) . "\n\n";

echo "3. Getting permission names from IDs:\n";
$permissions = App\Models\Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
echo "Permissions: " . json_encode($permissions) . "\n\n";

echo "4. Expected result: Only approval-surat-jalan-view and approval-surat-jalan-approve\n";
$expected = ['approval-surat-jalan-view', 'approval-surat-jalan-approve'];
sort($permissions);
sort($expected);

if ($permissions === $expected) {
    echo "✅ UI INTEGRATION SUCCESS: Form correctly handles approval-surat-jalan permissions!\n";
} else {
    echo "❌ UI INTEGRATION ISSUE:\n";
    echo "Expected: " . json_encode($expected) . "\n";
    echo "Got: " . json_encode($permissions) . "\n";
}

echo "\n=== UI INTEGRATION TEST COMPLETE ===\n";