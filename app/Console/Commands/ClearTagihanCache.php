<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearTagihanCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear tagihan kontainer sewa filter cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keys = ['tagihan_vendors', 'tagihan_sizes', 'tagihan_periodes'];

        foreach ($keys as $key) {
            Cache::forget($key);
            $this->info("Cleared cache key: {$key}");
        }

        $this->info('All tagihan cache cleared successfully!');
        return 0;
    }
}
