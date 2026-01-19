<?php

namespace App\Console\Commands;

use App\Models\Manifest;
use App\Models\NaikKapal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncNaikKapalToManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:naik-kapal-to-manifest 
                            {--voyage= : Specific voyage number to sync}
                            {--kapal= : Specific ship name to sync}
                            {--dry-run : Show what would be synced without actually syncing}
                            {--force : Force sync even if manifest already exists}
                            {--all : Sync all records, not just those with sudah_ob=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from naik_kapal table (only sudah_ob=true) to manifests table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $voyage = $this->option('voyage');
        $kapal = $this->option('kapal');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $all = $this->option('all');

        $this->info('=== Sync NaikKapal to Manifest ===');
        $this->info('Voyage: ' . ($voyage ?: 'All'));
        $this->info('Kapal: ' . ($kapal ?: 'All'));
        $this->info('Filter: ' . ($all ? 'ALL records' : 'Only sudah_ob=true'));
        $this->info('Mode: ' . ($dryRun ? 'DRY RUN (no changes)' : 'LIVE'));
        $this->newLine();

        // Build query for naik_kapal
        $query = NaikKapal::query();
        
        // By default, only sync records that have sudah_ob = true
        if (!$all) {
            $query->where('sudah_ob', true);
        }
        
        if ($voyage) {
            $query->where('no_voyage', $voyage);
        }
        
        if ($kapal) {
            $query->where('nama_kapal', 'like', '%' . $kapal . '%');
        }

        $naikKapals = $query->get();
        
        $this->info("Found {$naikKapals->count()} records in naik_kapal");

        // Check existing manifests
        $existingManifests = Manifest::query();
        if ($voyage) {
            $existingManifests->where('no_voyage', $voyage);
        }
        if ($kapal) {
            $existingManifests->where('nama_kapal', 'like', '%' . $kapal . '%');
        }
        $manifestCount = $existingManifests->count();
        
        $this->info("Found {$manifestCount} existing records in manifests");
        $this->newLine();

        $created = 0;
        $skipped = 0;
        $errors = 0;

        $this->output->progressStart($naikKapals->count());

        foreach ($naikKapals as $nk) {
            $this->output->progressAdvance();

            // Check if manifest already exists for this container
            $existingManifest = Manifest::where('nomor_kontainer', $nk->nomor_kontainer)
                ->where('no_voyage', $nk->no_voyage)
                ->where('nama_kapal', $nk->nama_kapal)
                ->first();

            if ($existingManifest && !$force) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->newLine();
                $this->line("  Would create manifest for: {$nk->nomor_kontainer} ({$nk->nama_kapal} - {$nk->no_voyage})");
                $created++;
                continue;
            }

            try {
                DB::beginTransaction();

                $manifestData = [
                    'nomor_kontainer' => $nk->nomor_kontainer,
                    'no_seal' => $nk->no_seal,
                    'tipe_kontainer' => $nk->tipe_kontainer,
                    'size_kontainer' => $nk->size_kontainer,
                    'nama_kapal' => $nk->nama_kapal,
                    'no_voyage' => $nk->no_voyage,
                    'pelabuhan_asal' => $nk->pelabuhan_asal,
                    'pelabuhan_tujuan' => $nk->pelabuhan_tujuan,
                    'nama_barang' => $nk->jenis_barang,
                    'asal_kontainer' => $nk->asal_kontainer,
                    'ke' => $nk->ke,
                    'tonnage' => $nk->total_tonase,
                    'volume' => $nk->total_volume,
                    'kuantitas' => $nk->kuantitas ?? 1,
                    'prospek_id' => $nk->prospek_id,
                    'created_by' => $nk->created_by,
                    'updated_by' => $nk->updated_by,
                ];

                // Get additional data from prospek if available
                if ($nk->prospek) {
                    $prospek = $nk->prospek;
                    $manifestData['pengirim'] = $prospek->pt_pengirim ?? $prospek->pengirim ?? null;
                    $manifestData['alamat_pengirim'] = $prospek->alamat_pengirim ?? null;
                    $manifestData['penerima'] = $prospek->pt_penerima ?? $prospek->penerima ?? null;
                    $manifestData['alamat_penerima'] = $prospek->alamat_penerima ?? null;
                    $manifestData['pelabuhan_muat'] = $prospek->port_muat ?? $nk->pelabuhan_asal ?? null;
                    $manifestData['pelabuhan_bongkar'] = $prospek->port_bongkar ?? $nk->pelabuhan_tujuan ?? null;
                }

                if ($existingManifest && $force) {
                    // Update existing
                    $existingManifest->update($manifestData);
                } else {
                    // Create new
                    Manifest::create($manifestData);
                }

                DB::commit();
                $created++;

            } catch (\Exception $e) {
                DB::rollBack();
                $errors++;
                $this->newLine();
                $this->error("  Error for {$nk->nomor_kontainer}: " . $e->getMessage());
            }
        }

        $this->output->progressFinish();
        $this->newLine();

        $this->info('=== Summary ===');
        $this->info("Created/Updated: {$created}");
        $this->info("Skipped (already exists): {$skipped}");
        $this->info("Errors: {$errors}");

        if ($dryRun) {
            $this->newLine();
            $this->warn('This was a DRY RUN. No changes were made.');
            $this->warn('Remove --dry-run option to actually sync the data.');
        }

        return Command::SUCCESS;
    }
}
