<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apply = in_array('--apply', $argv);

try {
    echo "Clean Tagihan DB script\n";
    echo $apply ? "Running in APPLY mode (will delete)\n" : "Running in DRY-RUN mode (no changes)\n";

    $tagihanCount = \DB::table('tagihan_kontainer_sewa')->count();
    $pivotCount = \DB::table('tagihan_kontainer_sewa_kontainers')->count();

    // Orphan pivots: pivot.tagihan_id not in tagihan table
    $orphanPivots = \DB::table('tagihan_kontainer_sewa_kontainers')
        ->whereNotIn('tagihan_id', function($q){ $q->select('id')->from('tagihan_kontainer_sewa'); })
        ->get();
    $orphanCount = $orphanPivots->count();

    // Pivots referencing non-existent kontainers
    $invalidKontainers = \DB::table('tagihan_kontainer_sewa_kontainers as t')
        ->leftJoin('kontainers as k', 't.kontainer_id', '=', 'k.id')
        ->whereNull('k.id')
        ->select('t.id','t.tagihan_id','t.kontainer_id')
        ->get();
    $invalidKontainersCount = $invalidKontainers->count();

    // Duplicate pivots (same tagihan_id + kontainer_id more than once)
    $dupeRows = \DB::table('tagihan_kontainer_sewa_kontainers')
        ->select('tagihan_id', 'kontainer_id', \DB::raw('COUNT(*) as cnt'), \DB::raw('MIN(id) as keep_id'))
        ->groupBy('tagihan_id','kontainer_id')
        ->having('cnt', '>', 1)
        ->get();
    $dupeCount = $dupeRows->count();

    echo "Summary:\n";
    echo "  tagihan rows: $tagihanCount\n";
    echo "  pivot rows: $pivotCount\n";
    echo "  orphan pivot rows (tagihan missing): $orphanCount\n";
    echo "  pivot rows referencing missing kontainers: $invalidKontainersCount\n";
    echo "  duplicate pivot groups (same tagihan+kontainer): $dupeCount\n";

    if ($orphanCount > 0) {
        echo "\nSample orphan pivots:\n";
        foreach ($orphanPivots as $o) {
            echo sprintf("  pivot id=%d tagihan_id=%s kontainer_id=%s\n", $o->id, $o->tagihan_id, $o->kontainer_id);
        }
        if ($apply) {
            $ids = $orphanPivots->pluck('id')->toArray();
            $deleted = \DB::table('tagihan_kontainer_sewa_kontainers')->whereIn('id', $ids)->delete();
            echo "Deleted $deleted orphan pivot(s).\n";
        } else {
            echo "(dry-run) to delete run with --apply\n";
        }
    }

    if ($invalidKontainersCount > 0) {
        echo "\nPivot rows with missing kontainer references:\n";
        foreach ($invalidKontainers as $r) {
            echo sprintf("  pivot id=%d tagihan_id=%s kontainer_id=%s\n", $r->id, $r->tagihan_id, $r->kontainer_id);
        }
        if ($apply) {
            $ids = $invalidKontainers->pluck('id')->toArray();
            $deleted = \DB::table('tagihan_kontainer_sewa_kontainers')->whereIn('id', $ids)->delete();
            echo "Deleted $deleted pivot(s) referencing missing kontainers.\n";
        } else {
            echo "(dry-run) to delete run with --apply\n";
        }
    }

    if ($dupeCount > 0) {
        echo "\nDuplicate pivot groups (will keep lowest id per group if applied):\n";
        foreach ($dupeRows as $d) {
            $tagihan_id = $d->tagihan_id;
            $kontainer_id = $d->kontainer_id;
            $keepId = $d->keep_id;
            // list duplicates for this group
            $dups = \DB::table('tagihan_kontainer_sewa_kontainers')
                ->where('tagihan_id', $tagihan_id)
                ->where('kontainer_id', $kontainer_id)
                ->orderBy('id')
                ->get();
            echo sprintf("  tagihan %s kontainer %s => keep id %s (others will be removed):\n", $tagihan_id, $kontainer_id, $keepId);
            foreach ($dups as $r) {
                echo sprintf("    pivot id=%d tagihan_id=%s kontainer_id=%s\n", $r->id, $r->tagihan_id, $r->kontainer_id);
            }
            if ($apply) {
                $idsToDelete = collect($dups)->pluck('id')->filter(function($id) use ($keepId){ return $id != $keepId; })->toArray();
                if (!empty($idsToDelete)) {
                    $deleted = \DB::table('tagihan_kontainer_sewa_kontainers')->whereIn('id', $idsToDelete)->delete();
                    echo "    deleted $deleted duplicate pivot(s) for this group\n";
                }
            }
        }
        if (!$apply) echo "(dry-run) to remove duplicates run with --apply\n";
    }

    echo "\nDone.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
