<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING IMPORT ACCESS ===\n\n";

// Get admin user and simulate login
$admin = User::where('username', 'admin')->first();

if ($admin) {
    echo "👤 Found admin user: {$admin->username} (ID: {$admin->id})\n";

    // Simulate authentication
    Auth::login($admin);

    echo "🔐 Authenticated as: " . Auth::user()->username . "\n";

    // Check permissions
    $hasCreatePermission = Auth::user()->can('tagihan-kontainer-sewa-create');
    $hasIndexPermission = Auth::user()->can('tagihan-kontainer-sewa-index');

    echo "\n🔍 Permission check:\n";
    echo "  tagihan-kontainer-sewa-create: " . ($hasCreatePermission ? '✅' : '❌') . "\n";
    echo "  tagihan-kontainer-sewa-index: " . ($hasIndexPermission ? '✅' : '❌') . "\n";

    if ($hasCreatePermission) {
        echo "\n✅ User should be able to access import functionality!\n";
        echo "\n🔗 Import URL: http://127.0.0.1:8000/daftar-tagihan-kontainer-sewa/import\n";
        echo "🔗 Main page: http://127.0.0.1:8000/daftar-tagihan-kontainer-sewa\n";
    } else {
        echo "\n❌ User does not have permission to access import\n";
    }

} else {
    echo "❌ Admin user not found\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test if controller method exists
echo "\n🧪 Testing controller method availability:\n";
try {
    $controller = new \App\Http\Controllers\DaftarTagihanKontainerSewaController();
    if (method_exists($controller, 'importPage')) {
        echo "✅ importPage method exists in controller\n";
    } else {
        echo "❌ importPage method NOT found in controller\n";
    }

    if (method_exists($controller, 'importCsv')) {
        echo "✅ importCsv method exists in controller\n";
    } else {
        echo "❌ importCsv method NOT found in controller\n";
    }

} catch (Exception $e) {
    echo "❌ Error testing controller: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
