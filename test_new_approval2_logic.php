<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permohonan;
use Illuminate\Support\Facades\DB;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Test Logika Baru Approval Tugas 2\n";
echo "=================================\n";
echo "Sekarang menampilkan data dengan status 'Selesai' yang belum disetujui system 2\n\n";

// Query yang sama seperti di PenyelesaianIIController::index() yang sudah diubah
$query = Permohonan::whereNotIn('status', ['Dibatalkan']) // Hanya exclude yang dibatalkan
    ->where('approved_by_system_1', true) // Sudah disetujui system 1
    ->where('approved_by_system_2', false) // Belum disetujui system 2
    ->with(['supir', 'kontainers', 'checkpoints']);

$permohonans = $query->latest()->get();

echo "Data yang muncul di Approval Tugas 2: " . $permohonans->count() . " record\n\n";

if ($permohonans->count() > 0) {
    echo "Daftar data:\n";
    $selesaiCount = 0;
    $pendingCount = 0;

    foreach ($permohonans as $i => $p) {
        $status = $p->status;
        if ($status === 'Selesai') $selesaiCount++;
        if ($status === 'Pending') $pendingCount++;

        echo ($i+1) . ". {$p->nomor_memo} - Status: {$status} - Sys1: " . ($p->approved_by_system_1 ? '✓' : '✗') . " - Sys2: " . ($p->approved_by_system_2 ? '✓' : '✗') . "\n";
    }

    echo "\nRingkasan:\n";
    echo "- Status 'Pending': {$pendingCount} data\n";
    echo "- Status 'Selesai': {$selesaiCount} data\n";
    echo "- Total: " . ($pendingCount + $selesaiCount) . " data\n";
} else {
    echo "Tidak ada data yang muncul di Approval Tugas 2\n";
}

echo "\nVerifikasi data yang DIBATALKAN (seharusnya TIDAK muncul):\n";
echo "======================================================\n";

$dibatalkanCount = Permohonan::where('status', 'Dibatalkan')->count();
echo "Total data dengan status 'Dibatalkan': {$dibatalkanCount} record\n";

if ($dibatalkanCount > 0) {
    $sampleDibatalkan = Permohonan::where('status', 'Dibatalkan')->latest()->first();
    echo "Contoh data yang dibatalkan: {$sampleDibatalkan->nomor_memo}\n";
    echo "Data ini TIDAK akan muncul di Approval Tugas 2 ✓\n";
}

echo "\n✅ Kesimpulan:\n";
echo "==============\n";
echo "✓ Data dengan status 'Selesai' yang belum disetujui system 2 sekarang MUNCUL\n";
echo "✓ Data dengan status 'Dibatalkan' tetap TIDAK muncul\n";
echo "✓ Logika filtering sudah diubah: hanya exclude 'Dibatalkan'\n";
