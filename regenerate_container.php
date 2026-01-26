<?php
// regenerate_container.php
// Usage: php regenerate_container.php EMCU6063235 [--to-period=13] [--dry-run] [--force]
// - --to-period=N : regenerate until period N (default: max(current periods, 13))
// - --dry-run : do not apply changes, just show what would be done
// - --force : apply changes (deletes existing container records for container and inserts regenerated ones)

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$container = $argv[1] ?? null;
if (!$container) {
    echo "Usage: php regenerate_container.php CONTAINER [--to-period=N] [--dry-run] [--force]\n";
    exit(1);
}

// parse options
$toPeriod = null;
$dryRun = false;
$force = false;
foreach ($argv as $arg) {
    if (strpos($arg, '--to-period=') === 0) {
        $toPeriod = intval(substr($arg, strlen('--to-period=')));
    }
    if ($arg === '--dry-run') $dryRun = true;
    if ($arg === '--force') $force = true;
}

// Load existing records
$existing = DaftarTagihanKontainerSewa::where('nomor_kontainer', $container)->orderBy('periode')->get();

if ($existing->isEmpty()) {
    echo "No records found for container: {$container}\n";
    exit(1);
}

$currentMax = $existing->max('periode');
if (!$toPeriod) $toPeriod = max($currentMax, 13);

// Determine template data from smallest periode (prefer periode=1)
$template = $existing->where('periode', 1)->first();
if (!$template) $template = $existing->first();

$vendor = $template->vendor;
$size = $template->size;
group = $template->group;
$masa = strtolower($template->masa ?? '');
$tarif = $template->tarif;
$tarif_nominal = $template->tarif_nominal;
$dpp = $template->dpp;
$adjustment = $template->adjustment;
$adjustment_note = $template->adjustment_note;
$invoice_vendor = $template->invoice_vendor;
$ppn = $template->ppn;
$pph = $template->pph;
$grand_total_template = $template->grand_total;

// Determine start date: earliest tanggal_awal among existing
$startDate = $existing->min('tanggal_awal');
$start = $startDate ? Carbon::parse($startDate) : null;
if (!$start) {
    echo "Cannot determine start date for container {$container}. Aborting.\n";
    exit(1);
}

// Build periods
$generated = [];
$periodStart = $start->copy();
for ($p = 1; $p <= $toPeriod; $p++) {
    if (strpos($masa, 'bulanan') !== false || strpos($tarif, 'Bulanan') !== false) {
        // end = start +1 month -1 day
        $periodEnd = $periodStart->copy()->addMonth()->subDay();
    } else {
        // fallback: if existing has same period use its end span, else use 30 days
        $existingRow = $existing->where('periode', $p)->first();
        if ($existingRow && $existingRow->tanggal_akhir) {
            $periodEnd = Carbon::parse($existingRow->tanggal_akhir);
        } else {
            $periodEnd = $periodStart->copy()->addDays(29); // 30-day window
        }
    }

    $generated[] = [
        'nomor_kontainer' => $container,
        'vendor' => $vendor,
        'size' => $size,
        'tanggal_awal' => $periodStart->toDateString(),
        'tanggal_akhir' => $periodEnd->toDateString(),
        'group' => $group,
        'masa' => $template->masa,
        'tarif' => $tarif,
        'tarif_nominal' => $tarif_nominal,
        'dpp' => $dpp,
        'adjustment' => $adjustment,
        'adjustment_note' => $adjustment_note,
        'invoice_vendor' => $invoice_vendor,
        'tanggal_vendor' => $template->tanggal_vendor ? Carbon::parse($template->tanggal_vendor)->toDateString() : null,
        'dpp_nilai_lain' => $template->dpp_nilai_lain,
        'ppn' => $ppn,
        'pph' => $pph,
        'grand_total' => $grand_total_template,
        'status' => $template->status,
        'status_pranota' => $template->status_pranota,
        'periode' => $p,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    // next period start is periodEnd +1 day
    $periodStart = $periodEnd->copy()->addDay();
}

// Show summary
echo "Container: {$container}\n";
echo "Existing records: " . $existing->count() . " (max periode={$currentMax})\n";
echo "Will generate periods up to: {$toPeriod}\n";
echo "Detected masa: " . ($template->masa ?? 'N/A') . "\n";
echo "Generated preview (first 5):\n";
foreach (array_slice($generated, 0, 5) as $g) {
    echo "  Periode {$g['periode']}: {$g['tanggal_awal']} - {$g['tanggal_akhir']}, tarif_nominal=" . ($g['tarif_nominal'] ?? 'null') . "\n";
}
if (count($generated) > 5) echo "  ... total " . count($generated) . " periods\n";

if ($dryRun) {
    echo "DRY RUN: no changes will be made. Use --force to apply changes.\n";
    exit(0);
}

if (!$force) {
    echo "No --force flag provided; aborting before making changes. Add --force to apply.\n";
    exit(0);
}

// Apply changes: replace existing records for this kontainer
DB::beginTransaction();
try {
    // delete existing
    $delCount = DaftarTagihanKontainerSewa::where('nomor_kontainer', $container)->delete();
    echo "Deleted {$delCount} existing records.\n";

    // insert generated
    foreach ($generated as $row) {
        DaftarTagihanKontainerSewa::create($row);
    }

    DB::commit();
    echo "Regeneration complete: inserted " . count($generated) . " records for {$container}.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR during regeneration: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

