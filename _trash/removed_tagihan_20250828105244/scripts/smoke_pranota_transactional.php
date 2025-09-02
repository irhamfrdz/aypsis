<?php
// Smoke test for pranota creation that uses a DB transaction and rolls back at the end
// so nothing is persisted in your MySQL database.

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

try {
    DB::beginTransaction();

    echo "[smoke] Starting transactional smoke test (will rollback)...\n";

    // pick two kontainer ids to test
    $kontainers = DB::table('kontainers')->limit(2)->get();
    if (!$kontainers || $kontainers->count() < 2) {
        echo "[smoke] Need at least 2 kontainers in DB to run the test. Found: " . ($kontainers ? $kontainers->count() : 0) . "\n";
        DB::rollBack();
        exit(1);
    }

    $ids = $kontainers->pluck('id')->toArray();
    echo "[smoke] Using kontainer ids: " . implode(',', $ids) . "\n";

    // compute per-kontainer price (use harga_satuan if present, else try latest non-Pranota tagihan.harga)
    $aggDpp = 0.0; $aggPpn = 0.0; $aggPph = 0.0; $aggGrand = 0.0;
    foreach ($kontainers as $k) {
        $base = null;
        if (isset($k->harga_satuan) && is_numeric($k->harga_satuan)) $base = (float) $k->harga_satuan;
        if ($base === null) {
            $row = DB::table('tagihan_kontainer_sewa')
                ->join('tagihan_kontainer_sewa_kontainers', 'tagihan_kontainer_sewa.id', '=', 'tagihan_kontainer_sewa_kontainers.tagihan_id')
                ->where('tagihan_kontainer_sewa_kontainers.kontainer_id', $k->id)
                ->where('tagihan_kontainer_sewa.tarif', '!=', 'Pranota')
                ->orderBy('tagihan_kontainer_sewa.id', 'desc')
                ->select('tagihan_kontainer_sewa.harga')
                ->first();
            if ($row && isset($row->harga) && is_numeric($row->harga)) $base = (float) $row->harga;
        }
        if ($base === null) $base = 0.0;
        $dpp = round(($base * 11) / 12, 2);
        $ppn = round($dpp * 0.12, 2);
        $pph = round($base * 0.02, 2);
        $grand = round($base + $ppn - $pph, 2);
        $aggDpp += $dpp; $aggPpn += $ppn; $aggPph += $pph; $aggGrand += $grand;
        echo "[smoke] kontainer {$k->id}: base={$base}, dpp={$dpp}, ppn={$ppn}, pph={$pph}, grand={$grand}\n";
    }

    // create a pranota-like tagihan row (but inside transaction so we can rollback)
    $nomor = 'SMK' . date('ymdHis');
    $tagihanId = DB::table('tagihan_kontainer_sewa')->insertGetId([
        'vendor' => 'SMOKE_TEST_VENDOR',
        'tarif' => 'Pranota',
        'ukuran_kontainer' => 'NA',
        'harga' => round($aggGrand,2),
        'dpp' => round($aggDpp,2),
        'ppn' => round($aggPpn,2),
        'pph' => round($aggPph,2),
        'grand_total' => round($aggGrand,2),
        'nomor_kontainer' => implode(',', array_map('strval', $ids)),
        'tanggal_harga_awal' => date('Y-m-d'),
        'group_code' => $nomor,
        'nomor_pranota' => $nomor,
        'is_pranota' => true,
        'keterangan' => 'Smoke test pranota'
    ]);

    echo "[smoke] Created pranota id (transactional): $tagihanId\n";

    foreach ($ids as $kid) {
        DB::table('tagihan_kontainer_sewa_kontainers')->insert([
            'tagihan_id' => $tagihanId,
            'kontainer_id' => $kid,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    $pivotCount = DB::table('tagihan_kontainer_sewa_kontainers')->where('tagihan_id', $tagihanId)->count();
    echo "[smoke] Pivot links created (transactional): $pivotCount\n";

    // read back pranota row
    $p = DB::table('tagihan_kontainer_sewa')->where('id', $tagihanId)->first();
    echo "[smoke] Pranota row (transactional): " . json_encode($p) . "\n";

    // list related original tagihan ids for those kontainers (non-pranota)
    $related = DB::table('tagihan_kontainer_sewa_kontainers')
        ->whereIn('kontainer_id', $ids)
        ->pluck('tagihan_id')
        ->unique()
        ->toArray();
    echo "[smoke] Related tagihan ids for kontainers: " . implode(',', $related) . "\n";

    echo "[smoke] Rolling back transaction (no DB changes will persist)\n";
    DB::rollBack();
    echo "[smoke] Rollback complete.\n";

} catch (Exception $e) {
    if (DB::transactionLevel() > 0) DB::rollBack();
    echo "[smoke] Exception: " . $e->getMessage() . "\n";
    exit(1);
}

?>
