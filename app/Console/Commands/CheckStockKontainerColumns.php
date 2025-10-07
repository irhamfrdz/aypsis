<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckStockKontainerColumns extends Command
{
    protected $signature = 'check:stock-kontainer-columns';
    protected $description = 'Check columns in stock_kontainers table';

    public function handle()
    {
        $this->info('Checking stock_kontainers table columns...');

        try {
            $columns = Schema::getColumnListing('stock_kontainers');

            $this->info("Found " . count($columns) . " columns:");
            foreach ($columns as $column) {
                $this->info("- {$column}");
            }

            // Check if nomor_seri still exists
            if (in_array('nomor_seri', $columns)) {
                $this->error("⚠️ nomor_seri column still exists!");
            } else {
                $this->info("✅ nomor_seri column successfully removed");
            }

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}
