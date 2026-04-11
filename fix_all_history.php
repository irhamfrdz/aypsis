<?php

use App\Models\HistoryKontainer;
use App\Models\Gudang;
use Illuminate\Support\Facades\DB;

$histories = HistoryKontainer::whereNull('asal_gudang_id')
    ->orderBy('id', 'asc')
    ->get();

$fixedCount = 0;

foreach ($histories as $hk) {
    $asalId = null;

    // Strategy 1: Check previous history for this container
    $previous = HistoryKontainer::where('nomor_kontainer', $hk->nomor_kontainer)
        ->where('id', '<', $hk->id)
        ->orderBy('id', 'desc')
        ->first();

    if ($previous && $previous->gudang_id) {
        $asalId = $previous->gudang_id;
    }

    // Strategy 2: If no previous history, try to parse keterangan
    if (!$asalId && $hk->keterangan) {
        if (preg_match('/(?:Pemindahan dari|dari)\s+(.*?)(?:\s+\(.*?\))?\s+(?:ke tujuan baru|ke|menuju)/i', $hk->keterangan, $m)) {
            $gudangName = trim($m[1]);
            $g = Gudang::where('nama_gudang', $gudangName)->first();
            if ($g) {
                $asalId = $g->id;
            }
        }
    }

    if ($asalId) {
        $hk->asal_gudang_id = $asalId;
        $hk->save();
        $fixedCount++;
        echo "Fixed HK ID {$hk->id} with Asal Gudang ID {$asalId}\n";
    }
}

echo "Total records fixed: $fixedCount\n";
