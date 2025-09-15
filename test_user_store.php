<?php
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing User Store with tagihan-kontainer permissions:\n";
echo "=====================================================\n";

// Simulate form data that would be sent when user checks all tagihan-kontainer permissions
$formData = [
    'username' => 'test_user_form_' . time(),
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'karyawan_id' => null,
    'permissions' => [
        'tagihan-kontainer' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'approve' => '1',
            'print' => '1',
            'export' => '1'
        ]
    ]
];

echo "Simulated form data:\n";
echo "Username: {$formData['username']}\n";
echo "Permissions:\n";
foreach ($formData['permissions']['tagihan-kontainer'] as $action => $value) {
    echo "  - {$action}: {$value}\n";
}
echo "\n";

// Create a mock request
$request = new Request();
$request->merge($formData);

// Create UserController instance
$controller = new App\Http\Controllers\UserController();

// Use reflection to access the store method
$reflection = new ReflectionClass($controller);
$storeMethod = $reflection->getMethod('store');

// Make the method accessible
$storeMethod->setAccessible(true);

// Call the store method
try {
    $response = $storeMethod->invoke($controller, $request);
    echo "Store method executed successfully!\n";

    // Check if user was created
    $user = User::where('username', $formData['username'])->first();
    if ($user) {
        echo "User created: {$user->username} (ID: {$user->id})\n";

        // Check permissions
        $userPermissions = $user->permissions()->get();
        echo "User has " . $userPermissions->count() . " permissions:\n";

        foreach ($userPermissions as $perm) {
            echo "- {$perm->name} (ID: {$perm->id})\n";
        }

        // Clean up
        $user->delete();
        echo "\nTest user cleaned up\n";
    } else {
        echo "User was not created!\n";
    }
} catch (Exception $e) {
    echo "Error calling store method: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
