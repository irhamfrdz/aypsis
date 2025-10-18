<?php
require 'vendor/autoload.php';

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Karyawan;
use App\Http\Controllers\AuditLogController;
use Illuminate\Http\Request;

echo "=== DEBUG AJAX REQUEST SIMULATION ===\n";

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
        }
    } else {
        echo "❌ No audit log data in response\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error calling controller: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=================================================\n";
echo "🎯 DEBUGGING COMPLETED\n";
echo "=================================================\n";