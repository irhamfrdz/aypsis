<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Memulai pembaruan nama supir...\n";

$targetNames = ['nur cece', 'nur', 'cece', 'NUR', 'NUR  CECE'];
$newName = 'NUR CECE';

$totalSupir = 0;
$totalSupir2 = 0;

foreach ($targetNames as $name) {
    $count1 = DB::table('surat_jalans')
        ->whereRaw('LOWER(supir) = ?', [strtolower($name)])
        ->whereRaw('BINARY supir != ?', [$newName])
        ->update(['supir' => $newName]);
    if ($count1 > 0) {
        echo "supir '{$name}' -> '{$newName}': {$count1} baris diperbarui.\n";
        $totalSupir += $count1;
    }

    $count2 = DB::table('surat_jalans')
        ->whereRaw('LOWER(supir2) = ?', [strtolower($name)])
        ->whereRaw('BINARY supir2 != ?', [$newName])
        ->update(['supir2' => $newName]);
    if ($count2 > 0) {
        echo "supir2 '{$name}' -> '{$newName}': {$count2} baris diperbarui.\n";
        $totalSupir2 += $count2;
    }
}

echo "\nTotal supir diperbarui : {$totalSupir} baris.\n";
echo "Total supir2 diperbarui: {$totalSupir2} baris.\n";
echo "Selesai.\n";
