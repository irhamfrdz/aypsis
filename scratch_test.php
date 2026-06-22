<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$no_pranota = 'PURK-06-26-000024';

echo "=== PRANOTA MASTER ===\n";
$master = \App\Models\PranotaUangRitKenek::where('no_pranota', $no_pranota)->first();
if ($master) {
    echo "ID: {$master->id}\n";
    echo "No. Pranota: {$master->no_pranota}\n";
    echo "Kenek Nama: '{$master->kenek_nama}'\n";
    echo "No. Surat Jalan: '{$master->no_surat_jalan}'\n";
} else {
    echo "Master not found!\n";
}

echo "\n=== PRANOTA DETAILS ===\n";
$details = \App\Models\PranotaUangRitKenekDetail::where('no_pranota', $no_pranota)->get();
foreach ($details as $d) {
    echo "ID: {$d->id}, Kenek Nama: '{$d->kenek_nama}', Total: {$d->total_uang_kenek}\n";
}
