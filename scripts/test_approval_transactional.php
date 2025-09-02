<?php
require __DIR__ . '/../vendor/autoload.php';
// Boot Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permohonan;
use App\Models\TagihanKontainerSewa;

echo "Transactional test: simulate approval -> tagihan creation (will ROLLBACK at end)\n";

DB::beginTransaction();
try {
    $permohonans = Permohonan::with(['kontainers','checkpoints'])
        ->where('status','Selesai')
        ->get();

    if ($permohonans->isEmpty()) {
        echo "No Selesai permohonan found. Nothing to do.\n";
        DB::rollBack();
        exit(0);
    }

    $actions = [];

    foreach ($permohonans as $p) {
        $vendor = $p->vendor_perusahaan ?: ($p->kontainers->first()->pemilik_kontainer ?? null);
        if (!$vendor) {
            $actions[] = "SKIP permohonan#{$p->id} (no vendor)";
            continue;
        }
        $kontainers = $p->kontainers->pluck('nomor_kontainer')->filter()->values()->all();
        $kontCsv = implode(', ', $kontainers);

        // determine earliest checkpoint date for this permohonan
        $checkpointDates = $p->checkpoints->pluck('tanggal_checkpoint')->filter()->all();
        $tanggal_awal = count($checkpointDates) ? min($checkpointDates) : null;

        // Find existing tagihan for same vendor and same tanggal_harga_awal (date compare)
        $existing = null;
        if ($tanggal_awal) {
            $existing = TagihanKontainerSewa::where('vendor', $vendor)
                ->whereDate('tanggal_harga_awal', $tanggal_awal)
                ->first();
        }

        if ($existing) {
            // would append kontainer numbers (avoid duplicates)
            $current = $existing->nomor_kontainer ?? '';
            $currentList = array_filter(array_map('trim', explode(',', $current)));
            $merged = array_unique(array_merge($currentList, $kontainers));
            $newCsv = implode(', ', $merged);
            $existing->nomor_kontainer = $newCsv;
            $existing->dpp = ($existing->dpp ?? 0) + ($p->dpp ?? 0);
            $existing->ppn = ($existing->ppn ?? 0) + ($p->ppn ?? 0);
            $existing->pph = ($existing->pph ?? 0) + ($p->pph ?? 0);
            $existing->grand_total = ($existing->grand_total ?? 0) + ($p->grand_total ?? 0);
            $existing->save();
            $actions[] = "UPDATE tagihan#{$existing->id} (append kontainers: {$kontCsv})";
        } else {
            $t = new TagihanKontainerSewa();
            $t->vendor = $vendor;
            $t->nomor_kontainer = $kontCsv;
            $t->tanggal_harga_awal = $tanggal_awal;
            $t->tanggal_harga_akhir = $tanggal_awal; // simplistic: same day unless logic differs
            $t->periode = 1;
            $t->dpp = $p->dpp ?? 0;
            $t->ppn = $p->ppn ?? 0;
            $t->pph = $p->pph ?? 0;
            $t->grand_total = $p->grand_total ?? ($t->dpp + $t->ppn - $t->pph);
            $t->keterangan = 'Created by transactional test';
            $t->save();
            $actions[] = "INSERT tagihan#{$t->id} vendor={$vendor} kontainers={$kontCsv}";
        }
    }

    echo "Simulation complete — actions that WOULD be executed:\n";
    foreach ($actions as $a) echo " - $a\n";

    // show a short dump of the (temporary) tagihan rows created in this transaction
    $sample = TagihanKontainerSewa::orderBy('id','desc')->limit(10)->get();
    echo "\nSample rows (in-transaction, not persisted after rollback):\n";
    foreach ($sample as $s) {
        echo "#{$s->id} vendor={$s->vendor} kontainers={$s->nomor_kontainer} DPP={$s->dpp} PPN={$s->ppn} PPH={$s->pph} GRAND={$s->grand_total}\n";
    }

    echo "\nRolling back transaction to leave DB unchanged...\n";
    DB::rollBack();
    echo "Rollback done. No changes persisted.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error during simulation: " . $e->getMessage() . "\n";
    exit(1);
}
