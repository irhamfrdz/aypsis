<?php
/**
 * Script untuk Export semua data tanda_terima_lcl ke CSV
 * 
 * Cara menjalankan:
 * php export_tanda_terima_lcl.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== EXPORT TANDA TERIMA LCL KE CSV ===\n";
    echo "Mengambil data dari database...\n\n";

    // Ambil semua data dari tabel tanda_terima_lcl (semua kolom)
    $data = DB::table('tanda_terima_lcl')
        ->orderBy('id', 'asc')
        ->get();
    
    // Ambil nama kolom dari first record atau dari database
    $columns = [];
    if ($data->count() > 0) {
        $columns = array_keys((array) $data->first());
    } else {
        // Jika tidak ada data, ambil struktur kolom dari database
        $columnInfo = DB::select('DESCRIBE tanda_terima_lcl');
        $columns = array_column((array) $columnInfo, 'Field');
    }

    $totalRecords = $data->count();
    echo "Total data ditemukan: {$totalRecords} records\n\n";

    if ($totalRecords === 0) {
        echo "❌ Tidak ada data untuk diexport.\n";
        exit(1);
    }

    // Nama file CSV dengan timestamp
    $filename = 'tanda_terima_lcl_export_' . date('Y-m-d_His') . '.csv';
    $filepath = __DIR__ . '/' . $filename;

    // Buka file untuk ditulis
    $file = fopen($filepath, 'w');

    // Tulis BOM untuk Excel UTF-8 support
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

    // Header CSV (menggunakan nama kolom dari database)
    $headers = array_map(function($col) {
        return ucwords(str_replace('_', ' ', $col));
    }, $columns);

    fputcsv($file, $headers);

    // Tulis data
    $rowCount = 0;
    foreach ($data as $row) {
        $rowData = [];
        foreach ($columns as $column) {
            $rowData[] = $row->{$column};
        }
        fputcsv($file, $rowData);
        $rowCount++;

        // Progress indicator setiap 100 records
        if ($rowCount % 100 === 0) {
            echo "Progress: {$rowCount}/{$totalRecords} records...\n";
        }
    }

    fclose($file);

    echo "\n✅ Export berhasil!\n";
    echo "File disimpan di: {$filepath}\n";
    echo "Total baris data: {$rowCount} records\n";
    echo "Ukuran file: " . number_format(filesize($filepath) / 1024, 2) . " KB\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
