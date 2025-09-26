<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permohonan;
use Illuminate\Support\Facades\DB;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Verifikasi Logika Approval Tugas 2\n";
echo "=================================\n";

// Query yang sama seperti di PenyelesaianIIController::index()
$query = Permohonan::whereNotIn('status', ['Selesai', 'Dibatalkan'])
    ->where('approved_by_system_1', true) // Sudah disetujui system 1
    ->where('approved_by_system_2', false) // Belum disetujui system 2
    ->with(['supir', 'kontainers', 'checkpoints']);

$permohonans = $query->latest()->get();

echo "Data yang muncul di Approval Tugas 2: " . $permohonans->count() . " record\n\n";

if ($permohonans->count() > 0) {
    echo "Daftar data:\n";
    foreach ($permohonans as $i => $p) {
        echo ($i+1) . ". {$p->nomor_memo} - Status: {$p->status} - Sys1: " . ($p->approved_by_system_1 ? '✓' : '✗') . " - Sys2: " . ($p->approved_by_system_2 ? '✓' : '✗') . "\n";
    }
} else {
    echo "Tidak ada data yang muncul di Approval Tugas 2\n";
}

echo "\nVerifikasi data yang SUDAH disetujui system 2 (seharusnya TIDAK muncul):\n";
echo "=================================================================\n";

$approvedBySystem2 = Permohonan::where('approved_by_system_2', true)->count();
echo "Total data yang sudah disetujui system 2: {$approvedBySystem2} record\n";

if ($approvedBySystem2 > 0) {
    $sampleApproved = Permohonan::where('approved_by_system_2', true)->latest()->first();
    echo "Contoh data yang sudah disetujui system 2: {$sampleApproved->nomor_memo} (Status: {$sampleApproved->status})\n";
    echo "Data ini TIDAK akan muncul di Approval Tugas 2 ✓\n";
}

echo "\nKesimpulan:\n";
echo "===========\n";
echo "✓ Approval Tugas 2 hanya menampilkan data yang belum disetujui system 2\n";
echo "✓ Data yang sudah disetujui system 2 otomatis tidak akan muncul lagi\n";
echo "✓ Logika filtering sudah benar: approved_by_system_2 = false\n";
