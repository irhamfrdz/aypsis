<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LangsirBatam;
use Illuminate\Support\Facades\DB;

class DeleteLangsirJuli extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'langsir:delete-juli {--year=2026 : Tahun dari data yang akan dihapus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus transaksi langsir batam dari tanggal 11 Juli sampai 24 Juli';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->option('year');
        $startDate = "$year-07-11";
        $endDate = "$year-07-24";
        
        $this->info("Akan menghapus data Langsir Batam dari tanggal $startDate sampai $endDate...");
        
        if ($this->confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $count = LangsirBatam::whereBetween('tanggal', [$startDate, $endDate])->count();
            
            if ($count === 0) {
                $this->info("Tidak ada data yang ditemukan pada range tanggal tersebut.");
                return;
            }
            
            DB::beginTransaction();
            try {
                LangsirBatam::whereBetween('tanggal', [$startDate, $endDate])->delete();
                DB::commit();
                $this->info("Berhasil menghapus $count baris data.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Gagal menghapus data: " . $e->getMessage());
            }
        } else {
            $this->info('Dibatalkan.');
        }
    }
}
