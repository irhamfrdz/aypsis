<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Detail Isi Table daftar_tagihan_kontainer_sewa ===\n\n";

// Total data
$total = DB::table('daftar_tagihan_kontainer_sewa')->count();
echo "Total records: {$total}\n\n";

// Group by vendor
echo "1. Breakdown by Vendor:\n";
$vendors = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('vendor', DB::raw('COUNT(*) as count'))
    ->groupBy('vendor')
    ->get();
foreach ($vendors as $v) {
    echo "   - {$v->vendor}: {$v->count} records\n";
}

// Group by status_pranota
echo "\n2. Breakdown by Status Pranota:\n";
$statuses = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('status_pranota', DB::raw('COUNT(*) as count'))
    ->groupBy('status_pranota')
    ->get();
foreach ($statuses as $s) {
    $label = $s->status_pranota ?? 'NULL (Belum masuk pranota)';
    echo "   - {$label}: {$s->count} records\n";
}

// Group by tarif
echo "\n3. Breakdown by Tarif:\n";
$tarifs = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('tarif', DB::raw('COUNT(*) as count'))
    ->groupBy('tarif')
    ->get();
foreach ($tarifs as $t) {
    echo "   - {$t->tarif}: {$t->count} records\n";
}

// Sample detail 1 kontainer
echo "\n4. Detail Sample - Kontainer CBHU3952697:\n";
$details = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'CBHU3952697')
    ->orderBy('periode')
    ->get();

foreach ($details as $d) {
    $status = $d->status_pranota ?? 'belum masuk pranota';
    echo "   Periode {$d->periode}: {$d->tanggal_awal} s/d {$d->tanggal_akhir}\n";
    echo "      Group: {$d->group} | Tarif: {$d->tarif} | DPP: " . number_format($d->dpp, 0, ',', '.') . "\n";
    echo "      Status: {$status}\n";
    if ($d->adjustment) {
        echo "      Adjustment: " . number_format($d->adjustment, 0, ',', '.') . " ({$d->adjustment_note})\n";
    }
    echo "\n";
}

// Periode range
echo "\n5. Range Periode:\n";
$periodeStats = DB::table('daftar_tagihan_kontainer_sewa')
    ->select(DB::raw('MIN(periode) as min'), DB::raw('MAX(periode) as max'))
    ->first();
echo "   Min Periode: {$periodeStats->min}\n";
echo "   Max Periode: {$periodeStats->max}\n";

// Tanggal range
echo "\n6. Range Tanggal:\n";
$tanggalStats = DB::table('daftar_tagihan_kontainer_sewa')
    ->select(DB::raw('MIN(tanggal_awal) as min_awal'), DB::raw('MAX(tanggal_akhir) as max_akhir'))
    ->first();
echo "   Tanggal Awal Terlama: {$tanggalStats->min_awal}\n";
echo "   Tanggal Akhir Terbaru: {$tanggalStats->max_akhir}\n";

echo "\n=== SELESAI ===\n";
