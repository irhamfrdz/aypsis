<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$q1 = App\Models\SuratJalan::doesntHave('tandaTerima')
    ->whereNotIn('status', ['cancelled', 'draft'])
    ->where('status_pembayaran_uang_jalan', 'dibayar')
    ->count();

$q2 = App\Models\SuratJalan::whereNull('tanggal_tanda_terima')
    ->whereNotIn('status', ['cancelled', 'draft'])
    ->where('status_pembayaran_uang_jalan', 'dibayar')
    ->count();

echo json_encode(['doesntHave' => $q1, 'whereNull' => $q2]);
