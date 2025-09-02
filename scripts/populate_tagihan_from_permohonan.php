<?php
require __DIR__ . '/../vendor/autoload.php';
// Boot Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permohonan;
use App\Models\TagihanKontainerSewa;

echo "Populate: create/append tagihan_kontainer_sewa from Permohonan (will COMMIT changes)\n";

// backup existing tagihan table to CSV
$ts = date('Ymd_His');
$backupDir = __DIR__ . '/../backups';
if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
$backupFile = $backupDir . "/tagihan_kontainer_sewa_backup_{$ts}.csv";

$rows = DB::table('tagihan_kontainer_sewa')->get();
if ($rows->isNotEmpty()) {
    $fp = fopen($backupFile, 'w');
    // header
    fputcsv($fp, array_keys((array)$rows->first()));
    foreach ($rows as $r) {
        fputcsv($fp, (array)$r);
    }
    fclose($fp);
    echo "Backup written to: {$backupFile}\n";
} else {
    echo "No existing tagihan rows to backup. Skipping backup file creation.\n";
}

$permohonans = Permohonan::with(['kontainers','checkpoints'])
    ->where('status','Selesai')
    ->get();

if ($permohonans->isEmpty()) {
    echo "No Selesai permohonan found. Nothing to do.\n";
    exit(0);
}

$inserted = 0; $updated = 0; $skipped = 0; $errors = 0;

DB::beginTransaction();
try {
    foreach ($permohonans as $p) {
        $vendor = $p->vendor_perusahaan ?: ($p->kontainers->first()->pemilik_kontainer ?? null);
        if (!$vendor) {
            echo "SKIP permohonan#{$p->id} — no vendor\n";
            $skipped++;
            continue;
        }

        $kontainers = $p->kontainers->pluck('nomor_kontainer')->filter()->values()->all();
        if (empty($kontainers)) {
            echo "SKIP permohonan#{$p->id} — no kontainer\n";
            $skipped++;
            continue;
        }
        $kontCsv = implode(', ', $kontainers);

        $checkpointDates = $p->checkpoints->pluck('tanggal_checkpoint')->filter()->all();
        $tanggal_awal = count($checkpointDates) ? min($checkpointDates) : null;

        // find existing by vendor + same tanggal_harga_awal (date)
        $existing = null;
        if ($tanggal_awal) {
            $existing = TagihanKontainerSewa::where('vendor', $vendor)
                ->whereDate('tanggal_harga_awal', $tanggal_awal)
                ->first();
        }

        if ($existing) {
            $current = $existing->nomor_kontainer ?? '';
            $currentList = array_filter(array_map('trim', explode(',', $current)));
            $merged = array_unique(array_merge($currentList, $kontainers));
            $existing->nomor_kontainer = implode(', ', $merged);
            $existing->dpp = ($existing->dpp ?? 0) + ($p->dpp ?? 0);
            // recompute dpp_nilai_lain from dpp after merge
            $existing->dpp_nilai_lain = round((float)($existing->dpp ?? 0) * 11 / 12, 2);
            // recompute ppn from dpp_nilai_lain unless p provides override
            $existing->ppn = round((float)($existing->dpp_nilai_lain ?? 0) * 0.12, 2) + ($p->ppn ?? 0);
            // recompute or add incoming pph; prefer incoming pph added to existing
            $existing->pph = ($existing->pph ?? 0) + ($p->pph ?? round((float)($p->dpp ?? 0) * 0.02, 2));
            $existing->grand_total = ($existing->grand_total ?? 0) + ($p->grand_total ?? 0);
            $existing->save();
            $updated++;
            echo "UPDATE tagihan#{$existing->id} vendor={$vendor} append {$kontCsv}\n";
        } else {
            $t = new TagihanKontainerSewa();
            $t->vendor = $vendor;
            $t->nomor_kontainer = $kontCsv;
            $t->tanggal_harga_awal = $tanggal_awal;
            $t->tanggal_harga_akhir = $tanggal_awal;
            $t->periode = 1;
            $t->massa = $p->massa ?? 0;
            $t->dpp = $p->dpp ?? 0;
            // compute secondary base amount as dpp * 11/12 when not provided
            $t->dpp_nilai_lain = ($p->dpp_nilai_lain ?? null) ?: round((float)($t->dpp ?? 0) * 11 / 12, 2);
            // compute ppn from dpp_nilai_lain unless explicit provided
            $t->ppn = ($p->ppn ?? null) ?: round((float)($t->dpp_nilai_lain ?? 0) * 0.12, 2);
            // compute pph from dpp (2%) unless explicit provided
            $t->pph = ($p->pph ?? null) ?: round((float)($t->dpp ?? 0) * 0.02, 2);
            $t->pph = $p->pph ?? 0;
            $t->grand_total = $p->grand_total ?? ($t->dpp + $t->ppn - $t->pph);
            $t->keterangan = 'Imported from approval';
            $t->save();
            $inserted++;
            echo "INSERT tagihan#{$t->id} vendor={$vendor} kontainers={$kontCsv}\n";
        }
    }

    DB::commit();
    echo "\nDone. inserted={$inserted}, updated={$updated}, skipped={$skipped}, errors={$errors}\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
