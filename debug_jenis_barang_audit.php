<?php
require 'vendor/autoload.php';

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AuditLog;
use App\Models\JenisBarang;

echo "=== DEBUG AUDIT LOG JENIS BARANG ===\n";

// Check audit logs for JenisBarang model
$jenisBarangLogs = AuditLog::where('auditable_type', 'App\\Models\\JenisBarang')
    ->orderBy('created_at', 'desc')
    ->get();

echo "📊 Total audit logs untuk JenisBarang: " . $jenisBarangLogs->count() . "\n";

if ($jenisBarangLogs->count() > 0) {
    echo "\n📝 Detail audit logs:\n";
    foreach ($jenisBarangLogs as $log) {
        echo "   - ID: {$log->id}\n";
        echo "     Auditable ID: {$log->auditable_id}\n";
        echo "     Action: {$log->action}\n";
        echo "     Description: {$log->description}\n";
        echo "     Created: {$log->created_at}\n";
        echo "   ---\n";
    }
} else {
    echo "\n❌ Tidak ada audit log untuk model JenisBarang\n";
}

// Check if JenisBarang model uses Auditable trait
echo "\n🔍 Checking JenisBarang model...\n";
try {
    $jenisBarang = JenisBarang::first();
    if ($jenisBarang) {
        echo "✅ Model JenisBarang ditemukan\n";
        echo "Class: " . get_class($jenisBarang) . "\n";

        // Check traits
        $traits = class_uses_recursive(get_class($jenisBarang));
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
        echo "❌ Tidak ada data JenisBarang\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=================================================\n";
