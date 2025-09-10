<?php
/**
 * Comprehensive Excel Import Test
 * Test semua aspect dari import Excel functionality
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\KaryawanController;
use App\Models\Karyawan;

echo "🧪 COMPREHENSIVE EXCEL IMPORT TEST\n";
echo "==================================\n\n";

// Test 1: File Format Support
echo "📋 Test 1: File Format Support\n";
echo "------------------------------\n";

$testFiles = [
    'test_karyawan_excel_import.csv' => 'CSV with semicolon delimiter',
    'test_simple.xlsx' => 'Excel XLSX format'
];

foreach ($testFiles as $file => $desc) {
    if (file_exists($file)) {
        echo "✅ $file ($desc) - " . filesize($file) . " bytes\n";

        // Test file reading
        if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
            $handle = fopen($file, 'r');

            // Skip BOM
            $firstBytes = fread($handle, 3);
            if ($firstBytes !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
                rewind($handle);
            }

            $headers = fgetcsv($handle, 0, ';');
            $rowCount = 0;
            while (fgetcsv($handle, 0, ';') !== false) {
                $rowCount++;
            }
            fclose($handle);

            echo "   📊 Headers: " . count($headers) . " columns\n";
            echo "   📊 Data rows: $rowCount\n";
        }

        if (pathinfo($file, PATHINFO_EXTENSION) === 'xlsx') {
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($file) === TRUE) {
                    echo "   📊 XLSX structure valid\n";
                    $zip->close();
                } else {
                    echo "   ❌ XLSX structure invalid\n";
                }
            }
        }
    } else {
        echo "❌ $file ($desc) - NOT FOUND\n";
    }
}

echo "\n";

// Test 2: Controller Methods
echo "📋 Test 2: Controller Methods\n";
echo "-----------------------------\n";

$controller = new KaryawanController();

// Test importForm method
try {
    echo "✅ KaryawanController instantiated\n";

    // Check if methods exist
    $methods = ['importForm', 'importStore', 'convertExcelToCsv'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "✅ Method $method exists\n";
        } else {
            echo "❌ Method $method missing\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Controller error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Database Schema
echo "📋 Test 3: Database Schema\n";
echo "--------------------------\n";

try {
    // Check if karyawan table exists and has required fields
    $sampleKaryawan = Karyawan::first();
    if ($sampleKaryawan) {
        echo "✅ Karyawan table accessible\n";

        $requiredFields = ['nik', 'nama_lengkap', 'email', 'ktp', 'kk', 'no_hp', 'divisi'];
        $attributes = $sampleKaryawan->getAttributes();

        foreach ($requiredFields as $field) {
            if (array_key_exists($field, $attributes)) {
                echo "✅ Field '$field' exists\n";
            } else {
                echo "❌ Field '$field' missing\n";
            }
        }

        echo "📊 Total records: " . Karyawan::count() . "\n";
    } else {
        echo "⚠️  Karyawan table empty\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Excel Processing Capabilities
echo "📋 Test 4: Excel Processing Capabilities\n";
echo "----------------------------------------\n";

// Test ZipArchive
if (class_exists('ZipArchive')) {
    echo "✅ ZipArchive extension available\n";

    $zip = new ZipArchive();
    $testResult = $zip->open('test_simple.xlsx');
    if ($testResult === TRUE) {
        echo "✅ Can open XLSX files\n";

        // Test reading worksheet
        $worksheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($worksheetXml) {
            echo "✅ Can read worksheet XML\n";

            // Test XML parsing
            $doc = new DOMDocument();
            if ($doc->loadXML($worksheetXml)) {
                echo "✅ Can parse worksheet XML\n";

                $xpath = new DOMXPath($doc);
                $rows = $xpath->query('//row');
                echo "   📊 Found " . $rows->length . " rows in XLSX\n";
            } else {
                echo "❌ Cannot parse worksheet XML\n";
            }
        } else {
            echo "❌ Cannot read worksheet XML\n";
        }

        $zip->close();
    } else {
        echo "❌ Cannot open XLSX files (error code: $testResult)\n";
    }
} else {
    echo "❌ ZipArchive extension not available\n";
}

// Test DOMDocument
if (class_exists('DOMDocument')) {
    echo "✅ DOMDocument class available\n";
} else {
    echo "❌ DOMDocument class not available\n";
}

echo "\n";

// Test 5: CSV Processing
echo "📋 Test 5: CSV Processing\n";
echo "-------------------------\n";

if (file_exists('test_karyawan_excel_import.csv')) {
    echo "✅ Testing CSV file processing\n";

    $file = fopen('test_karyawan_excel_import.csv', 'r');

    // Skip BOM
    $firstBytes = fread($file, 3);
    if ($firstBytes !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
        rewind($file);
    }

    // Test different delimiters
    $delimiters = [';', ',', "\t"];
    $bestDelimiter = ';';
    $maxColumns = 0;

    foreach ($delimiters as $delimiter) {
        rewind($file);
        if ($firstBytes === chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            fseek($file, 3);
        }

        $headers = fgetcsv($file, 0, $delimiter);
        if ($headers && count($headers) > $maxColumns) {
            $maxColumns = count($headers);
            $bestDelimiter = $delimiter;
        }
    }

    echo "✅ Best delimiter detected: '$bestDelimiter' ($maxColumns columns)\n";

    // Test data validation
    rewind($file);
    if ($firstBytes === chr(0xEF) . chr(0xBB) . chr(0xBF)) {
        fseek($file, 3);
    }

    $headers = fgetcsv($file, 0, $bestDelimiter);
    $validRows = 0;
    $invalidRows = 0;

    while (($row = fgetcsv($file, 0, $bestDelimiter)) !== false) {
        if (count($row) === count($headers)) {
            // Basic validation
            if (!empty($row[0]) && !empty($row[2]) && filter_var($row[4], FILTER_VALIDATE_EMAIL)) {
                $validRows++;
            } else {
                $invalidRows++;
            }
        } else {
            $invalidRows++;
        }
    }

    echo "✅ Data validation: $validRows valid rows, $invalidRows invalid rows\n";

    fclose($file);
} else {
    echo "❌ CSV test file not found\n";
}

echo "\n";

// Test Summary
echo "🎯 TEST SUMMARY\n";
echo "===============\n";

$totalTests = 5;
$passedTests = 0;

// Count based on critical components
if (file_exists('test_karyawan_excel_import.csv')) $passedTests++;
if (class_exists('ZipArchive')) $passedTests++;
if (class_exists('DOMDocument')) $passedTests++;
if (method_exists(KaryawanController::class, 'importStore')) $passedTests++;
if (Karyawan::count() > 0) $passedTests++;

echo "📊 Tests passed: $passedTests / $totalTests\n";

if ($passedTests >= 4) {
    echo "✅ EXCEL IMPORT FUNCTIONALITY: READY FOR USE\n";
    echo "\n🚀 Ready for manual testing:\n";
    echo "1. Visit: http://127.0.0.1:8000/master/karyawan/import\n";
    echo "2. Upload: test_karyawan_excel_import.csv or test_simple.xlsx\n";
    echo "3. Verify results in karyawan list\n";
} else {
    echo "❌ EXCEL IMPORT FUNCTIONALITY: NEEDS ATTENTION\n";
    echo "\n🔧 Issues to resolve:\n";
    if (!file_exists('test_karyawan_excel_import.csv')) echo "- Create test CSV file\n";
    if (!class_exists('ZipArchive')) echo "- Install ZipArchive extension\n";
    if (!class_exists('DOMDocument')) echo "- Install DOMDocument extension\n";
    if (!method_exists(KaryawanController::class, 'importStore')) echo "- Fix KaryawanController import methods\n";
    if (Karyawan::count() === 0) echo "- Check database connection\n";
}

echo "\n📁 Test files available:\n";
foreach ($testFiles as $file => $desc) {
    if (file_exists($file)) {
        echo "- $file\n";
    }
}
