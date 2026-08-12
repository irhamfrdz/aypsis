<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fix F/E Data Surat Jalan Tarik Kosong Batam ===\n\n";

// Count old format records
$countE = DB::table('surat_jalan_tarik_kosong_batams')->where('f_e', 'E')->count();
$countF = DB::table('surat_jalan_tarik_kosong_batams')->where('f_e', 'F')->count();

echo "Data lama ditemukan:\n";
echo "  - f_e = 'E' : {$countE} records\n";
echo "  - f_e = 'F' : {$countF} records\n\n";

if ($countE === 0 && $countF === 0) {
    echo "Tidak ada data yang perlu diperbaiki. Semua sudah menggunakan format baru.\n";
    exit(0);
}

// Update 'E' -> 'Empty'
$updatedE = DB::table('surat_jalan_tarik_kosong_batams')
    ->where('f_e', 'E')
    ->update(['f_e' => 'Empty']);

echo "Updated {$updatedE} records: 'E' -> 'Empty'\n";

// Update 'F' -> 'Full'
$updatedF = DB::table('surat_jalan_tarik_kosong_batams')
    ->where('f_e', 'F')
    ->update(['f_e' => 'Full']);

echo "Updated {$updatedF} records: 'F' -> 'Full'\n\n";

echo "=== Selesai! Total {$updatedE} + {$updatedF} = " . ($updatedE + $updatedF) . " records diperbaiki ===\n";
