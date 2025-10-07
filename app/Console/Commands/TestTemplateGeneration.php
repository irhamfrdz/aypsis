<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestTemplateGeneration extends Command
{
    protected $signature = 'test:template-generation';
    protected $description = 'Test template generation directly';

    public function handle()
    {
        $this->info('=== TEST TEMPLATE GENERATION ===');
        
        // Simulate template generation
        $csvData = [
            ['Awalan Kontainer', 'Nomor Seri', 'Akhiran', 'Ukuran', 'Vendor']
        ];
        
        $filename = 'direct_template_test.csv';
        $file = fopen($filename, 'w');
        
        foreach ($csvData as $row) {
            fputcsv($file, $row, ';');
        }
        
        fclose($file);
        
        $this->info("Template generated: {$filename}");
        
        // Read and verify
        $content = file_get_contents($filename);
        $this->info('Content:');
        $this->line($content);
        
        // Parse and check
        $handle = fopen($filename, 'r');
        $header = fgetcsv($handle, 1000, ';');
        fclose($handle);
        
        $this->info('Parsed header: ' . implode(', ', $header));
        
        $expectedHeader = ['Awalan Kontainer', 'Nomor Seri', 'Akhiran', 'Ukuran', 'Vendor'];
        
        if ($header === $expectedHeader) {
            $this->info('✓ Header format correct');
        } else {
            $this->error('✗ Header format incorrect');
        }
        
        $this->info('=== TEST SELESAI ===');
        return 0;
    }
}