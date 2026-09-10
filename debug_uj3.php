<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$uj = App\Models\UangJalan::where('surat_jalan_id', 852)->first();
if ($uj) {
    echo "UJ Tanggal: " . $uj->tanggal_uang_jalan . "\n";
} else {
    echo "No UJ with SJ ID 852\n";
}
