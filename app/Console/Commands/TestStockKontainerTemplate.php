<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\StockKontainerImportController;

class TestStockKontainerTemplate extends Command
{
    protected $signature = 'test:stock-kontainer-template';
    protected $description = 'Test stock kontainer template download';

    public function handle()
    {
        $this->info('Testing Stock Kontainer Template Download...');

        try {
            // Test controller instantiation
            $controller = new StockKontainerImportController();
            $this->info("✓ Controller instantiated successfully");

            // Test method exists
            if (method_exists($controller, 'downloadTemplate')) {
                $this->info("✓ downloadTemplate method exists");
            } else {
                $this->error("✗ downloadTemplate method not found");
                return;
            }

            // Test if controller can be called (simulate request)
            try {
                // Create a mock response to test the method
                $response = $controller->downloadTemplate();
                $this->info("✓ downloadTemplate method executed successfully");
                $this->info("Response type: " . get_class($response));
            } catch (\Exception $e) {
                $this->error("✗ Error calling downloadTemplate: " . $e->getMessage());
                $this->error($e->getTraceAsString());
            }

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
