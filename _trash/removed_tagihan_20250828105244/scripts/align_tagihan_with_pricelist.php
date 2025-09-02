<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;

$rows = \DB::table('tagihan_kontainer_sewa')->get();
$updated = 0;
foreach ($rows as $r) {
    $tanggal = $r->tanggal_harga_awal ? Carbon::parse($r->tanggal_harga_awal)->format('Y-m-d') : null;
    if (!$tanggal) continue;

    $master = \DB::table('master_pricelist_sewa_kontainers')
        ->where('vendor', $r->vendor)
        ->where('ukuran_kontainer', $r->ukuran_kontainer)
        ->where('tanggal_harga_awal', '<=', $tanggal)
        ->where(function($q) use ($tanggal) {
            $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $tanggal);
        })
        ->orderBy('tanggal_harga_awal', 'desc')
        ->first();

    if ($master) {
        \DB::table('tagihan_kontainer_sewa')->where('id', $r->id)->update([
            'tarif' => $master->tarif,
            'harga' => $master->harga,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
        $updated++;
        echo "updated id={$r->id} set tarif={$master->tarif} harga={$master->harga}\n";
    } else {
        // If tarif currently numeric, set tarif='Custom' to match controller behavior
        if (is_numeric($r->tarif)) {
            \DB::table('tagihan_kontainer_sewa')->where('id', $r->id)->update([
                'tarif' => 'Custom',
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
            $updated++;
            echo "updated id={$r->id} set tarif=Custom\n";
        }
    }
}

echo "total updated: $updated\n";
