<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== TESTING URL ACCESS ===" . PHP_EOL;
echo "URL: http://localhost:8000/approval/surat-jalan" . PHP_EOL;
echo PHP_EOL;

// Create a GET request
$request = Illuminate\Http\Request::create(
    '/approval/surat-jalan',
    'GET',
    [],
    [], // cookies
    [], // files
    ['HTTP_HOST' => 'localhost:8000'] // server variables
);

// Start session
$app->make('session')->start();

// Authenticate user
$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    \Illuminate\Support\Facades\Auth::login($user);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    echo "✅ User authenticated: " . $user->username . PHP_EOL;
} else {
    echo "❌ User admin not found" . PHP_EOL;
    exit(1);
}

echo PHP_EOL . "Processing request..." . PHP_EOL;

try {
    $response = $kernel->handle($request);

    $statusCode = $response->getStatusCode();
    echo PHP_EOL . "Response Status Code: " . $statusCode . PHP_EOL;

    if ($statusCode == 404) {
        echo "❌ 404 NOT FOUND!" . PHP_EOL;
        echo PHP_EOL . "Response Content:" . PHP_EOL;
        echo substr($response->getContent(), 0, 1000) . PHP_EOL;
    } elseif ($statusCode == 403) {
        echo "❌ 403 FORBIDDEN - Permission denied" . PHP_EOL;
    } elseif ($statusCode == 302 || $statusCode == 301) {
        echo "🔄 REDIRECT to: " . $response->headers->get('Location') . PHP_EOL;
    } elseif ($statusCode == 200) {
        echo "✅ SUCCESS! Page loaded successfully" . PHP_EOL;

        // Check if it's the correct view
        $content = $response->getContent();
        if (strpos($content, 'Approval Surat Jalan') !== false) {
            echo "✅ Correct page content detected" . PHP_EOL;
        } else {
            echo "⚠️ Page loaded but content seems wrong" . PHP_EOL;
        }
    } else {
        echo "Status: " . $statusCode . PHP_EOL;
    }

    $kernel->terminate($request, $response);

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    echo PHP_EOL . "Stack Trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}

echo PHP_EOL . "=== END TEST ===" . PHP_EOL;
