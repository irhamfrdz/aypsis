<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Memulai pembaruan nama supir NUR -> NUR CECE...\n\n";

$newName = 'NUR CECE';
$tables = ['surat_jalans', 'surat_jalan_bongkarans'];

foreach ($tables as $table) {
    echo "[{$table}]\n";

    $preview1 = DB::table($table)
        ->whereRaw("LOWER(supir) = 'nur'")
        ->whereRaw('BINARY supir != ?', [$newName])
        ->select('id', 'supir', 'supir2')
        ->get();

    $preview2 = DB::table($table)
        ->whereRaw("LOWER(supir2) = 'nur'")
        ->whereRaw('BINARY supir2 != ?', [$newName])
        ->select('id', 'supir', 'supir2')
        ->get();

    if ($preview1->isEmpty() && $preview2->isEmpty()) {
        echo "  Tidak ada data dengan nama 'NUR' (exact) ditemukan.\n\n";
        continue;
    }

    echo "  Data yang akan diubah:\n";
    foreach ($preview1 as $r) {
        echo "    [supir]  id={$r->id} supir='{$r->supir}'\n";
    }
    foreach ($preview2 as $r) {
        echo "    [supir2] id={$r->id} supir2='{$r->supir2}'\n";
    }

    $count1 = DB::table($table)
        ->whereRaw("LOWER(supir) = 'nur'")
        ->whereRaw('BINARY supir != ?', [$newName])
        ->update(['supir' => $newName]);

    $count2 = DB::table($table)
        ->whereRaw("LOWER(supir2) = 'nur'")
        ->whereRaw('BINARY supir2 != ?', [$newName])
        ->update(['supir2' => $newName]);

    echo "  Total supir diperbarui : {$count1} baris.\n";
    echo "  Total supir2 diperbarui: {$count2} baris.\n\n";
}

echo "Selesai.\n";
