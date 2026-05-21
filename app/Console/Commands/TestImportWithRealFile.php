<?php

namespace App\Console\Commands;

use App\Http\Controllers\StockKontainerImportController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class TestImportWithRealFile extends Command
{
    protected $signature = 'test:import-real-file';

    protected $description = 'Test import with a real CSV file to see actual errors';

    public function handle()
    {
        $this->info('🧪 Testing Real File Import...');

        try {
            // Create a proper CSV file
            $csvContent = "Nomor Kontainer;Ukuran;Tipe Kontainer;Status;Tahun Pembuatan;Keterangan\n";
            $csvContent .= "TESTREAL001;20ft;Dry;available;2020;Test import real file\n";
            $csvContent .= "TESTREAL002;40ft;Reefer;maintenance;2019;Test import real file 2\n";

            // Write to temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'test_import_real').'.csv';
            file_put_contents($tempFile, $csvContent);
            $this->info("✅ Created test file: {$tempFile}");
            $this->info('📄 File contents:');
            $this->info($csvContent);

            // Create UploadedFile instance
            $uploadedFile = new UploadedFile(
                $tempFile,
                'test_import_real.csv',
                'text/csv',
                null,
                true
            );

            $this->info('✅ Created UploadedFile instance');
            $this->info('  - Original name: '.$uploadedFile->getClientOriginalName());
            $this->info('  - Size: '.$uploadedFile->getSize().' bytes');
            $this->info('  - MIME type: '.$uploadedFile->getClientMimeType());

            // Create request
            $request = new Request;
            $request->files->set('excel_file', $uploadedFile);

            $this->info('✅ Created request with file');

            // Test import
            $controller = new StockKontainerImportController;
            $this->info('🚀 Starting import process...');

            $response = $controller->import($request);

            $this->info('✅ Import completed!');
            $this->info('📤 Response type: '.get_class($response));

            // Check if it's a redirect response with session data
            if (method_exists($response, 'getSession')) {
                $session = $response->getSession();
                if ($session && $session->has('success')) {
                    $this->info('🎉 Success message: '.$session->get('success'));
                }
                if ($session && $session->has('error')) {
                    $this->error('❌ Error message: '.$session->get('error'));
                }
            }

            // Clean up
            if (file_exists($tempFile)) {
                unlink($tempFile);
                $this->info('🧹 Cleaned up temporary file');
            }

            // Check if records were created
            $testRecords = \App\Models\StockKontainer::whereIn('nomor_kontainer', ['TESTREAL001', 'TESTREAL002'])->get();
            $this->info('📊 Test records found in database: '.$testRecords->count());

            foreach ($testRecords as $record) {
                $this->info("  - {$record->nomor_kontainer}: {$record->ukuran}, {$record->status}");
            }

        } catch (\Exception $e) {
            $this->error('❌ Import test failed: '.$e->getMessage());
            $this->error('📍 File: '.$e->getFile());
            $this->error('📍 Line: '.$e->getLine());
            $this->error('🔍 Stack trace:');
            $this->error($e->getTraceAsString());
        }
    }
}
