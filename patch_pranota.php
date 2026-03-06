<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PranotaInvoiceVendorSupir;

$pranotas = PranotaInvoiceVendorSupir::where('pph', '>', 0)->get();
foreach($pranotas as $p) {
    if ($p->grand_total > 0 && $p->total_nominal != $p->grand_total) {
        $oldTotal = $p->total_nominal;
        $p->total_nominal = $p->grand_total;
        $p->save();
        echo "Updated {$p->no_pranota} (Old: {$oldTotal}, New: {$p->total_nominal})\n";
    }
}
echo "Done.\n";
