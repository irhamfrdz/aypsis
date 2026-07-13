<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$manifests = \App\Models\Manifest::where('no_voyage', 'SA12JP26')
    ->where(function ($q) {
        $q->where('tipe_kontainer', 'CARGO')
          ->orWhereNull('size_kontainer')
          ->orWhere('size_kontainer', '');
    })->get(['nama_barang', 'kuantitas', 'tipe_kontainer', 'size_kontainer', 'pelabuhan_tujuan', 'pelabuhan_bongkar'])->toArray();
echo json_encode($manifests, JSON_PRETTY_PRINT);
