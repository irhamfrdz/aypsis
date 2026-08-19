<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOrphanedManifests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifest:clean-stock-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up mistakenly imported stock amprahan data from the manifests table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mencari data stok amprahan yang nyasar di tabel manifests...');

        // Cari record yang nama_kapal-nya berawalan BCA, EBK, atau JVM
        $badRecords = DB::table('manifests')
            ->where('nama_kapal', 'like', 'BCA%')
            ->orWhere('nama_kapal', 'like', 'EBK%')
            ->orWhere('nama_kapal', 'like', 'JVM%')
            ->get();

        if ($badRecords->isEmpty()) {
            $this->info('Tidak ada data salah yang ditemukan.');
            return;
        }

        $this->info('Ditemukan ' . $badRecords->count() . ' baris data yang salah.');

        if ($this->confirm('Apakah Anda yakin ingin menghapus ' . $badRecords->count() . ' baris data ini?')) {
            $deleted = DB::table('manifests')
                ->where('nama_kapal', 'like', 'BCA%')
                ->orWhere('nama_kapal', 'like', 'EBK%')
                ->orWhere('nama_kapal', 'like', 'JVM%')
                ->delete();

            $this->info("Berhasil menghapus {$deleted} baris data.");
        } else {
            $this->info('Operasi dibatalkan.');
        }
    }
}
