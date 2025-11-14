<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "Script Hapus Data BL Kapal Tertentu\n";
echo "========================================\n\n";

// Kapal yang akan dihapus
$namaKapal = [
    'KM. SEKAR PERMATA',
    'KM. SUMBER ABADI 17'
];

echo "Kapal yang akan dihapus:\n";
foreach ($namaKapal as $kapal) {
    echo "- {$kapal}\n";
}
echo "\n";

// Hitung jumlah data yang akan dihapus
$jumlahData = DB::table('bls')
    ->whereIn('nama_kapal', $namaKapal)
    ->count();

echo "Jumlah data BL yang akan dihapus: {$jumlahData}\n";

if ($jumlahData === 0) {
    echo "\nTidak ada data BL untuk kapal tersebut.\n";
    exit(0);
}

// Tampilkan detail data yang akan dihapus
echo "\nDetail data BL yang akan dihapus:\n";
echo "========================================\n";

$dataYangAkanDihapus = DB::table('bls')
    ->whereIn('nama_kapal', $namaKapal)
    ->select('id', 'nomor_bl', 'nomor_kontainer', 'nama_kapal', 'no_voyage', 'nama_barang')
    ->get();

foreach ($dataYangAkanDihapus as $bl) {
    echo "ID: {$bl->id}\n";
    echo "  Nomor BL: " . ($bl->nomor_bl ?: '-') . "\n";
    echo "  Nomor Kontainer: {$bl->nomor_kontainer}\n";
    echo "  Kapal: {$bl->nama_kapal}\n";
    echo "  Voyage: {$bl->no_voyage}\n";
    echo "  Nama Barang: " . ($bl->nama_barang ?: '-') . "\n";
    echo "----------------------------------------\n";
}

// Konfirmasi penghapusan
echo "\nApakah Anda yakin ingin menghapus {$jumlahData} data BL ini? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes') {
    echo "\nPenghapusan dibatalkan.\n";
    exit(0);
}

// Hapus data
echo "\nMenghapus data BL...\n";

try {
    $deleted = DB::table('bls')
        ->whereIn('nama_kapal', $namaKapal)
        ->delete();
    
    echo "\n✓ Berhasil menghapus {$deleted} data BL.\n";
    
    echo "\nRingkasan:\n";
    echo "- Total data dihapus: {$deleted}\n";
    echo "- Kapal: " . implode(', ', $namaKapal) . "\n";
    
} catch (\Exception $e) {
    echo "\n✗ Error saat menghapus data: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nSelesai!\n";
