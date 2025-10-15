<?php

/**
 * Backup script untuk data vendor sebelum update
 * Jalankan script ini sebelum menjalankan update vendor
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

try {
    // Inisialisasi Laravel app
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "====================================================\n";
    echo "BACKUP DATA VENDOR SEBELUM UPDATE\n";
    echo "====================================================\n";
    echo "Waktu backup: " . now()->format('Y-m-d H:i:s') . "\n\n";

    // Buat nama file backup dengan timestamp
    $backupFileName = 'vendor_backup_' . now()->format('Y_m_d_H_i_s') . '.sql';
    $backupPath = __DIR__ . '/storage/backups/';

    // Buat direktori backup jika belum ada
    if (!is_dir($backupPath)) {
        mkdir($backupPath, 0755, true);
    }

    $fullBackupPath = $backupPath . $backupFileName;

    // Query untuk backup data yang memiliki invoice_vendor
    $dataWithVendor = DaftarTagihanKontainerSewa::whereNotNull('invoice_vendor')
        ->where('invoice_vendor', '!=', '')
        ->get();

    echo "Ditemukan " . $dataWithVendor->count() . " record yang memiliki invoice vendor\n";

    // Buat SQL backup
    $backupSql = "-- Backup data vendor invoice dari daftar_tagihan_kontainer_sewa\n";
    $backupSql .= "-- Generated on: " . now()->format('Y-m-d H:i:s') . "\n";
    $backupSql .= "-- Total records: " . $dataWithVendor->count() . "\n\n";

    $backupSql .= "-- Untuk restore, jalankan UPDATE query di bawah ini:\n\n";

    foreach ($dataWithVendor as $record) {
        $invoiceVendor = addslashes($record->invoice_vendor);
        $tanggalVendor = $record->tanggal_vendor ? $record->tanggal_vendor->format('Y-m-d') : 'NULL';

        if ($tanggalVendor === 'NULL') {
            $backupSql .= "UPDATE daftar_tagihan_kontainer_sewa SET invoice_vendor = '{$invoiceVendor}', tanggal_vendor = NULL WHERE id = {$record->id};\n";
        } else {
            $backupSql .= "UPDATE daftar_tagihan_kontainer_sewa SET invoice_vendor = '{$invoiceVendor}', tanggal_vendor = '{$tanggalVendor}' WHERE id = {$record->id};\n";
        }
    }

    // Simpan backup ke file
    file_put_contents($fullBackupPath, $backupSql);

    echo "✅ Backup berhasil disimpan ke: {$fullBackupPath}\n";
    echo "📁 Ukuran file: " . number_format(filesize($fullBackupPath) / 1024, 2) . " KB\n";

    // Buat juga backup dalam format CSV untuk referensi
    $csvBackupPath = $backupPath . 'vendor_backup_' . now()->format('Y_m_d_H_i_s') . '.csv';
    $csvFile = fopen($csvBackupPath, 'w');

    // Header CSV
    fputcsv($csvFile, [
        'id',
        'nomor_kontainer',
        'vendor',
        'invoice_vendor',
        'tanggal_vendor',
        'periode',
        'masa',
        'grand_total'
    ]);

    // Data CSV
    foreach ($dataWithVendor as $record) {
        fputcsv($csvFile, [
            $record->id,
            $record->nomor_kontainer,
            $record->vendor,
            $record->invoice_vendor,
            $record->tanggal_vendor ? $record->tanggal_vendor->format('Y-m-d') : '',
            $record->periode,
            $record->masa,
            $record->grand_total
        ]);
    }

    fclose($csvFile);

    echo "✅ Backup CSV disimpan ke: {$csvBackupPath}\n";
    echo "📁 Ukuran file CSV: " . number_format(filesize($csvBackupPath) / 1024, 2) . " KB\n";

    echo "\n====================================================\n";
    echo "BACKUP SELESAI\n";
    echo "====================================================\n";
    echo "Total record di-backup: " . $dataWithVendor->count() . "\n";
    echo "File SQL backup: {$backupFileName}\n";
    echo "File CSV backup: vendor_backup_" . now()->format('Y_m_d_H_i_s') . ".csv\n";
    echo "Lokasi: {$backupPath}\n";
    echo "\n🔔 PENTING: Simpan file backup ini sebelum menjalankan update!\n";
    echo "====================================================\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR saat backup: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
