<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestoreTagihanPranota extends Command
{
    protected $signature = 'restore:tagihan-pranota {file?} {--yes}';
    protected $description = 'Restore tagihan_kontainer_sewa and pivot rows from a backup CSV produced by clean:tagihan-pranota';

    public function handle()
    {
        $file = $this->argument('file') ?: storage_path('app/backup-tagihan-pranota-20250827_093824.csv');
        if (!file_exists($file)) {
            $this->error('Backup file not found: ' . $file);
            return 1;
        }

        $content = file_get_contents($file);
        // split at pivot marker
        $parts = preg_split('/^--pivot--$/m', $content);
        if (count($parts) < 2) {
            $this->error('Invalid backup format: missing --pivot-- separator');
            return 1;
        }

        $tagihanCsv = array_filter(array_map('trim', explode("\n", trim($parts[0]))));
        $pivotCsv = array_filter(array_map('trim', explode("\n", trim($parts[1]))));

        // parse header and rows for tagihan
        $tagHeader = str_getcsv(array_shift($tagihanCsv));
        $tagRows = array_map(function($line){ return str_getcsv($line); }, $tagihanCsv);

        $pivotHeader = str_getcsv(array_shift($pivotCsv));
        $pivotRows = array_map(function($line){ return str_getcsv($line); }, $pivotCsv);

        $this->info('Parsed ' . count($tagRows) . ' tagihan rows and ' . count($pivotRows) . ' pivot rows');

        // Dry-run: report what would be inserted, check for conflicts
        $existingTagCount = DB::table('tagihan_kontainer_sewa')->count();
        $existingPivotCount = DB::table('tagihan_kontainer_sewa_kontainers')->count();
        $this->info('Existing tagihan rows: ' . $existingTagCount . ', pivot rows: ' . $existingPivotCount);

        // Prepare inserts but avoid setting ID collisions: we'll insert rows and map old id -> new id
        $idMap = [];
        $toInsertTag = [];
        foreach ($tagRows as $cols) {
            if (count($cols) !== count($tagHeader)) continue;
            $row = array_combine($tagHeader, $cols);
            $oldId = isset($row['id']) ? trim($row['id']) : null;
            unset($row['id']);
            // normalize empty strings to null
            foreach ($row as $k => $v) { if ($v === '') $row[$k] = null; }
            $toInsertTag[] = $row;
            $idMap[$oldId] = null;
        }

        $this->info('Will insert ' . count($toInsertTag) . ' tagihan rows');

        if (!$this->option('yes')) {
            $this->info('Dry-run only. Rerun with --yes to perform insert.');
            return 0;
        }

        DB::beginTransaction();
        try {
            // insert tag rows and capture new ids
            foreach ($toInsertTag as $row) {
                $newId = DB::table('tagihan_kontainer_sewa')->insertGetId($row);
                // try to find old id by matching unique-ish tuple (vendor,tanggal_harga_awal,harga)
                $this->line('Inserted tagihan id=' . $newId . ' vendor=' . ($row['vendor'] ?? '-'));
                // map best-effort: find first unmapped old id and assign
                foreach ($idMap as $old => $val) { if ($val === null) { $idMap[$old] = $newId; break; } }
            }

            // insert pivot rows, remap tagihan_id
            $insertedPivots = 0;
            foreach ($pivotRows as $cols) {
                if (count($cols) !== count($pivotHeader)) continue;
                $prow = array_combine($pivotHeader, $cols);
                $oldTag = isset($prow['tagihan_id']) ? trim($prow['tagihan_id']) : null;
                $kontainerId = isset($prow['kontainer_id']) ? trim($prow['kontainer_id']) : null;
                $newTag = null;
                // pick mapped new id if exists
                if ($oldTag !== null && isset($idMap[$oldTag])) $newTag = $idMap[$oldTag];
                if ($newTag === null) {
                    // try to find a tagihan with same vendor+tanggal or same harga
                    $candidate = DB::table('tagihan_kontainer_sewa')->whereNull('deleted_at')->orderBy('id','desc')->first();
                    $newTag = $candidate ? $candidate->id : null;
                }
                if ($newTag) {
                    // sanitize timestamps — some backup rows have empty strings which MySQL rejects
                    $created = (!empty($prow['created_at']) && strtotime($prow['created_at'])) ? $prow['created_at'] : now();
                    $updated = (!empty($prow['updated_at']) && strtotime($prow['updated_at'])) ? $prow['updated_at'] : now();
                    // ensure kontainer_id is numeric
                    if (!is_numeric($kontainerId)) continue;
                    DB::table('tagihan_kontainer_sewa_kontainers')->insert([
                        'tagihan_id' => $newTag,
                        'kontainer_id' => (int) $kontainerId,
                        'created_at' => $created,
                        'updated_at' => $updated,
                    ]);
                    $insertedPivots++;
                }
            }

            DB::commit();
            $this->info('Restore completed: inserted ' . count($toInsertTag) . ' tagihan rows and ' . $insertedPivots . ' pivot rows');
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Restore failed: ' . $e->getMessage());
            return 1;
        }
    }
}
