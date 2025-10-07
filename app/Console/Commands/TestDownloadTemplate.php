<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\KontainerImportController;
use Illuminate\Http\Request;

class TestDownloadTemplate extends Command
{
    protected $signature = 'test:download-template';
    protected $description = 'Test download template functionality';

    public function handle()
    {
        $this->info('=== TEST DOWNLOAD TEMPLATE ===');
        
        try {
            $controller = new KontainerImportController();
            $response = $controller->downloadTemplate();
            
            // Get content
            $content = $response->getContent();
            
            $this->info('Template content:');
            $this->line($content);
            
            // Save to file for verification
            $filename = 'downloaded_template_test.csv';
            file_put_contents($filename, $content);
            
            $this->info("Template saved to: {$filename}");
            
            // Verify format
            $lines = explode("\n", trim($content));
            $header = str_getcsv($lines[0], ';');
            
            $expectedHeader = ['Awalan Kontainer', 'Nomor Seri', 'Akhiran', 'Ukuran', 'Vendor'];
            
            if ($header === $expectedHeader) {
                $this->info('✓ Header format correct');
            } else {
                $this->error('✗ Header format incorrect');
                $this->error('Expected: ' . implode(';', $expectedHeader));
                $this->error('Actual: ' . implode(';', $header));
            }
            
            // Check if file has only header (clean template)
            if (count($lines) === 1) {
                $this->info('✓ Template is clean (header only)');
            } else {
                $this->warn('Template contains example data');
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        $this->info('=== TEST DOWNLOAD SELESAI ===');
        return 0;
    }
}