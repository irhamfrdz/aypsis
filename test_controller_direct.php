<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\SuratJalanApprovalController;

echo "=== Test Controller Approval Surat Jalan ===\n";

// Get admin user
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan\n";
    exit;
}

// Simulate authentication
Auth::login($admin);
echo "✅ User admin berhasil login\n";

// Create controller instance
$controller = new SuratJalanApprovalController();

try {
    // Test controller method langsung
    echo "\n🔧 Testing controller index method...\n";

    // Create a mock request
    $request = Request::create('/approval/surat-jalan', 'GET');
    $request->setUserResolver(function () use ($admin) {
        return $admin;
    });

    // Test akses ke controller
    $response = $controller->index();

    if ($response) {
        echo "✅ Controller index() berhasil dijalankan\n";
        echo "Response type: " . get_class($response) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error saat menjalankan controller: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Selesai ===\n";
