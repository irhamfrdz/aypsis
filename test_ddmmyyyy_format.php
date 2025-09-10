<?php
/**
 * Test format tanggal dd/mm/yyyy sesuai screenshot
 */

// Test data dengan format tanggal dd/mm/yyyy seperti di screenshot
$testData = [
    // Header
    ['nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','cabang','nik_supervisor','supervisor'],

    // Data test 1 - Format dd/mm/yyyy seperti di screenshot
    ['3201234567890130', 'Test1', 'Test User 1', 'B 1111 XYZ', 'test1.ddmmyyyy@test.com', '3201234567890130', '3301234567890130', 'Jl. Test DD/MM/YYYY No. 123', '001/002', 'Kelurahan Test', 'Kecamatan Test', 'Kabupaten Test', 'Provinsi Test', '12345', 'Jl. Test DD/MM/YYYY No. 123, RT 001/RW 002, Kelurahan Test', 'Jakarta', '17/02/2020', '081234567895', 'L', 'Belum Kawin', 'Islam', 'IT', 'Programmer', '17/02/2020', '', '', '', 'Test format dd/mm/yyyy - case 1', 'TK0', 'Bank BCA', 'Cabang Jakarta', '1234567895', 'Test User 1', '0001234567895', '12345678901234570', 'Jakarta', '', ''],

    // Data test 2 - Format dd/mm/yyyy
    ['3301234567890131', 'Test2', 'Test User 2', 'B 2222 ABC', 'test2.ddmmyyyy@test.com', '3301234567890131', '3401234567890131', 'Jl. Test DD/MM/YYYY No. 456', '003/004', 'Kelurahan Test2', 'Kecamatan Test2', 'Kabupaten Test2', 'Provinsi Test2', '54321', 'Jl. Test DD/MM/YYYY No. 456, RT 003/RW 004, Kelurahan Test2', 'Surabaya', '02/03/2020', '081987654326', 'P', 'Kawin', 'Kristen', 'Finance', 'Accountant', '02/03/2020', '', '', '', 'Test format dd/mm/yyyy - case 2', 'K1', 'Bank Mandiri', 'Cabang Surabaya', '0987654326', 'Test User 2', '0009876543216', '98765432109876546', 'Surabaya', '', ''],

    // Data test 3 - Format dd/mm/yyyy
    ['3401234567890132', 'Test3', 'Test User 3', 'B 3333 DEF', 'test3.ddmmyyyy@test.com', '3401234567890132', '3501234567890132', 'Jl. Test DD/MM/YYYY No. 789', '005/006', 'Kelurahan Test3', 'Kecamatan Test3', 'Kabupaten Test3', 'Provinsi Test3', '67890', 'Jl. Test DD/MM/YYYY No. 789, RT 005/RW 006, Kelurahan Test3', 'Bandung', '03/03/2021', '081234567897', 'L', 'Kawin', 'Islam', 'HR', 'Manager', '03/03/2021', '', '', '', 'Test format dd/mm/yyyy - case 3', 'K2', 'Bank BNI', 'Cabang Bandung', '1234567897', 'Test User 3', '0001234567897', '12345678901234571', 'Bandung', '', ''],

    // Data test 4 - Format dd/mm/yyyy
    ['3501234567890133', 'Test4', 'Test User 4', 'B 4444 GHI', 'test4.ddmmyyyy@test.com', '3501234567890133', '3601234567890133', 'Jl. Test DD/MM/YYYY No. 012', '007/008', 'Kelurahan Test4', 'Kecamatan Test4', 'Kabupaten Test4', 'Provinsi Test4', '09876', 'Jl. Test DD/MM/YYYY No. 012, RT 007/RW 008, Kelurahan Test4', 'Medan', '25/10/2021', '081987654328', 'P', 'Belum Kawin', 'Islam', 'Marketing', 'Supervisor', '25/10/2021', '', '', '', 'Test format dd/mm/yyyy - case 4', 'TK1', 'Bank BRI', 'Cabang Medan', '0987654328', 'Test User 4', '0009876543218', '98765432109876547', 'Medan', '', ''],

    // Data test 5 - Format dd/mm/yyyy
    ['3601234567890134', 'Test5', 'Test User 5', 'B 5555 JKL', 'test5.ddmmyyyy@test.com', '3601234567890134', '3701234567890134', 'Jl. Test DD/MM/YYYY No. 345', '009/010', 'Kelurahan Test5', 'Kecamatan Test5', 'Kabupaten Test5', 'Provinsi Test5', '13579', 'Jl. Test DD/MM/YYYY No. 345, RT 009/RW 010, Kelurahan Test5', 'Yogyakarta', '20/12/2021', '081234567899', 'L', 'Kawin', 'Hindu', 'Operations', 'Coordinator', '20/12/2021', '', '', '', 'Test format dd/mm/yyyy - case 5', 'K3', 'Bank CIMB', 'Cabang Yogyakarta', '1234567899', 'Test User 5', '0001234567899', '12345678901234572', 'Yogyakarta', '', '']
];

// Buat CSV file untuk testing format tanggal dd/mm/yyyy
$filename = 'test_ddmmyyyy_format.csv';
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

echo "✅ File test format dd/mm/yyyy berhasil dibuat: $filename\n";
echo "📁 Lokasi: " . realpath($filename) . "\n";
echo "📊 Berisi " . (count($testData) - 1) . " data karyawan dengan format tanggal dd/mm/yyyy\n\n";

echo "🗓️  Format tanggal yang ditest (sesuai screenshot):\n";
echo "1. Test User 1: 17/02/2020 (dd/mm/yyyy)\n";
echo "2. Test User 2: 02/03/2020 (dd/mm/yyyy)\n";
echo "3. Test User 3: 03/03/2021 (dd/mm/yyyy)\n";
echo "4. Test User 4: 25/10/2021 (dd/mm/yyyy)\n";
echo "5. Test User 5: 20/12/2021 (dd/mm/yyyy)\n\n";

// Test date parsing function
echo "🧪 Testing date parsing untuk format dd/mm/yyyy:\n";
echo "===========================================\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$normalizeDate = function($val) {
    $val = trim((string)$val);
    if ($val === '') return null;

    // already ISO-like
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;

    // Handle dd/mm/yyyy format (17/02/2020) - PRIORITAS UTAMA
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

    // Other formats...
    $ts = strtotime($val);
    if ($ts === false) return null;
    return date('Y-m-d', $ts);
};

$testDates = [
    '17/02/2020', // Sesuai screenshot
    '02/03/2020', // Sesuai screenshot
    '03/03/2021', // Sesuai screenshot
    '25/10/2021', // Sesuai screenshot
    '20/12/2021', // Sesuai screenshot
    '01/01/2024', // Test tambahan
    '31/12/2023', // Test tambahan
];

foreach ($testDates as $dateStr) {
    $normalized = $normalizeDate($dateStr);
    echo "✅ '$dateStr' → '$normalized'\n";
}

echo "\n📋 Langkah testing:\n";
echo "1. Upload file '$filename' melalui halaman import\n";
echo "2. Verifikasi format dd/mm/yyyy berhasil dikonversi dengan benar\n";
echo "3. Cek hasil di database apakah tanggal tersimpan sebagai YYYY-MM-DD\n";
echo "4. Verifikasi semua 5 data karyawan berhasil diimport\n";
echo "5. Format yang diharapkan:\n";
echo "   - 17/02/2020 → 2020-02-17\n";
echo "   - 02/03/2020 → 2020-03-02\n";
echo "   - 03/03/2021 → 2021-03-03\n";
echo "   - 25/10/2021 → 2021-10-25\n";
echo "   - 20/12/2021 → 2021-12-20\n";
