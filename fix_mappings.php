<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;
use App\Models\TandaTerima;
use Illuminate\Support\Facades\DB;

$dryRun = isset($argv[1]) && $argv[1] === 'live' ? false : true;

echo "=== Prospek Tanda Terima Re-linking Script ===\n";
echo "Mode: " . ($dryRun ? "DRY RUN (no DB changes)" : "LIVE (updating DB)") . "\n\n";

$prospeks = Prospek::whereNotNull('tanda_terima_id')->with('tandaTerima')->get();
echo "Found " . $prospeks->count() . " prospeks with tanda_terima_id set.\n";

$mismatchedCount = 0;
$fixedCount = 0;

foreach ($prospeks as $prospek) {
    $tt = $prospek->tandaTerima;
    if (!$tt) {
        echo "Prospek ID {$prospek->id} (SJ: {$prospek->no_surat_jalan}) has orphaned tanda_terima_id {$prospek->tanda_terima_id} (not found in DB).\n";
        $mismatchedCount++;
        if (!$dryRun) {
            $prospek->update(['tanda_terima_id' => null]);
            echo "  -> Set tanda_terima_id to NULL.\n";
        }
        continue;
    }

    $idMatch = $prospek->surat_jalan_id && $tt->surat_jalan_id && ($prospek->surat_jalan_id == $tt->surat_jalan_id);
    
    // Normalize string compare
    $prospekSj = strtoupper(trim($prospek->no_surat_jalan));
    $ttSj = strtoupper(trim($tt->no_surat_jalan));
    $sjMatch = $prospekSj && $ttSj && ($prospekSj === $ttSj || strpos($prospekSj, $ttSj) === 0 || strpos($ttSj, $prospekSj) === 0);

    if (!$idMatch && !$sjMatch) {
        $mismatchedCount++;
        echo "MISMATCH DETECTED for Prospek ID {$prospek->id}:\n";
        echo "  - Prospek SJ ID: '{$prospek->surat_jalan_id}', SJ No: '{$prospek->no_surat_jalan}'\n";
        echo "  - Tanda Terima ID: '{$tt->id}', SJ ID: '{$tt->surat_jalan_id}', No: '{$tt->no_surat_jalan}'\n";

        // Find correct Tanda Terima
        $correctTt = null;
        if ($prospek->surat_jalan_id) {
            $correctTt = TandaTerima::where('surat_jalan_id', $prospek->surat_jalan_id)->first();
        }
        if (!$correctTt && $prospek->no_surat_jalan) {
            $baseNoSuratJalan = preg_replace('/-\d+$/', '', $prospek->no_surat_jalan);
            $correctTt = TandaTerima::where('no_surat_jalan', $baseNoSuratJalan)->first();
        }

        if ($correctTt) {
            echo "  -> FOUND correct Tanda Terima: ID {$correctTt->id} (No: {$correctTt->no_surat_jalan})\n";
            if (!$dryRun) {
                $prospek->update(['tanda_terima_id' => $correctTt->id]);
                echo "    -> Updated successfully.\n";
                $fixedCount++;
            }
        } else {
            echo "  -> NO correct Tanda Terima found in DB.\n";
            if (!$dryRun) {
                $prospek->update(['tanda_terima_id' => null]);
                echo "    -> Set to NULL.\n";
                $fixedCount++;
            }
        }
    }
}

echo "\nSummary:\n";
echo "Total mismatches found: $mismatchedCount\n";
echo "Total fixed: $fixedCount\n";
