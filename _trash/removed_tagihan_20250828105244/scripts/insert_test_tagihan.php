<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Boot Laravel app
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = Carbon::parse('2025-08-28');
$e = $s->copy()->addMonths(1)->subDay();
$masa = $s->locale('id')->isoFormat('D MMMM') . ' - ' . $e->locale('id')->isoFormat('D MMMM');

$id = DB::table('tagihan_kontainer_sewa')->insertGetId([
    'vendor' => 'TEST',
    'tarif' => 'Bulanan',
    'ukuran_kontainer' => '20',
    'harga' => 0,
    'dpp' => 0,
    'ppn' => 0,
    'pph' => 0,
    'grand_total' => 0,
    'nomor_kontainer' => 'T1',
    'tanggal_harga_awal' => $s->toDateString(),
    'periode' => 1,
    'nomor_pranota' => 'PRTEST',
    'keterangan' => 'test',
    'masa' => $masa,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Inserted id={$id} masa={$masa}\n";
