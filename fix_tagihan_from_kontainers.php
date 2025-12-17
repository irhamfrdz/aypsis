<?php

require "vendor/autoload.php";

$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Models\Kontainer;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$options = getopt('', ['apply','delete-extra','verbose','container:','preserve-paid','help']);
$apply = isset($options['apply']);
$deleteExtra = isset($options['delete-extra']);
$verbose = isset($options['verbose']);
$preservePaid = isset($options['preserve-paid']);
$limitContainer = $options['container'] ?? null;

if (isset($options['help'])) {
    echo "Usage: php fix_tagihan_from_kontainers.php [--apply] [--delete-extra] [--verbose] [--container=CONTAINER_NO]\n";
    echo "  --apply         Actually perform DB changes (default is dry-run)\n";
    echo "  --delete-extra  When used with --apply, delete extra tagihan not matching container range\n";
    echo "  --verbose       More logging\n";
    echo "  --container=ID  Limit to a single container (nomor_seri_gabungan)\n";
    echo "  --preserve-paid  When resolving duplicate rows prefer paid/pranota rows\n";
    exit(0);
}

echo "🔧 Fixing daftar_tagihan_kontainer_sewa based on kontainers' rental dates\n";
if (!$apply) echo "(Dry run) -- nothing will be changed. Use --apply to persist changes.\n";
if ($deleteExtra && !$apply) echo "Note: --delete-extra ignored without --apply.\n";

$query = Kontainer::whereNotNull('tanggal_mulai_sewa')
    ->where('status', '!=', 'Dikembalikan');

if ($limitContainer) {
    $query->where('nomor_seri_gabungan', $limitContainer);
}

$kontainers = $query->get();

echo "Found " . $kontainers->count() . " kontainers with tanggal_mulai_sewa.\n";

$summary = ['created'=>0,'updated'=>0,'deleted'=>0,'skipped'=>0];

foreach ($kontainers as $kontainer) {
    echo "\n🔄 Processing container: " . ($kontainer->nomor_seri_gabungan ?? $kontainer->getNomorKontainerAttribute()) . "\n";

    try {
        $tanggalMulai = Carbon::parse($kontainer->tanggal_mulai_sewa);
    } catch (Exception $e) {
        echo "  ⚠️  Invalid tanggal_mulai_sewa, skipping.\n";
        $summary['skipped']++;
        continue;
    }

    $tanggalSelesai = $kontainer->tanggal_selesai_sewa ? Carbon::parse($kontainer->tanggal_selesai_sewa) : null;
    $endDate = $tanggalSelesai ?: Carbon::now()->endOfMonth();

    // Build expected periods starting from tanggal_mulai_sewa
    $currentStart = $tanggalMulai->copy()->startOfMonth();
    $period = 1;
    $expected = [];

    while ($currentStart->lte($endDate)) {
        $currentEnd = $currentStart->copy()->endOfMonth();
        if ($tanggalSelesai && $currentEnd->gt($tanggalSelesai)) {
            $currentEnd = $tanggalSelesai->copy();
        }

        $expected[$period] = [
            'tanggal_awal' => $currentStart->toDateString(),
            'tanggal_akhir' => $currentEnd->toDateString(),
            'masa' => $currentStart->format('j M Y') . ' - ' . $currentEnd->format('j M Y'),
        ];

        $currentStart->addMonth();
        $period++;
    }

    $expectedCount = count($expected);

    if ($verbose) echo "  Expected periods: $expectedCount\n";

    // Load existing tagihan for this container
    $nomorKontainer = $kontainer->nomor_seri_gabungan;
    $existingGroups = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->orderBy('periode')->get()->groupBy('periode');

    // Create or update expected periods
    foreach ($expected as $p => $info) {
        $group = $existingGroups->get($p);
        if (!$group || $group->count() == 0) {
            // Missing -> create
            $dpp = 0;
            $nearest = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->orderBy('periode', 'desc')->first();
            if ($nearest) $dpp = $nearest->dpp ?? 0;

            echo "  -> Missing periode {$p}. Will create (dpp={$dpp}).\n";

            if ($apply) {
                $new = DaftarTagihanKontainerSewa::create([
                    'vendor' => $kontainer->vendor ?? 'ZONA',
                    'nomor_kontainer' => $nomorKontainer,
                    'size' => $kontainer->ukuran ?? null,
                    'tanggal_awal' => $info['tanggal_awal'],
                    'tanggal_akhir' => $info['tanggal_akhir'],
                    'periode' => $p,
                    'masa' => $info['masa'],
                    'tarif' => 'Bulanan',
                    'status' => 'ongoing',
                    'dpp' => $dpp,
                ]);
                echo "    ✅ Created tagihan id {$new->id}\n";
                $summary['created']++;
            }
        } elseif ($group->count() == 1) {
            // Single row -> verify
            $row = $group->first();
            $needUpdate = false;
            $updates = [];
            if ($row->tanggal_awal != $info['tanggal_awal']) { $updates['tanggal_awal'] = $info['tanggal_awal']; $needUpdate = true; }
            if ($row->tanggal_akhir != $info['tanggal_akhir']) { $updates['tanggal_akhir'] = $info['tanggal_akhir']; $needUpdate = true; }
            if (($row->masa ?? '') != $info['masa']) { $updates['masa'] = $info['masa']; $needUpdate = true; }

            if ($needUpdate) {
                echo "  -> Periode {$p} exists but dates differ.\n";
                if ($apply) {
                    $row->fill($updates);
                    $row->save();
                    echo "    ✅ Updated tagihan id {$row->id}\n";
                    $summary['updated']++;
                } else {
                    echo "    (dry-run) would update: "; print_r($updates);
                }
            } else {
                if ($verbose) echo "  -> Periode {$p} OK.\n";
            }
        } else {
            // Multiple rows for same periode -> resolve duplicates
            echo "  ⚠️  Multiple rows for periode {$p} (count: {$group->count()}). Resolving...\n";

            // prefer paid/pranota rows if flag set
            $preferred = null;
            if (isset($options['preserve-paid'])) {
                $preferred = $group->firstWhere('status_pranota', 'paid');
                if (!$preferred) {
                    $preferred = $group->first(function($r){ return !empty($r->pranota_id); });
                }
            }

            // otherwise prefer non-harian tarif
            if (!$preferred) {
                $preferred = $group->first(function($r){ return strtolower($r->tarif ?? '') !== 'harian'; });
            }

            // fallback to first
            if (!$preferred) $preferred = $group->first();

            // determine updates for preferred
            $needUpdate = false;
            $updates = [];
            if ($preferred->tanggal_awal != $info['tanggal_awal']) { $updates['tanggal_awal'] = $info['tanggal_awal']; $needUpdate = true; }
            if ($preferred->tanggal_akhir != $info['tanggal_akhir']) { $updates['tanggal_akhir'] = $info['tanggal_akhir']; $needUpdate = true; }
            if (($preferred->masa ?? '') != $info['masa']) { $updates['masa'] = $info['masa']; $needUpdate = true; }

            if ($needUpdate) {
                echo "    -> Preferred id {$preferred->id} dates differ.\n";
                if ($apply) {
                    $preferred->fill($updates);
                    $preferred->save();
                    echo "      ✅ Updated id {$preferred->id}\n";
                    $summary['updated']++;
                } else {
                    echo "      (dry-run) would update id {$preferred->id}: "; print_r($updates);
                }
            }

            // mark others as extras
            foreach ($group as $r) {
                if ($r->id == $preferred->id) continue;
                // these are duplicates
                if ($apply && $deleteExtra) {
                    $r->delete();
                    echo "      🗑️  Deleted duplicate id {$r->id}\n";
                    $summary['deleted']++;
                } else {
                    echo "      (dry-run) would delete duplicate id {$r->id}\n";
                }
            }
        }
    }

    // Identify extras: existing periods outside expected set or with dates outside start..end
    $extras = [];
    foreach ($existingGroups as $p => $group) {
        if (!isset($expected[$p])) {
            foreach ($group as $row) $extras[] = $row;
            continue;
        }
        foreach ($group as $row) {
            // also check bound dates
            if ($row->tanggal_awal < $tanggalMulai->toDateString() || ($tanggalSelesai && $row->tanggal_akhir > $tanggalSelesai->toDateString())) {
                $extras[] = $row;
            }
        }
    }

    if (count($extras) > 0) {
        echo "  ⚠️  Found " . count($extras) . " extra/out-of-range tagihan for this container.\n";
        foreach ($extras as $ex) {
            echo "    - id {$ex->id} periode {$ex->periode} (" . $ex->tanggal_awal . " - " . $ex->tanggal_akhir . ")\n";
            if ($apply && $deleteExtra) {
                $ex->delete();
                echo "      🗑️  Deleted id {$ex->id}\n";
                $summary['deleted']++;
            } elseif ($apply && !$deleteExtra) {
                echo "      (apply mode) To remove this row re-run with --delete-extra\n";
            } else {
                echo "      (dry-run) would consider deleting id {$ex->id}\n";
            }
        }
    }

}

echo "\n✅ Summary:\n";
echo "  Created: {$summary['created']}\n";
echo "  Updated: {$summary['updated']}\n";
echo "  Deleted: {$summary['deleted']}\n";
echo "  Skipped: {$summary['skipped']}\n";

if (!$apply) {
    echo "\nNote: This was a dry-run. Re-run with --apply to persist changes.\n";
    echo "If you want to remove extras, add --delete-extra together with --apply (ensure DB backup first).\n";
}

?>