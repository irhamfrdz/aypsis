<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

echo "=== Checking Invoices Kontainer Sewa Table ===\n";

// Check existing invoices with MS-1125 pattern
echo "\n1. Existing MS-1125 invoices:\n";
$invoices = DB::table('invoices_kontainer_sewa')
    ->where('nomor_invoice', 'LIKE', 'MS-1125%')
    ->orderBy('nomor_invoice')
    ->get(['id', 'nomor_invoice', 'tanggal_invoice', 'status']);

foreach($invoices as $invoice) {
    echo "   - ID: {$invoice->id}, Invoice: {$invoice->nomor_invoice}, Date: {$invoice->tanggal_invoice}, Status: {$invoice->status}\n";
}

echo "\n2. Current nomor_terakhir for MS module:\n";
$nomorTerakhir = DB::table('nomor_terakhir')->where('modul', 'MS')->first();
if ($nomorTerakhir) {
    echo "   - Current: {$nomorTerakhir->nomor_terakhir}\n";
    echo "   - Next will be: " . sprintf('MS-1125-%07d', $nomorTerakhir->nomor_terakhir + 1) . "\n";
} else {
    echo "   - No record found for MS module\n";
}

echo "\n3. Checking for specific invoice MS-1125-0000003:\n";
$specific = DB::table('invoices_kontainer_sewa')
    ->where('nomor_invoice', 'MS-1125-0000003')
    ->first();

if ($specific) {
    echo "   - FOUND: Invoice MS-1125-0000003 exists with ID: {$specific->id}\n";
    echo "   - Status: {$specific->status}\n";
    echo "   - Created: {$specific->created_at}\n";
} else {
    echo "   - NOT FOUND: Invoice MS-1125-0000003 does not exist\n";
}

echo "\n=== End Check ===\n";