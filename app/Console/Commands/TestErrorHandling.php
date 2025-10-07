<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\KontainerImportController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class TestErrorHandling extends Command
{
    protected $signature = 'test:error-handling';
    protected $description = 'Test error handling in import';

    public function handle()
    {
        $this->info('=== TEST ERROR HANDLING ===');
        
        try {
            // Read CSV content from our test file
            $csvContent = file_get_contents('test_error_handling.csv');
            
            $tempFile = tempnam(sys_get_temp_dir(), 'test_error') . '.csv';
            file_put_contents($tempFile, $csvContent);
            
            $this->info('Test CSV with errors created');
            
            // Create mock UploadedFile
            $uploadedFile = new UploadedFile(
                $tempFile,
                'test_error.csv',
                'text/csv',
                null,
                true
            );
            
            // Create mock request
            $request = new Request();
            $request->files->set('excel_file', $uploadedFile);
            
            // Clear session
            session()->flush();
            
            // Test controller
            $controller = new KontainerImportController();
            $response = $controller->import($request);
            
            // Check response
            if (session()->has('success')) {
                $this->info('✓ Success: ' . session('success'));
            }
            
            if (session()->has('error')) {
                $this->warn('Errors: ' . session('error'));
            }
            
            if (session()->has('warning')) {
                $this->warn('Warnings: ' . session('warning'));
            }
            
            // Verify specific records
            $records = \App\Models\Kontainer::whereIn('awalan_kontainer', ['ERRO', 'PASS', 'FAIL', 'GOOD'])->get();
            
            $this->info('');
            $this->info('Records found:');
            foreach ($records as $record) {
                $this->info("- {$record->nomor_seri_gabungan} | {$record->ukuran}ft | {$record->vendor}");
            }
            
            // Expected results:
            // ERRO12345: Should fail (5 digit nomor seri instead of 6)
            // PASS1234567: Should pass (valid format)
            // FAIL12345672: Should fail (30ft ukuran not allowed)
            // GOOD6543213: Should pass (valid format)
            
            $this->info('');
            $this->info('Expected: 2 success, 2 errors');
            $this->info('Actual: ' . $records->count() . ' records imported');
            
            // Cleanup
            unlink($tempFile);
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        $this->info('=== TEST ERROR HANDLING SELESAI ===');
        return 0;
    }
}