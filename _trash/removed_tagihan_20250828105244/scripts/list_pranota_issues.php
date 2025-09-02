<?php
// scripts/list_pranota_issues.php
// Usage: php scripts/list_pranota_issues.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $bad = DB::select("select id, group_code, periode, tanggal_harga_awal, vendor, created_at from tagihan_kontainer_sewa where tarif = 'Pranota' and group_code like 'PR%' order by id desc limit 200");
    echo json_encode(['count' => count($bad), 'rows' => $bad], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
