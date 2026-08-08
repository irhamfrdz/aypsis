<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuratJalanBongkaranBatam;
use Illuminate\Support\Facades\DB;

class DeleteSuratJalanBongkaranBatam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sjb-batam:delete-by-date {start_date=2026-07-11} {end_date=2026-07-24}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus Surat Jalan Bongkaran Batam berdasarkan rentang tanggal (Default: 11 Juli 2026 - 24 Juli 2026)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = $this->argument('start_date');
        $endDate = $this->argument('end_date');

        $this->info("Mencari data surat jalan bongkaran batam dari tanggal {$startDate} sampai {$endDate}...");

        $count = SuratJalanBongkaranBatam::whereBetween('tanggal_surat_jalan', [$startDate, $endDate])->count();
        
        if ($count === 0) {
            $this->warn("Tidak ada data ditemukan pada rentang tanggal tersebut.");
            return;
        }

        $this->info("Ditemukan {$count} data.");
        
        if ($this->confirm("Apakah Anda yakin ingin menghapus {$count} data ini?")) {
            DB::transaction(function () use ($startDate, $endDate) {
                SuratJalanBongkaranBatam::whereBetween('tanggal_surat_jalan', [$startDate, $endDate])->delete();
            });

            $this->info("✅ {$count} data surat jalan bongkaran batam berhasil dihapus.");
        } else {
            $this->info("❌ Operasi dibatalkan.");
        }
    }
}
