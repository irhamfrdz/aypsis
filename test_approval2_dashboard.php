<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permohonan;
use Illuminate\Support\Facades\DB;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Approval Tugas 2 Dashboard - Data yang seharusnya muncul:\n";
echo "===============================================================\n";

// Simulasi query yang sama seperti di PenyelesaianIIController::index()
$query = Permohonan::whereNotIn('status', ['Selesai', 'Dibatalkan'])
    ->where('approved_by_system_1', true) // Sudah disetujui system 1
    ->where('approved_by_system_2', false) // Belum disetujui system 2
    ->with(['supir', 'kontainers', 'checkpoints']);

$permohonans = $query->latest()->get();

echo "Total data yang muncul di Approval Tugas 2: " . $permohonans->count() . "\n\n";

if ($permohonans->count() > 0) {
    echo "Data yang akan muncul:\n";
    foreach ($permohonans as $permohonan) {
        echo "- {$permohonan->nomor_permohonan} - Status: {$permohonan->status} - Sys1: " . ($permohonan->approved_by_system_1 ? 'true' : 'false') . " - Sys2: " . ($permohonan->approved_by_system_2 ? 'true' : 'false') . "\n";
    }
} else {
    echo "Tidak ada data yang muncul di Approval Tugas 2.\n";
}

echo "\nTest selesai!\n";
