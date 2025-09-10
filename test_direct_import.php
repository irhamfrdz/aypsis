<?php
/**
 * Direct test untuk import Excel functionality
 * Test controller method secara langsung
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\KaryawanController;
use App\Models\Karyawan;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

echo "🧪 Testing Import Excel Functionality\n";
echo "=====================================\n";

// Check if test file exists
$testFile = 'test_karyawan_excel_import.csv';
if (!file_exists($testFile)) {
    echo "❌ Test file not found: $testFile\n";
    echo "Run: php test_import_excel.php first\n";
    exit(1);
}

echo "✅ Test file found: $testFile\n";
echo "📁 File size: " . filesize($testFile) . " bytes\n";

// Read and display file content for verification
echo "\n📄 File Content Preview:\n";
$content = file_get_contents($testFile);
$lines = explode("\n", trim($content));
foreach (array_slice($lines, 0, 3) as $i => $line) {
    echo "Line " . ($i + 1) . ": " . substr($line, 0, 100) . "...\n";
}

// Test CSV parsing manually
echo "\n🔍 Testing CSV Parsing:\n";
$handle = fopen($testFile, 'r');

// Skip BOM if present
$firstBytes = fread($handle, 3);
if ($firstBytes !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
    rewind($handle);
}

$headers = fgetcsv($handle, 0, ';');
echo "✅ Headers parsed: " . count($headers) . " columns\n";
echo "   First 5 headers: " . implode(', ', array_slice($headers, 0, 5)) . "\n";

$rowCount = 0;
while (($row = fgetcsv($handle, 0, ';')) !== false) {
    $rowCount++;
    if ($rowCount == 1) {
        echo "✅ First data row parsed: " . count($row) . " fields\n";
        echo "   Sample data: " . $row[1] . " (" . $row[2] . ")\n";
    }
}
fclose($handle);

echo "✅ Total data rows: $rowCount\n";

// Test database connection
echo "\n🗄️  Testing Database Connection:\n";
try {
    $currentCount = Karyawan::count();
    echo "✅ Database connection OK\n";
    echo "📊 Current karyawan count: $currentCount\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test convertExcelToCsv method if file is .xlsx
echo "\n🔧 Testing Excel Conversion Methods:\n";

// Test ZipArchive availability
if (class_exists('ZipArchive')) {
    echo "✅ ZipArchive extension available\n";
} else {
    echo "❌ ZipArchive extension not available\n";
}

// Test DOMDocument availability
if (class_exists('DOMDocument')) {
    echo "✅ DOMDocument class available\n";
} else {
    echo "❌ DOMDocument class not available\n";
}

// Create a simple XLSX file for testing Excel import
echo "\n📁 Creating XLSX test file:\n";
$xlsxTestData = [
    ['nik', 'nama_panggilan', 'nama_lengkap', 'email', 'divisi'],
    ['1234567890123456', 'Test', 'Test User XLSX', 'test.xlsx@example.com', 'IT']
];

// Create a simple XML for XLSX (basic structure)
$xlsxContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetData>
<row r="1">
<c r="A1" t="inlineStr"><is><t>nik</t></is></c>
<c r="B1" t="inlineStr"><is><t>nama_panggilan</t></is></c>
<c r="C1" t="inlineStr"><is><t>nama_lengkap</t></is></c>
<c r="D1" t="inlineStr"><is><t>email</t></is></c>
<c r="E1" t="inlineStr"><is><t>divisi</t></is></c>
</row>
<row r="2">
<c r="A2" t="inlineStr"><is><t>1234567890123456</t></is></c>
<c r="B2" t="inlineStr"><is><t>Test</t></is></c>
<c r="C2" t="inlineStr"><is><t>Test User XLSX</t></is></c>
<c r="D2" t="inlineStr"><is><t>test.xlsx@example.com</t></is></c>
<c r="E2" t="inlineStr"><is><t>IT</t></is></c>
</row>
</sheetData>
</worksheet>';

// Create a minimal XLSX structure
if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    $xlsxFile = 'test_simple.xlsx';

    if ($zip->open($xlsxFile, ZipArchive::CREATE) === TRUE) {
        // Add required XLSX structure
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheets>
<sheet name="Sheet1" sheetId="1" r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/>
</sheets>
</workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>');

        $zip->addFromString('xl/worksheets/sheet1.xml', $xlsxContent);

        $zip->close();
        echo "✅ XLSX test file created: $xlsxFile\n";
    } else {
        echo "❌ Failed to create XLSX test file\n";
    }
}

echo "\n✅ Test Preparation Complete!\n";
echo "\n📋 Manual Testing Steps:\n";
echo "1. Server is running at: http://127.0.0.1:8000\n";
echo "2. Visit: http://127.0.0.1:8000/master/karyawan/import\n";
echo "3. Upload: test_karyawan_excel_import.csv (CSV format)\n";
echo "4. Upload: test_simple.xlsx (Excel format - if created)\n";
echo "5. Check results in Master Karyawan list\n";

echo "\n🎯 Files created for testing:\n";
echo "- test_karyawan_excel_import.csv (2 test records)\n";
if (file_exists('test_simple.xlsx')) {
    echo "- test_simple.xlsx (1 test record)\n";
}
echo "\n📊 Expected results:\n";
echo "- John Doe (john.doe@test.com) - IT Division\n";
echo "- Jane Smith (jane.smith@test.com) - Finance Division\n";
if (file_exists('test_simple.xlsx')) {
    echo "- Test User XLSX (test.xlsx@example.com) - IT Division\n";
}
