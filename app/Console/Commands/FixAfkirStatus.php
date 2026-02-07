<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockBan;

class FixAfkirStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ban:fix-afkir';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates status of bans with kondisi "afkir" to "Rusak"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for bans with kondisi "afkir"...');

        $bans = StockBan::where('kondisi', 'afkir')
            ->where('status', '!=', 'Rusak')
            ->get();

        if ($bans->isEmpty()) {
            $this->info('No bans with kondisi "afkir" and incorrect status found.');
            return 0;
        }

        $count = 0;
        foreach ($bans as $ban) {
            $oldStatus = $ban->status;
            $ban->status = 'Rusak';
            $ban->save();
            
            $this->line("Updated Ban ID {$ban->id} ({$ban->nomor_seri}): {$oldStatus} -> Rusak");
            $count++;
        }

        $this->info("Successfully updated {$count} bans to status 'Rusak'.");
        return 0;
    }
}
