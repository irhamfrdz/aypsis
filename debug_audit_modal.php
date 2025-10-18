<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Karyawan;
use App\Models\AuditLog;

echo "=== DEBUG AUDIT LOG MODAL ISSUE ===\n\n";

// Cari karyawan ABDUL ROHMAN
$karyawan = Karyawan::where('nama_lengkap', 'like', '%ABDUL ROHMAN%')->first();

if (!$karyawan) {
    echo "❌ Karyawan ABDUL ROHMAN tidak ditemukan\n";
    echo "🔍 Mencari karyawan dengan nama mengandung 'ABDUL'...\n";
    $karyawans = Karyawan::where('nama_lengkap', 'like', '%ABDUL%')->get();
    if ($karyawans->count() > 0) {
        foreach ($karyawans as $k) {
            echo "   - ID: {$k->id}, Nama: {$k->nama_lengkap}\n";
        }
        $karyawan = $karyawans->first();
        echo "\n✅ Menggunakan: {$karyawan->nama_lengkap} (ID: {$karyawan->id})\n\n";
    } else {
        echo "❌ Tidak ada karyawan dengan nama mengandung 'ABDUL'\n";
        exit(1);
    }
} else {
    echo "✅ Karyawan ditemukan: {$karyawan->nama_lengkap} (ID: {$karyawan->id})\n\n";
}

// Check model class
$modelClass = get_class($karyawan);
echo "📋 Model class: {$modelClass}\n\n";

// Cek audit logs untuk karyawan ini
echo "🔍 Mencari audit logs untuk karyawan ini...\n";
$auditLogs = AuditLog::where('auditable_type', $modelClass)
    ->where('auditable_id', $karyawan->id)
    ->with('user')
    ->orderBy('created_at', 'desc')
    ->get();

echo "📊 Jumlah audit logs ditemukan: " . $auditLogs->count() . "\n\n";

if ($auditLogs->count() > 0) {
    echo "📝 Detail audit logs:\n";
    foreach ($auditLogs->take(5) as $log) {
        echo "   - ID: {$log->id}\n";
        echo "     Action: {$log->action}\n";
        echo "     Description: {$log->description}\n";
        echo "     User: " . $log->getUserDisplayName() . "\n";
        echo "     Created: " . $log->created_at->format('d/m/Y H:i:s') . "\n";
        echo "     Changes: " . json_encode($log->getFormattedChanges()) . "\n";
        echo "   ---\n";
    }
} else {
    echo "❌ Tidak ada audit logs ditemukan\n";

    // Cek apakah ada audit logs untuk model Karyawan secara umum
    echo "\n🔍 Mengecek audit logs untuk model Karyawan secara umum...\n";
    $allKaryawanLogs = AuditLog::where('auditable_type', $modelClass)->get();
    echo "📊 Total audit logs untuk semua karyawan: " . $allKaryawanLogs->count() . "\n";

    if ($allKaryawanLogs->count() > 0) {
        echo "📝 Beberapa audit logs untuk karyawan lain:\n";
        foreach ($allKaryawanLogs->take(3) as $log) {
            echo "   - Karyawan ID: {$log->auditable_id}, Action: {$log->action}, Time: " . $log->created_at->format('d/m/Y H:i:s') . "\n";
        }
    }
}

// Simulasi AJAX request
echo "\n🔄 Simulasi AJAX request ke getModelAuditLogs...\n";
$requestData = [
    'model_type' => $modelClass,
    'model_id' => $karyawan->id
];

echo "Request data:\n";
echo "   model_type: {$requestData['model_type']}\n";
echo "   model_id: {$requestData['model_id']}\n\n";

// Test query langsung seperti di controller
$testLogs = AuditLog::where('auditable_type', $requestData['model_type'])
    ->where('auditable_id', $requestData['model_id'])
    ->with('user')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "🎯 Hasil query langsung: " . $testLogs->count() . " logs\n";

if ($testLogs->count() > 0) {
    echo "✅ Data ditemukan via query langsung\n";
    $mapped = $testLogs->map(function ($log) {
        return [
            'id' => $log->id,
            'action' => $log->action,
            'description' => $log->description,
            'user_name' => $log->getUserDisplayName(),
            'created_at' => $log->created_at->format('d/m/Y H:i:s'),
            'changes' => $log->getFormattedChanges() ?? []
        ];
    });

    echo "📋 Mapped data:\n";
    foreach ($mapped as $item) {
        echo "   - {$item['action']}: {$item['description']} by {$item['user_name']} at {$item['created_at']}\n";
    }
} else {
    echo "❌ Tidak ada data ditemukan via query langsung\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🔍 KESIMPULAN:\n";

if ($auditLogs->count() > 0 && $testLogs->count() > 0) {
    echo "✅ Data audit log tersedia - masalah kemungkinan di frontend\n";
    echo "🔧 Periksa JavaScript console di browser untuk error\n";
    echo "🔧 Periksa network tab untuk melihat response AJAX\n";
    echo "🔧 Pastikan model_type dikirim dengan benar\n";
} else {
    echo "❌ Data audit log tidak tersedia untuk karyawan ini\n";
    echo "🔧 Pastikan karyawan sudah pernah diupdate\n";
    echo "🔧 Pastikan model menggunakan Auditable trait\n";
}

echo str_repeat("=", 50) . "\n";
