<?php
// Test file untuk debug registrasi user
echo "=== TESTING REGISTER USER ROUTE ===\n";

// Set up environment
define('LARAVEL_START', microtime(true));
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "1. Testing route exists...\n";
    $url = route('register.user');
    echo "Route URL: " . $url . "\n";

    echo "\n2. Testing AuthController method...\n";
    $controller = new App\Http\Controllers\AuthController();

    echo "3. Testing Karyawan model and user relationship...\n";
    $karyawansWithoutUser = App\Models\Karyawan::whereDoesntHave('user')->get();
    echo "Karyawan without user account: " . $karyawansWithoutUser->count() . "\n";

    if ($karyawansWithoutUser->count() > 0) {
        echo "First karyawan without user: " . $karyawansWithoutUser->first()->nama_lengkap . "\n";
    }

    echo "\n4. Testing controller method directly...\n";
    $response = $controller->showUserRegisterForm();
    echo "Controller method executed successfully!\n";

    echo "\n✅ ALL TESTS PASSED - Register User should work!\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
