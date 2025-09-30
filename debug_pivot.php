<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$pranota = \App\Models\PranotaPerbaikanKontainer::whereDoesntHave('pembayaranPranotaPerbaikanKontainers')
    ->where('status', 'belum_dibayar')
    ->first();

if ($pranota) {
    echo 'Pranota ID: ' . $pranota->id . PHP_EOL;
    $pivot = DB::table('pranota_perbaikan_kontainer_items')->where('pranota_perbaikan_kontainer_id', $pranota->id)->get();
    echo 'Pivot count: ' . $pivot->count() . PHP_EOL;
    foreach ($pivot as $p) {
        echo 'Perbaikan ID: ' . $p->perbaikan_kontainer_id . PHP_EOL;
        $perbaikan = \App\Models\PerbaikanKontainer::find($p->perbaikan_kontainer_id);
        if ($perbaikan) {
            echo 'Kontainer ID: ' . ($perbaikan->kontainer_id ?? 'null') . PHP_EOL;
            if ($perbaikan->kontainer_id) {
                $kontainer = \App\Models\Kontainer::find($perbaikan->kontainer_id);
                echo 'Kontainer Nomor: ' . ($kontainer->nomor_kontainer ?? 'N/A') . PHP_EOL;
            } else {
                echo 'Kontainer ID is null in perbaikan' . PHP_EOL;
            }
        } else {
            echo 'Perbaikan not found' . PHP_EOL;
        }
    }
} else {
    echo "No pranota found" . PHP_EOL;
}
