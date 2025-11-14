<?php

/**
 * Script untuk menghapus tanggal mulai sewa dan tanggal akhir sewa
 * dari semua kontainer di tabel master_kontainer
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "Script Hapus Tanggal Sewa Kontainer\n";
echo "==============================================\n\n";

try {
    // Hitung jumlah kontainer yang memiliki tanggal sewa
    $kontainerWithDates = DB::table('kontainers')
        ->where(function($query) {
            $query->whereNotNull('tanggal_mulai_sewa')
                  ->orWhereNotNull('tanggal_selesai_sewa');
        })
        ->count();

    if ($kontainerWithDates === 0) {
        echo "✓ Tidak ada kontainer dengan tanggal sewa yang perlu dihapus.\n";
        exit(0);
    }

    echo "Ditemukan {$kontainerWithDates} kontainer yang memiliki tanggal sewa.\n";
    echo "Apakah Anda yakin ingin menghapus semua tanggal sewa? (yes/no): ";
    
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);

    if ($confirmation !== 'yes') {
        echo "\n❌ Operasi dibatalkan.\n";
        exit(0);
    }

    echo "\n🔄 Memulai proses penghapusan tanggal sewa...\n\n";

    // Hapus tanggal sewa dari semua kontainer
    $updated = DB::table('kontainers')
        ->where(function($query) {
            $query->whereNotNull('tanggal_mulai_sewa')
                  ->orWhereNotNull('tanggal_selesai_sewa');
        })
        ->update([
            'tanggal_mulai_sewa' => null,
            'tanggal_selesai_sewa' => null,
            'updated_at' => now()
        ]);

    echo "✓ Berhasil menghapus tanggal sewa dari {$updated} kontainer.\n\n";

    // Tampilkan ringkasan
    $totalKontainer = DB::table('kontainers')->count();
    $tersedia = DB::table('kontainers')->where('status', 'Tersedia')->count();
    $disewa = DB::table('kontainers')->where('status', 'Disewa')->count();

    echo "==============================================\n";
    echo "RINGKASAN\n";
    echo "==============================================\n";
    echo "Total kontainer       : {$totalKontainer}\n";
    echo "Kontainer tersedia    : {$tersedia}\n";
    echo "Kontainer disewa      : {$disewa}\n";
    echo "Tanggal sewa dihapus  : {$updated}\n";
    echo "==============================================\n\n";

    echo "✓ Proses selesai!\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
