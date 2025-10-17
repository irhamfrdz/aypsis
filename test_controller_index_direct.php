<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING CONTROLLER DIRECTLY ===" . PHP_EOL;

// Authenticate user
$user = \App\Models\User::where('username', 'admin')->first();
\Illuminate\Support\Facades\Auth::login($user);
echo "✅ User authenticated: " . $user->username . PHP_EOL;

try {
    echo PHP_EOL . "Testing controller index method..." . PHP_EOL;

    // Instantiate controller
    $controller = new \App\Http\Controllers\SuratJalanApprovalController();

    // Call index method
    $response = $controller->index();

    echo "✅ Controller method executed successfully" . PHP_EOL;
    echo "Response type: " . get_class($response) . PHP_EOL;

    if (method_exists($response, 'getStatusCode')) {
        echo "Status code: " . $response->getStatusCode() . PHP_EOL;
    }

    if (method_exists($response, 'getData') && is_callable([$response, 'getData'])) {
        $data = $response->getData();
        echo "Response data keys: " . implode(', ', array_keys($data)) . PHP_EOL;
    }

} catch (\Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    echo "Trace: " . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
}

echo PHP_EOL . "=== TEST COMPLETE ===" . PHP_EOL;
