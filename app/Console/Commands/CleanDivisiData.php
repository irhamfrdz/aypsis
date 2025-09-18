<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanDivisiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-divisi-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all divisi data from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Divisi::count();

        if ($count === 0) {
            $this->info('Tidak ada data divisi yang perlu dibersihkan.');
            return;
        }

        $this->warn("Akan menghapus {$count} data divisi dari database.");

        if (!$this->confirm('Apakah Anda yakin ingin melanjutkan?')) {
            $this->info('Operasi dibatalkan.');
            return;
        }

        try {
            \App\Models\Divisi::truncate();
            $this->info("Berhasil menghapus {$count} data divisi.");
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
