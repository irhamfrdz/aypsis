<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\SuratJalan;
use App\Models\TandaTerima;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UpdateTandaTerimaPenerima extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tanda-terima:update-penerima 
                            {--order-id= : ID order tertentu yang akan diupdate}
                            {--all : Update semua tanda terima yang terkait dengan order}
                            {--dry-run : Tampilkan preview tanpa melakukan update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update data penerima dan alamat pada table tanda_terima berdasarkan data order yang sudah diisi di approval order';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== Update Tanda Terima Penerima ===');
        $this->newLine();

        $orderId = $this->option('order-id');
        $updateAll = $this->option('all');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('MODE DRY RUN - Tidak ada data yang akan diupdate');
            $this->newLine();
        }

        try {
            DB::beginTransaction();

            // Query orders yang memiliki data penerima
            $query = Order::where(function($q) {
                $q->whereNotNull('penerima')
                  ->orWhereNotNull('penerima_id')
                  ->orWhereNotNull('alamat_penerima')
                  ->orWhereNotNull('kontak_penerima');
            });

            // Filter berdasarkan order ID jika diberikan
            if ($orderId) {
                $query->where('id', $orderId);
            }

            $orders = $query->with([
                'suratJalans.tandaTerima',
                'penerima'
            ])->get();

            if ($orders->isEmpty()) {
                $this->warn('Tidak ada order dengan data penerima yang ditemukan.');
                return 0;
            }

            $this->info("Ditemukan {$orders->count()} order dengan data penerima");
            $this->newLine();

            $totalUpdated = 0;
            $totalTandaTerima = 0;
            $totalWithChanges = 0;
            $orderCount = $orders->count();

            $this->info("Memproses {$orderCount} order...");
            $this->newLine();
            
            $bar = $this->output->createProgressBar($orderCount);
            $bar->start();

            foreach ($orders as $index => $order) {
                // Ambil data penerima dari order
                // penerima bisa berupa string (attribute) atau relationship ke MasterPengirimPenerima
                $penerimaName = null;
                
                // Cek jika ada penerima_id, maka load relationship
                if ($order->penerima_id) {
                    try {
                        $penerimaRelation = $order->penerima;  // This triggers the relationship
                        if (is_object($penerimaRelation) && isset($penerimaRelation->nama)) {
                            $penerimaName = $penerimaRelation->nama;
                        }
                    } catch (\Exception $e) {
                        // Jika relationship gagal, fallback ke attribute
                    }
                }
                
                // Jika masih null, coba ambil dari attribute penerima (string)
                if (!$penerimaName) {
                    $penerimaAttr = $order->getAttributes()['penerima'] ?? null;
                    if ($penerimaAttr && is_string($penerimaAttr)) {
                        $penerimaName = $penerimaAttr;
                    }
                }
                
                $alamatPenerima = $order->alamat_penerima;
                
                // Loop melalui surat jalan yang terkait dengan order
                foreach ($order->suratJalans as $suratJalan) {
                    if ($suratJalan->tandaTerima) {
                        $tandaTerima = $suratJalan->tandaTerima;
                        $totalTandaTerima++;
                        
                        // Cek apakah ada perubahan
                        $hasChanges = false;
                        
                        if ($penerimaName && $tandaTerima->penerima != $penerimaName) {
                            $hasChanges = true;
                        }
                        
                        if ($alamatPenerima && $tandaTerima->alamat_penerima != $alamatPenerima) {
                            $hasChanges = true;
                        }
                        
                        if ($hasChanges) {
                            $totalWithChanges++;
                            
                            if (!$dryRun) {
                                // Update tanda terima
                                if ($penerimaName) {
                                    $tandaTerima->penerima = $penerimaName;
                                }
                                if ($alamatPenerima) {
                                    $tandaTerima->alamat_penerima = $alamatPenerima;
                                }
                                $tandaTerima->save();
                                $totalUpdated++;
                            }
                        }
                    }
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);

            if (!$dryRun) {
                DB::commit();
                $this->newLine();
                $this->info("=== SELESAI ===");
                $this->info("Total Order diproses: {$orders->count()}");
                $this->info("Total Tanda Terima ditemukan: {$totalTandaTerima}");
                $this->info("Total Tanda Terima dengan perubahan: {$totalWithChanges}");
                $this->info("Total Tanda Terima berhasil diupdate: {$totalUpdated}");
                
                // Record last run time
                Cache::put('last_tanda_terima_update', now(), now()->addDays(7));
            } else {
                DB::rollBack();
                $this->newLine();
                $this->info("=== DRY RUN SELESAI ===");
                $this->info("Total Order diproses: {$orders->count()}");
                $this->info("Total Tanda Terima ditemukan: {$totalTandaTerima}");
                $this->info("Total Tanda Terima yang akan diupdate: {$totalWithChanges}");
                $this->newLine();
                if ($totalWithChanges > 0) {
                    $this->warn("Gunakan perintah tanpa --dry-run untuk melakukan update sebenarnya");
                } else {
                    $this->info("Tidak ada data yang perlu diupdate (semua data sudah sama)");
                }
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
