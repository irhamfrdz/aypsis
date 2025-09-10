<?php
/**
 * Test format tanggal dd/mmm/yyyy untuk import Excel
 */

// Test data dengan berbagai format tanggal
$testData = [
    // Header
    ['nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','cabang','nik_supervisor','supervisor'],

    // Data test 1 - Format dd/mmm/yyyy
    ['3201234567890125', 'Alex', 'Alex Johnson', 'B 9999 XYZ', 'alex.johnson@test.com', '3201234567890125', '3301234567890125', 'Jl. Test Format Tanggal No. 123', '001/002', 'Kelurahan Test', 'Kecamatan Test', 'Kabupaten Test', 'Provinsi Test', '12345', 'Jl. Test Format Tanggal No. 123, RT 001/RW 002, Kelurahan Test', 'Jakarta', '15/Jan/1990', '081234567891', 'L', 'Belum Kawin', 'Islam', 'IT', 'Programmer', '01/Feb/2024', '', '', '', 'Test format dd/mmm/yyyy', 'TK0', 'Bank BCA', 'Cabang Jakarta', '1234567891', 'Alex Johnson', '0001234567891', '12345678901234568', 'Jakarta', '', ''],

    // Data test 2 - Format dd-mmm-yyyy (dengan dash)
    ['3301234567890126', 'Maria', 'Maria Santos', 'B 8888 ABC', 'maria.santos@test.com', '3301234567890126', '3401234567890126', 'Jl. Test Format Tanggal No. 456', '003/004', 'Kelurahan Santos', 'Kecamatan Santos', 'Kabupaten Santos', 'Provinsi Santos', '54321', 'Jl. Test Format Tanggal No. 456, RT 003/RW 004, Kelurahan Santos', 'Surabaya', '20-Dec-1992', '081987654322', 'P', 'Kawin', 'Kristen', 'Finance', 'Accountant', '15-Mar-2024', '', '', '', 'Test format dd-mmm-yyyy', 'K1', 'Bank Mandiri', 'Cabang Surabaya', '0987654322', 'Maria Santos', '0009876543211', '98765432109876544', 'Surabaya', '', ''],

    // Data test 3 - Format mixed (menggunakan bulan Indonesia)
    ['3401234567890127', 'Budi', 'Budi Setiawan', 'B 7777 DEF', 'budi.setiawan@test.com', '3401234567890127', '3501234567890127', 'Jl. Test Format Tanggal No. 789', '005/006', 'Kelurahan Budi', 'Kecamatan Budi', 'Kabupaten Budi', 'Provinsi Budi', '67890', 'Jl. Test Format Tanggal No. 789, RT 005/RW 006, Kelurahan Budi', 'Bandung', '25/Mei/1988', '081234567893', 'L', 'Kawin', 'Islam', 'HR', 'Manager', '10/Jun/2024', '', '', '', 'Test format dengan bulan Indonesia', 'K2', 'Bank BNI', 'Cabang Bandung', '1234567893', 'Budi Setiawan', '0001234567893', '12345678901234569', 'Bandung', '', ''],

    // Data test 4 - Format ISO untuk perbandingan
    ['3501234567890128', 'Siti', 'Siti Nurhaliza', 'B 6666 GHI', 'siti.nurhaliza@test.com', '3501234567890128', '3601234567890128', 'Jl. Test Format Tanggal No. 012', '007/008', 'Kelurahan Siti', 'Kecamatan Siti', 'Kabupaten Siti', 'Provinsi Siti', '09876', 'Jl. Test Format Tanggal No. 012, RT 007/RW 008, Kelurahan Siti', 'Medan', '1985-08-30', '081987654324', 'P', 'Belum Kawin', 'Islam', 'Marketing', 'Supervisor', '2024-04-20', '', '', '', 'Test format ISO standard', 'TK1', 'Bank BRI', 'Cabang Medan', '0987654324', 'Siti Nurhaliza', '0009876543213', '98765432109876545', 'Medan', '', '']
];

// Buat CSV file untuk testing format tanggal
$filename = 'test_date_format_excel.csv';
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

echo "✅ File test format tanggal berhasil dibuat: $filename\n";
echo "📁 Lokasi: " . realpath($filename) . "\n";
echo "📊 Berisi " . (count($testData) - 1) . " data karyawan dengan format tanggal berbeda\n\n";

echo "🗓️  Format tanggal yang ditest:\n";
echo "1. Alex Johnson    : 15/Jan/1990 (dd/mmm/yyyy)\n";
echo "2. Maria Santos    : 20-Dec-1992 (dd-mmm-yyyy)\n";
echo "3. Budi Setiawan   : 25/Mei/1988 (dd/mmm/yyyy dengan bulan Indonesia)\n";
echo "4. Siti Nurhaliza  : 1985-08-30 (YYYY-MM-DD standard)\n\n";

echo "🔍 Preview isi file:\n";
echo "===================\n";
$content = file_get_contents($filename);
echo substr($content, 3, 500) . "...\n\n"; // Skip BOM, show first 500 chars

// Test date parsing function
echo "🧪 Testing date parsing:\n";
echo "========================\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$normalizeDate = function($val) {
    $val = trim((string)$val);
    if ($val === '') return null;

    // already ISO-like
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;

    // Handle dd/mmm/yyyy format (15/Jan/2024)
    if (preg_match('/^(\d{1,2})\/([A-Za-z]{3})\/(\d{4})$/', $val, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $monthAbbr = strtolower($matches[2]);
        $year = $matches[3];

        // Map month abbreviations to numbers
        $monthMap = [
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
            'may' => '05', 'mei' => '05', 'jun' => '06', 'jul' => '07',
            'aug' => '08', 'agu' => '08', 'sep' => '09', 'oct' => '10',
            'okt' => '10', 'nov' => '11', 'dec' => '12', 'des' => '12'
        ];

        if (isset($monthMap[$monthAbbr])) {
            return $year . '-' . $monthMap[$monthAbbr] . '-' . $day;
        }
    }

    // Handle dd-mmm-yyyy format (15-Jan-2024)
    if (preg_match('/^(\d{1,2})-([A-Za-z]{3})-(\d{4})$/', $val, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $monthAbbr = strtolower($matches[2]);
        $year = $matches[3];

        // Map month abbreviations to numbers
        $monthMap = [
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
            'may' => '05', 'mei' => '05', 'jun' => '06', 'jul' => '07',
            'aug' => '08', 'agu' => '08', 'sep' => '09', 'oct' => '10',
            'okt' => '10', 'nov' => '11', 'dec' => '12', 'des' => '12'
        ];

        if (isset($monthMap[$monthAbbr])) {
            return $year . '-' . $monthMap[$monthAbbr] . '-' . $day;
        }
    }

    // For other formats, use existing fallback logic...
    $ts = strtotime($val);
    if ($ts === false) return null;
    return date('Y-m-d', $ts);
};

$testDates = [
    '15/Jan/1990',
    '20-Dec-1992',
    '25/Mei/1988',
    '1985-08-30',
    '01/Feb/2024',
    '15-Mar-2024',
    '10/Jun/2024',
    '2024-04-20'
];

foreach ($testDates as $dateStr) {
    $normalized = $normalizeDate($dateStr);
    echo "✅ '$dateStr' → '$normalized'\n";
}

echo "\n📋 Langkah testing:\n";
echo "1. Upload file '$filename' melalui halaman import\n";
echo "2. Verifikasi tanggal berhasil dikonversi dengan benar\n";
echo "3. Cek hasil di database apakah tanggal tersimpan sebagai YYYY-MM-DD\n";
echo "4. Verifikasi semua 4 data karyawan berhasil diimport\n";
