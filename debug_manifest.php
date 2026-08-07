<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nk = App\Models\NaikKapal::where('nomor_kontainer', 'AYPU2425772')->where('no_voyage', 'SR15JB26')->first();
$prospek = $nk ? $nk->prospek : null;
$tt = $prospek ? $prospek->tandaTerima : null;

print_r([
    'nk_id' => $nk->id ?? '',
    'nk_jenis_barang' => $nk->jenis_barang ?? '',
    'tt_exists' => $tt ? 'yes' : 'no',
    'tt_dimensi_items' => $tt->dimensi_items ?? '',
    'tt_nama_barang' => $tt->nama_barang ?? '',
    'tt_dimensi_details' => $tt->dimensi_details ?? '',
    'prospek_id' => $prospek->id ?? ''
]);
