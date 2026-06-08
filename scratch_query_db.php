<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$details = \App\Models\PranotaUangRitSupirDetail::where('supir_nama', 'like', '%ABDULLAH%')->get();
foreach ($details as $d) {
    echo "No Pranota: " . $d->no_pranota . "\n";
    echo " - Total Uang: " . $d->total_uang_supir . "\n";
    echo " - Hutang: " . $d->hutang . "\n";
    echo " - Tabungan: " . $d->tabungan . "\n";
    echo " - BPJS: " . $d->bpjs . "\n";
    echo " - Adjustment: " . $d->adjustment . "\n";
    echo " - Grand Total in DB: " . $d->grand_total . "\n";
    echo " - Recalculated: " . ($d->total_uang_supir - $d->hutang - $d->tabungan - $d->bpjs + $d->adjustment) . "\n";
    echo " - Created: " . $d->created_at . "\n";
    echo " - Updated: " . $d->updated_at . "\n";
}
