<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\TandaTerima;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTandaTerimaTerms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tanda-terima:sync-terms 
                            {--order-id= : ID order tertentu yang akan disinkronkan}
                            {--all : Jalankan sinkronisasi untuk semua data tanda terima}
                            {--dry-run : Tampilkan preview perubahan tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data term (condition) dari Order ke Surat Jalan (term_id) dan Tanda Terima (nama_status)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== Sinkronisasi Term Tanda Terima & Surat Jalan ===');
        $this->newLine();

        $orderId = $this->option('order-id');
        $syncAll = $this->option('all');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('MODE DRY RUN - Perubahan tidak akan disimpan ke database.');
            $this->newLine();
        }

        if (! $orderId && ! $syncAll) {
            $this->error('Harap tentukan opsi: gunakan --all untuk sinkronisasi semua, atau --order-id=ID untuk order spesifik.');

            return 1;
        }

        try {
            DB::beginTransaction();

            $query = Order::query();

            if ($orderId) {
                $query->where('id', $orderId);
            }

            $orders = $query->with(['suratJalans.tandaTerima', 'term'])->get();

            if ($orders->isEmpty()) {
                $this->warn('Tidak ada order yang ditemukan.');

                return 0;
            }

            $this->info("Memproses {$orders->count()} data order...");
            $this->newLine();

            $updatedSjCount = 0;
            $updatedTtCount = 0;
            $bar = $this->output->createProgressBar($orders->count());
            $bar->start();

            foreach ($orders as $order) {
                $termId = $order->term_id;
                $termName = $order->term->nama_status ?? null;

                foreach ($order->suratJalans as $suratJalan) {
                    // 1. Sync term_id ke SuratJalan jika berbeda
                    if ($termId && $suratJalan->term !== $termId) {
                        $updatedSjCount++;
                        if (! $dryRun) {
                            $suratJalan->update(['term' => $termId]);
                        }
                    }

                    // 2. Sync term name ke TandaTerima jika berbeda
                    if ($suratJalan->tandaTerima) {
                        $tandaTerima = $suratJalan->tandaTerima;
                        if ($termName && $tandaTerima->term !== $termName) {
                            $updatedTtCount++;
                            if (! $dryRun) {
                                $tandaTerima->update(['term' => $termName]);
                            }
                        }
                    }
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            if (! $dryRun) {
                DB::commit();
                $this->info('=== SINKRONISASI SELESAI ===');
                $this->info("Total Surat Jalan yang diperbarui: {$updatedSjCount}");
                $this->info("Total Tanda Terima yang diperbarui: {$updatedTtCount}");
            } else {
                DB::rollBack();
                $this->info('=== PREVIEW DRY RUN SELESAI ===');
                $this->info("Surat Jalan yang akan diperbarui: {$updatedSjCount}");
                $this->info("Tanda Terima yang akan diperbarui: {$updatedTtCount}");
                $this->newLine();
                $this->warn('Gunakan perintah tanpa --dry-run untuk menyimpan perubahan.');
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: '.$e->getMessage());
            $this->error($e->getTraceAsString());

            return 1;
        }
    }
}
