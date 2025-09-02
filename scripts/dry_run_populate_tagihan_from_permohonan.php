<?php
require __DIR__ . '/../vendor/autoload.php';
// Boot Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permohonan;
use App\Models\TagihanKontainerSewa;
use Illuminate\Support\Collection;

echo "Dry-run: populate tagihan_kontainer_sewa from Permohonan (status Selesai)\n";

$permohonan = Permohonan::with(['kontainers','checkpoints'])->where('status','Selesai')->get();
if ($permohonan->isEmpty()) {
    echo "No completed permohonan found.\n";
    exit(0);
}

$summary = [];
foreach ($permohonan as $p) {
    // derive vendor same as controller: vendor_perusahaan or kontainer pemilik_kontainer
    $vendor = $p->vendor_perusahaan ?: ($p->kontainers->first()->pemilik_kontainer ?? null);
    if (!$vendor) {
        $summary[] = [
            'permohonan_id' => $p->id,
            'vendor' => null,
            'nomor_kontainer' => implode(', ', $p->kontainers->pluck('nomor_kontainer')->filter()->values()->all()),
            'tanggal_harga_awal' => null,
            'dpp' => $p->dpp ?? 0,
            'ppn' => $p->ppn ?? 0,
            'pph' => $p->pph ?? 0,
            'grand_total' => $p->grand_total ?? 0,
        ];
        continue;
    }
    $konts = $p->kontainers->pluck('nomor_kontainer')->filter()->values()->all();
    $kontList = implode(', ', $konts);
    $checkpointDates = $p->checkpoints->pluck('tanggal_checkpoint')->filter()->all();
    $tanggal_awal = count($checkpointDates) ? min($checkpointDates) : null;
    // Compute financials roughly using existing fields on permohonan if available
    $dpp = $p->dpp ?? 0;
    $ppn = $p->ppn ?? 0;
    $pph = $p->pph ?? 0;
    $grand = $p->grand_total ?? ($dpp + $ppn - $pph);

    $summary[] = [
        'permohonan_id' => $p->id,
        'vendor' => $vendor,
        'nomor_kontainer' => $kontList,
        'tanggal_harga_awal' => $tanggal_awal,
        'dpp' => $dpp,
        'ppn' => $ppn,
        'pph' => $pph,
        'grand_total' => $grand,
    ];
}

echo "Found " . count($summary) . " permohonan(s) to convert. Sample output:\n\n";
foreach ($summary as $s) {
    echo "Permohonan #{$s['permohonan_id']}: vendor={$s['vendor']} kontainers={$s['nomor_kontainer']} tanggal_awal={$s['tanggal_harga_awal']} DPP={$s['dpp']} PPN={$s['ppn']} PPH={$s['pph']} GRAND={$s['grand_total']}\n";
}

echo "\nDry-run complete. No DB changes made.\n";
