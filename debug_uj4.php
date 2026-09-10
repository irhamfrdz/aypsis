<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$uj2 = App\Models\UangJalan::where('surat_jalan_bongkaran_id', 852)->first();
if ($uj2) {
    echo "UJ Bongkaran Tanggal: " . $uj2->tanggal_uang_jalan . "\n";
} else {
    echo "No UJ with SJB ID 852\n";
}
