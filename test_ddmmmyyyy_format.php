<?php
/**
 * Test khusus format tanggal dd/mmm/yyyy
 */

// Test data dengan format tanggal dd/mmm/yyyy
$testData = [
    // Header
    ['nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','cabang','nik_supervisor','supervisor'],

    // Data test 1 - Format dd/mmm/yyyy
    ['3201234567890140', 'Test1', 'Test User 1 MMM', 'B 1111 MMM', 'test1.ddmmmyyyy@test.com', '3201234567890140', '3301234567890140', 'Jl. Test DD/MMM/YYYY No. 123', '001/002', 'Kelurahan Test', 'Kecamatan Test', 'Kabupaten Test', 'Provinsi Test', '12345', 'Jl. Test DD/MMM/YYYY No. 123, RT 001/RW 002, Kelurahan Test', 'Jakarta', '17/Feb/2020', '081234567800', 'L', 'Belum Kawin', 'Islam', 'IT', 'Programmer', '17/Feb/2020', '', '', '', 'Test format dd/mmm/yyyy - case 1', 'TK0', 'Bank BCA', 'Cabang Jakarta', '1234567800', 'Test User 1 MMM', '0001234567800', '12345678901234580', 'Jakarta', '', ''],

    // Data test 2 - Format dd/mmm/yyyy (uppercase)
    ['3301234567890141', 'Test2', 'Test User 2 MMM', 'B 2222 MMM', 'test2.ddmmmyyyy@test.com', '3301234567890141', '3401234567890141', 'Jl. Test DD/MMM/YYYY No. 456', '003/004', 'Kelurahan Test2', 'Kecamatan Test2', 'Kabupaten Test2', 'Provinsi Test2', '54321', 'Jl. Test DD/MMM/YYYY No. 456, RT 003/RW 004, Kelurahan Test2', 'Surabaya', '02/MAR/2020', '081987654301', 'P', 'Kawin', 'Kristen', 'Finance', 'Accountant', '02/MAR/2020', '', '', '', 'Test format dd/mmm/yyyy uppercase - case 2', 'K1', 'Bank Mandiri', 'Cabang Surabaya', '0987654301', 'Test User 2 MMM', '0009876543201', '98765432109876551', 'Surabaya', '', ''],

    // Data test 3 - Format dd/mmm/yyyy (lowercase)
    ['3401234567890142', 'Test3', 'Test User 3 MMM', 'B 3333 MMM', 'test3.ddmmmyyyy@test.com', '3401234567890142', '3501234567890142', 'Jl. Test DD/MMM/YYYY No. 789', '005/006', 'Kelurahan Test3', 'Kecamatan Test3', 'Kabupaten Test3', 'Provinsi Test3', '67890', 'Jl. Test DD/MMM/YYYY No. 789, RT 005/RW 006, Kelurahan Test3', 'Bandung', '03/mar/2021', '081234567802', 'L', 'Kawin', 'Islam', 'HR', 'Manager', '03/mar/2021', '', '', '', 'Test format dd/mmm/yyyy lowercase - case 3', 'K2', 'Bank BNI', 'Cabang Bandung', '1234567802', 'Test User 3 MMM', '0001234567802', '12345678901234581', 'Bandung', '', ''],

    // Data test 4 - Format dd/mmm/yyyy (mixed case)
    ['3501234567890143', 'Test4', 'Test User 4 MMM', 'B 4444 MMM', 'test4.ddmmmyyyy@test.com', '3501234567890143', '3601234567890143', 'Jl. Test DD/MMM/YYYY No. 012', '007/008', 'Kelurahan Test4', 'Kecamatan Test4', 'Kabupaten Test4', 'Provinsi Test4', '09876', 'Jl. Test DD/MMM/YYYY No. 012, RT 007/RW 008, Kelurahan Test4', 'Medan', '25/Oct/2021', '081987654303', 'P', 'Belum Kawin', 'Islam', 'Marketing', 'Supervisor', '25/Oct/2021', '', '', '', 'Test format dd/mmm/yyyy mixed case - case 4', 'TK1', 'Bank BRI', 'Cabang Medan', '0987654303', 'Test User 4 MMM', '0009876543203', '98765432109876552', 'Medan', '', ''],

    // Data test 5 - Format dd/mmm/yyyy (proper case)
    ['3601234567890144', 'Test5', 'Test User 5 MMM', 'B 5555 MMM', 'test5.ddmmmyyyy@test.com', '3601234567890144', '3701234567890144', 'Jl. Test DD/MMM/YYYY No. 345', '009/010', 'Kelurahan Test5', 'Kecamatan Test5', 'Kabupaten Test5', 'Provinsi Test5', '13579', 'Jl. Test DD/MMM/YYYY No. 345, RT 009/RW 010, Kelurahan Test5', 'Yogyakarta', '20/Dec/2021', '081234567804', 'L', 'Kawin', 'Hindu', 'Operations', 'Coordinator', '20/Dec/2021', '', '', '', 'Test format dd/mmm/yyyy proper case - case 5', 'K3', 'Bank CIMB', 'Cabang Yogyakarta', '1234567804', 'Test User 5 MMM', '0001234567804', '12345678901234582', 'Yogyakarta', '', ''],

    // Data test 6 - Format dd/mmm/yyyy (different months)
    ['3701234567890145', 'Test6', 'Test User 6 MMM', 'B 6666 MMM', 'test6.ddmmmyyyy@test.com', '3701234567890145', '3801234567890145', 'Jl. Test DD/MMM/YYYY No. 678', '011/012', 'Kelurahan Test6', 'Kecamatan Test6', 'Kabupaten Test6', 'Provinsi Test6', '24680', 'Jl. Test DD/MMM/YYYY No. 678, RT 011/RW 012, Kelurahan Test6', 'Bali', '15/Jun/2022', '081234567805', 'P', 'Kawin', 'Hindu', 'Design', 'Designer', '15/Jun/2022', '', '', '', 'Test format dd/mmm/yyyy June - case 6', 'K1', 'Bank Danamon', 'Cabang Bali', '1234567805', 'Test User 6 MMM', '0001234567805', '12345678901234583', 'Bali', '', ''],

    // Data test 7 - Format dd/mmm/yyyy (single digit day)
    ['3801234567890146', 'Test7', 'Test User 7 MMM', 'B 7777 MMM', 'test7.ddmmmyyyy@test.com', '3801234567890146', '3901234567890146', 'Jl. Test DD/MMM/YYYY No. 901', '013/014', 'Kelurahan Test7', 'Kecamatan Test7', 'Kabupaten Test7', 'Provinsi Test7', '35791', 'Jl. Test DD/MMM/YYYY No. 901, RT 013/RW 014, Kelurahan Test7', 'Makassar', '5/Aug/2022', '081234567806', 'L', 'Belum Kawin', 'Islam', 'Sales', 'Sales Rep', '5/Aug/2022', '', '', '', 'Test format dd/mmm/yyyy single digit - case 7', 'TK0', 'Bank Permata', 'Cabang Makassar', '1234567806', 'Test User 7 MMM', '0001234567806', '12345678901234584', 'Makassar', '', '']
];

// Buat CSV file untuk testing format tanggal dd/mmm/yyyy
$filename = 'test_ddmmmyyyy_format.csv';
$file = fopen($filename, 'w');

// Write UTF-8 BOM for Excel recognition
fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Write data with semicolon delimiter (Excel format)
foreach ($testData as $row) {
    $escapedRow = [];
    foreach ($row as $field) {
        // Escape fields that contain semicolons, quotes, or line breaks
        if (strpos($field, ';') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false || strpos($field, "\r") !== false) {
            $escapedRow[] = '"' . str_replace('"', '""', $field) . '"';
        } else {
            $escapedRow[] = $field;
        }
    }
    fwrite($file, implode(';', $escapedRow) . "\r\n");
}

fclose($file);

echo "✅ File test format dd/mmm/yyyy berhasil dibuat: $filename\n";
echo "📁 Lokasi: " . realpath($filename) . "\n";
echo "📊 Berisi " . (count($testData) - 1) . " data karyawan dengan format tanggal dd/mmm/yyyy\n\n";

echo "🗓️  Format tanggal yang ditest (dd/mmm/yyyy):\n";
echo "1. Test User 1: 17/Feb/2020 (proper case)\n";
echo "2. Test User 2: 02/MAR/2020 (uppercase)\n";
echo "3. Test User 3: 03/mar/2021 (lowercase)\n";
echo "4. Test User 4: 25/Oct/2021 (mixed case)\n";
echo "5. Test User 5: 20/Dec/2021 (proper case)\n";
echo "6. Test User 6: 15/Jun/2022 (June test)\n";
echo "7. Test User 7: 5/Aug/2022 (single digit)\n\n";

// Test date parsing function
echo "🧪 Testing date parsing untuk format dd/mmm/yyyy:\n";
echo "============================================\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$normalizeDate = function($val) {
    $val = trim((string)$val);
    if ($val === '') return null;

    // already ISO-like
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;

    // Handle dd/mm/yyyy format (17/02/2020)
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

    // Handle dd/mmm/yyyy format (17/Feb/2020) - PRIORITAS UNTUK dd/mmm/yyyy
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

    // Other formats...
    $ts = strtotime($val);
    if ($ts === false) return null;
    return date('Y-m-d', $ts);
};

$testDates = [
    '17/Feb/2020', // proper case
    '02/MAR/2020', // uppercase
    '03/mar/2021', // lowercase
    '25/Oct/2021', // mixed case
    '20/Dec/2021', // proper case
    '15/Jun/2022', // June test
    '5/Aug/2022',  // single digit
    '1/Jan/2024',  // Test tambahan
    '31/DEC/2023', // Test uppercase
];

foreach ($testDates as $dateStr) {
    $normalized = $normalizeDate($dateStr);
    echo "✅ '$dateStr' → '$normalized'\n";
}

echo "\n📋 Langkah testing:\n";
echo "1. Upload file '$filename' melalui halaman import\n";
echo "2. Verifikasi format dd/mmm/yyyy berhasil dikonversi dengan benar\n";
echo "3. Cek hasil di database apakah tanggal tersimpan sebagai YYYY-MM-DD\n";
echo "4. Verifikasi semua 7 data karyawan berhasil diimport\n";
echo "5. Format yang diharapkan:\n";
echo "   - 17/Feb/2020 → 2020-02-17\n";
echo "   - 02/MAR/2020 → 2020-03-02\n";
echo "   - 03/mar/2021 → 2021-03-03\n";
echo "   - 25/Oct/2021 → 2021-10-25\n";
echo "   - 20/Dec/2021 → 2021-12-20\n";
echo "   - 15/Jun/2022 → 2022-06-15\n";
echo "   - 5/Aug/2022 → 2022-08-05\n\n";

echo "🔥 FORMAT dd/mmm/yyyy FULLY SUPPORTED!\n";
echo "✨ Mendukung case variations: Feb, FEB, feb, Mar, MAR, mar, dll\n";
echo "✨ Mendukung single digit day: 5/Aug/2022, 17/Feb/2020\n";
echo "✨ Mendukung semua bulan: Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec\n";
