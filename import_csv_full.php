<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== IMPORT DATA KARYAWAN DARI CSV ===\n";

$csvFile = 'C:\Users\amanda\Downloads\datakaryawan.csv';

if (!file_exists($csvFile)) {
    echo "❌ File CSV tidak ditemukan: $csvFile\n";
    exit;
}

$csvContent = file_get_contents($csvFile);

// Detect delimiter
$delimiters = [';', ',', "\t"];
$delimiter = ';'; // Default karena dari CSV terlihat menggunakan semicolon

echo "Using delimiter: ';'\n";

$lines = explode("\n", trim($csvContent));
$headers = str_getcsv(array_shift($lines), $delimiter);

echo "Headers found: " . implode(', ', $headers) . "\n\n";

$imported = 0;
$errors = 0;

foreach ($lines as $lineNumber => $line) {
    if (empty(trim($line))) continue;

    $data = str_getcsv($line, $delimiter);

    if (count($data) !== count($headers)) {
        echo "❌ Line " . ($lineNumber + 2) . ": Column count mismatch\n";
        $errors++;
        continue;
    }

    $rowData = array_combine($headers, $data);

    try {
        // Map CSV columns to database columns
        $karyawanData = [
            'nik' => $rowData['nik'] ?? '',
            'nama_lengkap' => $rowData['nama_lengkap'] ?? '',
            'nama_panggilan' => $rowData['nama_panggilan'] ?? '',
            'email' => $rowData['email'] ?? '',
            'tempat_lahir' => $rowData['tempat_lahir'] ?? '',
            'tanggal_lahir' => $rowData['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $rowData['jenis_kelamin'] ?? '',
            'agama' => $rowData['agama'] ?? '',
            'status_perkawinan' => $rowData['status_perkawinan'] ?? '',
            'no_hp' => $rowData['no_hp'] ?? '',
            'ktp' => $rowData['ktp'] ?? '',
            'kk' => $rowData['kk'] ?? '',
            'divisi' => $rowData['divisi'] ?? '',
            'pekerjaan' => $rowData['pekerjaan'] ?? '',
            'tanggal_masuk' => $rowData['tanggal_masuk'] ?? null,
            'tanggal_berhenti' => $rowData['tanggal_berhenti'] ?? null,
            'tanggal_masuk_sebelumnya' => $rowData['tanggal_masuk_sebelumnya'] ?? null,
            'tanggal_berhenti_sebelumnya' => $rowData['tanggal_berhenti_sebelumnya'] ?? null,
            'nik_supervisor' => $rowData['nik_supervisor'] ?? '',
            'supervisor' => $rowData['supervisor'] ?? '',
            'cabang' => $rowData['cabang'] ?? '',
            'plat' => $rowData['plat'] ?? '',
            'alamat' => $rowData['alamat'] ?? '',
            'rt_rw' => $rowData['rt_rw'] ?? '',
            'kelurahan' => $rowData['kelurahan'] ?? '',
            'kecamatan' => $rowData['kecamatan'] ?? '',
            'kabupaten' => $rowData['kabupaten'] ?? '',
            'provinsi' => $rowData['provinsi'] ?? '',
            'kode_pos' => $rowData['kode_pos'] ?? '',
            'alamat_lengkap' => $rowData['alamat_lengkap'] ?? '',
            'nama_bank' => $rowData['nama_bank'] ?? '',
            'akun_bank' => $rowData['akun_bank'] ?? '',
            'atas_nama' => $rowData['atas_nama'] ?? '',
            'status_pajak' => $rowData['status_pajak'] ?? '',
            'jkn' => $rowData['jkn'] ?? '',
            'no_ketenagakerjaan' => $rowData['no_ketenagakerjaan'] ?? '', // PENTING!
            'catatan' => $rowData['catatan'] ?? ''
        ];

        // Clean empty values
        $karyawanData = array_filter($karyawanData, function($value) {
            return !empty(trim($value));
        });

        if (empty($karyawanData['nik'])) {
            echo "❌ Line " . ($lineNumber + 2) . ": NIK kosong\n";
            $errors++;
            continue;
        }

        $karyawan = \App\Models\Karyawan::updateOrCreate(
            ['nik' => $karyawanData['nik']],
            $karyawanData
        );

        echo "✅ NIK: {$karyawan->nik} | {$karyawan->nama_lengkap} | No Ketenagakerjaan: " . ($karyawan->no_ketenagakerjaan ?: 'KOSONG') . "\n";
        $imported++;

    } catch (\Exception $e) {
        echo "❌ Error NIK {$rowData['nik']}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== HASIL IMPORT ===\n";
echo "✅ Berhasil diimport: $imported record\n";
echo "❌ Error: $errors record\n";

// Verifikasi beberapa data yang ada no_ketenagakerjaan
echo "\n=== VERIFIKASI NO KETENAGAKERJAAN ===\n";
$withKetenagakerjaan = \App\Models\Karyawan::whereNotNull('no_ketenagakerjaan')
    ->where('no_ketenagakerjaan', '!=', '')
    ->limit(10)
    ->get();

foreach ($withKetenagakerjaan as $k) {
    echo "NIK: {$k->nik} | Nama: {$k->nama_lengkap} | No Ketenagakerjaan: {$k->no_ketenagakerjaan}\n";
}
