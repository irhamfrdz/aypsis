<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NomorTerakhir;

$record = NomorTerakhir::where('modul', 'TEST_MODULE')->first();

if ($record) {
    echo "✅ Test record found:\n";
    echo "Modul: {$record->modul}\n";
    echo "Nomor Terakhir (raw): {$record->nomor_terakhir}\n";
    echo "Nomor Terakhir (formatted): " . str_pad($record->nomor_terakhir, 6, '0', STR_PAD_LEFT) . "\n";
    echo "Type: " . gettype($record->nomor_terakhir) . "\n";
} else {
    echo "❌ Test record not found\n";
}
