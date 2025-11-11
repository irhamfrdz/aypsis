<?php
require_once 'vendor/autoload.php';

use Maatwebsite\Excel\Facades\Excel;

// Create test Excel file
$data = [
    ['nopol', 'jenis', 'merk', 'tipe'],
    ['B 1234 ABC', 'Truk', 'Mitsubishi', 'Colt Diesel'],
    ['B 5678 DEF', 'Pickup', 'Toyota', 'Hilux'],
    ['B 9101 GHI', 'Truk', 'Isuzu', 'Elf']
];

try {
    $export = new class($data) {
        private $data;
        
        public function __construct($data) {
            $this->data = $data;
        }
        
        public function array(): array {
            return $this->data;
        }
    };
    
    // Create a simple collection export
    $collection = collect($data);
    
    // Save to storage/app directory
    $filename = 'test_mobil_import.xlsx';
    $path = storage_path('app/' . $filename);
    
    // Use PhpSpreadsheet directly to create Excel file
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    foreach ($data as $rowIndex => $row) {
        foreach ($row as $colIndex => $value) {
            $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
        }
    }
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($path);
    
    echo "Test Excel file created at: " . $path . "\n";
    echo "File size: " . filesize($path) . " bytes\n";
    
} catch (Exception $e) {
    echo "Error creating Excel file: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}