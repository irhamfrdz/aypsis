<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Manifest;
use App\Models\Prospek;
use App\Models\TandaTerima;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UpdateManifestPenerima extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifest:update-penerima
                            {--manifest-id= : ID manifest tertentu yang ingin diupdate}
                            {--all : Update semua manifest}
                            {--dry-run : Preview changes tanpa update database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update data penerima dan alamat pada manifest berdasarkan data dari tanda terima melalui prospek_id, serta update table prospek';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Update Manifest & Prospek ===');
        $this->newLine();

        $manifestId = $this->option('manifest-id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('MODE DRY RUN - Tidak ada data yang akan diupdate');
            $this->newLine();
        }

        try {
            DB::beginTransaction();

            $totalProspekUpdated = 0;
            $totalProspekWithChanges = 0;
            $totalProspek = 0;

            // --- 1. Update Prospek dari Tanda Terima ---
            $this->info('--- Memproses Update Prospek ---');
            
            // Query prospek yang memiliki tanda_terima_id
            $prospeks = Prospek::whereNotNull('tanda_terima_id')->with('tandaTerima')->get();

            if ($prospeks->isNotEmpty()) {
                $bar = $this->output->createProgressBar($prospeks->count());
                $bar->start();

                foreach ($prospeks as $prospek) {
                    $totalProspek++;
                    $tandaTerima = $prospek->tandaTerima;

                    if (!$tandaTerima) {
                        $bar->advance();
                        continue;
                    }

                    $hasChanges = false;

                    // Update fields
                    // pt_pengirim
                    if ($tandaTerima->pengirim && $prospek->pt_pengirim != $tandaTerima->pengirim) {
                        $prospek->pt_pengirim = $tandaTerima->pengirim;
                        $hasChanges = true;
                    }

                    // tujuan_pengiriman
                    if ($tandaTerima->tujuan_pengiriman && $prospek->tujuan_pengiriman != $tandaTerima->tujuan_pengiriman) {
                        $prospek->tujuan_pengiriman = $tandaTerima->tujuan_pengiriman;
                        $hasChanges = true;
                    }

                    // nama_supir
                    if ($tandaTerima->supir && $prospek->nama_supir != $tandaTerima->supir) {
                        $prospek->nama_supir = $tandaTerima->supir;
                        $hasChanges = true;
                    }

                    // barang / jenis_barang
                    if ($tandaTerima->jenis_barang && $prospek->barang != $tandaTerima->jenis_barang) {
                        $prospek->barang = $tandaTerima->jenis_barang;
                        $hasChanges = true;
                    }
                    
                    // nama_kapal / estimasi_nama_kapal
                    if ($tandaTerima->estimasi_nama_kapal && $prospek->nama_kapal != $tandaTerima->estimasi_nama_kapal) {
                        $prospek->nama_kapal = $tandaTerima->estimasi_nama_kapal;
                        $hasChanges = true;
                    }

                    if ($hasChanges) {
                        $totalProspekWithChanges++;
                        if (!$dryRun) {
                            $prospek->save();
                            $totalProspekUpdated++;
                        }
                    }

                    $bar->advance();
                }
                $bar->finish();
                $this->newLine();
            } else {
                $this->info('Tidak ada prospek dengan tanda terima yang ditemukan.');
            }

            $this->newLine();

            // --- 2. Update Manifest dari Prospek -> Tanda Terima ---
            $this->info('--- Memproses Update Manifest ---');

            // Query manifest yang memiliki prospek_id
            $query = Manifest::whereNotNull('prospek_id')
                ->with(['prospek.tandaTerima']);

            if ($manifestId) {
                $query->where('id', $manifestId);
            }

            $manifests = $query->get();

            $totalUpdated = 0;
            $totalManifestCount = 0; // Renamed to avoid confusion with loop var
            $totalWithChanges = 0;

            if ($manifests->isEmpty()) {
                $this->warn('Tidak ada manifest dengan prospek_id yang ditemukan');
            } else {
                $this->info("Ditemukan {$manifests->count()} manifest dengan prospek_id");
                
                $bar = $this->output->createProgressBar($manifests->count());
                $bar->start();

                foreach ($manifests as $manifest) {
                    $totalManifestCount++;
                    
                    // Cek apakah prospek memiliki tanda terima
                    if (!$manifest->prospek || !$manifest->prospek->tandaTerima) {
                        $bar->advance();
                        continue;
                    }

                    $tandaTerima = $manifest->prospek->tandaTerima;
                    
                    // Ambil data penerima dari tanda terima
                    $penerimaName = $tandaTerima->penerima;
                    $alamatPenerima = $tandaTerima->alamat_penerima;
                    
                    // Cek apakah ada perubahan
                    $hasChanges = false;
                    
                    if ($penerimaName && $manifest->penerima != $penerimaName) {
                        $hasChanges = true;
                    }
                    
                    if ($alamatPenerima && $manifest->alamat_penerima != $alamatPenerima) {
                        $hasChanges = true;
                    }
                    
                    if ($hasChanges) {
                        $totalWithChanges++;
                        
                        if (!$dryRun) {
                            // Update manifest
                            if ($penerimaName) {
                                $manifest->penerima = $penerimaName;
                            }
                            if ($alamatPenerima) {
                                $manifest->alamat_penerima = $alamatPenerima;
                            }
                            $manifest->save();
                            $totalUpdated++;
                        }
                    }
                    
                    $bar->advance();
                }
                
                $bar->finish();
                $this->newLine(2);
            }

            if (!$dryRun) {
                DB::commit();
                $this->newLine();
                $this->info('=== SELESAI ===');
                $this->info("Total Prospek diproses: {$totalProspek}");
                $this->info("Total Prospek updated: {$totalProspekUpdated}");
                $this->line("------------------------------------------------");
                $this->info("Total Manifest diproses: {$manifests->count()}");
                $this->info("Total Manifest dengan perubahan: {$totalWithChanges}");
                $this->info("Total Manifest berhasil diupdate: {$totalUpdated}");
                
                // Record last run time
                Cache::put('last_manifest_update', now(), now()->addDays(7));
            } else {
                DB::rollBack();
                $this->newLine();
                $this->info('=== DRY RUN SELESAI ===');
                $this->info("Total Prospek diproses: {$totalProspek}");
                $this->info("Total Prospek changes preview: {$totalProspekWithChanges}");
                $this->line("------------------------------------------------");
                $this->info("Total Manifest diproses: {$manifests->count()}");
                $this->info("Total Manifest changes preview: {$totalWithChanges}");
                $this->newLine();
                if ($totalWithChanges > 0 || $totalProspekWithChanges > 0) {
                    $this->warn('Gunakan perintah tanpa --dry-run untuk melakukan update sebenarnya');
                } else {
                    $this->info('Tidak ada data yang perlu diupdate (semua data sudah sama)');
                }
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
