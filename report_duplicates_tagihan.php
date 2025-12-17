<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Running duplicate report for daftar_tagihan_kontainer_sewa...\n\n";

$groups = DB::select("SELECT nomor_kontainer, periode, COUNT(*) AS cnt FROM daftar_tagihan_kontainer_sewa GROUP BY nomor_kontainer, periode HAVING cnt > 1 ORDER BY cnt DESC LIMIT 200");

$totalGroups = count($groups);
echo "Duplicate groups found: $totalGroups (showing up to 200)\n\n";

$summaryTotalRows = 0;
foreach ($groups as $g) {
    echo "Container: {$g->nomor_kontainer} | Periode: {$g->periode} | Count: {$g->cnt}\n";
    $rows = DB::select("SELECT id, tanggal_awal, tanggal_akhir, masa, tarif, dpp, status_pranota, pranota_id, created_at FROM daftar_tagihan_kontainer_sewa WHERE nomor_kontainer = ? AND periode = ? ORDER BY id", [$g->nomor_kontainer, $g->periode]);
    foreach ($rows as $r) {
        echo sprintf("  - id %d | %s - %s | masa: %s | tarif: %s | dpp: %s | status_pranota: %s | pranota_id: %s | created_at: %s\n",
            $r->id, $r->tanggal_awal ?? '(null)', $r->tanggal_akhir ?? '(null)', $r->masa ?? '-', $r->tarif ?? '-', number_format($r->dpp ?? 0,2), $r->status_pranota ?? '-', $r->pranota_id ?? '-', $r->created_at ?? '-');
    }
    echo "\n";
    $summaryTotalRows += $g->cnt;
}

echo "Total duplicate groups: $totalGroups\n";
echo "Total rows involved in these groups (counted): $summaryTotalRows\n";

// Quick overall stat: how many containers have duplicates
$distinctContainers = DB::select("SELECT COUNT(DISTINCT nomor_kontainer) as c FROM (SELECT nomor_kontainer FROM daftar_tagihan_kontainer_sewa GROUP BY nomor_kontainer, periode HAVING COUNT(*)>1) x");
echo "Containers with duplicate periods: " . ($distinctContainers[0]->c ?? 0) . "\n";

echo "\nDone.\n";
?>