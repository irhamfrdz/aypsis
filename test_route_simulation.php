<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found\n";
    exit(1);
}

// Simulate authentication
Auth::login($user);
echo "Testing with user: {$user->name}\n";

// Test route resolution
try {
    $request = Request::create('/master/karyawan', 'GET');

    // Add CSRF token to bypass CSRF middleware
    $request->session()->put('_token', 'test-token');

    $response = app()->handle($request);

    echo "Response status: " . $response->getStatusCode() . "\n";

    if ($response->getStatusCode() == 403) {
        echo "✅ Got 403 response - middleware is working!\n";
        echo "Response preview: " . substr($response->getContent(), 0, 200) . "...\n";
    } elseif ($response->getStatusCode() == 200) {
        echo "✅ Got 200 response - user has permission!\n";
    } else {
        echo "⚠️  Unexpected status: " . $response->getStatusCode() . "\n";
        echo "Response content: " . substr($response->getContent(), 0, 500) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
