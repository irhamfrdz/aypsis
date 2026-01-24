<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixKapalSumberAbadi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:kapal-sumber-abadi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix data kapal KM SUMBER ABADI (remove number from name and move to voyage)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix KM SUMBER ABADI data...');

        $bls = \App\Models\Bl::where('nama_kapal', 'LIKE', 'KM SUMBER ABADI%')->get();
        $count = $bls->count();
        
        if ($count === 0) {
            $this->info('No data found for KM SUMBER ABADI.');
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $updatedCount = 0;

        foreach ($bls as $bl) {
            // Cek apakah nama kapal mengandung angka di akhir (contoh: KM SUMBER ABADI 178)
            // Regex match: (Nama Kapal)(Spasi)(Angka)
            if (preg_match('/(KM\.?\s*SUMBER\s*ABADI)\s+(\d+)$/i', $bl->nama_kapal, $matches)) {
                $baseName = "KM SUMBER ABADI"; 
                $number = $matches[2]; // The number (e.g., 178)
                
                $originalName = $bl->nama_kapal;
                
                // Update nama kapal menjadi bersih
                $bl->nama_kapal = $baseName;
                
                // Update no_voyage jika kosong atau placeholder
                if (empty($bl->no_voyage) || trim($bl->no_voyage) == '-') {
                    $bl->no_voyage = $number;
                }
                
                $bl->save();
                $updatedCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done! Processed $count records. Updated $updatedCount records.");
    }
}
