<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "  VERIFIKASI DATA MASTER YANG DIIMPOR\n";
echo "==============================================\n\n";

$tables = [
    'master_kegiatans' => ['kode_kegiatan', 'nama_kegiatan', 'status'],
    'master_pricelist_sewa_kontainers' => ['vendor', 'periode_sewa', 'ukuran', 'tarif'],
    'divisis' => ['kode_divisi', 'nama_divisi'],
    'pekerjaans' => ['kode_pekerjaan', 'nama_pekerjaan'],
    'pajaks' => ['kode_pajak', 'nama_pajak', 'persentase'],
    'banks' => ['kode_bank', 'nama_bank'],
    'akun_coa' => ['kode_akun', 'nama_akun'],
    'cabangs' => ['kode_cabang', 'nama_cabang'],
    'tipe_akuns' => ['kode', 'nama'],
    'kode_nomor' => ['kode'],
    'nomor_terakhir' => ['jenis', 'nomor_terakhir'],
];

foreach ($tables as $table => $columns) {
    echo "📋 Tabel: $table\n";
    echo str_repeat("-", 50) . "\n";
    
    try {
        $count = DB::table($table)->count();
        echo "Total Record: $count\n";
        
        if ($count > 0) {
            echo "Sample Data (5 baris pertama):\n";
            $data = DB::table($table)->limit(5)->get();
            
            foreach ($data as $index => $row) {
                $values = [];
                foreach ($columns as $col) {
                    if (property_exists($row, $col)) {
                        $values[] = "$col: " . ($row->$col ?? 'NULL');
                    }
                }
                echo "  " . ($index + 1) . ". " . implode(", ", $values) . "\n";
            }
        } else {
            echo "⚠ Tabel kosong\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "==============================================\n";
echo "  RINGKASAN TOTAL DATA\n";
echo "==============================================\n";

$totalRecords = 0;
foreach (array_keys($tables) as $table) {
    try {
        $count = DB::table($table)->count();
        $totalRecords += $count;
        echo sprintf("%-40s: %5d record\n", $table, $count);
    } catch (Exception $e) {
        echo sprintf("%-40s: ERROR\n", $table);
    }
}

echo str_repeat("=", 50) . "\n";
echo sprintf("%-40s: %5d record\n", "TOTAL", $totalRecords);
echo "==============================================\n";
