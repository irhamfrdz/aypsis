<?php

// test_http_form_submission.php
// Script untuk simulate HTTP form submission seperti dari browser

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING HTTP FORM SUBMISSION SIMULATION ===\n";
echo "===============================================\n\n";

// Test data - user admin
$user = User::where('username', 'admin')->first();
if (!$user) {
    echo "❌ User admin not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Simulate exact form data that would be sent from browser
// This mimics the HTML form structure from edit.blade.php
$formData = [
    '_token' => 'test-csrf-token', // CSRF token
    '_method' => 'PUT', // Method spoofing
    'username' => 'admin',
    'password' => '',
    'password_confirmation' => '',
    'karyawan_id' => '44',
    // Permission checkboxes - this is how they appear in the form
    'permissions' => [
        'tagihan-cat' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'print' => '1',
            'export' => '1'
        ]
    ]
];

echo "Simulated HTTP POST data (from browser form):\n";
print_r($formData);
echo "\n";

// Create HTTP request simulation
echo "=== SIMULATING HTTP REQUEST ===\n";
try {
    // Create request with PUT method (Laravel method spoofing)
    $request = Request::create(
        '/master/user/' . $user->id,
        'POST', // Browser sends POST
        $formData
    );

    // Set headers like browser would
    $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
    $request->headers->set('X-CSRF-TOKEN', 'test-csrf-token');
    $request->headers->set('Referer', 'http://localhost/master/user/' . $user->id . '/edit');

    echo "Request method: " . $request->getMethod() . "\n";
    echo "Request URI: " . $request->getRequestUri() . "\n";
    echo "Content-Type: " . $request->header('Content-Type') . "\n";
    echo "CSRF Token: " . $request->header('X-CSRF-TOKEN') . "\n\n";

    // Test the permission data extraction
    $permissionsData = $request->input('permissions');
    echo "Permissions data from request:\n";
    print_r($permissionsData);

    if (isset($permissionsData['tagihan-cat'])) {
        echo "\n✅ SUCCESS: tagihan-cat permissions found in request\n";
        echo "Tagihan-cat data:\n";
        print_r($permissionsData['tagihan-cat']);
    } else {
        echo "\n❌ ERROR: tagihan-cat permissions not found in request\n";
    }

    // Test controller method
    echo "\n=== TESTING CONTROLLER METHOD ===\n";
    $controller = new UserController();

    // Access private method using reflection
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('convertMatrixPermissionsToIds');
    $method->setAccessible(true);

    $permissionIds = $method->invoke($controller, $permissionsData);

    echo "Converted permission IDs:\n";
    print_r($permissionIds);

    if (count($permissionIds) > 0) {
        echo "\n✅ SUCCESS: Permissions converted to IDs\n";
        echo "Count: " . count($permissionIds) . "\n";

        // Verify each permission exists
        $validPermissions = 0;
        foreach ($permissionIds as $permId) {
            $perm = \App\Models\Permission::find($permId);
            if ($perm) {
                echo "✓ Permission {$perm->name} (ID: {$perm->id}) exists\n";
                $validPermissions++;
            } else {
                echo "✗ Permission ID {$permId} not found\n";
            }
        }
        echo "\nValid permissions: {$validPermissions}/" . count($permissionIds) . "\n";
    } else {
        echo "\n❌ ERROR: No permissions converted\n";
    }

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== CONCLUSION ===\n";
echo "If all tests above pass, the backend is working correctly.\n";
echo "The issue is likely in the frontend (JavaScript, form submission, or browser).\n";
