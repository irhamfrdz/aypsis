<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Illuminate\Support\Facades\DB;

echo "=== PERBAIKAN DPP CALCULATION BUG ===\n";

// Step 1: Identifikasi semua record yang bermasalah  
echo "Step 1: Mencari semua record dengan masalah DPP...\n";

// Cari record yang DPP-nya jauh lebih besar dari master pricelist
$problematicRecords = [];
$allRecords = DaftarTagihanKontainerSewa::all();

foreach ($allRecords as $record) {
    $masterPricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $record->size)
        ->where('vendor', $record->vendor)
        ->first();
    
    if ($masterPricelist && strtolower($masterPricelist->tarif) === 'bulanan') {
        // Untuk tarif bulanan, DPP seharusnya sama dengan harga master pricelist
        $expectedDpp = $masterPricelist->harga;
        $actualDpp = floatval($record->dpp);
        
        // Jika selisih lebih dari 10%, berarti ada masalah
        if (abs($actualDpp - $expectedDpp) > ($expectedDpp * 0.1)) {
            $problematicRecords[] = [
                'record' => $record,
                'expected_dpp' => $expectedDpp,
                'actual_dpp' => $actualDpp,
                'pricelist' => $masterPricelist
            ];
        }
    }
}

echo "Ditemukan " . count($problematicRecords) . " records dengan DPP yang salah\n\n";

if (count($problematicRecords) == 0) {
    echo "Tidak ada record yang perlu diperbaiki.\n";
    exit;
}

// Step 2: Tampilkan preview perbaikan
echo "=== PREVIEW PERBAIKAN ===\n";
$totalSelisih = 0;

foreach (array_slice($problematicRecords, 0, 10) as $item) {
    $record = $item['record'];
    $expectedDpp = $item['expected_dpp'];
    $actualDpp = $item['actual_dpp'];
    
    $expectedPpn = $expectedDpp * 0.11;
    $expectedPph = $expectedDpp * 0.02;
    $expectedGrandTotal = $expectedDpp + $expectedPpn - $expectedPph;
    
    $selisihDpp = $actualDpp - $expectedDpp;
    $selisihGrandTotal = floatval($record->grand_total) - $expectedGrandTotal;
    $totalSelisih += $selisihGrandTotal;
    
    echo "Container: {$record->nomor_kontainer} ({$record->vendor} {$record->size}ft) - Periode: {$record->periode}\n";
    echo "  Current DPP: Rp " . number_format($actualDpp, 0, ',', '.') . "\n";
    echo "  Correct DPP: Rp " . number_format($expectedDpp, 0, ',', '.') . "\n";
    echo "  Selisih: Rp " . number_format($selisihDpp, 0, ',', '.') . "\n";
    echo "  Grand Total Selisih: Rp " . number_format($selisihGrandTotal, 0, ',', '.') . "\n\n";
}

if (count($problematicRecords) > 10) {
    echo "... dan " . (count($problematicRecords) - 10) . " record lainnya\n\n";
}

$avgSelisih = count($problematicRecords) > 0 ? $totalSelisih / min(10, count($problematicRecords)) : 0;
echo "Total estimasi selisih Grand Total: Rp " . number_format($avgSelisih * count($problematicRecords), 0, ',', '.') . "\n\n";

// Step 3: Konfirmasi untuk melakukan perbaikan
echo "Apakah Anda ingin melanjutkan perbaikan? (y/n): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if (strtolower($input) !== 'y') {
    echo "Perbaikan dibatalkan.\n";
    exit;
}

// Step 4: Lakukan perbaikan
echo "\n=== MEMULAI PERBAIKAN ===\n";

$fixedCount = 0;
$errorCount = 0;

DB::beginTransaction();

try {
    foreach ($problematicRecords as $item) {
        $record = $item['record'];
        $correctDpp = $item['expected_dpp'];
        $pricelist = $item['pricelist'];
        
        // Hitung ulang PPN dan PPh berdasarkan DPP yang benar
        $correctPpn = $correctDpp * 0.11;
        $correctPph = $correctDpp * 0.02;
        $correctGrandTotal = $correctDpp + $correctPpn - $correctPph;
        
        // Update record
        $record->dpp = $correctDpp;
        $record->ppn = $correctPpn;
        $record->pph = $correctPph;
        $record->grand_total = $correctGrandTotal;
        
        if ($record->save()) {
            $fixedCount++;
            echo "✓ Fixed: {$record->nomor_kontainer} - DPP: Rp " . number_format($correctDpp, 0, ',', '.') . "\n";
        } else {
            $errorCount++;
            echo "✗ Error fixing: {$record->nomor_kontainer}\n";
        }
    }
    
    DB::commit();
    
    echo "\n=== PERBAIKAN SELESAI ===\n";
    echo "Records berhasil diperbaiki: {$fixedCount}\n";
    echo "Records yang error: {$errorCount}\n";
    echo "Total records diproses: " . count($problematicRecords) . "\n";
    
    if ($fixedCount > 0) {
        echo "\nPerbaikan berhasil disimpan ke database.\n";
        echo "DPP untuk semua tarif bulanan telah disesuaikan dengan master pricelist.\n";
    }
    
} catch (Exception $e) {
    DB::rollback();
    echo "\nError terjadi selama perbaikan: " . $e->getMessage() . "\n";
    echo "Semua perubahan telah di-rollback.\n";
}

echo "\n=== VERIFIKASI HASIL ===\n";

// Verifikasi dengan mengecek TEXU7210230 lagi
$texuRecord = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'TEXU7210230')->first();
if ($texuRecord) {
    echo "Verifikasi TEXU7210230:\n";
    echo "  DPP saat ini: Rp " . number_format(floatval($texuRecord->dpp), 0, ',', '.') . "\n";
    echo "  PPN: Rp " . number_format(floatval($texuRecord->ppn), 0, ',', '.') . "\n";
    echo "  PPh: Rp " . number_format(floatval($texuRecord->pph), 0, ',', '.') . "\n";
    echo "  Grand Total: Rp " . number_format(floatval($texuRecord->grand_total), 0, ',', '.') . "\n";
}

echo "\nScript selesai.\n";

// Step 4: Lakukan perbaikan
echo "\n=== MELAKUKAN PERBAIKAN ===\n";
$correctedCount = 0;
$errors = [];

DB::beginTransaction();

try {
    foreach ($incorrectRecords as $record) {
        $masterPricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $record->size)
            ->where('vendor', $record->vendor)
            ->first();
        
        if ($masterPricelist && strtolower($masterPricelist->tarif) === 'bulanan') {
            // Hitung ulang semua nilai financial
            $correctDpp = $masterPricelist->harga;
            $correctPpn = round($correctDpp * 0.11, 2);
            $correctPph = round($correctDpp * 0.02, 2);
            $correctGrandTotal = round($correctDpp + $correctPpn - $correctPph, 2);
            
            // Update record
            $updated = $record->update([
                'dpp' => $correctDpp,
                'ppn' => $correctPpn,
                'pph' => $correctPph,
                'grand_total' => $correctGrandTotal,
                // Keep existing adjustment and dpp_nilai_lain
                'dpp_nilai_lain' => round($correctDpp * 11 / 12, 2), // Update this too
            ]);
            
            if ($updated) {
                $correctedCount++;
                echo "✅ {$record->nomor_kontainer}: DPP " . number_format($record->dpp, 0, ',', '.') . " → " . number_format($correctDpp, 0, ',', '.') . "\n";
            } else {
                $errors[] = "Gagal update {$record->nomor_kontainer}";
            }
        }
    }
    
    if (empty($errors)) {
        DB::commit();
        echo "\n🎉 PERBAIKAN SELESAI!\n";
        echo "Berhasil memperbaiki $correctedCount record\n";
    } else {
        DB::rollback();
        echo "\n❌ PERBAIKAN GAGAL!\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }
    
} catch (Exception $e) {
    DB::rollback();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}