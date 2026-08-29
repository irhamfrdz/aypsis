<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sjb = \App\Models\SuratJalanBongkaran::find(905);
if ($sjb) {
    $sjb->status_pembayaran_uang_rit = 'belum_bayar';
    $sjb->save();
    echo 'Fixed data SS0003220';
}
