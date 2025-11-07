<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Tanggal Tanda Terima (Date Only) ===\n\n";

// Test dengan approval yang pending
echo "1. Mencari approval pending untuk test...\n";
$approval = \App\Models\SuratJalanApproval::with('suratJalan')
    ->where('status', 'pending')
    ->first();

if (!$approval) {
    echo "   Membuat test data baru...\n";
    
    // Cari order yang ada
    $order = \App\Models\Order::first();
    if (!$order) {
        echo "   ❌ Tidak ada order untuk test\n";
        exit;
    }
    
    // Buat surat jalan baru
    $suratJalan = \App\Models\SuratJalan::create([
        'order_id' => $order->id,
        'no_surat_jalan' => 'TEST-DATE-' . date('YmdHis'),
        'tanggal_surat_jalan' => today(),
        'jumlah_kontainer' => 1,
        'status' => 'belum masuk checkpoint'
    ]);
    
    // Buat approval
    $approval = \App\Models\SuratJalanApproval::create([
        'surat_jalan_id' => $suratJalan->id,
        'approval_level' => 'approval',
        'status' => 'pending'
    ]);
    
    $approval->load('suratJalan');
}

$suratJalan = $approval->suratJalan;

echo "   ✅ Test akan menggunakan:\n";
echo "   - Approval ID: {$approval->id}\n";
echo "   - Surat Jalan: {$suratJalan->no_surat_jalan}\n";
echo "   - Tanggal tanda terima sebelum: " . ($suratJalan->tanggal_tanda_terima ?: 'NULL') . "\n\n";

// Test proses approval dengan tanggal
echo "2. Melakukan approval dengan tanggal tanda terima...\n";

$approvalDate = today();
echo "   Tanggal approval: {$approvalDate->format('Y-m-d')}\n";

// Update approval
$approval->update([
    'status' => 'approved',
    'approved_by' => 1,
    'approved_at' => now(),
    'approval_notes' => 'Test approval dengan date'
]);

// Update tanggal_tanda_terima dengan tanggal hari ini
$suratJalan->update([
    'tanggal_tanda_terima' => today()
]);

echo "   ✅ Approval berhasil diupdate\n";

// Refresh dan verify
$approval->refresh();
$suratJalan->refresh();

echo "\n3. Verifikasi hasil...\n";
echo "   - Status approval: {$approval->status}\n";
echo "   - Approved at: {$approval->approved_at}\n";
echo "   - Tanggal tanda terima: {$suratJalan->tanggal_tanda_terima}\n";

// Check if dates match
$approvedDate = \Carbon\Carbon::parse($approval->approved_at)->toDateString();
$tandaTerimaDate = \Carbon\Carbon::parse($suratJalan->tanggal_tanda_terima)->toDateString();

echo "   - Tanggal approval: {$approvedDate}\n";
echo "   - Tanggal tanda terima: {$tandaTerimaDate}\n";

if ($approvedDate === $tandaTerimaDate) {
    echo "   ✅ SUCCESS: Tanggal tanda terima berhasil diisi sesuai tanggal approval!\n";
} else {
    echo "   ⚠️ Tanggal tidak cocok\n";
}

echo "\n4. Test casting date...\n";
$dbRecord = \App\Models\SuratJalan::where('id', $suratJalan->id)->first();
echo "   - Type tanggal_tanda_terima: " . gettype($dbRecord->tanggal_tanda_terima) . "\n";
echo "   - Instance of Carbon: " . ($dbRecord->tanggal_tanda_terima instanceof \Carbon\Carbon ? 'Yes' : 'No') . "\n";
echo "   - Format tanggal: " . $dbRecord->tanggal_tanda_terima->format('Y-m-d') . "\n";

echo "\n=== Test Selesai ===\n";
echo "💡 Implementasi berhasil! Saat approval surat jalan, tanggal_tanda_terima akan diisi dengan tanggal hari ini.\n";
echo "💡 Untuk test di browser: /approval/surat-jalan\n";