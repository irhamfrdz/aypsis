<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\SuratJalanApproval;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\SuratJalanApprovalController;

echo "=== DEBUGGING CONTROLLER SURAT JALAN APPROVAL ===\n";

try {
    // Get admin user
    $admin = User::where('username', 'admin')->first();
    if (!$admin) {
        echo "❌ User admin tidak ditemukan\n";
        exit;
    }

    echo "✅ User admin ditemukan: {$admin->username}\n";

    // Test permissions
    echo "\n🔧 Testing permissions...\n";
    $permissions = [
        'surat-jalan-approval-dashboard',
        'surat-jalan-approval-level-1-view',
        'surat-jalan-approval-level-2-view'
    ];

    foreach ($permissions as $perm) {
        $has = $admin->can($perm);
        echo "- {$perm}: " . ($has ? '✅' : '❌') . "\n";
    }

    // Simulate login
    Auth::login($admin);
    echo "\n✅ User logged in\n";

    // Test models
    echo "\n🔧 Testing models...\n";

    // Count data
    $suratJalanCount = SuratJalan::count();
    $approvalCount = SuratJalanApproval::count();

    echo "- SuratJalan records: {$suratJalanCount}\n";
    echo "- SuratJalanApproval records: {$approvalCount}\n";

    // Test query dari controller
    echo "\n🔧 Testing controller queries...\n";

    $approvalLevel = 'level-1';

    // Test query pending approvals
    $pendingQuery = SuratJalanApproval::with(['suratJalan', 'approver'])
        ->where('approval_level', $approvalLevel)
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc');

    echo "- Pending approvals query: ✅\n";
    $pendingCount = $pendingQuery->count();
    echo "- Pending count: {$pendingCount}\n";

    // Test stats queries
    $statsQueries = [
        'pending' => SuratJalanApproval::where('approval_level', $approvalLevel)
            ->where('status', 'pending')->count(),
        'approved_today' => SuratJalanApproval::where('approval_level', $approvalLevel)
            ->where('status', 'approved')
            ->whereDate('approved_at', now())->count(),
        'approved_total' => SuratJalanApproval::where('approval_level', $approvalLevel)
            ->where('status', 'approved')->count(),
    ];

    foreach ($statsQueries as $key => $value) {
        echo "- Stats {$key}: {$value}\n";
    }

    // Test controller method
    echo "\n🔧 Testing controller index method...\n";
    $controller = new SuratJalanApprovalController();

    $request = Request::create('/approval/surat-jalan', 'GET');
    $request->setUserResolver(function () use ($admin) {
        return $admin;
    });

    $response = $controller->index();
    echo "✅ Controller index berhasil: " . get_class($response) . "\n";

    // Test view data
    if (method_exists($response, 'getData')) {
        $data = $response->getData();
        echo "- View data keys: " . implode(', ', array_keys($data)) . "\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUGGING SELESAI ===\n";
