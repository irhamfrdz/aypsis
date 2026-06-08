<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$invs = DB::table('invoice_aktivitas_lain')
    ->whereIn('id', [577, 578])
    ->get();
foreach ($invs as $inv) {
    echo "ID: {$inv->id}, Nomor: {$inv->nomor_invoice}, Total: {$inv->total}, Created: {$inv->created_at}, Updated: {$inv->updated_at}\n";
}
