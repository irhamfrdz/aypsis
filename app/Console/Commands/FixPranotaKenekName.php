<?php

namespace App\Console\Commands;

use App\Models\PranotaUangRitKenek;
use App\Models\PranotaUangRitKenekDetail;
use Illuminate\Console\Command;

class FixPranotaKenekName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pranota:fix-kenek {pranota=PURK-06-26-000024} {--old=RIKY RISWANTO} {--new=WANTO}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes incorrect kenek name in Pranota Uang Rit Kenek master and details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $noPranota = $this->argument('pranota');
        $oldName = $this->option('old');
        $newName = $this->option('new');

        $this->info("Fixing Pranota: {$noPranota}");
        $this->info("Replacing '{$oldName}' with '{$newName}'");

        // 1. Update the master record
        $master = PranotaUangRitKenek::where('no_pranota', $noPranota)->first();
        if ($master) {
            $oldKenekNama = $master->kenek_nama;
            if (str_contains($oldKenekNama, $oldName)) {
                $master->kenek_nama = str_replace($oldName, $newName, $oldKenekNama);
                $master->save();
                $this->info("Master record updated: '{$oldKenekNama}' -> '{$master->kenek_nama}'");
            } else {
                $this->warn("Master record kenek_nama does not contain '{$oldName}'. Current value: '{$oldKenekNama}'");
            }
        } else {
            $this->error("Pranota master record {$noPranota} not found.");
        }

        // 2. Update the detail record
        $detail = PranotaUangRitKenekDetail::where('no_pranota', $noPranota)
            ->where('kenek_nama', $oldName)
            ->first();
        if ($detail) {
            $detail->kenek_nama = $newName;
            $detail->save();
            $this->info("Detail record updated: Kenek name changed to '{$newName}'");
        } else {
            // Let's also check if it's already updated
            $alreadyUpdated = PranotaUangRitKenekDetail::where('no_pranota', $noPranota)
                ->where('kenek_nama', $newName)
                ->exists();
            if ($alreadyUpdated) {
                $this->info("Detail record already has '{$newName}' as kenek name.");
            } else {
                $this->warn("Detail record for '{$oldName}' not found under Pranota {$noPranota}.");
            }
        }

        $this->info('Fix execution finished.');

        return 0;
    }
}
