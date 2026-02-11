<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Manifest;
use App\Models\Prospek;
use App\Models\TandaTerima;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Update data penerima dan alamat pada manifest berdasarkan data dari tanda terima melalui prospek_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Update Manifest Penerima ===');
        $this->newLine();

        $manifestId = $this->option('manifest-id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('MODE DRY RUN - Tidak ada data yang akan diupdate');
            $this->newLine();
        }

        try {
            DB::beginTransaction();

            // Query manifest yang memiliki prospek_id
            $query = Manifest::whereNotNull('prospek_id')
                ->with(['prospek.tandaTerima']);

            if ($manifestId) {
                $query->where('id', $manifestId);
            }

            $manifests = $query->get();

            if ($manifests->isEmpty()) {
                $this->warn('Tidak ada manifest dengan prospek_id yang ditemukan');
                return 0;
            }

            $this->info("Ditemukan {$manifests->count()} manifest dengan prospek_id");
            $this->newLine();

            $totalUpdated = 0;
            $totalManifest = 0;
            $totalWithChanges = 0;
            $manifestCount = $manifests->count();

            $this->info("Memproses {$manifestCount} manifest...");
            $this->newLine();
            
            $bar = $this->output->createProgressBar($manifestCount);
            $bar->start();

            foreach ($manifests as $manifest) {
                $totalManifest++;
                
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

            if (!$dryRun) {
                DB::commit();
                $this->newLine();
                $this->info('=== SELESAI ===');
                $this->info("Total Manifest diproses: {$manifests->count()}");
                $this->info("Total Manifest dengan Tanda Terima: {$totalManifest}");
                $this->info("Total Manifest dengan perubahan: {$totalWithChanges}");
                $this->info("Total Manifest berhasil diupdate: {$totalUpdated}");
            } else {
                DB::rollBack();
                $this->newLine();
                $this->info('=== DRY RUN SELESAI ===');
                $this->info("Total Manifest diproses: {$manifests->count()}");
                $this->info("Total Manifest dengan Tanda Terima: {$totalManifest}");
                $this->info("Total Manifest yang akan diupdate: {$totalWithChanges}");
                $this->newLine();
                if ($totalWithChanges > 0) {
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
