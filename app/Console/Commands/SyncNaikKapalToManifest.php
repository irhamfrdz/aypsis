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
        $this->info('Voyage: '.($voyage ?: 'All'));
        $this->info('Kapal: '.($kapal ?: 'All'));
        $this->info('Filter: '.($all ? 'ALL records' : 'Only sudah_ob=true'));
        $this->info('Mode: '.($dryRun ? 'DRY RUN (no changes)' : 'LIVE'));
        $this->newLine();

        // Build query for naik_kapal
        $query = NaikKapal::query();

        // By default, only sync records that have sudah_ob = true
        if (! $all) {
            $query->where('sudah_ob', true);
        }

        if ($voyage) {
            $query->where('no_voyage', $voyage);
        }

        if ($kapal) {
            $query->where('nama_kapal', 'like', '%'.$kapal.'%');
        }

        $naikKapals = $query->get();

        $this->info("Found {$naikKapals->count()} records in naik_kapal");

        // Check existing manifests
        $existingManifests = Manifest::query();
        if ($voyage) {
            $existingManifests->where('no_voyage', $voyage);
        }
        if ($kapal) {
            $existingManifests->where('nama_kapal', 'like', '%'.$kapal.'%');
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

            $isLcl = (strtoupper(trim($nk->prospek->tipe ?? $nk->tipe_kontainer ?? '')) === 'LCL');

            if ($isLcl) {
                $tandaTerimaRecords = \App\Models\TandaTerimaLclKontainerPivot::where('nomor_kontainer', $nk->nomor_kontainer)
                    ->when($nk->no_seal, function ($q) use ($nk) {
                        return $q->where('nomor_seal', $nk->no_seal);
                    })
                    ->with('tandaTerima.items')
                    ->get();

                if ($tandaTerimaRecords->count() > 0) {
                    foreach ($tandaTerimaRecords as $pivot) {
                        $tandaTerima = $pivot->tandaTerima;
                        if (! $tandaTerima) {
                            continue;
                        }

                        // Check if manifest already exists for this specific tanda terima
                        $existingManifest = Manifest::where('nomor_kontainer', $nk->nomor_kontainer)
                            ->where('no_voyage', $nk->no_voyage)
                            ->where('nama_kapal', $nk->nama_kapal)
                            ->where('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima)
                            ->first();

                        if ($existingManifest && ! $force) {
                            $skipped++;

                            continue;
                        }

                        if ($dryRun) {
                            $this->newLine();
                            $this->line("  Would create LCL manifest for: {$nk->nomor_kontainer} (TT: {$tandaTerima->nomor_tanda_terima})");
                            $created++;

                            continue;
                        }

                        try {
                            DB::beginTransaction();

                            $manifestData = [
                                'nomor_kontainer' => $nk->nomor_kontainer,
                                'no_seal' => $pivot->nomor_seal ?? $nk->no_seal,
                                'tipe_kontainer' => $nk->tipe_kontainer ?? 'LCL',
                                'size_kontainer' => $nk->size_kontainer,
                                'nama_kapal' => $nk->nama_kapal,
                                'no_voyage' => $nk->no_voyage,
                                'pelabuhan_asal' => $nk->pelabuhan_asal,
                                'pelabuhan_tujuan' => $nk->pelabuhan_tujuan,
                                'nomor_tanda_terima' => $tandaTerima->nomor_tanda_terima,
                                'pengirim' => $tandaTerima->nama_pengirim,
                                'penerima' => $tandaTerima->nama_penerima,
                                'alamat_pengirim' => $tandaTerima->alamat_pengirim,
                                'alamat_penerima' => $tandaTerima->alamat_penerima,
                                'alamat_pengiriman' => $tandaTerima->alamat_penerima,
                                'contact_person' => $tandaTerima->contact_person,
                                'volume' => $tandaTerima->items->sum('meter_kubik'),
                                'tonnage' => $tandaTerima->items->sum('tonase'),
                                'kuantitas' => $tandaTerima->total_koli ?? 1,
                                'prospek_id' => $nk->prospek_id,
                                'created_by' => $nk->created_by,
                                'updated_by' => $nk->updated_by,
                            ];

                            $itemNames = $tandaTerima->items->pluck('nama_barang')->filter()->unique()->toArray();
                            $manifestData['nama_barang'] = ! empty($itemNames) ? implode(', ', $itemNames) : $nk->jenis_barang;

                            $units = $tandaTerima->items->pluck('satuan')->unique()->filter();
                            if ($units->count() === 1) {
                                $manifestData['satuan'] = $units->first();
                            } elseif ($units->count() > 1) {
                                $manifestData['satuan'] = 'PKGS';
                            }

                            $manifestData['term'] = $tandaTerima->term ? ($tandaTerima->term instanceof \App\Models\Term ? $tandaTerima->term->kode : $tandaTerima->term) : null;

                            if ($existingManifest && $force) {
                                $existingManifest->update($manifestData);
                            } else {
                                $lastManifest = Manifest::whereNotNull('nomor_bl')->orderBy('id', 'desc')->first();
                                if ($lastManifest && $lastManifest->nomor_bl) {
                                    preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                                    $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                                    $manifestData['nomor_bl'] = 'MNF-'.str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                                } else {
                                    $manifestData['nomor_bl'] = 'MNF-000001';
                                }
                                Manifest::create($manifestData);
                            }

                            DB::commit();
                            $created++;
                        } catch (\Exception $e) {
                            DB::rollBack();
                            $errors++;
                            $this->newLine();
                            $this->error("  Error LCL sync for {$nk->nomor_kontainer}: ".$e->getMessage());
                        }
                    }

                    continue;
                }
            }

            // Check if manifest already exists for this container (non-LCL fallback)
            $existingManifest = Manifest::where('nomor_kontainer', $nk->nomor_kontainer)
                ->where('no_voyage', $nk->no_voyage)
                ->where('nama_kapal', $nk->nama_kapal)
                ->first();

            if ($existingManifest && ! $force) {
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

                    // Extract actual item name from Tanda Terima if available
                    if ($prospek->tandaTerima) {
                        $tt = $prospek->tandaTerima;
                        $manifestData['nomor_tanda_terima'] = $tt->nomor_tanda_terima ?? $tt->no_surat_jalan;

                        $itemNames = [];
                        if (! empty($tt->dimensi_items) && is_array($tt->dimensi_items)) {
                            foreach ($tt->dimensi_items as $item) {
                                if (! empty($item['nama_barang'])) {
                                    $itemNames[] = $item['nama_barang'];
                                }
                            }
                        } elseif (! empty($tt->dimensi_details) && is_array($tt->dimensi_details)) {
                            foreach ($tt->dimensi_details as $item) {
                                if (! empty($item['nama_barang'])) {
                                    $itemNames[] = $item['nama_barang'];
                                }
                            }
                        } elseif (! empty($tt->nama_barang)) {
                            if (is_array($tt->nama_barang)) {
                                $itemNames = $tt->nama_barang;
                            } elseif (is_string($tt->nama_barang) && $tt->nama_barang !== 'null') {
                                $itemNames[] = $tt->nama_barang;
                            }
                        }

                        if (! empty($itemNames)) {
                            $manifestData['nama_barang'] = implode(', ', $itemNames);
                        }
                    }
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
                $this->error("  Error for {$nk->nomor_kontainer}: ".$e->getMessage());
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
