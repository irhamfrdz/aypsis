<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$inv = App\Models\InvoiceAktivitasLain::where('nomor_invoice', 'IAL-01-26-000011')->first();
if ($inv) {
    echo json_encode($inv->toArray(), JSON_PRETTY_PRINT);
} else {
    echo "Not found";
}
