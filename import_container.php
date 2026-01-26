<?php
// import_container.php
// Usage (on production server): php import_container.php /path/to/container_EMCU6063235.json [--dry-run]

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

$argv_count = count($argv);
if ($argv_count < 2) {
    echo "Usage: php import_container.php /path/to/container_EMCU6063235.json [--dry-run]\n";
    exit(1);
}

$file = $argv[1];
$dryRun = in_array('--dry-run', $argv);

if (!file_exists($file)) {
    echo "File not found: {$file}\n";
    exit(1);
}

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) {
    echo "Invalid JSON file: {$file}\n";
    exit(1);
}

echo "Records to import: " . count($data) . "\n";
if ($dryRun) echo "DRY RUN: no database changes will be made.\n";

$summary = ['insert' => 0, 'update' => 0, 'skipped' => 0];

DB::beginTransaction();
try {
    foreach ($data as $row) {
        // key for upsert: nomor_kontainer + periode
        $key = ['nomor_kontainer' => $row['nomor_kontainer'], 'periode' => $row['periode']];

        // keep only allowed fields to write
        $write = $row;
        unset($write['created_at']);
        unset($write['updated_at']);
        unset($write['pranota_id']); // optional: keep as-is if you want pranota references updated

        $existing = DaftarTagihanKontainerSewa::where($key)->first();
        if ($existing) {
            // determine if update is needed by comparing important fields
            $needsUpdate = false;
            foreach ($write as $k => $v) {
                // treat null/"" differences fine; convert numbers to string for safe compare
                $existingVal = $existing->{$k};
                $existingStr = $existingVal instanceof \Carbon\Carbon ? $existingVal->toDateString() : (string)$existingVal;
                $newStr = $v === null ? '' : (string)$v;
                if ($existingStr !== $newStr) {
                    $needsUpdate = true;
                    break;
                }
            }

            if ($needsUpdate) {
                echo "UPDATE: container={$key['nomor_kontainer']} periode={$key['periode']}\n";
                if (!$dryRun) $existing->update($write);
                $summary['update']++;
            } else {
                $summary['skipped']++;
            }
        } else {
            echo "INSERT: container={$key['nomor_kontainer']} periode={$key['periode']}\n";
            if (!$dryRun) DaftarTagihanKontainerSewa::create($write);
            $summary['insert']++;
        }
    }

    if ($dryRun) {
        DB::rollBack();
        echo "Dry run complete, no changes were committed.\n";
    } else {
        DB::commit();
        echo "Import complete and committed.\n";
    }

    echo "Summary: Inserted={$summary['insert']}, Updated={$summary['update']}, Skipped={$summary['skipped']}\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

