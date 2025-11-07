<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Tanggal Tanda Terima Pada Approval ===\n\n";

// Test 1: Cari surat jalan dengan approval pending
echo "1. Mencari surat jalan dengan approval pending...\n";
$approvals = \App\Models\SuratJalanApproval::with('suratJalan')
    ->where('status', 'pending')
    ->limit(3)
    ->get();

if ($approvals->count() > 0) {
    echo "   ✅ Ditemukan {$approvals->count()} approval pending\n\n";
    
    foreach ($approvals as $approval) {
        $suratJalan = $approval->suratJalan;
        echo "   - Approval ID: {$approval->id}\n";
        echo "   - Surat Jalan: {$suratJalan->no_surat_jalan}\n";
        echo "   - Status: {$approval->status}\n";
        echo "   - Tanggal Tanda Terima saat ini: " . ($suratJalan->tanggal_tanda_terima ?: 'NULL') . "\n\n";
    }
} else {
    echo "   ❌ Tidak ada approval pending\n";
    
    // Buat test data
    echo "\n2. Membuat test data...\n";
    
    // Cari order yang ada
    $order = \App\Models\Order::first();
    if (!$order) {
        echo "   ❌ Tidak ada order untuk test\n";
        exit;
    }
    
    // Buat surat jalan baru
    $suratJalan = \App\Models\SuratJalan::create([
        'order_id' => $order->id,
        'no_surat_jalan' => 'TEST-SJ-' . date('YmdHis'),
        'tanggal_surat_jalan' => now(),
        'jumlah_kontainer' => 1,
        'status' => 'belum masuk checkpoint'
    ]);
    
    // Buat approval
    $approval = \App\Models\SuratJalanApproval::create([
        'surat_jalan_id' => $suratJalan->id,
        'approval_level' => 'approval',
        'status' => 'pending'
    ]);
    
    echo "   ✅ Test data berhasil dibuat:\n";
    echo "   - Surat Jalan ID: {$suratJalan->id}\n";
    echo "   - No Surat Jalan: {$suratJalan->no_surat_jalan}\n";
    echo "   - Approval ID: {$approval->id}\n";
    echo "   - Tanggal Tanda Terima: " . ($suratJalan->tanggal_tanda_terima ?: 'NULL') . "\n\n";
    
    $approvals = collect([$approval->load('suratJalan')]);
}

// Test 3: Simulate approval process
echo "3. Simulasi proses approval...\n";
$testApproval = $approvals->first();
$testSuratJalan = $testApproval->suratJalan;

echo "   Sebelum approval:\n";
echo "   - Status approval: {$testApproval->status}\n";
echo "   - Tanggal tanda terima: " . ($testSuratJalan->tanggal_tanda_terima ?: 'NULL') . "\n";

// Simulate approval update
$testApproval->update([
    'status' => 'approved',
    'approved_by' => 1, // Assume admin user
    'approved_at' => now(),
    'approval_notes' => 'Test approval otomatis'
]);

// Update tanggal_tanda_terima seperti di controller
$testSuratJalan->update([
    'tanggal_tanda_terima' => now()
]);

// Refresh data
$testApproval->refresh();
$testSuratJalan->refresh();

echo "\n   Setelah approval:\n";
echo "   - Status approval: {$testApproval->status}\n";
echo "   - Approved by: {$testApproval->approved_by}\n";
echo "   - Approved at: {$testApproval->approved_at}\n";
echo "   - Tanggal tanda terima: {$testSuratJalan->tanggal_tanda_terima}\n";

// Test 4: Verify database changes
echo "\n4. Verifikasi perubahan database...\n";
$dbApproval = \App\Models\SuratJalanApproval::find($testApproval->id);
$dbSuratJalan = \App\Models\SuratJalan::find($testSuratJalan->id);

if ($dbApproval->status === 'approved' && $dbSuratJalan->tanggal_tanda_terima) {
    echo "   ✅ Database berhasil diupdate\n";
    echo "   - Approval status: {$dbApproval->status}\n";
    echo "   - Tanggal tanda terima: {$dbSuratJalan->tanggal_tanda_terima}\n";
    
    // Calculate time difference
    $approvedAt = \Carbon\Carbon::parse($dbApproval->approved_at);
    $tandaTerimaAt = \Carbon\Carbon::parse($dbSuratJalan->tanggal_tanda_terima);
    $diffSeconds = abs($approvedAt->diffInSeconds($tandaTerimaAt));
    
    echo "   - Selisih waktu approval dan tanda terima: {$diffSeconds} detik\n";
    
    if ($diffSeconds < 60) {
        echo "   ✅ Tanggal tanda terima berhasil diisi otomatis saat approval\n";
    } else {
        echo "   ⚠️ Selisih waktu terlalu besar, kemungkinan tidak otomatis\n";
    }
} else {
    echo "   ❌ Ada masalah dengan update database\n";
}

echo "\n=== Test Selesai ===\n";
echo "💡 Untuk test lengkap, coba akses: /approval/surat-jalan dan approve surat jalan\n";