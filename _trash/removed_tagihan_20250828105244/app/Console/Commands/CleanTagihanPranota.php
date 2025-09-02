<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanTagihanPranota extends Command
{
    protected $signature = 'clean:tagihan-pranota {--yes : Actually perform deletions} {--export-path= : Export CSV path (storage/app/...)}';
    protected $description = 'Export and safely clean tagihan_kontainer_sewa rows and related pranota/pivot rows';

    public function handle()
    {
        $this->info('Starting dry-run export of current tagihan/pranota data...');

        $rows = DB::table('tagihan_kontainer_sewa')->select('*')->get();
        $pivot = DB::table('tagihan_kontainer_sewa_kontainers')->select('*')->get();

        $timestamp = date('Ymd_His');
        $exportPath = $this->option('export-path') ?: storage_path('app/backup-tagihan-pranota-' . $timestamp . '.csv');

        // write a combined CSV: first header for tagihan, then a separator line, then pivots
        $fh = fopen($exportPath, 'w');
        if (!$fh) {
            $this->error('Failed to open export path: ' . $exportPath);
            return 1;
        }

        // export tagihan rows
        if ($rows->count() > 0) {
            $this->line('Exporting ' . $rows->count() . ' tagihan rows...');
            // write header
            fputcsv($fh, array_keys((array)$rows->first()));
            foreach ($rows as $r) {
                fputcsv($fh, (array)$r);
            }
        } else {
            $this->line('No tagihan rows found.');
        }

        // separator
        fputcsv($fh, ['--pivot--']);

        if ($pivot->count() > 0) {
            $this->line('Exporting ' . $pivot->count() . ' pivot rows...');
            fputcsv($fh, array_keys((array)$pivot->first()));
            foreach ($pivot as $p) {
                fputcsv($fh, (array)$p);
            }
        } else {
            $this->line('No pivot rows found.');
        }

        fclose($fh);

        $this->info('Exported backup to: ' . $exportPath);

        if (!$this->option('yes')) {
            $this->warn('Dry run complete. No deletions performed. Re-run with --yes to delete.');
            return 0;
        }

        // Proceed with deletion (destructive)
        $this->warn('Deleting pivot rows from tagihan_kontainer_sewa_kontainers...');
        DB::beginTransaction();
        try {
            DB::table('tagihan_kontainer_sewa_kontainers')->delete();
            $this->line('Pivot table cleared.');

            $this->warn('Deleting rows from tagihan_kontainer_sewa...');
            // Optionally, you might want to only delete pranota rows (tarif = 'Pranota') or everything.
            // Here we delete all rows. Adjust as needed.
            DB::table('tagihan_kontainer_sewa')->delete();
            DB::commit();
            $this->info('All tagihan_kontainer_sewa and pivot rows deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Deletion failed: ' . $e->getMessage());
            return 2;
        }

        return 0;
    }
}
