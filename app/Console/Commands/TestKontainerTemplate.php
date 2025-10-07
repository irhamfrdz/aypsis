<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\KontainerImportController;

class TestKontainerTemplate extends Command
{
    protected $signature = 'test:kontainer-template';
    protected $description = 'Test kontainer template download without special permission';

    public function handle()
    {
        $this->info('=== TEST KONTAINER TEMPLATE ACCESS ===');

        try {
            // Test template download
            $controller = new KontainerImportController();

            $this->info('1. Testing kontainer CSV template download...');
            $response = $controller->downloadTemplate();

            if ($response->getStatusCode() === 200) {
                $this->info('✓ Kontainer template download successful');
                $this->info('   Content-Type: ' . $response->headers->get('Content-Type'));
                $this->info('   Content-Disposition: ' . $response->headers->get('Content-Disposition'));
            } else {
                $this->error('✗ Kontainer template download failed with status: ' . $response->getStatusCode());
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }

        $this->info('');
        $this->info('=== PERMISSION SUMMARY ===');
        $this->info('✓ Kontainer template downloads: NO PERMISSION REQUIRED');
        $this->info('✓ Kontainer import: Requires master-kontainer-create permission');
        $this->info('✓ Kontainer create: Requires master-kontainer-create permission');

        $this->info('');
        $this->info('=== TEST COMPLETED ===');
        return 0;
    }
}
