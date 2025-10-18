<?php
require 'vendor/autoload.php';

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AuditLog;
use App\Models\Pengirim;

echo "=== DEBUG AUDIT LOG PENGIRIM ===\n";

// Check audit logs for Pengirim model
$pengirimLogs = AuditLog::where('auditable_type', 'App\\Models\\Pengirim')
    ->orderBy('created_at', 'desc')
    ->get();

echo "📊 Total audit logs untuk Pengirim: " . $pengirimLogs->count() . "\n";

if ($pengirimLogs->count() > 0) {
    echo "\n📝 Detail audit logs:\n";
    foreach ($pengirimLogs as $log) {
        echo "   - ID: {$log->id}\n";
        echo "     Auditable ID: {$log->auditable_id}\n";
        echo "     Action: {$log->action}\n";
        echo "     Description: {$log->description}\n";
        echo "     Created: {$log->created_at}\n";
        echo "   ---\n";
    }
} else {
    echo "\n❌ Tidak ada audit log untuk model Pengirim\n";
}

// Check if Pengirim model uses Auditable trait
echo "\n🔍 Checking Pengirim model...\n";
try {
    $pengirim = Pengirim::first();
    if ($pengirim) {
        echo "✅ Model Pengirim ditemukan\n";
        echo "Class: " . get_class($pengirim) . "\n";
        
        // Check traits
        $traits = class_uses_recursive(get_class($pengirim));
        echo "Traits:\n";
        foreach ($traits as $trait) {
            echo "   - {$trait}\n";
        }
        
        if (in_array('App\\Traits\\Auditable', $traits)) {
            echo "✅ Auditable trait sudah digunakan\n";
        } else {
            echo "❌ Auditable trait TIDAK digunakan\n";
        }
    } else {
        echo "❌ Tidak ada data Pengirim\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=================================================\n";