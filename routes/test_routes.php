<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-approval-permissions/{userId}', function ($userId) {
    $user = \App\Models\User::findOrFail($userId);

    echo "<h1>Testing Approval Permissions for User: {$user->username}</h1>";

    // Test assign approval permissions
    $approvalPermissions = \App\Models\Permission::where('name', 'like', 'approval-tugas%')->get();

    echo "<h2>Available Approval Permissions:</h2>";
    foreach ($approvalPermissions as $perm) {
        echo "ID: {$perm->id} - Name: {$perm->name}<br>";
    }

    // Assign permissions
    $permissionIds = $approvalPermissions->pluck('id')->toArray();
    $user->permissions()->sync($permissionIds);

    echo "<h2>Assigned Permissions:</h2>";
    $userPermissions = $user->permissions()->get();
    foreach ($userPermissions as $perm) {
        echo "ID: {$perm->id} - Name: {$perm->name}<br>";
    }

    // Test conversion to matrix
    $controller = new \App\Http\Controllers\UserController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('convertPermissionsToMatrix');
    $method->setAccessible(true);

    $permissionNames = $userPermissions->pluck('name')->toArray();
    $matrix = $method->invoke($controller, $permissionNames);

    echo "<h2>Matrix Conversion:</h2>";
    echo "<pre>" . print_r($matrix, true) . "</pre>";

    return "Test completed";
});
