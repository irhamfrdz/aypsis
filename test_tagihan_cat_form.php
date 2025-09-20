<?php

// test_tagihan_cat_form_submission.php
// Script khusus untuk test form submission permission tagihan-cat

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING TAGIHAN-CAT FORM SUBMISSION ===\n";
echo "===========================================\n\n";

// Test data - user admin
$user = User::where('username', 'admin')->first();
if (!$user) {
    echo "❌ User admin not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Simulate form data that would be sent when user checks tagihan-cat permissions
$formData = [
    'username' => 'admin',
    'karyawan_id' => $user->karyawan_id,
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

echo "Simulated form data:\n";
print_r($formData);
echo "\n";

// Check if permissions exist in database
echo "=== CHECKING PERMISSIONS IN DATABASE ===\n";
$tagihanCatPermissions = Permission::where('name', 'like', 'tagihan-cat%')->get();
echo "Found " . $tagihanCatPermissions->count() . " tagihan-cat permissions:\n";
foreach ($tagihanCatPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}
echo "\n";

// Test convertMatrixPermissionsToIds method
echo "=== TESTING PERMISSION CONVERSION ===\n";
try {
    $controller = new UserController();

    // Access private method using reflection
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('convertMatrixPermissionsToIds');
    $method->setAccessible(true);

    $permissionIds = $method->invoke($controller, $formData['permissions']);

    echo "Converted Permission IDs:\n";
    print_r($permissionIds);

    // Check if tagihan-cat permissions are included
    $tagihanCatIds = array_filter($permissionIds, function($id) {
        $permission = \App\Models\Permission::find($id);
        return $permission && strpos($permission->name, 'tagihan-cat') === 0;
    });

    echo "\nTagihan-CAT Permission IDs Found:\n";
    print_r($tagihanCatIds);

    if (empty($tagihanCatIds)) {
        echo "\n❌ ERROR: No tagihan-cat permissions found in conversion!\n";
    } else {
        echo "\n✅ SUCCESS: Tagihan-cat permissions converted successfully\n";
        echo "Count: " . count($tagihanCatIds) . "\n";

        // Verify each permission exists
        foreach ($tagihanCatIds as $permId) {
            $perm = Permission::find($permId);
            if ($perm) {
                echo "✓ Permission {$perm->name} exists\n";
            } else {
                echo "✗ Permission ID {$permId} not found in database\n";
            }
        }
    }

} catch (Exception $e) {
    echo "\n❌ ERROR in conversion: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== TESTING ACTUAL USER UPDATE ===\n";
try {
    // Create mock request
    $request = Request::create('/master/user/' . $user->id, 'PUT', $formData);
    $request->headers->set('X-CSRF-TOKEN', 'test-token');

    // Call the controller method
    $response = app()->call('App\Http\Controllers\UserController@update', [
        'request' => $request,
        'user' => $user
    ]);

    echo "Controller response status: " . $response->getStatusCode() . "\n";

    if ($response->getStatusCode() == 302) {
        echo "✅ SUCCESS: User updated successfully (redirect response)\n";
    } else {
        echo "⚠️  WARNING: Unexpected response status\n";
    }

    // Check if user now has tagihan-cat permissions
    $user->refresh();
    $userPermissions = $user->permissions->pluck('name')->toArray();
    $tagihanCatUserPerms = array_filter($userPermissions, function($perm) {
        return strpos($perm, 'tagihan-cat') !== false;
    });

    echo "\nUser's tagihan-cat permissions after update:\n";
    print_r($tagihanCatUserPerms);

    if (count($tagihanCatUserPerms) > 0) {
        echo "\n✅ SUCCESS: Tagihan-cat permissions saved to user!\n";
    } else {
        echo "\n❌ ERROR: No tagihan-cat permissions saved to user!\n";
    }

} catch (Exception $e) {
    echo "\n❌ ERROR in user update: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
