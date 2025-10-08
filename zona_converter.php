<?php
/**
 * Script untuk memperbaiki format CSV Zona menjadi format yang kompatibel dengan sistem import
 */

echo "=== ZONA CSV CONVERTER ===\n";

$inputFile = 'Zona.csv';
$outputFile = 'Zona_SIAP_IMPORT_TARIF_BENAR.csv';

if (!file_exists($inputFile)) {
    echo "ERROR: File $inputFile tidak ditemukan!\n";
    exit(1);
}

echo "Membaca file: $inputFile\n";

// Baca file CSV dengan delimiter semicolon
$handle = fopen($inputFile, 'r');
if (!$handle) {
    echo "ERROR: Tidak dapat membuka file $inputFile\n";
    exit(1);
}

// Skip header
$header = fgetcsv($handle, 1000, ';');
echo "Header asli: " . implode(' | ', $header) . "\n\n";

// Siapkan file output
$outputHandle = fopen($outputFile, 'w');
if (!$outputHandle) {
    echo "ERROR: Tidak dapat membuat file $outputFile\n";
    fclose($handle);
    exit(1);
}

// Tulis header yang benar (dengan kolom adjustment dan periode)
$newHeader = ['vendor', 'nomor_kontainer', 'size', 'tanggal_awal', 'tanggal_akhir', 'tarif', 'adjustment', 'periode', 'group', 'status'];
fputcsv($outputHandle, $newHeader);

$totalProcessed = 0;
$totalErrors = 0;

echo "Memproses data...\n";

while (($row = fgetcsv($handle, 1000, ';')) !== false) {
    $totalProcessed++;
    
    try {
        // Mapping kolom berdasarkan posisi:
        // 0=Group, 1=Kontainer, 2=Awal, 3=Akhir, 4=Ukuran, 5=Harga, 6=Periode, 7=Status, 8=Hari, 9=DPP, 10=PPN, 11=PPH, 12=Adjustment
        
        $group = trim($row[0] ?? '');
        $kontainer = trim($row[1] ?? '');
        $tanggalAwal = trim($row[2] ?? '');
        $tanggalAkhir = trim($row[3] ?? '');
        $ukuran = trim($row[4] ?? '');
        $harga = trim($row[5] ?? '');
        $periodeAsli = trim($row[6] ?? ''); // Periode dari kolom 6
        $status = trim($row[7] ?? '');
        $adjustment = trim($row[12] ?? '0'); // Kolom adjustment dari index 12
        
        // Skip jika data kosong
        if (empty($kontainer) || empty($tanggalAwal) || empty($tanggalAkhir)) {
            continue;
        }
        
        // Konversi tanggal dari format "07 Jun 23" ke "2023-06-07"
        $tanggalAwalConverted = convertDate($tanggalAwal);
        $tanggalAkhirConverted = convertDate($tanggalAkhir);
        
        // Clean harga (remove spaces and format properly) - ini untuk DPP calculation nanti
        $hargaCleaned = cleanTarif($harga);
        
        // Clean adjustment value
        $adjustmentCleaned = cleanTarif($adjustment);
        
        // Clean periode value
        $periodeCleaned = trim($periodeAsli);
        
        // Tentukan tarif type dari kolom Status (kolom 7)
        $statusAsli = strtolower(trim($status));
        if ($statusAsli === 'bulanan') {
            $tarifType = 'Bulanan';
        } else if ($statusAsli === 'harian') {
            $tarifType = 'Harian';
        } else {
            $tarifType = 'Bulanan'; // Default ke Bulanan
        }
        
        // Tentukan vendor
        $vendor = 'ZONA';
        
        // Clean size
        $sizeCleaned = trim($ukuran);
        
        // Status selalu ongoing untuk data ZONA
        $statusCleaned = 'ongoing';
        
        // Buat baris baru
        $newRow = [
            $vendor,                    // vendor
            $kontainer,                 // nomor_kontainer
            $sizeCleaned,              // size
            $tanggalAwalConverted,     // tanggal_awal
            $tanggalAkhirConverted,    // tanggal_akhir
            $tarifType,                // tarif (Bulanan/Harian)
            $adjustmentCleaned,        // adjustment
            $periodeCleaned,           // periode
            $group,                    // group
            $statusCleaned             // status
        ];
        
        // Tulis ke file output
        fputcsv($outputHandle, $newRow);
        
        if ($totalProcessed <= 10) {
            echo "Row $totalProcessed: " . implode(' | ', $newRow) . "\n";
        }
        
    } catch (Exception $e) {
        $totalErrors++;
        echo "ERROR on row $totalProcessed: " . $e->getMessage() . "\n";
    }
}

fclose($handle);
fclose($outputHandle);

echo "\n=== HASIL KONVERSI ===\n";
echo "Total baris diproses: $totalProcessed\n";
echo "Total error: $totalErrors\n";
echo "File output: $outputFile\n";
echo "\nFile siap untuk diimport ke sistem!\n";

/**
 * Konversi format tanggal dari "07 Jun 23" ke "2023-06-07"
 */
function convertDate($dateStr) {
    if (empty($dateStr)) return '';
    
    $months = [
        'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
        'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
        'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
    ];
    
    // Parse format "07 Jun 23"
    $parts = explode(' ', trim($dateStr));
    if (count($parts) === 3) {
        $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
        $month = $months[$parts[1]] ?? '01';
        $year = '20' . $parts[2]; // Convert 23 to 2023
        
        return "$year-$month-$day";
    }
    
    return $dateStr; // Return as is if can't parse
}

/**
 * Clean tarif from " 675.676 " to "675676"
 */
function cleanTarif($tarif) {
    // Remove spaces and dots
    $cleaned = str_replace([' ', '.', ','], '', trim($tarif));
    
    // Return as integer
    return is_numeric($cleaned) ? $cleaned : '0';
}