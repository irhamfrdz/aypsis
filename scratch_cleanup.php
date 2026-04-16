<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$trashed = \App\Models\PembayaranAktivitasLain::onlyTrashed()->get();
foreach ($trashed as $p) {
    \App\Models\CoaTransaction::where('nomor_referensi', $p->nomor)->delete();
    echo "Deleted COA transactions for {$p->nomor}\n";
}
