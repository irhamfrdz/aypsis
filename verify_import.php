<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== Verifikasi Data di Database ===\n\n";

$total = DaftarTagihanKontainerSewa::count();
echo "Total records: $total\n\n";

if ($total > 0) {
    // Group by vendor
    echo "Breakdown by Vendor:\n";
    $byVendor = DaftarTagihanKontainerSewa::selectRaw('vendor, count(*) as total')
        ->groupBy('vendor')
        ->get();
    foreach ($byVendor as $v) {
        echo "  {$v->vendor}: {$v->total} records\n";
    }

    echo "\nBreakdown by Size:\n";
    $bySize = DaftarTagihanKontainerSewa::selectRaw('size, count(*) as total')
        ->groupBy('size')
        ->get();
    foreach ($bySize as $s) {
        echo "  {$s->size}ft: {$s->total} records\n";
    }

    echo "\nBreakdown by Status:\n";
    $byStatus = DaftarTagihanKontainerSewa::selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->get();
    foreach ($byStatus as $st) {
        echo "  {$st->status}: {$st->total} records\n";
    }

    echo "\n=== Sample Records (5 pertama) ===\n";
    $samples = DaftarTagihanKontainerSewa::orderBy('id', 'asc')->limit(5)->get();

    foreach ($samples as $s) {
        echo "\nID: {$s->id}\n";
        echo "  Vendor: {$s->vendor}\n";
        echo "  Kontainer: {$s->nomor_kontainer}\n";
        echo "  Size: {$s->size}ft\n";
        echo "  Tanggal: {$s->tanggal_awal} s/d {$s->tanggal_akhir}\n";
        echo "  Periode: {$s->periode} hari\n";
        echo "  Masa: {$s->masa}\n";
        echo "  Tarif/hari: Rp " . number_format($s->tarif, 0, ',', '.') . "\n";
        echo "  DPP: Rp " . number_format($s->dpp, 0, ',', '.') . "\n";
        echo "  PPN (11%): Rp " . number_format($s->ppn, 0, ',', '.') . "\n";
        echo "  PPH (2%): Rp " . number_format($s->pph, 0, ',', '.') . "\n";
        echo "  Grand Total: Rp " . number_format($s->grand_total, 0, ',', '.') . "\n";
        echo "  Status: {$s->status}\n";
    }

    echo "\n=== Financial Summary ===\n";
    $totalDpp = DaftarTagihanKontainerSewa::sum('dpp');
    $totalPpn = DaftarTagihanKontainerSewa::sum('ppn');
    $totalPph = DaftarTagihanKontainerSewa::sum('pph');
    $totalGrandTotal = DaftarTagihanKontainerSewa::sum('grand_total');

    echo "Total DPP: Rp " . number_format($totalDpp, 0, ',', '.') . "\n";
    echo "Total PPN: Rp " . number_format($totalPpn, 0, ',', '.') . "\n";
    echo "Total PPH: Rp " . number_format($totalPph, 0, ',', '.') . "\n";
    echo "Total Grand Total: Rp " . number_format($totalGrandTotal, 0, ',', '.') . "\n";

} else {
    echo "Tidak ada data di database.\n";
}

echo "\n=== Selesai ===\n";
