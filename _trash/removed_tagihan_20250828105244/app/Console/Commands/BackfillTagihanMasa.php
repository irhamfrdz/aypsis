<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackfillTagihanMasa extends Command
{
    protected $signature = 'tagihan:backfill-masa {--batch=1000}';
    protected $description = 'Backfill masa_awal and masa_akhir for existing tagihan_kontainer_sewa rows';

    public function handle()
    {
    // The TagihanKontainerSewa model was removed as part of a refactor.
    // This command is intentionally disabled to avoid runtime errors.
    $this->info('BackfillTagihanMasa is disabled because TagihanKontainerSewa has been removed.');
    $this->info('If you need backfill logic, re-implement it against the new model or use a safe DB-based script.');
    return 0;
    }
}
