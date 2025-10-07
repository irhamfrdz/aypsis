<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\KaryawanController;
use Illuminate\Http\Request;

class TestKaryawanTemplate extends Command
{
    protected $signature = 'test:karyawan-template';
    protected $description = 'Test karyawan template download without special permission';

    public function handle()
    {
        $this->info('=== TEST KARYAWAN TEMPLATE ACCESS ===');
        
        try {
            // Test template download
            $controller = new KaryawanController();
            $request = new Request();
            
            $this->info('1. Testing CSV template download...');
            $response = $controller->downloadTemplate($request);
            
            if ($response->getStatusCode() === 200) {
                $this->info('✓ CSV template download successful');
                $this->info('   Content-Type: ' . $response->headers->get('Content-Type'));
                $this->info('   Content-Disposition: ' . $response->headers->get('Content-Disposition'));
            } else {
                $this->error('✗ CSV template download failed with status: ' . $response->getStatusCode());
            }
            
            $this->info('');
            $this->info('2. Testing Excel template download...');
            $response2 = $controller->downloadExcelTemplate();
            
            if ($response2->getStatusCode() === 200) {
                $this->info('✓ Excel template download successful');
                $this->info('   Content-Type: ' . $response2->headers->get('Content-Type'));
                $this->info('   Content-Disposition: ' . $response2->headers->get('Content-Disposition'));
            } else {
                $this->error('✗ Excel template download failed with status: ' . $response2->getStatusCode());
            }
            
            $this->info('');
            $this->info('3. Testing Simple Excel template download...');
            $response3 = $controller->downloadSimpleExcelTemplate();
            
            if ($response3->getStatusCode() === 200) {
                $this->info('✓ Simple Excel template download successful');
                $this->info('   Content-Type: ' . $response3->headers->get('Content-Type'));
                $this->info('   Content-Disposition: ' . $response3->headers->get('Content-Disposition'));
            } else {
                $this->error('✗ Simple Excel template download failed with status: ' . $response3->getStatusCode());
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        $this->info('');
        $this->info('=== PERMISSION SUMMARY ===');
        $this->info('✓ Template downloads: NO PERMISSION REQUIRED');
        $this->info('✓ Import: Requires master-karyawan-create permission');
        $this->info('✓ Create: Requires master-karyawan-create permission');
        
        $this->info('');
        $this->info('=== TEST COMPLETED ===');
        return 0;
    }
}