<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuratJalanBongkaran;
use App\Models\UangJalanBongkaran;

// Cari surat jalan bongkaran dengan nomor SS 0002058
$suratJalan = SuratJalanBongkaran::where('nomor_surat_jalan', 'SS 0002058')->first();

if ($suratJalan) {
    echo "Surat Jalan Bongkaran ditemukan:\n";
    echo "ID: {$suratJalan->id}\n";
    echo "Nomor: {$suratJalan->nomor_surat_jalan}\n";
    echo "Supir: {$suratJalan->supir}\n";
    echo "No Plat: {$suratJalan->no_plat}\n";
    echo "\n";
    
    // Cek apakah ada uang jalan untuk surat jalan ini
    $uangJalan = UangJalanBongkaran::where('surat_jalan_bongkaran_id', $suratJalan->id)->get();
    
    if ($uangJalan->count() > 0) {
        echo "Uang Jalan Bongkaran yang terkait:\n";
        foreach ($uangJalan as $uj) {
            echo "- ID: {$uj->id}, Nomor: {$uj->nomor_uang_jalan}, Status: {$uj->status}, Deleted At: " . ($uj->deleted_at ?? 'NULL') . "\n";
        }
    } else {
        echo "Tidak ada uang jalan bongkaran yang terkait dengan surat jalan ini.\n";
    }
    
    // Cek dengan soft deletes
    $uangJalanWithTrashed = UangJalanBongkaran::withTrashed()
        ->where('surat_jalan_bongkaran_id', $suratJalan->id)
        ->get();
    
    if ($uangJalanWithTrashed->count() > 0) {
        echo "\nUang Jalan Bongkaran (termasuk yang di-soft delete):\n";
        foreach ($uangJalanWithTrashed as $uj) {
            echo "- ID: {$uj->id}, Nomor: {$uj->nomor_uang_jalan}, Status: {$uj->status}, Deleted At: " . ($uj->deleted_at ?? 'NULL') . "\n";
        }
    }
} else {
    echo "Surat Jalan Bongkaran dengan nomor 'SS 0002058' tidak ditemukan.\n";
}
