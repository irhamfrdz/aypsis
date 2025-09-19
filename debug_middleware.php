<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

// Get admin user and authenticate
$user = User::with('permissions')->where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found!\n";
    exit;
}

// Manually authenticate the user for this test
Auth::login($user);

echo "=== TESTING MIDDLEWARE 'can' ===\n";
echo "Authenticated as: {$user->username}\n";

// Create a mock request
$request = Request::create('/master/kode-nomor', 'GET');

// Test the 'can' middleware directly
$middleware = app(\Illuminate\Auth\Middleware\Authorize::class);

echo "\n=== TESTING MIDDLEWARE WITH GATE ===\n";

try {
    // Test middleware with master-kode-nomor-view
    $middleware->handle($request, function($req) {
        echo "✅ Middleware 'can:master-kode-nomor-view' PASSED\n";
        return response('OK');
    }, 'master-kode-nomor-view');

} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "❌ Middleware 'can:master-kode-nomor-view' FAILED: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "❌ Middleware error: " . $e->getMessage() . "\n";
}

echo "\n=== DIRECT GATE TESTING ===\n";
echo "Gate::allows('master-kode-nomor-view'): " . (\Illuminate\Support\Facades\Gate::allows('master-kode-nomor-view') ? 'TRUE' : 'FALSE') . "\n";
echo "Auth::user()->can('master-kode-nomor-view'): " . (Auth::user()->can('master-kode-nomor-view') ? 'TRUE' : 'FALSE') . "\n";

echo "\n=== CONCLUSION ===\n";
echo "If middleware fails but user->can() works, then the issue is with Gate::allows()\n";
echo "This would explain why routes are blocked but sidebar logic works\n";
