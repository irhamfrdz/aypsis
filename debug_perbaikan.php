<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pranota = \App\Models\PranotaPerbaikanKontainer::whereDoesntHave('pembayaranPranotaPerbaikanKontainers')
    ->where('status', 'belum_dibayar')
    ->first();

if ($pranota) {
    echo 'Pranota ID: ' . $pranota->id . PHP_EOL;
    $perbaikans = \App\Models\PerbaikanKontainer::where('pranota_perbaikan_kontainer_id', $pranota->id)->get();
    echo 'Perbaikan count: ' . $perbaikans->count() . PHP_EOL;
    foreach ($perbaikans as $p) {
        echo 'Perbaikan ID: ' . $p->id . ', Kontainer ID: ' . ($p->kontainer_id ?? 'null') . PHP_EOL;
        if ($p->kontainer_id) {
            $kontainer = \App\Models\Kontainer::find($p->kontainer_id);
            echo 'Kontainer Nomor: ' . ($kontainer->nomor_kontainer ?? 'N/A') . PHP_EOL;
        } else {
            echo 'Kontainer ID is null' . PHP_EOL;
        }
    }
} else {
    echo "No pranota found" . PHP_EOL;
}
