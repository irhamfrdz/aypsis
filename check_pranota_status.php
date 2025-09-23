<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;

echo "Checking Pranota records:\n";
$pranotaList = Pranota::all();

foreach ($pranotaList as $pranota) {
    echo "ID: {$pranota->id}, Status: {$pranota->status}, No Invoice: {$pranota->no_invoice}\n";
}

echo "\nTotal Pranota: " . $pranotaList->count() . "\n";
echo "Belum Lunas: " . Pranota::where('status', 'Belum Lunas')->count() . "\n";
echo "Unpaid: " . Pranota::where('status', 'unpaid')->count() . "\n";
