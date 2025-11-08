<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== SCRIPT PERBAIKAN DATA PERIODE KONTAINER SEWA ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Cari data kontainer RXTU4540180 yang bermasalah
echo "1. Mencari data kontainer RXTU4540180 dengan ketidaksesuaian tanggal dan masa...\n";

$problemRecords = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'RXTU4540180')
    ->whereNotNull('tanggal_awal')
    ->whereNotNull('tanggal_akhir')
    ->whereNotNull('masa')
    ->get()
    ->filter(function($record) {
        // Periksa apakah masa tidak sesuai dengan tanggal_awal dan tanggal_akhir
        try {
            $tanggalAwal = Carbon::parse($record->tanggal_awal);
            $tanggalAkhir = Carbon::parse($record->tanggal_akhir);
            
            // Parse masa untuk mendapatkan tanggal
            $masaParts = explode(' - ', $record->masa);
            if (count($masaParts) != 2) return false;
            
            $masaAwal = Carbon::parse($masaParts[0]);
            $masaAkhir = Carbon::parse($masaParts[1]);
            
            // Periksa apakah tanggal tidak cocok
            return !$tanggalAwal->isSameDay($masaAwal) || !$tanggalAkhir->isSameDay($masaAkhir);
            
        } catch (Exception $e) {
            return false;
        }
    });

echo "Ditemukan " . $problemRecords->count() . " record kontainer RXTU4540180 dengan masalah periode.\n\n";

if ($problemRecords->count() == 0) {
    echo "Tidak ada data kontainer RXTU4540180 yang perlu diperbaiki.\n";
    
    // Tampilkan semua data RXTU4540180 untuk referensi
    $allRXTU = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'RXTU4540180')->get();
    if ($allRXTU->count() > 0) {
        echo "\nData kontainer RXTU4540180 yang ditemukan:\n";
        echo "ID\tTanggal Awal\tTanggal Akhir\tMasa\n";
        echo "----\t-----------\t-----------\t----\n";
        foreach ($allRXTU as $record) {
            echo sprintf(
                "%d\t%s\t%s\t%s\n",
                $record->id,
                $record->tanggal_awal ? $record->tanggal_awal->format('d-M-Y') : 'NULL',
                $record->tanggal_akhir ? $record->tanggal_akhir->format('d-M-Y') : 'NULL',
                $record->masa ?? 'NULL'
            );
        }
    } else {
        echo "Kontainer RXTU4540180 tidak ditemukan dalam database.\n";
    }
    exit;
}

// 2. Tampilkan data kontainer RXTU4540180 yang bermasalah
echo "2. Data kontainer RXTU4540180 yang bermasalah:\n";
echo "ID\tVendor\tKontainer\tTanggal Awal\tTanggal Akhir\tMasa (Bermasalah)\n";
echo "----\t------\t---------\t-----------\t-----------\t-------------------\n";

$problemRecords->each(function($record) {
    echo sprintf(
        "%d\t%s\t%s\t%s\t%s\t%s\n",
        $record->id,
        substr($record->vendor, 0, 10),
        $record->nomor_kontainer,
        $record->tanggal_awal->format('d-M-Y'),
        $record->tanggal_akhir->format('d-M-Y'),
        $record->masa
    );
});

echo "\n";

// 3. Konfirmasi untuk melanjutkan
echo "3. Apakah Anda ingin melanjutkan perbaikan? (y/n): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) != 'y' && strtolower($confirmation) != 'yes') {
    echo "Perbaikan dibatalkan.\n";
    exit;
}

// 4. Mulai perbaikan
echo "\n4. Memulai perbaikan data...\n";

$fixedCount = 0;
$errorCount = 0;

foreach ($problemRecords as $record) {
    try {
        $tanggalAwal = Carbon::parse($record->tanggal_awal);
        $tanggalAkhir = Carbon::parse($record->tanggal_akhir);
        
        // Format masa yang benar berdasarkan tanggal_awal dan tanggal_akhir
        $masaBaru = $tanggalAwal->format('d F Y') . ' - ' . $tanggalAkhir->format('d F Y');
        
        // Update record
        $record->update([
            'masa' => $masaBaru
        ]);
        
        $fixedCount++;
        
        echo "✓ Fixed ID {$record->id}: {$record->nomor_kontainer} - {$masaBaru}\n";
        
    } catch (Exception $e) {
        $errorCount++;
        echo "✗ Error ID {$record->id}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== HASIL PERBAIKAN ===\n";
echo "Total data diperiksa: " . $problemRecords->count() . "\n";
echo "Berhasil diperbaiki: {$fixedCount}\n";
echo "Error: {$errorCount}\n";

// 5. Verifikasi hasil perbaikan untuk kontainer RXTU4540180
echo "\n5. Verifikasi hasil perbaikan untuk kontainer RXTU4540180...\n";

$stillProblemRecords = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'RXTU4540180')
    ->whereNotNull('tanggal_awal')
    ->whereNotNull('tanggal_akhir')
    ->whereNotNull('masa')
    ->get()
    ->filter(function($record) {
        try {
            $tanggalAwal = Carbon::parse($record->tanggal_awal);
            $tanggalAkhir = Carbon::parse($record->tanggal_akhir);
            
            $masaParts = explode(' - ', $record->masa);
            if (count($masaParts) != 2) return false;
            
            $masaAwal = Carbon::parse($masaParts[0]);
            $masaAkhir = Carbon::parse($masaParts[1]);
            
            return !$tanggalAwal->isSameDay($masaAwal) || !$tanggalAkhir->isSameDay($masaAkhir);
            
        } catch (Exception $e) {
            return false;
        }
    });

if ($stillProblemRecords->count() > 0) {
    echo "⚠️ Masih ada " . $stillProblemRecords->count() . " record RXTU4540180 dengan masalah!\n";
    echo "Silakan periksa manual:\n";
    
    $stillProblemRecords->each(function($record) {
        echo "ID {$record->id}: {$record->nomor_kontainer} - {$record->masa}\n";
    });
} else {
    echo "✅ Data periode kontainer RXTU4540180 sudah konsisten!\n";
    
    // Tampilkan hasil akhir
    $finalData = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'RXTU4540180')->get();
    if ($finalData->count() > 0) {
        echo "\nData final kontainer RXTU4540180:\n";
        echo "ID\tTanggal Awal\tTanggal Akhir\tMasa\n";
        echo "----\t-----------\t-----------\t----\n";
        foreach ($finalData as $record) {
            echo sprintf(
                "%d\t%s\t%s\t%s\n",
                $record->id,
                $record->tanggal_awal ? $record->tanggal_awal->format('d-M-Y') : 'NULL',
                $record->tanggal_akhir ? $record->tanggal_akhir->format('d-M-Y') : 'NULL',
                $record->masa ?? 'NULL'
            );
        }
    }
}

echo "\n=== SCRIPT SELESAI ===\n";
echo "Waktu selesai: " . date('Y-m-d H:i:s') . "\n";