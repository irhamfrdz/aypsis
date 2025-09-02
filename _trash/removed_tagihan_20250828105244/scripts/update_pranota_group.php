<?php
// scripts/update_pranota_group.php
// Usage: php scripts/update_pranota_group.php [PRANOTA_ID_or_NOMOR] [NEW_GROUP_CODE]
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$target = $argv[1] ?? 'PR20250827033229';
$newGroup = $argv[2] ?? 'A016';

try {
    DB::beginTransaction();

    // Try to locate by numeric id first
    if (ctype_digit((string)$target)) {
        $pr = DB::select('select id, group_code, periode, vendor from tagihan_kontainer_sewa where id = ? limit 1', [(int)$target]);
    } else {
        $pr = DB::select('select id, group_code, periode, vendor from tagihan_kontainer_sewa where group_code = ? limit 1', [$target]);
    }

    if (empty($pr)) {
        echo json_encode(['error' => 'Pranota not found', 'target' => $target]);
        DB::rollBack();
        exit(1);
    }

    $before = $pr[0];

    $updated = DB::update('update tagihan_kontainer_sewa set group_code = ?, updated_at = now() where id = ?', [$newGroup, $before->id]);

    $after = DB::select('select id, group_code, periode, vendor from tagihan_kontainer_sewa where id = ? limit 1', [$before->id]);

    DB::commit();

    echo json_encode(['before' => $before, 'affected' => $updated, 'after' => $after[0]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    DB::rollBack();
    echo json_encode(['error' => $e->getMessage()]);
    exit(1);
}
