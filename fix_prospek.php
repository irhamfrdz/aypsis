<?php

// Load sistem inti Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;

echo "Mencari data prospek yang perlu diperbaiki...\n";

// Cari prospek yang berisi koma atau terlewat menjadi null
$prospeks = Prospek::with('suratJalan')->where(function($q) {
    $q->where('nomor_kontainer', 'like', '%,%')
      ->orWhereNull('nomor_kontainer');
})->where('keterangan', 'like', '%Kontainer #%')->get();

$count = 0;

foreach ($prospeks as $prospek) {
    if (!$prospek->suratJalan) continue;
    
    $sj = $prospek->suratJalan;
    $kontainerArray = array_map('trim', explode(',', $sj->no_kontainer ?? ''));
    $sealArray = array_map('trim', explode(',', $sj->no_seal ?? ''));
    
    $index = 0;
    
    // Cari index urutan kontainer dari keterangan atau nomor surat jalan
    if (preg_match('/-(\d+)$/', $prospek->no_surat_jalan, $m)) {
        $index = intval($m[1]) - 1;
    } elseif (preg_match('/Kontainer #(\d+)/', $prospek->keterangan, $m)) {
        $index = intval($m[1]) - 1;
    }
    
    if (isset($kontainerArray[$index])) {
        $prospek->nomor_kontainer = $kontainerArray[$index];
        $prospek->no_seal = $sealArray[$index] ?? null;
        
        // Perbaiki jika format nomor surat jalan belum ada akhiran -1 atau -2
        if (!preg_match('/-(\d+)$/', $prospek->no_surat_jalan)) {
            $prospek->no_surat_jalan = $sj->no_surat_jalan . '-' . ($index + 1);
        }
        
        $prospek->save();
        echo "Berhasil memperbaiki Prospek ID {$prospek->id} menjadi Kontainer: {$prospek->nomor_kontainer}\n";
        $count++;
    }
}

echo "Selesai! Total {$count} baris prospek telah berhasil diperbaiki.\n";
