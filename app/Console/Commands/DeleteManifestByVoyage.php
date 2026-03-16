<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Manifest;

class DeleteManifestByVoyage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifest:delete {voyage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus data manifest berdasarkan nomor voyage tertentu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $voyage = $this->argument('voyage');
        
        $count = Manifest::where('no_voyage', $voyage)->count();

        if ($count == 0) {
            $this->error("Tidak ada data manifest dengan nomor voyage: {$voyage}");
            return Command::FAILURE;
        }

        if ($this->confirm("Apakah Anda yakin ingin menghapus {$count} data manifest untuk voyage {$voyage}?")) {
            Manifest::where('no_voyage', $voyage)->delete();
            $this->info("Berhasil menghapus {$count} data manifest untuk voyage {$voyage}.");
            return Command::SUCCESS;
        }

        $this->warn("Operasi dibatalkan.");
        return Command::SUCCESS;
    }
}
