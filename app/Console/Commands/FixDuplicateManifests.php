<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixDuplicateManifests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-duplicate-manifests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect and remove duplicate manifests based on unique keys, keeping the latest one.';

    public function handle()
    {
        $this->info('Scanning for duplicate manifests...');

        // Find duplicates grouped by the unique combination
        $duplicates = \Illuminate\Support\Facades\DB::table('manifests')
            ->select('nomor_kontainer', 'no_voyage', 'nama_kapal', 'nomor_tanda_terima', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy('nomor_kontainer', 'no_voyage', 'nama_kapal', 'nomor_tanda_terima')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate manifests found.');
            return;
        }

        $totalDeleted = 0;

        foreach ($duplicates as $duplicate) {
            $this->info("Processing duplicate: Kontainer {$duplicate->nomor_kontainer}, TT {$duplicate->nomor_tanda_terima} (Count: {$duplicate->count})");

            // Get all IDs for this combination ordered by ID descending (keep latest)
            $ids = \Illuminate\Support\Facades\DB::table('manifests')
                ->where('nomor_kontainer', $duplicate->nomor_kontainer)
                ->where('no_voyage', $duplicate->no_voyage)
                ->where('nama_kapal', $duplicate->nama_kapal)
                ->where('nomor_tanda_terima', $duplicate->nomor_tanda_terima) // Ensure null safety if needed by DB, but here these are grouping keys
                ->orderBy('id', 'desc')
                ->pluck('id')
                ->toArray();

            // Keep the first one (latest ID), delete the rest
            $keepId = array_shift($ids);
            
            if (!empty($ids)) {
                $deleted = \Illuminate\Support\Facades\DB::table('manifests')
                    ->whereIn('id', $ids)
                    ->delete();
                
                $this->line("  - Kept ID: $keepId");
                $this->line("  - Deleted IDs: " . implode(', ', $ids));
                $totalDeleted += $deleted;
            }
        }

        $this->info("Cleanup completed. Total records deleted: $totalDeleted");
    }
}
