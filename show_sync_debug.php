<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Models\Kontainer;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

$options = getopt('', ['container:','help']);
$containerNo = $options['container'] ?? null;
if (!$containerNo || isset($options['help'])) {
    echo "Usage: php show_sync_debug.php --container=AMFU8640522\n";
    exit(1);
}

echo "Debug sync for container: $containerNo\n\n";

$kontainer = Kontainer::where('nomor_seri_gabungan', $containerNo)->first();
if (!$kontainer) {
    echo "Container not found: $containerNo\n";
    exit(1);
}

echo "Kontainer record:\n";
echo "  Nomor: " . ($kontainer->nomor_seri_gabungan ?? $kontainer->getNomorKontainerAttribute()) . "\n";
echo "  Vendor: " . ($kontainer->vendor ?? '-') . "\n";
echo "  Ukuran: " . ($kontainer->ukuran ?? '-') . "\n";
echo "  Tanggal Mulai: " . ($kontainer->tanggal_mulai_sewa ?? 'NULL') . "\n";
echo "  Tanggal Selesai: " . ($kontainer->tanggal_selesai_sewa ?? 'NULL') . "\n";
echo "  Status: " . ($kontainer->status ?? '-') . "\n\n";

try {
    $tanggalMulai = Carbon::parse($kontainer->tanggal_mulai_sewa);
} catch (Exception $e) {
    echo "Invalid tanggal_mulai_sewa: " . $kontainer->tanggal_mulai_sewa . "\n";
    exit(1);
}
$tanggalSelesai = $kontainer->tanggal_selesai_sewa ? Carbon::parse($kontainer->tanggal_selesai_sewa) : null;
$endDate = $tanggalSelesai ?: Carbon::now()->endOfMonth();

$currentStart = $tanggalMulai->copy()->startOfMonth();
$period = 1;
$expected = [];
while ($currentStart->lte($endDate)) {
    $currentEnd = $currentStart->copy()->endOfMonth();
    if ($tanggalSelesai && $currentEnd->gt($tanggalSelesai)) {
        $currentEnd = $tanggalSelesai->copy();
    }
    $expected[$period] = [
        'periode' => $period,
        'tanggal_awal' => $currentStart->toDateString(),
        'tanggal_akhir' => $currentEnd->toDateString(),
        'masa' => $currentStart->format('j M Y') . ' - ' . $currentEnd->format('j M Y')
    ];
    $currentStart->addMonth();
    $period++;
}

echo "Expected periods (" . count($expected) . "):\n";
foreach ($expected as $p => $info) {
    echo "  Periode {$p}: " . $info['tanggal_awal'] . " - " . $info['tanggal_akhir'] . " (" . $info['masa'] . ")\n";
}

echo "\nExisting tagihan rows for $containerNo:\n";
$rows = DaftarTagihanKontainerSewa::where('nomor_kontainer', $containerNo)->orderBy('periode')->get();
if ($rows->isEmpty()) {
    echo "  (none)\n";
} else {
    foreach ($rows as $r) {
        echo sprintf("  id %d | periode %s | %s - %s | masa: %s | dpp: %s | tarif: %s | status_pranota: %s | pranota_id: %s\n",
            $r->id, $r->periode ?? '(null)', $r->tanggal_awal ?? '(null)', $r->tanggal_akhir ?? '(null)', $r->masa ?? '-', number_format($r->dpp ?? 0,2), $r->tarif ?? '-', $r->status_pranota ?? '-', $r->pranota_id ?? '-');
    }
}

// Compare
$existingPeriods = $rows->pluck('periode','periode')->toArray();
$extra = [];
$missing = [];
foreach ($expected as $p => $info) {
    if (!in_array($p, $existingPeriods) && !array_key_exists($p, $existingPeriods)) {
        $missing[$p] = $info;
    }
}
foreach ($rows as $r) {
    if (!isset($expected[$r->periode])) $extra[] = $r;
}

echo "\nSummary comparison:\n";
echo "  Expected periods: " . count($expected) . "\n";
echo "  Existing tagihan count: " . $rows->count() . "\n";
echo "  Missing periods: " . count($missing) . "\n";
if (count($missing)) {
    echo "    -> " . implode(', ', array_keys($missing)) . "\n";
}
echo "  Extra rows: " . count($extra) . "\n";
if (count($extra)) {
    echo "    -> ids: " . implode(', ', array_map(function($r){return $r->id;}, $extra)) . "\n";
}

// Show overlapping or out-of-bound rows
$overlaps = [];
foreach ($rows as $r) {
    if ($r->tanggal_awal && $r->tanggal_akhir) {
        if ($r->tanggal_awal < $tanggalMulai->toDateString() || ($tanggalSelesai && $r->tanggal_akhir > $tanggalSelesai->toDateString())) {
            $overlaps[] = $r;
        }
    }
}

echo "\nOut-of-range rows count: " . count($overlaps) . "\n";
if (count($overlaps)) {
    foreach ($overlaps as $o) {
        echo sprintf("  - id %d periode %s (%s - %s)\n", $o->id, $o->periode ?? '-', $o->tanggal_awal, $o->tanggal_akhir);
    }
}

echo "\nDone.\n";
?>