<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateSupirJunaidi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aypsis:update-supir-junaidi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update nama supir JUNAIDI menjadi DJUNAIDY di seluruh tabel Surat Jalan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oldName = 'JUNAIDI';
        $newName = 'DJUNAIDY';

        $models = [
            \App\Models\SuratJalan::class,
            \App\Models\SuratJalanBongkaran::class,
            \App\Models\SuratJalanBatam::class,
            \App\Models\SuratJalanBongkaranBatam::class,
        ];

        $this->info("Starting update supir from $oldName to $newName...");

        $totalUpdated = 0;

        foreach ($models as $modelClass) {
            $modelName = class_basename($modelClass);
            $this->info("Updating $modelName...");

            // Update supir
            $updated1 = $modelClass::whereRaw('UPPER(supir) = ?', [strtoupper($oldName)])
                ->update(['supir' => $newName]);

            // Update supir2
            $updated2 = $modelClass::whereRaw('UPPER(supir2) = ?', [strtoupper($oldName)])
                ->update(['supir2' => $newName]);
                
            $tableTotal = $updated1 + $updated2;
            $totalUpdated += $tableTotal;

            $this->line("- $modelName: updated $tableTotal records.");
        }

        $this->info("Done! Total updated records: $totalUpdated");
    }
}
