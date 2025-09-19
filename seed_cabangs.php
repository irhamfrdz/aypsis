<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Cabang;

// Check if cabangs already exist
if (Cabang::count() > 0) {
    echo "Cabang records already exist:\n";
    Cabang::all()->each(function($cabang) {
        echo "- {$cabang->nama_cabang}\n";
    });
    exit;
}

// Create sample cabang records
$cabangs = [
    ['nama_cabang' => 'JKT', 'keterangan' => 'Jakarta'],
    ['nama_cabang' => 'BTM', 'keterangan' => 'Batam'],
    ['nama_cabang' => 'PNG', 'keterangan' => 'Pangkal Pinang'],
    ['nama_cabang' => 'SBY', 'keterangan' => 'Surabaya'],
    ['nama_cabang' => 'BDG', 'keterangan' => 'Bandung'],
];

foreach ($cabangs as $data) {
    Cabang::create($data);
    echo "Created cabang: {$data['nama_cabang']}\n";
}

echo "\nTotal cabang records: " . Cabang::count() . "\n";
