<?php
// sync_checkpoint_from_tanda.php
// Usage:
//   php sync_checkpoint_from_tanda.php            -> dry-run (preview only)
//   php sync_checkpoint_from_tanda.php --apply    -> actually update records
//   php sync_checkpoint_from_tanda.php --limit=10 -> limit number of updates
//   php sync_checkpoint_from_tanda.php --id=123   -> only sync single surat_jalan id

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\SuratJalan;
use App\Models\TandaTerima;

// parse args
$apply = false;
$limit = null;
$onlyId = null;

foreach ($argv as $arg) {
    if ($arg === '--apply') $apply = true;
    if (strpos($arg, '--limit=') === 0) $limit = intval(substr($arg, 8));
    if (strpos($arg, '--id=') === 0) $onlyId = intval(substr($arg, 5));
}

// Find latest TandaTerima per surat_jalan_id with non-null tanggal_checkpoint_supir
$tandaTerimaQuery = TandaTerima::whereNotNull('surat_jalan_id')
    ->whereNotNull('tanggal_checkpoint_supir')
    ->orderByDesc('created_at');

if ($onlyId) {
    $tandaTerimaQuery->where('surat_jalan_id', $onlyId);
}

$allTt = $tandaTerimaQuery->get()->unique('surat_jalan_id')->values();

$totalChecked = 0;
$totalChanged = 0;
$changes = [];

foreach ($allTt as $latest) {
    $sj = SuratJalan::find($latest->surat_jalan_id);
    if (!$sj) continue;

    $newDate = $latest->tanggal_checkpoint_supir ? $latest->tanggal_checkpoint_supir->format('Y-m-d') : null;
    $currentDate = $sj->tanggal_checkpoint ? (new \Carbon\Carbon($sj->tanggal_checkpoint))->format('Y-m-d') : null;

    $totalChecked++;

    if ($newDate !== $currentDate) {
        $changes[] = [
            'surat_jalan_id' => $sj->id,
            'no_surat_jalan' => $sj->no_surat_jalan,
            'current' => $currentDate,
            'new' => $newDate,
            'tanda_terima_id' => $latest->id,
            'tanda_terima_created_at' => $latest->created_at,
        ];

        if ($apply) {
            $sj->tanggal_checkpoint = $newDate;
            $sj->save();
            $totalChanged++;
        }
    }

    if ($limit && count($changes) >= $limit) break;
}

// Print output
echo "========= Sync Checkpoint From Tanda Terima =========\n";
echo "Mode: " . ($apply ? 'APPLY (updates will be made)' : 'DRY-RUN (preview only)') . "\n";
echo "Total Surat Jalan checked: {$totalChecked}\n";
echo "Planned updates: " . count($changes) . "\n";

if (count($changes) > 0) {
    echo "\nList of changes:\n";
    foreach ($changes as $c) {
        echo sprintf("- SJ ID: %d | No: %s | current: %s | new: %s | TandaTerima ID: %s (created: %s)\n",
            $c['surat_jalan_id'], $c['no_surat_jalan'], $c['current'] ?? '-', $c['new'] ?? '-', $c['tanda_terima_id'], $c['tanda_terima_created_at']);
    }
}

if ($apply) {
    echo "\nTotal updated: {$totalChanged}\n";
} else {
    echo "\nNo changes applied. Run with --apply to apply updates.\n";
}

echo "====================================================\n";