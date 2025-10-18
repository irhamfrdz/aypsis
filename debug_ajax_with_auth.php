<?php
require 'vendor/autoload.php';

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Karyawan;
use App\Models\User;
use App\Http\Controllers\AuditLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== DEBUG AJAX REQUEST WITH AUTH ===\n";

// Login as admin user
$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user tidak ditemukan\n";
    exit;
}

Auth::login($admin);
echo "✅ Logged in as: {$admin->username}\n";

// Check audit-log permission
$hasPermission = $admin->hasPermissionTo('audit-log-view');
echo "🔐 Audit-log permission: " . ($hasPermission ? '✅ Ya' : '❌ Tidak') . "\n";

// Find karyawan
$karyawan = Karyawan::where('nama_lengkap', 'ABDUL ROHMAN')->first();
if (!$karyawan) {
    echo "❌ Karyawan tidak ditemukan\n";
    exit;
}

echo "✅ Karyawan ditemukan: {$karyawan->nama_lengkap} (ID: {$karyawan->id})\n";

// Get the correct model class name
$modelClass = get_class($karyawan);
echo "📋 Model class: {$modelClass}\n";

// Create a mock request like what would be sent via AJAX
$requestData = [
    'model_type' => $modelClass,
    'model_id' => $karyawan->id
];

echo "\n🔄 Simulating AJAX request...\n";
echo "Request data: " . json_encode($requestData, JSON_PRETTY_PRINT) . "\n";

// Create a mock request
$request = new Request();
$request->merge($requestData);

// Create controller instance
$controller = new AuditLogController();

try {
    // Call the method directly
    $response = $controller->getModelAuditLogs($request);

    echo "\n📤 Response from controller:\n";
    $responseData = json_decode($response->getContent(), true);
    echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    echo "\n🔍 Response analysis:\n";
    echo "Success: " . ($responseData['success'] ? '✅ true' : '❌ false') . "\n";
    echo "Data count: " . count($responseData['data'] ?? []) . "\n";

    if (!empty($responseData['data'])) {
        echo "\n📝 Audit log entries:\n";
        foreach ($responseData['data'] as $index => $log) {
            echo "  " . ($index + 1) . ". {$log['action']} - {$log['description']} ({$log['created_at']})\n";
            if (!empty($log['changes'])) {
                $changesStr = is_array($log['changes']) ? json_encode($log['changes']) : $log['changes'];
                echo "     Changes: " . substr($changesStr, 0, 100) . "...\n";
            }
        }
    } else {
        echo "❌ No audit log data in response\n";
        if (isset($responseData['message'])) {
            echo "Message: {$responseData['message']}\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error calling controller: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=================================================\n";
echo "🎯 DEBUGGING COMPLETED\n";
echo "Jika response berhasil tapi data kosong, kemungkinan:\n";
echo "1. Masalah di frontend JavaScript\n";
echo "2. CSRF token issue\n";
echo "3. Network request error\n";
echo "=================================================\n";
