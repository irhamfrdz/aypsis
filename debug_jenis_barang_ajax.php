<?php
require 'vendor/autoload.php';

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JenisBarang;
use App\Models\User;
use App\Http\Controllers\AuditLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== DEBUG AUDIT LOG JENIS BARANG AJAX ===\n";

// Login as admin user
$admin = User::where('username', 'admin')->first();
Auth::login($admin);
echo "✅ Logged in as: {$admin->username}\n";

// Find jenis barang ID 1
$jenisBarang = JenisBarang::find(1);
if (!$jenisBarang) {
    echo "❌ JenisBarang ID 1 tidak ditemukan\n";
    exit;
}

echo "✅ JenisBarang ditemukan: {$jenisBarang->nama_barang} (ID: {$jenisBarang->id})\n";

// Get the correct model class name
$modelClass = get_class($jenisBarang);
echo "📋 Model class: {$modelClass}\n";

// Create a mock request like what would be sent via AJAX
$requestData = [
    'model_type' => $modelClass,
    'model_id' => $jenisBarang->id
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
    }

} catch (Exception $e) {
    echo "❌ Error calling controller: " . $e->getMessage() . "\n";
}

echo "\n=================================================\n";
