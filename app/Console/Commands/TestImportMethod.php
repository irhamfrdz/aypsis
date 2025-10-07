<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\KontainerImportController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class TestImportMethod extends Command
{
    protected $signature = 'test:import-method';
    protected $description = 'Test import method functionality';

    public function handle()
    {
        $this->info('=== TEST IMPORT METHOD ===');
        
        try {
            // Create test CSV file
            $csvContent = "Awalan Kontainer;Nomor Seri;Akhiran;Ukuran;Vendor\n";
            $csvContent .= "METH;111111;1;20;PT. Method Test A\n";
            $csvContent .= "CTRL;222222;2;40;PT. Method Test B\n";
            
            $tempFile = tempnam(sys_get_temp_dir(), 'test_import') . '.csv';
            file_put_contents($tempFile, $csvContent);
            
            $this->info('Test CSV created: ' . $tempFile);
            
            // Create mock UploadedFile
            $uploadedFile = new UploadedFile(
                $tempFile,
                'test_import.csv',
                'text/csv',
                null,
                true // test mode
            );
            
            // Create mock request
            $request = new Request();
            $request->files->set('excel_file', $uploadedFile);
            
            // Test controller
            $controller = new KontainerImportController();
            $response = $controller->import($request);
            
            // Check response
            if ($response->isRedirection()) {
                $this->info('Response is redirect (expected)');
                
                // Check session for messages
                if (session()->has('success')) {
                    $this->info('✓ Success message: ' . session('success'));
                } elseif (session()->has('error')) {
                    $this->error('✗ Error message: ' . session('error'));
                } else {
                    $this->warn('No session message found');
                }
            } else {
                $this->warn('Response is not redirect');
                $this->info('Response content: ' . $response->getContent());
            }
            
            // Verify data in database
            $count = \App\Models\Kontainer::where('awalan_kontainer', 'METH')->count();
            $this->info("Records with METH prefix: {$count}");
            
            if ($count > 0) {
                $this->info('✓ Data imported successfully');
                $kontainer = \App\Models\Kontainer::where('awalan_kontainer', 'METH')->first();
                $this->info("Sample record: {$kontainer->nomor_seri_gabungan} - {$kontainer->vendor}");
            } else {
                $this->error('✗ No data imported');
            }
            
            // Cleanup
            unlink($tempFile);
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        $this->info('=== TEST IMPORT METHOD SELESAI ===');
        return 0;
    }
}