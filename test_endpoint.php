<?php

// Simple test to check if the copy permissions endpoint is accessible
echo "Testing copy permissions endpoint accessibility...\n";

// Get the first user ID from database (simulating what would happen in the browser)
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

try {
    $user = User::first();
    if (!$user) {
        echo "❌ No users found in database\n";
        exit(1);
    }

    $userId = $user->id;
    $url = "http://localhost/master/user/{$userId}/permissions-for-copy";

    echo "Testing URL: {$url}\n";

    // Use curl to test the endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    echo "HTTP Code: {$httpCode}\n";

    if ($error) {
        echo "❌ cURL Error: {$error}\n";
    } else {
        echo "Response: {$response}\n";

        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            echo "✅ Endpoint accessible and returning valid JSON\n";
            echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
            echo "Permission count: " . ($data['count'] ?? 0) . "\n";
        } else {
            echo "❌ Invalid JSON response\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
