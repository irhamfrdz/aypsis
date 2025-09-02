<?php
// Safe pivot deleter: usage: php delete_pivot.php <pivot_id>
// Bootstraps Laravel and deletes the pivot row from tagihan_kontainer_sewa_kontainers in a transaction.

if ($argc < 2) {
    echo "Usage: php delete_pivot.php <pivot_id>\n";
    exit(1);
}
$pivotId = intval($argv[1]);
if ($pivotId <= 0) {
    echo "Invalid pivot id\n";
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var \Illuminate\Database\Connection $db */
$db = $app->make('db');

$before = $db->table('tagihan_kontainer_sewa_kontainers')->where('id', $pivotId)->first();
if (!$before) {
    echo json_encode(['status' => 'not_found', 'pivot_id' => $pivotId], JSON_PRETTY_PRINT) . "\n";
    exit(0);
}

echo "Found pivot:\n" . json_encode($before, JSON_PRETTY_PRINT) . "\n";

try {
    $db->beginTransaction();
    $deleted = $db->table('tagihan_kontainer_sewa_kontainers')->where('id', $pivotId)->delete();
    $db->commit();
    echo json_encode(['status' => 'deleted', 'pivot_id' => $pivotId, 'deleted' => (bool)$deleted], JSON_PRETTY_PRINT) . "\n";
} catch (\Exception $e) {
    try { $db->rollBack(); } catch (\Exception $er) {}
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_PRETTY_PRINT) . "\n";
    exit(1);
}

// show any remaining pivots for the same kontainer id (if available)
if (isset($before->kontainer_id)) {
    $remaining = $db->table('tagihan_kontainer_sewa_kontainers')->where('kontainer_id', $before->kontainer_id)->get();
    echo "Remaining pivots for kontainer_id={$before->kontainer_id}:\n" . json_encode($remaining, JSON_PRETTY_PRINT) . "\n";
}

// show current linked tagihan rows for that kontainer
if (isset($before->kontainer_id)) {
    $linked = $db->table('tagihan_kontainer_sewa')
        ->join('tagihan_kontainer_sewa_kontainers', 'tagihan_kontainer_sewa.id', '=', 'tagihan_kontainer_sewa_kontainers.tagihan_id')
        ->where('tagihan_kontainer_sewa_kontainers.kontainer_id', $before->kontainer_id)
        ->select('tagihan_kontainer_sewa.*')
        ->get();
    echo "Linked tagihan rows for kontainer_id={$before->kontainer_id}:\n" . json_encode($linked, JSON_PRETTY_PRINT) . "\n";
}

echo "Done.\n";
