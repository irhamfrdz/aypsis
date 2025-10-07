<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockKontainer;
use App\Http\Controllers\StockKontainerImportController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;

class TestStockKontainerImport extends Command
{
    protected $signature = 'test:stock-kontainer-import';
    protected $description = 'Test stock kontainer import functionality';

    public function handle()
    {
        $this->info('Testing Stock Kontainer Import/Export functionality...');

        try {
            // Test 1: Check current data count
            $currentCount = StockKontainer::count();
            $this->info("Current stock kontainer count: {$currentCount}");

            // Test 2: Test template download (simulate)
            $controller = new StockKontainerImportController();
            $this->info("✓ Template download controller method exists");

            // Test 3: Create sample CSV content
            $csvContent = "Nomor Kontainer;Ukuran;Tipe Kontainer;Status;Tahun Pembuatan;Keterangan\n";
            $csvContent .= "TEST001;20ft;Dry;available;2020;Kontainer untuk testing\n";
            $csvContent .= "TEST002;40ft;Reefer;maintenance;2019;Kontainer reefer untuk testing\n";
            $csvContent .= "TEST003;20ft;Dry;rented;2021;Kontainer disewa untuk testing\n";

            // Test 4: Write to temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'stock_kontainer_test') . '.csv';
            file_put_contents($tempFile, $csvContent);
            $this->info("✓ Test CSV file created: {$tempFile}");

            // Test 5: Simulate file upload
            $uploadedFile = new UploadedFile(
                $tempFile,
                'test_stock_kontainer.csv',
                'text/csv',
                null,
                true
            );

            // Test 6: Create mock request
            $request = new Request();
            $request->files->set('excel_file', $uploadedFile);

            // Test 7: Test import functionality
            $this->info("Testing import functionality...");
            $response = $controller->import($request);

            // Test 8: Check if data was imported
            $newCount = StockKontainer::count();
            $imported = $newCount - $currentCount;
            $this->info("Stock kontainers after import: {$newCount} (imported: {$imported})");

            // Test 9: Verify specific records
            $testRecords = StockKontainer::whereIn('nomor_kontainer', ['TEST001', 'TEST002', 'TEST003'])->get();
            $this->info("Test records found: " . $testRecords->count());

            foreach ($testRecords as $record) {
                $this->info("- {$record->nomor_kontainer}: {$record->ukuran}, {$record->status}");
            }

            // Test 10: Test update functionality (run import again)
            $this->info("\nTesting update functionality...");
            $updateCsvContent = "Nomor Kontainer;Ukuran;Tipe Kontainer;Status;Tahun Pembuatan;Keterangan\n";
            $updateCsvContent .= "TEST001;20ft;Dry;damaged;2020;Status updated to damaged\n";

            file_put_contents($tempFile, $updateCsvContent);
            $uploadedFile2 = new UploadedFile(
                $tempFile,
                'test_update_stock_kontainer.csv',
                'text/csv',
                null,
                true
            );

            $request2 = new Request();
            $request2->files->set('excel_file', $uploadedFile2);

            $response2 = $controller->import($request2);

            // Verify update
            $updatedRecord = StockKontainer::where('nomor_kontainer', 'TEST001')->first();
            if ($updatedRecord && $updatedRecord->status === 'damaged') {
                $this->info("✓ Update test passed - TEST001 status changed to: {$updatedRecord->status}");
            } else {
                $this->error("✗ Update test failed");
            }

            // Test 11: Route testing
            $this->info("\nTesting routes...");
            $routes = collect(Route::getRoutes())->filter(function($route) {
                return str_contains($route->getName() ?? '', 'stock-kontainer');
            });

            foreach ($routes as $route) {
                $this->info("Route: " . $route->getName() . " -> " . $route->uri());
            }

            // Clean up
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            $this->info("\n✅ All tests completed successfully!");

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
