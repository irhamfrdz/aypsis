<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "Script Update Status Kontainer\n";
echo "========================================\n\n";

echo "Update status kontainer yang tidak memiliki tanggal sewa menjadi 'Tidak Tersedia'\n\n";

// Hitung jumlah kontainer yang akan diupdate
$jumlahData = DB::table('kontainers')
    ->whereNull('tanggal_mulai_sewa')
    ->whereNull('tanggal_selesai_sewa')
    ->where('status', '!=', 'Tidak Tersedia')
    ->count();

echo "Jumlah kontainer yang akan diupdate: {$jumlahData}\n";

if ($jumlahData === 0) {
    echo "\nTidak ada kontainer yang perlu diupdate.\n";
    exit(0);
}

// Tampilkan detail kontainer yang akan diupdate
echo "\nDetail kontainer yang akan diupdate:\n";
echo "========================================\n";

$dataYangAkanDiupdate = DB::table('kontainers')
    ->whereNull('tanggal_mulai_sewa')
    ->whereNull('tanggal_selesai_sewa')
    ->where('status', '!=', 'Tidak Tersedia')
    ->select('id', 'nomor_seri_gabungan', 'ukuran', 'vendor', 'status')
    ->limit(10) // Tampilkan hanya 10 pertama untuk preview
    ->get();

foreach ($dataYangAkanDiupdate as $kontainer) {
    echo "ID: {$kontainer->id}\n";
    echo "  Nomor: {$kontainer->nomor_seri_gabungan}\n";
    echo "  Ukuran: {$kontainer->ukuran}\n";
    echo "  Vendor: " . ($kontainer->vendor ?: '-') . "\n";
    echo "  Status Saat Ini: {$kontainer->status}\n";
    echo "  Status Baru: Tidak Tersedia\n";
    echo "----------------------------------------\n";
}

if ($jumlahData > 10) {
    echo "\n... dan " . ($jumlahData - 10) . " kontainer lainnya\n";
}

// Konfirmasi update
echo "\nApakah Anda yakin ingin mengupdate {$jumlahData} kontainer ini? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes') {
    echo "\nUpdate dibatalkan.\n";
    exit(0);
}

// Update data
echo "\nMengupdate status kontainer...\n";

try {
    $updated = DB::table('kontainers')
        ->whereNull('tanggal_mulai_sewa')
        ->whereNull('tanggal_selesai_sewa')
        ->where('status', '!=', 'Tidak Tersedia')
        ->update(['status' => 'Tidak Tersedia']);
    
    echo "\n✓ Berhasil mengupdate {$updated} kontainer.\n";
    
    // Tampilkan ringkasan status setelah update
    echo "\nRingkasan Status Kontainer Setelah Update:\n";
    echo "========================================\n";
    
    $statusSummary = DB::table('kontainers')
        ->select('status', DB::raw('COUNT(*) as total'))
        ->groupBy('status')
        ->get();
    
    foreach ($statusSummary as $row) {
        echo "Status '{$row->status}': {$row->total} kontainer\n";
    }
    
} catch (\Exception $e) {
    echo "\n✗ Error saat mengupdate data: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nSelesai!\n";
