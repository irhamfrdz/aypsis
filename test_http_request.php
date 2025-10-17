<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Testing HTTP request to approval/surat-jalan..." . PHP_EOL;

// Create a request
$request = \Illuminate\Http\Request::create('/approval/surat-jalan', 'GET');

// Add session for authentication
$session = new \Illuminate\Session\Store(
    'laravel_session',
    new \Illuminate\Session\ArraySessionHandler(),
    null
);

$request->setLaravelSession($session);

// Set up authentication
$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    \Illuminate\Support\Facades\Auth::login($user);
    echo "✓ User authenticated: " . $user->username . PHP_EOL;
} else {
    echo "✗ Failed to authenticate user" . PHP_EOL;
}

try {
    // Handle the request
    $response = $kernel->handle($request);

    echo "✓ Request handled successfully" . PHP_EOL;
    echo "Status Code: " . $response->getStatusCode() . PHP_EOL;

    if ($response->getStatusCode() == 404) {
        echo "✗ 404 Not Found - Route not working" . PHP_EOL;
        echo "Content: " . substr($response->getContent(), 0, 500) . "..." . PHP_EOL;
    } elseif ($response->getStatusCode() == 200) {
        echo "✓ Success - Route is working" . PHP_EOL;
    } else {
        echo "Status: " . $response->getStatusCode() . PHP_EOL;
        echo "Content preview: " . substr($response->getContent(), 0, 200) . "..." . PHP_EOL;
    }

} catch (Exception $e) {
    echo "✗ Error handling request: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}

// Terminate
$kernel->terminate($request, $response ?? null);
