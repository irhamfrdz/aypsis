<?php
// scripts/find_pranota_pivots.php
// Usage: php scripts/find_pranota_pivots.php [PRANOTA_ID]
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$prId = isset($argv[1]) ? intval($argv[1]) : 20;

try {
    $pivots = DB::select('select * from tagihan_kontainer_sewa_kontainers where tagihan_id = ?', [$prId]);
    $kontainerIds = array_map(function($r){ return $r->kontainer_id; }, $pivots);
    $related = [];
    if (!empty($kontainerIds)) {
        $placeholders = implode(',', array_fill(0, count($kontainerIds), '?'));
        $related = DB::select("select * from tagihan_kontainer_sewa_kontainers where kontainer_id in ($placeholders) and tagihan_id != ? order by tagihan_id desc", array_merge($kontainerIds, [$prId]));
    }

    echo json_encode(['prId' => $prId, 'pivots' => $pivots, 'related' => $related], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
