<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mnf = App\Models\Manifest::where('nomor_kontainer', 'AYPU2425772')->where('no_voyage', 'SR15JB26')->first();
print_r([
    'nama_barang_di_manifest' => $mnf->nama_barang ?? '',
    'created_at' => $mnf->created_at ? $mnf->created_at->toDateTimeString() : '',
    'updated_at' => $mnf->updated_at ? $mnf->updated_at->toDateTimeString() : ''
]);
