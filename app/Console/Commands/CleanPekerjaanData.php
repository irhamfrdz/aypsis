<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanPekerjaanData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-pekerjaan-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all pekerjaan data from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Pekerjaan::count();
        
        if ($count === 0) {
            $this->info('Tidak ada data pekerjaan yang perlu dibersihkan.');
            return;
        }
        
        $this->warn("Akan menghapus {$count} data pekerjaan dari database.");
        
        if (!$this->confirm('Apakah Anda yakin ingin melanjutkan?')) {
            $this->info('Operasi dibatalkan.');
            return;
        }
        
        try {
            \App\Models\Pekerjaan::truncate();
            $this->info("Berhasil menghapus {$count} data pekerjaan.");
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
