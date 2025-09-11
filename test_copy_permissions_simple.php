<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Http\Controllers\UserController;

echo "Testing getUserPermissionsForCopy method...\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit(1);
}

echo "Found user: {$user->name} (ID: {$user->id})\n";

// Create controller instance
$controller = new UserController();

// Test the method
try {
    $response = $controller->getUserPermissionsForCopy($user);
    $data = $response->getData(true);

    echo "✅ Method executed successfully\n";
    echo "Response data:\n";
    echo "- Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "- Permission count: " . $data['count'] . "\n";
    echo "- Permissions: " . implode(', ', $data['permissions']) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
