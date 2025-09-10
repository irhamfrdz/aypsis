<?php
/**
 * Test script untuk import Excel
 * Membuat file Excel sederhana untuk testing import functionality
 */

// Buat data test untuk Excel
$testData = [
    // Header
    ['nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','cabang','nik_supervisor','supervisor'],

    // Data test 1
    ['3201234567890123', 'John', 'John Doe', 'B 1234 XYZ', 'john.doe@test.com', '3201234567890123', '3301234567890123', 'Jl. Test No. 123', '001/002', 'Kelurahan Test', 'Kecamatan Test', 'Kabupaten Test', 'Provinsi Test', '12345', 'Jl. Test No. 123, RT 001/RW 002, Kelurahan Test', 'Jakarta', '1990-01-15', '081234567890', 'L', 'Belum Kawin', 'Islam', 'IT', 'Programmer', '2024-01-01', '', '', '', 'Test import Excel', 'TK0', 'Bank BCA', 'Cabang Jakarta', '1234567890', 'John Doe', '0001234567890', '12345678901234567', 'Jakarta', '', ''],

    // Data test 2
    ['3301234567890124', 'Jane', 'Jane Smith', 'B 5678 ABC', 'jane.smith@test.com', '3301234567890124', '3401234567890124', 'Jl. Test No. 456', '003/004', 'Kelurahan Smith', 'Kecamatan Smith', 'Kabupaten Smith', 'Provinsi Smith', '54321', 'Jl. Test No. 456, RT 003/RW 004, Kelurahan Smith', 'Surabaya', '1992-05-20', '081987654321', 'P', 'Kawin', 'Kristen', 'Finance', 'Accountant', '2024-02-01', '', '', '', 'Test import Excel 2', 'K1', 'Bank Mandiri', 'Cabang Surabaya', '0987654321', 'Jane Smith', '0009876543210', '98765432109876543', 'Surabaya', '', '']
];

// Buat CSV file untuk testing (Excel-compatible)
$filename = 'test_karyawan_excel_import.csv';
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

echo "✅ File test Excel berhasil dibuat: $filename\n";
echo "📁 Lokasi: " . realpath($filename) . "\n";
echo "📊 Berisi " . (count($testData) - 1) . " data karyawan test\n\n";

echo "🔍 Preview isi file:\n";
echo "===================\n";
$content = file_get_contents($filename);
echo substr($content, 3); // Skip BOM

echo "\n\n📋 Langkah testing:\n";
echo "1. Buka file '$filename' dengan Excel untuk verifikasi\n";
echo "2. Akses halaman import: /master/karyawan/import\n";
echo "3. Upload file ini untuk test import Excel\n";
echo "4. Cek hasil import di database\n";
