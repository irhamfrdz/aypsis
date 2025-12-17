<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fix Tahun 0025 menjadi 2025 di Table Kontainers ===\n\n";

// Cari kontainer dengan tahun 0025
$wrongYearKontainers = DB::table('kontainers')
    ->where(function($q) {
        $q->where('tanggal_mulai_sewa', 'LIKE', '0025-%')
          ->orWhere('tanggal_selesai_sewa', 'LIKE', '0025-%')
          ->orWhere('tanggal_mulai_sewa', 'LIKE', '0021-%')
          ->orWhere('tanggal_selesai_sewa', 'LIKE', '0021-%');
    })
    ->get();

echo "Ditemukan " . $wrongYearKontainers->count() . " kontainer dengan tahun salah\n\n";

$fixed = 0;

foreach ($wrongYearKontainers as $kontainer) {
    echo "Kontainer: {$kontainer->nomor_seri_gabungan}\n";
    echo "  Tanggal Mulai Lama: {$kontainer->tanggal_mulai_sewa}\n";
    echo "  Tanggal Selesai Lama: " . ($kontainer->tanggal_selesai_sewa ?? 'NULL') . "\n";
    
    $updates = [];
    
    // Fix tanggal_mulai_sewa
    if ($kontainer->tanggal_mulai_sewa && strpos($kontainer->tanggal_mulai_sewa, '0025-') === 0) {
        $newDate = str_replace('0025-', '2025-', $kontainer->tanggal_mulai_sewa);
        $updates['tanggal_mulai_sewa'] = $newDate;
        echo "  Tanggal Mulai Baru: {$newDate}\n";
    }
    
    if ($kontainer->tanggal_mulai_sewa && strpos($kontainer->tanggal_mulai_sewa, '0021-') === 0) {
        $newDate = str_replace('0021-', '2021-', $kontainer->tanggal_mulai_sewa);
        $updates['tanggal_mulai_sewa'] = $newDate;
        echo "  Tanggal Mulai Baru: {$newDate}\n";
    }
    
    // Fix tanggal_selesai_sewa
    if ($kontainer->tanggal_selesai_sewa && strpos($kontainer->tanggal_selesai_sewa, '0025-') === 0) {
        $newDate = str_replace('0025-', '2025-', $kontainer->tanggal_selesai_sewa);
        $updates['tanggal_selesai_sewa'] = $newDate;
        echo "  Tanggal Selesai Baru: {$newDate}\n";
    }
    
    if ($kontainer->tanggal_selesai_sewa && strpos($kontainer->tanggal_selesai_sewa, '0021-') === 0) {
        $newDate = str_replace('0021-', '2021-', $kontainer->tanggal_selesai_sewa);
        $updates['tanggal_selesai_sewa'] = $newDate;
        echo "  Tanggal Selesai Baru: {$newDate}\n";
    }
    
    if (!empty($updates)) {
        DB::table('kontainers')
            ->where('id', $kontainer->id)
            ->update($updates);
        
        echo "  [UPDATED]\n";
        $fixed++;
    }
    
    echo "\n";
}

echo "===================================================================\n";
echo "Total diperbaiki: {$fixed} kontainer\n";
echo "===================================================================\n";

// Jalankan sync ulang untuk kontainer yang diperbaiki
if ($fixed > 0) {
    echo "\nSekarang akan sync ulang tagihan untuk kontainer yang diperbaiki...\n";
    echo "Jalankan: php sync_tagihan_kontainer_from_master.php --clean\n";
}
