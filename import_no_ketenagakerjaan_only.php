<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== IMPORT NO KETENAGAKERJAAN SAJA ===\n";

$csvFile = 'C:\Users\amanda\Downloads\datakaryawan.csv';

if (!file_exists($csvFile)) {
    echo "❌ File CSV tidak ditemukan: $csvFile\n";
    exit;
}

$csvContent = file_get_contents($csvFile);
$delimiter = ';';

$lines = explode("\n", trim($csvContent));
$headers = str_getcsv(array_shift($lines), $delimiter);

echo "Headers found: " . implode(', ', $headers) . "\n\n";

$imported = 0;
$errors = 0;
$skipped = 0;

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
        $nik = trim($rowData['nik'] ?? '');
        $no_ketenagakerjaan = trim($rowData['no_ketenagakerjaan'] ?? '');

        if (empty($nik)) {
            echo "❌ Line " . ($lineNumber + 2) . ": NIK kosong\n";
            $errors++;
            continue;
        }

        // Cari karyawan berdasarkan NIK
        $karyawan = \App\Models\Karyawan::where('nik', $nik)->first();

        if (!$karyawan) {
            // Jika belum ada, buat record baru dengan data minimal
            $karyawan = \App\Models\Karyawan::create([
                'nik' => $nik,
                'nama_lengkap' => trim($rowData['nama_lengkap'] ?? ''),
                'nama_panggilan' => trim($rowData['nama_panggilan'] ?? ''),
                'no_ketenagakerjaan' => $no_ketenagakerjaan,
                'divisi' => trim($rowData['divisi'] ?? ''),
                'pekerjaan' => trim($rowData['pekerjaan'] ?? '')
            ]);
            echo "✅ BARU - NIK: {$karyawan->nik} | {$karyawan->nama_lengkap} | No Ketenagakerjaan: " . ($no_ketenagakerjaan ?: 'KOSONG') . "\n";
        } else {
            // Update no_ketenagakerjaan jika sudah ada
            if (!empty($no_ketenagakerjaan)) {
                $karyawan->update(['no_ketenagakerjaan' => $no_ketenagakerjaan]);
                echo "✅ UPDATE - NIK: {$karyawan->nik} | {$karyawan->nama_lengkap} | No Ketenagakerjaan: {$no_ketenagakerjaan}\n";
            } else {
                echo "⏭️ SKIP - NIK: {$karyawan->nik} | {$karyawan->nama_lengkap} | No Ketenagakerjaan kosong\n";
                $skipped++;
                continue;
            }
        }

        $imported++;

    } catch (\Exception $e) {
        echo "❌ Error NIK {$nik}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== HASIL IMPORT ===\n";
echo "✅ Berhasil diimport/update: $imported record\n";
echo "⏭️ Dilewati (no_ketenagakerjaan kosong): $skipped record\n";
echo "❌ Error: $errors record\n";

// Verifikasi data yang memiliki no_ketenagakerjaan
echo "\n=== VERIFIKASI NO KETENAGAKERJAAN ===\n";
$withKetenagakerjaan = \App\Models\Karyawan::whereNotNull('no_ketenagakerjaan')
    ->where('no_ketenagakerjaan', '!=', '')
    ->orderBy('nik')
    ->get();

echo "Total karyawan dengan no_ketenagakerjaan: " . $withKetenagakerjaan->count() . "\n\n";

foreach ($withKetenagakerjaan as $k) {
    echo "NIK: {$k->nik} | Nama: {$k->nama_lengkap} | No Ketenagakerjaan: {$k->no_ketenagakerjaan}\n";
}
