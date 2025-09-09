<?php
/**
 * Test lengkap semua format tanggal yang didukung
 */

echo "🧪 COMPREHENSIVE DATE FORMAT TEST\n";
echo "=================================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$normalizeDate = function($val) {
    $val = trim((string)$val);
    if ($val === '') return null;
    
    // already ISO-like
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;

    // Handle dd/mm/yyyy format (17/02/2020) - PRIORITAS UTAMA SESUAI SCREENSHOT
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $val, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $year = $matches[3];
        return $year . '-' . $month . '-' . $day;
    }

    // Handle dd-mm-yyyy format (17-02-2020)
    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $val, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $year = $matches[3];
        return $year . '-' . $month . '-' . $day;
    }

    // Handle dd/mmm/yyyy format (17/Feb/2020) - Original format
    if (preg_match('/^(\d{1,2})\/([A-Za-z]{3})\/(\d{4})$/', $val, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $monthStr = ucfirst(strtolower($matches[2]));
        $year = $matches[3];
        
        $monthMap = [
            'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
            'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
            'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
        ];
        
        if (!isset($monthMap[$monthStr])) return null;
        return $year . '-' . $monthMap[$monthStr] . '-' . $day;
    }

    // Handle dd-mmm-yyyy format (17-Feb-2020)
    if (preg_match('/^(\d{1,2})-([A-Za-z]{3})-(\d{4})$/', $val, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $monthStr = ucfirst(strtolower($matches[2]));
        $year = $matches[3];
        
        $monthMap = [
            'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
            'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
            'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
        ];
        
        if (!isset($monthMap[$monthStr])) return null;
        return $year . '-' . $monthMap[$monthStr] . '-' . $day;
    }

    // Fallback to strtotime for other formats
    $ts = strtotime($val);
    if ($ts === false) return null;
    return date('Y-m-d', $ts);
};

echo "📋 FORMAT YANG DIDUKUNG:\n";
echo "1. ✅ dd/mm/yyyy (17/02/2020) - SESUAI SCREENSHOT USER\n";
echo "2. ✅ dd-mm-yyyy (17-02-2020)\n";
echo "3. ✅ dd/mmm/yyyy (17/Feb/2020) - Original format\n";
echo "4. ✅ dd-mmm-yyyy (17-Feb-2020)\n";
echo "5. ✅ yyyy-mm-dd (2020-02-17) - ISO format\n";
echo "6. ✅ Other standard formats via strtotime()\n\n";

$testCases = [
    // Format dari screenshot user - PRIORITAS UTAMA
    ['17/02/2020', '2020-02-17', 'dd/mm/yyyy (screenshot format)'],
    ['02/03/2020', '2020-03-02', 'dd/mm/yyyy (screenshot format)'],
    ['25/10/2021', '2021-10-25', 'dd/mm/yyyy (screenshot format)'],
    ['1/1/2024', '2024-01-01', 'dd/mm/yyyy single digit'],
    ['31/12/2023', '2023-12-31', 'dd/mm/yyyy end of year'],
    
    // Format dengan dash
    ['17-02-2020', '2020-02-17', 'dd-mm-yyyy'],
    ['02-03-2020', '2020-03-02', 'dd-mm-yyyy'],
    
    // Format original dengan nama bulan
    ['17/Feb/2020', '2020-02-17', 'dd/mmm/yyyy original'],
    ['17/JAN/2020', '2020-01-17', 'dd/mmm/yyyy uppercase'],
    ['17/feb/2020', '2020-02-17', 'dd/mmm/yyyy lowercase'],
    
    // Format dengan dash dan nama bulan
    ['17-Feb-2020', '2020-02-17', 'dd-mmm-yyyy'],
    ['17-JAN-2020', '2020-01-17', 'dd-mmm-yyyy uppercase'],
    
    // Format ISO
    ['2020-02-17', '2020-02-17', 'yyyy-mm-dd ISO'],
    ['2021-12-31', '2021-12-31', 'yyyy-mm-dd ISO'],
    
    // Edge cases
    ['', null, 'empty string'],
    ['invalid', null, 'invalid date'],
];

echo "🧪 TESTING ALL FORMATS:\n";
echo "======================\n";

$passed = 0;
$failed = 0;

foreach ($testCases as [$input, $expected, $description]) {
    $result = $normalizeDate($input);
    
    if ($result === $expected) {
        echo "✅ PASS: '$input' → '$result' ($description)\n";
        $passed++;
    } else {
        echo "❌ FAIL: '$input' → '$result' (expected: '$expected') ($description)\n";
        $failed++;
    }
}

echo "\n📊 TEST RESULTS:\n";
echo "================\n";
echo "✅ Passed: $passed tests\n";
echo "❌ Failed: $failed tests\n";
echo "📈 Success Rate: " . round(($passed / ($passed + $failed)) * 100, 1) . "%\n\n";

if ($failed === 0) {
    echo "🎉 SEMUA TEST BERHASIL! Sistem mendukung semua format tanggal yang dibutuhkan.\n";
    echo "🔥 Format dd/mm/yyyy dari screenshot Anda sudah FULLY SUPPORTED!\n\n";
    
    echo "💡 PETUNJUK PENGGUNAAN:\n";
    echo "1. Upload file Excel/CSV dengan format tanggal dd/mm/yyyy\n";
    echo "2. Sistem akan otomatis mendeteksi dan mengkonversi format\n";
    echo "3. Tanggal akan disimpan dalam format YYYY-MM-DD di database\n";
    echo "4. Format yang didukung: dd/mm/yyyy, dd/mmm/yyyy, dd-mm-yyyy, yyyy-mm-dd\n";
} else {
    echo "⚠️  Ada $failed test yang gagal. Perlu perbaikan.\n";
}

echo "\n📁 File test yang tersedia untuk upload:\n";
echo "1. test_ddmmyyyy_format.csv - Test khusus format dd/mm/yyyy\n";
echo "2. test_comprehensive_dates.csv - Test semua format (dibuat otomatis)\n";

// Buat file test comprehensive
$comprehensiveData = [
    ['nik','nama_panggilan','nama_lengkap','tanggal_lahir','tanggal_masuk'],
    ['001', 'Test1', 'User Format 1', '17/02/2020', '17/02/2020'], // dd/mm/yyyy
    ['002', 'Test2', 'User Format 2', '17-02-2020', '17-02-2020'], // dd-mm-yyyy  
    ['003', 'Test3', 'User Format 3', '17/Feb/2020', '17/Feb/2020'], // dd/mmm/yyyy
    ['004', 'Test4', 'User Format 4', '2020-02-17', '2020-02-17'], // yyyy-mm-dd
    ['005', 'Test5', 'User Format 5', '1/1/2024', '1/1/2024'], // single digit
];

$file = fopen('test_comprehensive_dates.csv', 'w');
fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

foreach ($comprehensiveData as $row) {
    fwrite($file, implode(';', $row) . "\r\n");
}
fclose($file);

echo "✅ File test_comprehensive_dates.csv berhasil dibuat!\n";
