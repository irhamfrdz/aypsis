<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kontainer;
use Illuminate\Support\Facades\DB;

class TestKontainerImport extends Command
{
    protected $signature = 'test:kontainer-import';
    protected $description = 'Test kontainer import functionality';

    public function handle()
    {
        $this->info('=== TEST IMPORT KONTAINER ===');
        
        // 1. Check CSV file
        $csvFile = base_path('test_kontainer_import.csv');
        if (!file_exists($csvFile)) {
            $this->error('File CSV test tidak ditemukan!');
            return 1;
        }
        
        $this->info('1. File CSV test ditemukan ✓');
        
        // 2. Read CSV
        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle, 1000, ';');
        
        $this->info('2. Header CSV: ' . implode(', ', $header));
        
        // 3. Validate header
        $expectedHeader = ['Awalan Kontainer', 'Nomor Seri', 'Akhiran', 'Ukuran', 'Vendor'];
        if ($header !== $expectedHeader) {
            $this->error('Header tidak sesuai!');
            return 1;
        }
        
        $this->info('3. Header valid ✓');
        
        // 4. Process data
        $importedCount = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                if (count($data) !== 5) {
                    $errors[] = "Data tidak lengkap: " . implode(';', $data);
                    continue;
                }
                
                [$awalan, $seri, $akhiran, $ukuran, $vendor] = $data;
                
                // Validate
                if (strlen($awalan) !== 4 || strlen($seri) !== 6 || strlen($akhiran) !== 1) {
                    $errors[] = "Format tidak valid: {$awalan}-{$seri}-{$akhiran}";
                    continue;
                }
                
                $nomorGabungan = $awalan . $seri . $akhiran;
                
                // Check duplicate
                if (Kontainer::where('nomor_seri_gabungan', $nomorGabungan)->exists()) {
                    $errors[] = "Sudah ada: {$nomorGabungan}";
                    continue;
                }
                
                // Create
                Kontainer::create([
                    'awalan_kontainer' => $awalan,
                    'nomor_seri_kontainer' => $seri,
                    'akhiran_kontainer' => $akhiran,
                    'nomor_seri_gabungan' => $nomorGabungan,
                    'ukuran' => $ukuran,
                    'tipe_kontainer' => 'Dry Container',
                    'vendor' => $vendor,
                    'status' => 'Tersedia'
                ]);
                
                $this->info("4." . ($importedCount + 1) . ". Import: {$nomorGabungan} - {$ukuran}ft - {$vendor}");
                $importedCount++;
            }
            
            DB::commit();
            
            $this->info('');
            $this->info('=== HASIL IMPORT ===');
            $this->info("Total berhasil diimport: {$importedCount}");
            
            if (!empty($errors)) {
                $this->warn('Errors:');
                foreach ($errors as $error) {
                    $this->warn("- {$error}");
                }
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        fclose($handle);
        
        // 5. Verify
        $this->info('');
        $this->info('=== VERIFIKASI ===');
        $total = Kontainer::count();
        $this->info("Total kontainer di database: {$total}");
        
        if ($total > 0) {
            $this->info('Data terbaru:');
            $kontainers = Kontainer::orderBy('created_at', 'desc')->take(5)->get();
            foreach ($kontainers as $k) {
                $this->info("- {$k->nomor_seri_gabungan} | {$k->ukuran}ft | {$k->vendor} | {$k->status}");
            }
        }
        
        $this->info('');
        $this->info('=== TEST SELESAI ===');
        
        return 0;
    }
}