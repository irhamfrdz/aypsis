<?php
// scripts/check_kontainer_pranota.php
// Usage: php scripts/check_kontainer_pranota.php [KONTAINER_NUMBER]
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$no = $argv[1] ?? null;
if (!$no) {
    echo json_encode(['error' => 'Provide kontainer number as first arg']);
    exit(1);
}

try {
    // Try matches on common fields
    $konts = DB::select('select id, nomor_seri_gabungan, awalan_kontainer, nomor_seri_kontainer, akhiran_kontainer from kontainers where nomor_seri_gabungan = ? or nomor_seri_kontainer = ? limit 20', [$no, $no]);

    // If not found, try matching concatenation
    if (empty($konts)) {
        $all = DB::select('select id, nomor_seri_gabungan, awalan_kontainer, nomor_seri_kontainer, akhiran_kontainer from kontainers');
        foreach ($all as $k) {
            $concat = trim(($k->awalan_kontainer ?? '') . ($k->nomor_seri_kontainer ?? '') . ($k->akhiran_kontainer ?? ''));
            if ($concat === $no) $konts[] = $k;
        }
    }

    $kontainerIds = array_map(function($k){ return $k->id; }, $konts);

    $pivots = [];
    $linkedTagihans = [];
    if (!empty($kontainerIds)) {
        $placeholders = implode(',', array_fill(0, count($kontainerIds), '?'));
        $pivots = DB::select("select tks.* from tagihan_kontainer_sewa_kontainers tks where tks.kontainer_id in ($placeholders)", $kontainerIds);

        // join to tagihan to show tarif/nomor_pranota
        $linkedTagihans = DB::select("select t.* from tagihan_kontainer_sewa t join tagihan_kontainer_sewa_kontainers tk on t.id = tk.tagihan_id where tk.kontainer_id in ($placeholders) order by t.id desc", $kontainerIds);
    }

    // Also check if any pranota exists directly for the vendor/date that includes this kontainer
    // (covered by linkedTagihans but include explicit filter)
    $pranotaLinked = [];
    if (!empty($kontainerIds)) {
        $pranotaLinked = DB::select("select t.*, tk.id as pivot_id from tagihan_kontainer_sewa t join tagihan_kontainer_sewa_kontainers tk on t.id = tk.tagihan_id where tk.kontainer_id in ($placeholders) and t.tarif = 'Pranota'", $kontainerIds);
    }

    echo json_encode([
        'query_for' => $no,
        'found_kontainers' => $konts,
        'kontainer_ids' => $kontainerIds,
        'pivots' => $pivots,
        'linked_tagihans' => $linkedTagihans,
        'pranota_linked' => $pranotaLinked,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
