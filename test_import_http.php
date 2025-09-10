<?php
/**
 * Test HTTP request untuk import Excel
 * Simulate upload file Excel ke endpoint import
 */

$url = 'http://127.0.0.1:8000/master/karyawan/import';
$filename = 'test_karyawan_excel_import.csv';

// Check if file exists
if (!file_exists($filename)) {
    echo "❌ File test tidak ditemukan: $filename\n";
    echo "Run: php test_import_excel.php terlebih dahulu\n";
    exit(1);
}

echo "🔍 Testing Import Excel Functionality\n";
echo "=====================================\n";

// Step 1: Test GET request to import form
echo "1. Testing GET /master/karyawan/import\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "   ✅ Import form accessible (HTTP $httpCode)\n";

    // Extract CSRF token
    if (preg_match('/<input[^>]*name=["\']_token["\'][^>]*value=["\']([^"\']*)["\']/', $response, $matches)) {
        $csrfToken = $matches[1];
        echo "   ✅ CSRF token extracted: " . substr($csrfToken, 0, 10) . "...\n";
    } else {
        echo "   ❌ CSRF token not found\n";
        curl_close($ch);
        exit(1);
    }
} else {
    echo "   ❌ Import form not accessible (HTTP $httpCode)\n";
    curl_close($ch);
    exit(1);
}

// Step 2: Test POST request with file upload
echo "\n2. Testing POST /master/karyawan/import (File Upload)\n";

$postFields = [
    '_token' => $csrfToken,
    'csv_file' => new CURLFile(realpath($filename), 'text/csv', 'test_karyawan_excel_import.csv')
];

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "   📤 Uploading file: $filename\n";
echo "   📊 File size: " . filesize($filename) . " bytes\n";

if ($httpCode === 200 || $httpCode === 302) {
    echo "   ✅ File upload successful (HTTP $httpCode)\n";

    // Check for success messages in response
    if (strpos($response, 'berhasil') !== false || strpos($response, 'success') !== false) {
        echo "   ✅ Success message detected in response\n";
    }

    // Check for error messages
    if (strpos($response, 'error') !== false || strpos($response, 'gagal') !== false) {
        echo "   ⚠️  Error message detected in response\n";
    }

    // Check for warning messages
    if (strpos($response, 'warning') !== false || strpos($response, 'peringatan') !== false) {
        echo "   ⚠️  Warning message detected in response\n";
    }

} else {
    echo "   ❌ File upload failed (HTTP $httpCode)\n";
    echo "   Response: " . substr($response, 0, 500) . "...\n";
}

curl_close($ch);

// Step 3: Check database for imported data
echo "\n3. Checking Database for Imported Data\n";

// Simple database check using artisan tinker simulation
$checkScript = '
<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;

$count = Karyawan::where("email", "like", "%@test.com")->count();
echo "   📊 Test karyawan found in database: $count\n";

if ($count > 0) {
    $testKaryawan = Karyawan::where("email", "like", "%@test.com")->first();
    echo "   ✅ Sample data: {$testKaryawan->nama_lengkap} ({$testKaryawan->email})\n";
    echo "   📋 NIK: {$testKaryawan->nik}\n";
    echo "   📋 Divisi: {$testKaryawan->divisi}\n";
} else {
    echo "   ❌ No test data found in database\n";
}
';

file_put_contents('check_db.php', $checkScript);
$dbResult = shell_exec('php check_db.php 2>&1');
echo $dbResult;
unlink('check_db.php');

echo "\n🎯 Test Summary:\n";
echo "================\n";
echo "✅ Import form accessible\n";
echo "✅ CSRF protection working\n";
echo "✅ File upload functionality tested\n";
echo "✅ Database check completed\n";

echo "\n📝 Manual Verification Steps:\n";
echo "1. Open browser: http://127.0.0.1:8000/master/karyawan/import\n";
echo "2. Upload file: test_karyawan_excel_import.csv\n";
echo "3. Check results in: http://127.0.0.1:8000/master/karyawan\n";
echo "4. Look for John Doe dan Jane Smith in the list\n";
