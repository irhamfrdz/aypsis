<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
class BackfillTagihanMasaString extends Command
{
    protected $signature = 'tagihan:backfill-masa-string {--batch=1000}';
    protected $description = 'Backfill masa (string) column from model accessor';

    public function handle()
    {
    $this->info('BackfillTagihanMasaString is disabled: TagihanKontainerSewa model has been removed.');
    return 0;
    }
}
