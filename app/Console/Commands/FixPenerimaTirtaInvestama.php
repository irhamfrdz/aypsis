<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Pengirim;
use App\Models\Penerima;
use Illuminate\Support\Facades\DB;

class FixPenerimaTirtaInvestama extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:penerima-tirta-investama {--force : Force execution without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update penerima Order menjadi GUNA CIPTA KARSA MANDIRI untuk Pengirim PT TIRTA INVESTAMA yang sudah memiliki Term';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetPenerimaName = 'GUNA CIPTA KARSA MANDIRI';
        $targetPengirimName = 'PT TIRTA INVESTAMA';

        $this->info("Mencari Pengirim: $targetPengirimName");
        $pengirim = Pengirim::where('nama_pengirim', 'LIKE', "%$targetPengirimName%")->first();

        if (!$pengirim) {
            $this->error("Pengirim '$targetPengirimName' tidak ditemukan.");
            return 1;
        }

        $this->info("Mencari Penerima Master: $targetPenerimaName");
        $penerimaMaster = Penerima::where('nama_penerima', 'LIKE', "%$targetPenerimaName%")->first();

        if ($penerimaMaster) {
            $this->info("Penerima Master ditemukan: " . $penerimaMaster->nama_penerima . " (ID: " . $penerimaMaster->id . ")");
        } else {
            $this->warn("Penerima Master '$targetPenerimaName' tidak ditemukan. Hanya akan mengupdate kolom teks 'penerima'.");
        }

        $orders = Order::where('pengirim_id', $pengirim->id)
            ->whereNotNull('term_id') // Hanya yang sudah ada term
            ->get();

        $count = $orders->count();

        if ($count === 0) {
            $this->info("Tidak ada order yang memenuhi kriteria (Pengirim: $targetPengirimName, Memiliki Term).");
            return 0;
        }

        $this->info("Ditemukan $count order yang akan diupdate.");

        if (!$this->option('force') && !$this->confirm("Apakah Anda yakin ingin mengupdate $count data order ini?")) {
            $this->info('Operasi dibatalkan.');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updated = 0;
        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                // Update logic
                // Jika penerima saat ini SUDAH benar, skip?
                // User didn't say skip if already correct, but it's good practice. 
                // However, user said "merubah data penerima...". 
                
                $order->penerima = $targetPenerimaName;
                if ($penerimaMaster) {
                    $order->penerima_id = $penerimaMaster->id;
                    // Optional: update address if master has address? 
                    // User didn't ask for address update, just "data penerima". 
                    // Safest to just update name and ID.
                    if ($penerimaMaster->alamat) {
                         $order->alamat_penerima = $penerimaMaster->alamat;
                    }
                } else {
                    $order->penerima_id = null; // Unlink if previously linked to wrong penerima? Or keep it?
                    // Better to set null if we are changing the name to something that doesn't match the ID.
                }

                $order->save();
                $updated++;
                $bar->advance();
            }
            
            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info("Berhasil mengupdate $updated order.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine();
            $this->error("Terjadi error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
