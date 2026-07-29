<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$masterKapal = \App\Models\MasterKapal::first();
$namaKapal = $masterKapal->nama_kapal;
$kapalClean = strtolower(str_replace('.', '', $namaKapal));

$pergerakanKapals = \App\Models\PergerakanKapal::where(function ($q) use ($namaKapal, $kapalClean) {
    $q->where('nama_kapal', $namaKapal)
        ->orWhereRaw("LOWER(REPLACE(nama_kapal, '.', '')) LIKE ?", ["%{$kapalClean}%"]);
})
    ->whereNotNull('voyage')
    ->where('voyage', '!=', '')
    ->select('voyage as no_voyage', 'tujuan_tujuan as pelabuhan_tujuan', 'tujuan_asal as pelabuhan_asal', 'tanggal_berangkat')
    ->orderBy('voyage', 'desc')
    ->get();

$groupedPergerakan = $pergerakanKapals->groupBy('no_voyage')->map(function ($items, $voyage) {
    $first = $items->first();
    return [
        'no_voyage' => $voyage,
        'pelabuhan_tujuan' => $first->pelabuhan_tujuan,
        'pelabuhan_asal' => $first->pelabuhan_asal,
        'pelabuhan_muat' => $first->pelabuhan_asal,
        'pelabuhan_bongkar' => $first->pelabuhan_tujuan,
        'tanggal_berangkat' => $first->tanggal_berangkat ? $first->tanggal_berangkat->format('Y-m-d') : null,
        'total_kontainer' => 0,
        'summary_bongkar' => '',
        'summary_muat' => '',
    ];
});

echo "Found " . $groupedPergerakan->count() . " voyages from pergerakan_kapal.\n";
