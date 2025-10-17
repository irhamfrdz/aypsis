<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking database tables..." . PHP_EOL;

try {
    $count = \DB::table('surat_jalan_approvals')->count();
    echo "✓ surat_jalan_approvals table exists (records: $count)" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ surat_jalan_approvals table NOT found: " . $e->getMessage() . PHP_EOL;
}

try {
    $count = \DB::table('surat_jalans')->count();
    echo "✓ surat_jalans table exists (records: $count)" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ surat_jalans table NOT found: " . $e->getMessage() . PHP_EOL;
}

// Test route access simulation
echo PHP_EOL . "Testing route access simulation..." . PHP_EOL;

try {
    // Simulate what happens when accessing the route
    $user = \App\Models\User::where('username', 'admin')->first();
    if (!$user) {
        echo "✗ Admin user not found" . PHP_EOL;
        exit(1);
    }

    // Set authenticated user
    \Illuminate\Support\Facades\Auth::login($user);

    // Check permissions
    if ($user->can('surat-jalan-approval-dashboard')) {
        echo "✓ User has surat-jalan-approval-dashboard permission" . PHP_EOL;
    } else {
        echo "✗ User does NOT have surat-jalan-approval-dashboard permission" . PHP_EOL;
    }

    // Test controller logic
    $controller = new \App\Http\Controllers\SuratJalanApprovalController();

    // Simulate request
    ob_start();
    try {
        $response = $controller->index();
        echo "✓ Controller index method executed successfully" . PHP_EOL;
        echo "Response type: " . get_class($response) . PHP_EOL;
    } catch (Exception $e) {
        echo "✗ Controller index method failed: " . $e->getMessage() . PHP_EOL;
        echo "Error at: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    }
    ob_end_clean();

} catch (Exception $e) {
    echo "✗ General error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "Test complete." . PHP_EOL;
