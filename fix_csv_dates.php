<?php

/**
 * Script untuk memperbaiki format tanggal di CSV
 * Dari format: "15 Jan 24" 
 * Ke format: "15/Jan/2024"
 */

// Path ke file CSV yang akan diperbaiki
$inputFile = 'C:\Users\User\Downloads\template_tanggal_sewa_kontainer_2025-11-13_11-16-00.csv';
$outputFile = 'C:\Users\User\Downloads\template_tanggal_sewa_kontainer_fixed.csv';

// Mapping bulan Indonesia ke format standar
$monthMapping = [
    'Jan' => 'Jan',
    'Feb' => 'Feb',
    'Mar' => 'Mar',
    'Apr' => 'Apr',
    'Mei' => 'Mei',
    'Jun' => 'Jun',
    'Jul' => 'Jul',
    'Agu' => 'Agu',
    'Sep' => 'Sep',
    'Okt' => 'Okt',
    'Nov' => 'Nov',
    'Des' => 'Des'
];

function convertDate($dateStr) {
    global $monthMapping;
    
    $dateStr = trim($dateStr);
    
    // Jika kosong, kembalikan kosong
    if (empty($dateStr)) {
        return '';
    }
    
    // Format: "15 Jan 24" -> "15/Jan/2024"
    // Format: "dd mmm yy" -> "dd/mmm/yyyy"
    
    // Split by space
    $parts = preg_split('/\s+/', $dateStr);
    
    if (count($parts) !== 3) {
        echo "Warning: Invalid date format: {$dateStr}\n";
        return $dateStr;
    }
    
    list($day, $month, $year) = $parts;
    
    // Convert year from 2-digit to 4-digit
    // Assume 00-25 = 2000-2025, 26-99 = 1926-1999
    if (strlen($year) == 2) {
        $yearInt = intval($year);
        if ($yearInt >= 0 && $yearInt <= 25) {
            $year = '20' . $year;
        } else {
            $year = '19' . $year;
        }
    }
    
    // Pad day dengan 0 jika perlu
    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
    
    // Validate month
    if (!isset($monthMapping[$month])) {
        echo "Warning: Invalid month: {$month}\n";
        return $dateStr;
    }
    
    // Return formatted date
    return $day . '/' . $monthMapping[$month] . '/' . $year;
}

try {
    // Baca file CSV
    if (!file_exists($inputFile)) {
        die("Error: File tidak ditemukan: {$inputFile}\n");
    }
    
    $handle = fopen($inputFile, 'r');
    if ($handle === FALSE) {
        die("Error: Tidak dapat membuka file: {$inputFile}\n");
    }
    
    // Buat file output
    $outputHandle = fopen($outputFile, 'w');
    if ($outputHandle === FALSE) {
        fclose($handle);
        die("Error: Tidak dapat membuat file: {$outputFile}\n");
    }
    
    $rowCount = 0;
    $processedCount = 0;
    
    while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
        $rowCount++;
        
        // Baris pertama adalah header, tulis ulang tanpa perubahan
        if ($rowCount === 1) {
            fputcsv($outputHandle, $data, ';');
            continue;
        }
        
        // Data baris:
        // [0] = Nomor Kontainer
        // [1] = Tanggal Mulai Sewa
        // [2] = Tanggal Selesai Sewa
        
        if (count($data) >= 3) {
            $data[1] = convertDate($data[1]);
            $data[2] = convertDate($data[2]);
            $processedCount++;
        }
        
        fputcsv($outputHandle, $data, ';');
    }
    
    fclose($handle);
    fclose($outputHandle);
    
    echo "✓ Berhasil!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Total baris: {$rowCount}\n";
    echo "Baris diproses: {$processedCount}\n";
    echo "\nFile output: {$outputFile}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\nContoh konversi:\n";
    echo "  15 Jan 24  →  15/Jan/2024\n";
    echo "  07 Jun 23  →  07/Jun/2023\n";
    echo "  24 Sep 25  →  24/Sep/2025\n";
    echo "\nSilakan upload file: template_tanggal_sewa_kontainer_fixed.csv\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
