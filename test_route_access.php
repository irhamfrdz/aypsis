<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use App\Models\User;
use Illuminate\Http\Request;

echo "=== TEST AKSES ROUTE MOBIL INDEX ===\n\n";

$user = User::where('username', 'suci')->first();

if (!$user) {
    echo "✗ User suci tidak ditemukan!\n";
    exit;
}

echo "✓ User: {$user->username} (ID: {$user->id})\n\n";

// Create request untuk route mobil.index
$request = Request::create('/mobil', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

try {
    echo "Mencoba akses route '/mobil'...\n\n";
    
    // Handle request
    $response = $kernel->handle($request);
    
    echo "Status Code: {$response->getStatusCode()}\n";
    
    if ($response->getStatusCode() === 200) {
        echo "✓ BERHASIL! Akses DIIZINKAN\n";
    } elseif ($response->getStatusCode() === 403) {
        echo "✗ GAGAL! Status 403 - Access Denied\n";
        echo "\nResponse content:\n";
        echo substr($response->getContent(), 0, 500);
    } elseif ($response->getStatusCode() === 302) {
        echo "↻ REDIRECT ke: " . $response->headers->get('Location') . "\n";
    } else {
        echo "Status lain: {$response->getStatusCode()}\n";
    }
    
} catch (\Exception $e) {
    echo "✗ ERROR:\n";
    echo "   Message: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n";
}

$kernel->terminate($request, $response ?? null);
