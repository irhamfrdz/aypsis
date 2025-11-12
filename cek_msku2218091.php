<?php
// cek_msku2218091.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$items = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'MSKU2218091')
    ->where('periode', 4)
    ->get();

foreach($items as $i) {
    echo "ID: {$i->id}\n";
    echo "Nomor: {$i->nomor_kontainer}\n";
    echo "Periode: {$i->periode}\n";
    echo "Tanggal Awal: {$i->tanggal_awal}\n";
    echo "Tanggal Akhir: {$i->tanggal_akhir}\n";
    echo "Masa (field): {$i->masa}\n";
    echo "DPP: {$i->dpp}\n";
    echo "Size: {$i->size}\n";
    echo "Tarif: {$i->tarif}\n";
    
    // Hitung selisih hari yang benar
    if (!empty($i->tanggal_awal) && !empty($i->tanggal_akhir)) {
        $start = \Carbon\Carbon::parse($i->tanggal_awal);
        $end = \Carbon\Carbon::parse($i->tanggal_akhir);
        $days = $start->diffInDays($end) + 1; // +1 karena inclusive (hitung kedua tanggal)
        echo "Calculated Days: {$days}\n";
        
        $correctDpp = 22522.53 * $days;
        echo "Correct DPP should be: " . number_format($correctDpp, 2) . "\n";
    }
    echo "\n";
}
