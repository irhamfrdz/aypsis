<?php
require_once 'vendor/autoload.php';

// Create test Excel file
$data = [
    ['nopol', 'jenis', 'merk', 'tipe'],
    ['B 1234 ABC', 'Truk', 'Mitsubishi', 'Colt Diesel'],
    ['B 5678 DEF', 'Pickup', 'Toyota', 'Hilux'],
    ['B 9101 GHI', 'Truk', 'Isuzu', 'Elf']
];

try {
    // Use PhpSpreadsheet directly to create Excel file
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    foreach ($data as $rowIndex => $row) {
        foreach ($row as $colIndex => $value) {
            $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
        }
    }
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $path = __DIR__ . '/storage/app/test_mobil_import.xlsx';
    
    // Create directory if not exists
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $writer->save($path);
    
    echo "Test Excel file created at: " . $path . "\n";
    echo "File size: " . filesize($path) . " bytes\n";
    
} catch (Exception $e) {
    echo "Error creating Excel file: " . $e->getMessage() . "\n";
}