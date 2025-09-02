<?php
// quick inspect script: prints last pembayaran and attached tagihan statuses
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PembayaranPranotaTagihanKontainer;
use App\Models\TagihanKontainerSewa;

$p = PembayaranPranotaTagihanKontainer::with('tagihans')->orderBy('id', 'desc')->first();
if (!$p) {
    echo "No pembayaran found\n";
    exit(0);
}

echo "Pembayaran id={$p->id} nomor={$p->nomor_pembayaran} total={$p->total_pembayaran} penyesuaian={$p->penyesuaian}\n";
foreach ($p->tagihans as $t) {
    echo "- Tagihan id={$t->id} harga={$t->harga} status_pembayaran={$t->status_pembayaran} keterangan={$t->keterangan}\n";
}
