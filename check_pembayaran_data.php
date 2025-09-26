<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PembayaranPranota;

echo "Checking pembayaran_pranota records with perbaikan relationships...\n";

$count = PembayaranPranota::whereHas('pranotas', function($query) {
    $query->whereHas('perbaikanKontainer');
})->count();

echo "Found $count records\n";

if ($count > 0) {
    $records = PembayaranPranota::whereHas('pranotas', function($query) {
        $query->whereHas('perbaikanKontainer');
    })->with(['pranotas.perbaikanKontainer'])->take(5)->get();

    foreach ($records as $record) {
        echo "ID: {$record->id}, Nomor: {$record->nomor_pembayaran}, Status: {$record->status}\n";
        echo "Pranota count: " . $record->pranotas->count() . "\n";
    }
}
