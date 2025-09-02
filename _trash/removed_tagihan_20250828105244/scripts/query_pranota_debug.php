<?php
// scripts/query_pranota_debug.php
// Usage: php scripts/query_pranota_debug.php [PRANOTA_NUMBER]
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$pr = $argv[1] ?? 'PR20250827033229';

try {
    $pranota = null;
    // Try to locate pranota by exact id first, then by group_code
    if (ctype_digit((string)$pr)) {
        $pranota = DB::select('select id, group_code, periode, tanggal_harga_awal, vendor, created_at from tagihan_kontainer_sewa where id = ? limit 1', [(int)$pr]);
    }
    if (empty($pranota)) {
        $pranota = DB::select('select id, group_code, periode, tanggal_harga_awal, vendor, created_at from tagihan_kontainer_sewa where group_code = ? limit 1', [$pr]);
    }
    $vendor = $pranota[0]->vendor ?? null;

    $samples_same_vendor = [];
    if ($vendor) {
        $samples_same_vendor = DB::select('select id, group_code, periode, tanggal_harga_awal, vendor from tagihan_kontainer_sewa where vendor = ? and tarif != "Pranota" and group_code is not null and group_code != "-" order by id desc limit 10', [$vendor]);
    }

    $samples_any = DB::select('select id, group_code, periode, tanggal_harga_awal, vendor from tagihan_kontainer_sewa where tarif != "Pranota" and group_code is not null and group_code != "-" order by id desc limit 10');

    echo json_encode([
        'pranota' => $pranota,
        'samples_same_vendor' => $samples_same_vendor,
        'samples_any' => $samples_any,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
