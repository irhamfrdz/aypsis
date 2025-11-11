<?php
require_once 'vendor/autoload.php';

use Maatwebsite\Excel\Facades\Excel;

echo "Testing Maatwebsite Excel...\n";

try {
    // Test if we can create a simple Excel file
    $data = [
        ['Name', 'Email'],
        ['John Doe', 'john@example.com'],
        ['Jane Doe', 'jane@example.com']
    ];
    
    echo "Excel facade available: " . (class_exists(Excel::class) ? 'YES' : 'NO') . "\n";
    echo "ZipArchive available: " . (class_exists('ZipArchive') ? 'YES' : 'NO') . "\n";
    
    echo "Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}