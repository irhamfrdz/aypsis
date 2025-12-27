<?php

/**
 * Script untuk memperbaiki size_kontainer yang kosong pada tabel naik_kapal
 * Mengkonversi dari ukuran_kontainer yang sudah ada
 * 
 * Format konversi:
 * "20 Feet" -> "20ft"
 * "40 Feet" -> "40ft"
 * "40HC Feet" -> "40hc"
 * "45 Feet" -> "45ft"
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "================================================\n";
echo "Script Perbaikan Size Kontainer - Halaman OB\n";
echo "================================================\n\n";

try {
    // Ambil semua record naik_kapal yang size_kontainer nya NULL tapi ukuran_kontainer ada
    $records = DB::table('naik_kapal')
        ->whereNull('size_kontainer')
        ->whereNotNull('ukuran_kontainer')
        ->where('ukuran_kontainer', '!=', '')
        ->get(['id', 'nomor_kontainer', 'ukuran_kontainer']);
    
    $totalRecords = $records->count();
    
    if ($totalRecords === 0) {
        echo "✓ Tidak ada data yang perlu diperbaiki.\n";
        echo "  Semua record naik_kapal sudah memiliki size_kontainer.\n\n";
        exit(0);
    }
    
    echo "Ditemukan {$totalRecords} record yang perlu diperbaiki.\n\n";
    
    // Konfirmasi sebelum melanjutkan
    echo "Data yang akan diperbaiki:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s %-20s %-20s %-20s\n", "No", "Nomor Kontainer", "Ukuran Lama", "Size Baru");
    echo str_repeat("-", 80) . "\n";
    
    $previewCount = 0;
    foreach ($records as $record) {
        $sizeKontainer = convertToSizeKontainer($record->ukuran_kontainer);
        printf("%-5s %-20s %-20s %-20s\n", 
            ++$previewCount, 
            $record->nomor_kontainer ?: '-', 
            $record->ukuran_kontainer, 
            $sizeKontainer
        );
        
        // Batasi preview hanya 10 baris
        if ($previewCount >= 10 && $totalRecords > 10) {
            echo "... dan " . ($totalRecords - 10) . " record lainnya\n";
            break;
        }
    }
    echo str_repeat("-", 80) . "\n\n";
    
    echo "Lanjutkan update? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($line) !== 'y') {
        echo "\nUpdate dibatalkan oleh user.\n\n";
        exit(0);
    }
    
    echo "\nMemulai update...\n\n";
    
    $successCount = 0;
    $failedCount = 0;
    $errors = [];
    
    DB::beginTransaction();
    
    try {
        foreach ($records as $record) {
            $sizeKontainer = convertToSizeKontainer($record->ukuran_kontainer);
            
            if ($sizeKontainer) {
                $updated = DB::table('naik_kapal')
                    ->where('id', $record->id)
                    ->update(['size_kontainer' => $sizeKontainer]);
                
                if ($updated) {
                    $successCount++;
                    echo "✓ [{$successCount}/{$totalRecords}] Updated: {$record->nomor_kontainer} -> {$sizeKontainer}\n";
                } else {
                    $failedCount++;
                    $errors[] = "Failed to update ID {$record->id} ({$record->nomor_kontainer})";
                }
            } else {
                $failedCount++;
                $errors[] = "Cannot convert ukuran_kontainer '{$record->ukuran_kontainer}' for ID {$record->id}";
            }
        }
        
        DB::commit();
        
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "Update selesai!\n";
        echo str_repeat("=", 80) . "\n";
        echo "✓ Berhasil diperbaiki: {$successCount} record\n";
        
        if ($failedCount > 0) {
            echo "✗ Gagal: {$failedCount} record\n\n";
            echo "Detail error:\n";
            foreach ($errors as $error) {
                echo "  - {$error}\n";
            }
        }
        
        echo "\n";
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
    
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}

/**
 * Konversi dari format ukuran_kontainer ke size_kontainer
 * 
 * @param string $ukuranKontainer Format: "20 Feet", "40 Feet", "40HC Feet", dll
 * @return string|null Format: "20ft", "40ft", "40hc", "45ft"
 */
function convertToSizeKontainer($ukuranKontainer)
{
    if (empty($ukuranKontainer)) {
        return null;
    }
    
    // Lowercase dan trim
    $ukuran = strtolower(trim($ukuranKontainer));
    
    // Hilangkan kata "feet" atau "ft"
    $ukuran = str_replace(['feet', 'foot'], '', $ukuran);
    $ukuran = trim($ukuran);
    
    // Jika sudah ada suffix 'hc', pertahankan
    if (strpos($ukuran, 'hc') !== false) {
        // Format: "40hc" atau "40 hc"
        $ukuran = str_replace(' ', '', $ukuran);
        return $ukuran; // "40hc"
    }
    
    // Jika sudah ada suffix 'ft', pertahankan
    if (strpos($ukuran, 'ft') !== false) {
        // Format: "20ft" atau "20 ft"
        $ukuran = str_replace(' ', '', $ukuran);
        return $ukuran; // "20ft"
    }
    
    // Jika hanya angka, tambahkan 'ft'
    $ukuran = trim($ukuran);
    if (is_numeric($ukuran)) {
        return $ukuran . 'ft'; // "20" -> "20ft"
    }
    
    // Default: tambahkan 'ft' jika belum ada suffix
    return $ukuran . 'ft';
}

echo "Script selesai.\n\n";
