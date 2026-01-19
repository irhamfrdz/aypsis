<?php

namespace App\Console\Commands;

use App\Models\Manifest;
use App\Models\NaikKapal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupAndSyncManifest extends Command
{
    protected $signature = 'manifest:cleanup-and-sync 
                            {voyage : The voyage number to process}
                            {--dry-run : Show what would be done without actually doing it}';

    protected $description = 'Cleanup wrong manifest data and re-sync only sudah_ob records';

    public function handle()
    {
        $voyage = $this->argument('voyage');
        $dryRun = $this->option('dry-run');

        $this->info('=== Manifest Cleanup and Sync ===');
        $this->info("Voyage: {$voyage}");
        $this->info('Mode: ' . ($dryRun ? 'DRY RUN' : 'LIVE'));
        $this->newLine();

        // Step 1: Show current counts
        $naikKapalTotal = NaikKapal::where('no_voyage', $voyage)->count();
        $naikKapalOB = NaikKapal::where('no_voyage', $voyage)->where('sudah_ob', true)->count();
        $manifestCount = Manifest::where('no_voyage', $voyage)->count();

        $this->info('Current Status:');
        $this->table(
            ['Table', 'Count'],
            [
                ['NaikKapal (Total)', $naikKapalTotal],
                ['NaikKapal (Sudah OB)', $naikKapalOB],
                ['Manifest', $manifestCount],
            ]
        );
        $this->newLine();

        // Step 2: Get container numbers that should be in manifest (only sudah_ob=true)
        $validContainers = NaikKapal::where('no_voyage', $voyage)
            ->where('sudah_ob', true)
            ->pluck('nomor_kontainer')
            ->toArray();

        $this->info("Valid containers (sudah OB): " . count($validContainers));

        // Step 3: Find manifests that should NOT exist (container not in valid list)
        $invalidManifests = Manifest::where('no_voyage', $voyage)
            ->whereNotIn('nomor_kontainer', $validContainers)
            ->get();

        $this->info("Manifests to DELETE (not sudah OB): " . $invalidManifests->count());

        if ($invalidManifests->count() > 0) {
            $this->table(
                ['ID', 'Nomor Kontainer', 'Nama Barang'],
                $invalidManifests->map(function ($m) {
                    return [$m->id, $m->nomor_kontainer, substr($m->nama_barang ?? '', 0, 30)];
                })->toArray()
            );
        }

        // Step 4: Find naik_kapal sudah_ob that are missing from manifest
        $existingContainers = Manifest::where('no_voyage', $voyage)
            ->pluck('nomor_kontainer')
            ->toArray();

        $missingInManifest = NaikKapal::where('no_voyage', $voyage)
            ->where('sudah_ob', true)
            ->whereNotIn('nomor_kontainer', $existingContainers)
            ->get();

        $this->newLine();
        $this->info("Manifests to CREATE (sudah OB but missing): " . $missingInManifest->count());

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY RUN - No changes made. Remove --dry-run to execute.');
            return Command::SUCCESS;
        }

        // Confirm before proceeding
        if (!$this->confirm('Do you want to proceed with cleanup and sync?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        // Step 5: Delete invalid manifests
        $this->info('Deleting invalid manifests...');
        $deleteCount = 0;
        foreach ($invalidManifests as $manifest) {
            $manifest->delete();
            $deleteCount++;
        }
        $this->info("Deleted: {$deleteCount}");

        // Step 6: Create missing manifests
        $this->info('Creating missing manifests...');
        $createCount = 0;
        $errors = 0;

        foreach ($missingInManifest as $nk) {
            try {
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

                if ($nk->prospek) {
                    $prospek = $nk->prospek;
                    $manifestData['pengirim'] = $prospek->pt_pengirim ?? $prospek->pengirim ?? null;
                    $manifestData['alamat_pengirim'] = $prospek->alamat_pengirim ?? null;
                    $manifestData['penerima'] = $prospek->pt_penerima ?? $prospek->penerima ?? null;
                    $manifestData['alamat_penerima'] = $prospek->alamat_penerima ?? null;
                    $manifestData['pelabuhan_muat'] = $prospek->port_muat ?? $nk->pelabuhan_asal ?? null;
                    $manifestData['pelabuhan_bongkar'] = $prospek->port_bongkar ?? $nk->pelabuhan_tujuan ?? null;
                }

                Manifest::create($manifestData);
                $createCount++;
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error creating manifest for {$nk->nomor_kontainer}: " . $e->getMessage());
            }
        }

        $this->info("Created: {$createCount}");
        if ($errors > 0) {
            $this->warn("Errors: {$errors}");
        }

        // Step 7: Show final counts
        $this->newLine();
        $this->info('=== Final Status ===');
        $finalManifestCount = Manifest::where('no_voyage', $voyage)->count();
        $this->table(
            ['Table', 'Count'],
            [
                ['NaikKapal (Sudah OB)', $naikKapalOB],
                ['Manifest', $finalManifestCount],
            ]
        );

        return Command::SUCCESS;
    }
}
