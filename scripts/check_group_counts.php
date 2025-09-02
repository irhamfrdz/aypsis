<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vendor = $argv[1] ?? null;
$tanggal = $argv[2] ?? null; // expecting Y-m-d

if (!$vendor || !$tanggal) {
    echo "Usage: php check_group_counts.php VENDOR YYYY-MM-DD\n";
    exit(1);
}

try {
    echo "Checking group counts for vendor=$vendor date=$tanggal\n";

    // index-style distinct container count
    $indexCountRow = \DB::table('permohonans')
        ->join('permohonan_kontainers', 'permohonans.id', '=', 'permohonan_kontainers.permohonan_id')
        ->where('vendor_perusahaan', $vendor)
        ->whereDate('tanggal_memo', $tanggal)
        ->selectRaw('COUNT(DISTINCT permohonan_kontainers.kontainer_id) as distinct_count')
        ->first();

    $indexCount = $indexCountRow->distinct_count ?? 0;

    // detail-style raw sum of counts per permohonan (pre-fix behavior)
    $permohonans = \DB::table('permohonans')
        ->where('vendor_perusahaan', $vendor)
        ->whereDate('tanggal_memo', $tanggal)
        ->get();

    $permIds = $permohonans->pluck('id')->toArray();

    $detailRaw = 0;
    $perPermCounts = [];
    if (!empty($permIds)) {
        $rows = \DB::table('permohonan_kontainers')
            ->whereIn('permohonan_id', $permIds)
            ->get();

        $grouped = [];
        foreach ($rows as $r) {
            $grouped[$r->permohonan_id][] = $r->kontainer_id;
        }

        foreach ($grouped as $pid => $list) {
            $cnt = count($list);
            $perPermCounts[$pid] = $cnt;
            $detailRaw += $cnt;
        }
    }

    // find duplicate kontainer ids across the group
    $allKontainers = \DB::table('permohonan_kontainers')
        ->whereIn('permohonan_id', $permIds)
        ->pluck('kontainer_id')
        ->toArray();

    $dupCounts = [];
    foreach ($allKontainers as $kid) {
        $dupCounts[$kid] = ($dupCounts[$kid] ?? 0) + 1;
    }
    $duplicates = array_filter($dupCounts, function($c){ return $c > 1; });

    echo "index distinct count: $indexCount\n";
    echo "detail raw sum (per-perm sum): $detailRaw\n";
    echo "permohonan counts breakdown:\n";
    foreach ($perPermCounts as $pid => $c) {
        echo "  permohonan $pid => $c kontainer(s)\n";
    }

    if (!empty($duplicates)) {
        echo "\nDuplicate kontainer ids across permohonans (kontainer_id => occurrences):\n";
        foreach ($duplicates as $kid => $c) {
            echo "  $kid => $c\n";
            // show sample pivot rows for this kontainer
            $samples = \DB::table('permohonan_kontainers')->where('kontainer_id', $kid)->limit(20)->get();
            foreach ($samples as $s) {
                echo sprintf("    pivot id=%d permohonan_id=%s kontainer_id=%s\n", $s->id, $s->permohonan_id, $s->kontainer_id);
            }
        }
    } else {
        echo "No duplicate kontainer ids found across this group's permohonans.\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
