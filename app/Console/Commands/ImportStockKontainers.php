<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\StockKontainer;

class ImportStockKontainers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:stock-kontainers {--file=aypsis1.sql} {--force : Force import without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import stock kontainers data from SQL backup file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Stock Kontainer Import Command');
        $this->info('=====================================');
        
        $file = $this->option('file');
        $force = $this->option('force');
        
        // Check if file exists
        if (!file_exists($file)) {
            $this->error("❌ File '$file' tidak ditemukan!");
            return 1;
        }
        
        $this->info("✅ File sumber: $file");
        
        // Check current data
        $currentCount = StockKontainer::count();
        $this->info("📊 Data saat ini: $currentCount records");
        
        if ($currentCount > 0 && !$force) {
            if (!$this->confirm('Data sudah ada. Lanjutkan import?')) {
                $this->info('❌ Import dibatalkan');
                return 0;
            }
            
            $action = $this->choice(
                'Pilih aksi:',
                ['skip' => 'Skip import', 'replace' => 'Hapus dan replace', 'add' => 'Tambah data baru'],
                'skip'
            );
            
            if ($action === 'skip') {
                $this->info('❌ Import dibatalkan');
                return 0;
            } elseif ($action === 'replace') {
                $this->info('🗑️ Menghapus data lama...');
                StockKontainer::truncate();
                $this->info('✅ Data lama berhasil dihapus');
            }
        }
        
        // Read and extract INSERT statement
        $this->info('🔍 Mencari data dalam file SQL...');
        
        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->error('❌ Tidak bisa membuka file SQL');
            return 1;
        }
        
        $insertStatement = '';
        $found = false;
        $lineNumber = 0;
        
        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            
            if (strpos($line, "INSERT INTO `stock_kontainers` VALUES") !== false) {
                $insertStatement = trim($line);
                $found = true;
                $this->info("✅ Data ditemukan pada baris $lineNumber");
                break;
            }
        }
        
        fclose($handle);
        
        if (!$found) {
            $this->error('❌ Data stock_kontainers tidak ditemukan dalam file SQL');
            return 1;
        }
        
        // Import data
        $this->info('📝 Mengimport data...');
        
        try {
            DB::beginTransaction();
            
            // Use INSERT IGNORE to handle duplicates
            $modifiedInsert = str_replace(
                "INSERT INTO `stock_kontainers` VALUES",
                "INSERT IGNORE INTO `stock_kontainers` VALUES",
                $insertStatement
            );
            
            $result = DB::statement($modifiedInsert);
            
            if (!$result) {
                throw new \Exception('Gagal mengeksekusi INSERT statement');
            }
            
            DB::commit();
            
            // Get final statistics
            $finalCount = StockKontainer::count();
            $imported = $finalCount - $currentCount;
            
            $this->info('🎉 Import berhasil!');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Data sebelumnya', $currentCount],
                    ['Data sekarang', $finalCount],
                    ['Data berhasil diimport', $imported],
                ]
            );
            
            // Show sample data
            $this->info('📋 Sample data:');
            $samples = StockKontainer::select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status')
                ->limit(5)
                ->get();
            
            $this->table(
                ['Nomor Kontainer', 'Ukuran', 'Tipe', 'Status'],
                $samples->map(function ($item) {
                    return [
                        $item->nomor_seri_gabungan,
                        $item->ukuran . 'ft',
                        $item->tipe_kontainer,
                        $item->status
                    ];
                })->toArray()
            );
            
            $this->info('✨ Migration selesai!');
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}