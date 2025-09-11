<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Http\Controllers\UserController;

echo "Testing getUserPermissionsForCopy method for edit page...\n";

try {
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
    $response = $controller->getUserPermissionsForCopy($user);
    $data = $response->getData(true);

    echo "✅ Method executed successfully\n";
    echo "Response structure:\n";
    echo "- Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "- Permission count: " . $data['count'] . "\n";
    echo "- User info: " . $data['user']['name'] . " (" . $data['user']['username'] . ")\n";
    echo "- Permissions: " . implode(', ', array_slice($data['permissions'], 0, 5)) . (count($data['permissions']) > 5 ? '...' : '') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
