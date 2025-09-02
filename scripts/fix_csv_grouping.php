<?php
/**
 * Script untuk memperbaiki CSV dengan logika grouping
 * Format: TK1YYMMXXXXXXX (14 digit)
 * - TK: 2 digit kode (semua vendor)
 * - 1: 1 digit nomor cetakan
 * - YY: 2 digit tahun
 * - MM: 2 digit bulan
 * - XXXXXXX: 7 digit running number
 */

// Input dan output file paths
$inputFile = 'input.csv';
$outputFile = 'output_with_groups.csv';

// Baca arguments dari command line
if ($argc > 1) {
    $inputFile = $argv[1];
}
if ($argc > 2) {
    $outputFile = $argv[2];
}

echo "Processing CSV file: $inputFile\n";
echo "Output file: $outputFile\n";

// Baca CSV
if (!file_exists($inputFile)) {
    echo "Error: File $inputFile tidak ditemukan!\n";
    exit(1);
}

$csvData = [];
$header = [];

if (($handle = fopen($inputFile, 'r')) !== FALSE) {
    $rowIndex = 0;
    while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) { // Menggunakan semicolon delimiter
        if ($rowIndex === 0) {
            $header = $data;
            // Jangan tambah kolom baru, tapi cari index kolom "group" yang sudah ada
            $groupColumnIndex = -1;
            foreach ($header as $idx => $col) {
                if (strtolower(trim($col, '"')) === 'group') {
                    $groupColumnIndex = $idx;
                    break;
                }
            }
            if ($groupColumnIndex === -1) {
                // Jika tidak ada kolom group, tambahkan di awal
                array_unshift($header, 'grup');
            }
        } else {
            $csvData[] = $data;
        }
        $rowIndex++;
    }
    fclose($handle);
}

echo "Loaded " . count($csvData) . " data rows\n";

// Deteksi index kolom yang diperlukan
$vendorIndex = -1;
$tanggalIndex = -1;
$groupColumnIndex = -1;

foreach ($header as $index => $col) {
    $col = strtolower(trim($col, '"'));
    if (in_array($col, ['vendor'])) {
        $vendorIndex = $index;
    }
    if (in_array($col, ['tanggal_awal', 'tanggal_mulai_sewa', 'tanggal_harga_awal', 'tgl_awal'])) {
        $tanggalIndex = $index;
    }
    if ($col === 'group') {
        $groupColumnIndex = $index;
    }
}

if ($vendorIndex === -1) {
    echo "Error: Kolom vendor tidak ditemukan!\n";
    echo "Header yang tersedia: " . implode(', ', $header) . "\n";
    exit(1);
}

if ($tanggalIndex === -1) {
    echo "Error: Kolom tanggal tidak ditemukan!\n";
    echo "Header yang tersedia: " . implode(', ', $header) . "\n";
    exit(1);
}

echo "Vendor column index: $vendorIndex\n";
echo "Tanggal column index: $tanggalIndex\n";

// Group data berdasarkan vendor dan tanggal
$groupedData = [];

foreach ($csvData as $row) {
    $vendor = $row[$vendorIndex] ?? '';
    $tanggalMulai = $row[$tanggalIndex] ?? '';

    // Generate vendor code - semua menggunakan TK
    $vendorCode = 'TK';

    // Parse tanggal mulai
    $year = '00';
    $month = '00';

    if (!empty($tanggalMulai)) {
        try {
            // Coba berbagai format tanggal
            $date = null;
            $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d'];

            foreach ($formats as $format) {
                $parsedDate = DateTime::createFromFormat($format, $tanggalMulai);
                if ($parsedDate !== false) {
                    $date = $parsedDate;
                    break;
                }
            }

            if ($date) {
                $year = $date->format('y'); // 2 digit year
                $month = $date->format('m'); // 2 digit month
            }
        } catch (Exception $e) {
            echo "Warning: Cannot parse date '$tanggalMulai', using default 00-00\n";
        }
    }

    // Create group key berdasarkan vendor dan tanggal lengkap
    $groupKey = $vendor . '_' . $tanggalMulai; // Menggunakan vendor asli dan tanggal lengkap sebagai key

    // Initialize group if not exists
    if (!isset($groupedData[$groupKey])) {
        $groupedData[$groupKey] = [];
    }

    // Add to group (belum generate group code)
    $groupedData[$groupKey][] = $row;
}

// Generate group codes dan running numbers
$outputData = [];
$globalGroupNumber = 1;

foreach ($groupedData as $groupKey => $groupItems) {
    $runningNumber = str_pad($globalGroupNumber, 7, '0', STR_PAD_LEFT);

    // Extract year and month from group key for formatting
    $parts = explode('_', $groupKey);
    $vendorPart = 'TK'; // Selalu gunakan TK untuk group code
    $datePart = $parts[1] ?? ''; // 2025-01-21

    $year = '00';
    $month = '00';
    if (!empty($datePart)) {
        try {
            $date = DateTime::createFromFormat('Y-m-d', $datePart);
            if ($date) {
                $year = $date->format('y'); // 25
                $month = $date->format('m'); // 01
            }
        } catch (Exception $e) {
            // Keep default 00-00
        }
    }

    $fullGroupCode = $vendorPart . '1' . $year . $month . $runningNumber;

    // Semua item dalam group yang sama mendapat group code yang sama
    foreach ($groupItems as $item) {
        if ($groupColumnIndex !== -1) {
            // Replace kolom group yang sudah ada
            $item[$groupColumnIndex] = $fullGroupCode;
        } else {
            // Add group code to beginning of row jika belum ada kolom group
            array_unshift($item, $fullGroupCode);
        }
        $outputData[] = $item;
    }

    $globalGroupNumber++;
}

echo "Generated " . count($outputData) . " rows with groups\n";

// Tulis output CSV
if (($handle = fopen($outputFile, 'w')) !== FALSE) {
    // Tulis header
    fputcsv($handle, $header, ';'); // Menggunakan semicolon delimiter

    // Tulis data
    foreach ($outputData as $row) {
        fputcsv($handle, $row, ';'); // Menggunakan semicolon delimiter
    }

    fclose($handle);
    echo "Output saved to: $outputFile\n";
} else {
    echo "Error: Cannot create output file $outputFile\n";
    exit(1);
}

// Tampilkan statistik grouping
echo "\n=== GROUPING STATISTICS ===\n";
foreach ($groupedData as $groupKey => $groupItems) {
    $count = count($groupItems);
    echo "Group $groupKey: $count items\n";
}

echo "\n=== SAMPLE GROUPS ===\n";
$sampleCount = 0;
foreach ($outputData as $row) {
    if ($sampleCount < 5) {
        echo "Group: {$row[0]} | Vendor: {$row[$vendorIndex + 1]} | Tanggal: {$row[$tanggalIndex + 1]}\n";
        $sampleCount++;
    }
}

echo "\nDone!\n";
